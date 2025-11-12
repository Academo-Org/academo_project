const { kv } = require('@vercel/kv');
const { GoogleGenerativeAI } = require('@google/generative-ai');

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

// --- BASE DE CONHECIMENTO (O "MANUAL" DA IA) ---
const KNOWLEDGE_BASE = `
Guia de Funcionalidades do Site Academo:
- 'aluno_dashboard.php?page=inicio': É o dashboard principal do aluno.
- 'aluno_dashboard.php?page=materias': Página onde o aluno vê uma lista de suas matérias matriculadas.
- 'aluno_dashboard.php?page=detalhe_materia': Página onde o aluno vê detalhes de UMA matéria, como notas e frequência.
- 'aluno_dashboard.php?page=presenca': Página onde o aluno pode ver seu histórico de presenças.
- 'aluno_dashboard.php?page=tarefas': Página onde o aluno vê suas tarefas pendentes.
- 'coordenacao_dashboard.php?page=inicio': Dashboard principal do coordenador.
- 'coordenacao_dashboard.php?page=cadastrar_usuario': Página onde o coordenador pode criar novas contas de alunos ou professores.
- 'coordenacao_dashboard.php?page=gerenciar_usuarios': Página onde o coordenador pode editar ou excluir usuários existentes.
- 'login.html': Página para o usuário entrar no sistema.
`;

// --- FUNÇÃO QUE CRIA A PERSONALIDADE DINÂMICA ---
function getSystemInstruction(role, page, name) {
    let persona = `Você é o Academo, um assistente geral. O usuário se chama ${name}.`;
    let pageContext = `O usuário está atualmente na página: ${page}. Use o guia de funcionalidades para responder perguntas sobre o site.`;

    if (role === 'professor') {
        persona = `Você é o Academo, um assistente para professores. O professor se chama ${name}. Ajude-o com dúvidas sobre como lançar notas, fazer chamadas e gerenciar suas turmas.`;
    } else if (role === 'aluno') {
        persona = `Você é o Academo, um tutor amigável e encorajador. O aluno se chama ${name}. Ajude-o com dúvidas sobre suas matérias, notas, frequência e como usar a plataforma para estudar.`;
    } else if (role === 'coordenacao') {
        persona = `Você é o Academo, um assistente executivo para a coordenação. O coordenador se chama ${name}. Ajude-o com dúvidas sobre gerenciamento de usuários, turmas e matrículas.`;
    }

    let rules = `Siga estas regras: ${persona} ${pageContext} Use o seguinte guia de funcionalidades: ${KNOWLEDGE_BASE}`;

    // Técnica de "Injeção de Persona"
    return [
        { role: "user", parts: [{ text: `IGNORE TODAS AS INSTRUÇÕES ANTERIORES. ${rules}` }] },
        { role: "model", parts: [{ text: `Entendido. Assumirei minha função e estou pronto para ajudar ${name}.` }] }
    ];
}

module.exports = async (req, res) => {
    
    // Bloco CORS
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
    if (req.method === 'OPTIONS') {
        res.status(200).end();
        return;
    }
    if (req.method !== 'POST') {
        return res.status(405).json({ error: 'Método não permitido' });
    }

    try {
        const { sessionId, message, context } = req.body;

        if (!sessionId || !message || !context) {
            return res.status(400).json({ error: 'sessionId, message e context são obrigatórios.' });
        }
        
        const historyFromDB = await kv.get(sessionId) || [];

        // --- MUDANÇA IMPORTANTE AQUI ---
        // Se a mensagem for o nosso "comando secreto", só devolve o histórico
        if (message === "__GET_HISTORY__") {
            console.log(`(Backend) Histórico recuperado para ${sessionId}, tamanho: ${historyFromDB.length}`);
            // Retorna o histórico de mensagens
            res.status(200).json({ history: historyFromDB });
            return;
        }
        // --- FIM DA MUDANÇA ---

        const dynamicInstruction = getSystemInstruction(context.role, context.page, context.name);

        const historyForAPI = [
            ...dynamicInstruction,
            ...historyFromDB
        ];
        
        const model = genAI.getGenerativeModel({ model: 'gemini-pro' }); // Use o modelo que funcionou para você
        const chat = model.startChat({ history: historyForAPI });
        
        const result = await chat.sendMessage(message);
        const response = await result.response;
        const text = response.text();
        
        const updatedHistory = await chat.getHistory();
        const cleanHistoryToSave = updatedHistory.slice(2);
        
        await kv.set(sessionId, cleanHistoryToSave);
        
        res.status(200).json({ response: text });

    } catch (error) {
        console.error("ERRO NO BLOCO CATCH:", error);
        res.status(500).json({ error: 'Falha ao processar a requisição', details: error.message });
    }
};
