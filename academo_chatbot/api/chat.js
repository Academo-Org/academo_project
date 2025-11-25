const { kv } = require('@vercel/kv');
const { GoogleGenerativeAI } = require('@google/generative-ai');

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

// --- BASE DE CONHECIMENTO VISUAL ---
const KNOWLEDGE_BASE = `
MANUAL DE INTERFACE E NAVEGAÇÃO DO ACADEMO:

[DIRETRIZES DE ESTILO - IMPORTANTE]
1. NUNCA mencione nomes de arquivos técnicos (como 'aluno_dashboard.php', '?page=inicio' ou 'href').
2. Fale a linguagem do usuário: use os nomes dos menus e descreva os ícones visuais.
3. Se o usuário perguntar onde clicar, guie-o pela "Barra Lateral Verde" à esquerda.

[IDENTIDADE VISUAL GERAL]
- Barra Lateral (Menu): Cor Verde Petróleo (Teal). Fica à esquerda.
- Botão do Chat: Cor Roxa. Fica flutuando no canto inferior direito.
- Botão de Sair: Fica no final da barra lateral, ícone de uma porta aberta.
- Limpar Chat: Para apagar o histórico, há um ícone de "Lixeira" no topo da janelinha do chat.

[PERFIL: ALUNO] (O que o aluno vê na tela)
- Ícone de Perfil: Usuário comum.
1. Início (Ícone Casa): Visão geral.
2. Matérias (Ícone Livro Aberto): Onde o aluno vê suas disciplinas, notas lançadas e faltas detalhadas.
3. Presença (Ícone Prancheta com Check): Histórico geral de presenças.
4. Tarefas (Ícone Lista com Check): Onde o aluno vê lições pendentes e prazos.

[PERFIL: PROFESSOR] (O que o professor vê na tela)
- Ícone de Perfil: Usuário com gravata.
1. Início (Ícone Casa): Painel principal.
2. Chamada (Ícone Prancheta com Check): Onde o professor registra quem faltou.
3. Lançar Notas (Ícone Lápis no Quadrado): Onde o professor digita as notas das turmas.
4. Gerenciar Tarefas (Ícone Caneta sobre Papel): Onde o professor CRIA novas tarefas para os alunos.
5. Avaliar Tarefas (Ícone Lista com Check): Onde o professor CORRIGE as tarefas enviadas.

[PERFIL: COORDENAÇÃO] (O que o coordenador vê na tela)
- Ícone de Perfil: Usuário com escudo.
1. Início (Ícone Casa): Visão da gestão.
2. Cadastrar Usuário (Ícone Usuário com +): Tela para criar conta de Aluno ou Professor.
3. Gerenciar Usuários (Ícone Usuários com Engrenagem): Tela para editar, banir ou excluir contas.
4. Gerenciar Turmas (Ícone Escola): Onde cria ou edita as turmas/disciplinas.
5. Gerenciar Matrículas (Ícone Chapéu de Formatura): Onde vincula alunos às turmas.
`;

// --- FUNÇÃO QUE CRIA A PERSONALIDADE DINÂMICA E LÊ A TELA ---
function getSystemInstruction(role, page, name, screenData) {
    // 1. Define a Persona
    let persona = "";
    if (role === 'professor') {
        persona = `Você é o Academo, assistente do Professor(a) ${name}.`;
    } else if (role === 'aluno') {
        persona = `Você é o Academo, tutor do aluno(a) ${name}.`;
    } else if (role === 'coordenacao') {
        persona = `Você é o Academo, assistente da Coordenação (usuário: ${name}).`;
    } else {
        persona = `Você é o Academo. O usuário é ${name}.`;
    }

    // 2. Analisa o conteúdo da tela (Injeção de Contexto)
    let visualContext = "";
    if (screenData && screenData.length > 10) {
        visualContext = `
        [[VISÃO DA TELA ATUAL]]
        Abaixo está o conteúdo textual exato que o usuário está vendo agora na página. 
        Use isso para responder perguntas específicas sobre notas, tarefas ou dados listados:
        """
        ${screenData}
        """
        `;
    } else {
        visualContext = "[[VISÃO DA TELA]] Não foi possível ler o conteúdo específico da página.";
    }

    // 3. Montagem das Regras (MODO DEMONSTRAÇÃO)
    let rules = `
    INSTRUÇÕES MESTRAS:
    1. ${persona}
    2. O usuário está na URL: ${page}.
    3. ${visualContext}
    4. MODO PROFESSOR ATIVADO: Se o usuário pedir dicas de estudo ou explicações, IDENTIFIQUE o nome da matéria na tela (ex: "Estrutura de Dados") e USE SEU CONHECIMENTO GERAL de IA para sugerir tópicos acadêmicos reais sobre esse assunto. Não precisa estar escrito na tela, use seu treinamento.
    5. Se a pergunta for sobre *dados pessoais* (notas, faltas), aí sim: LEIA estritamente o que está na tela.
    6. Se a pergunta for sobre navegação, use o Manual Visual.
    
    ${KNOWLEDGE_BASE}
    `;
    

    return [
        { role: "user", parts: [{ text: `SYSTEM OVERRIDE: ${rules}` }] },
        { role: "model", parts: [{ text: `Entendido. Estou analisando a tela de ${name} e pronto para ajudar com base no que estou "vendo" e no meu manual.` }] }
    ];
}

module.exports = async (req, res) => {
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
            return res.status(400).json({ error: 'Dados incompletos.' });
        }
        
        // Recupera histórico
        const historyFromDB = await kv.get(sessionId) || [];

        if (message === "__GET_HISTORY__") {
            res.status(200).json({ history: historyFromDB });
            return;
        }

        // Gera instrução atualizada com o contexto visual correto
        const dynamicInstruction = getSystemInstruction(context.role, context.page, context.name, context.screenData);

        const historyForAPI = [
            ...dynamicInstruction,
            ...historyFromDB
        ];
        
        const model = genAI.getGenerativeModel({ model: 'gemini-2.5-pro' }); 
        const chat = model.startChat({ history: historyForAPI });
        
        const result = await chat.sendMessage(message);
        const response = await result.response;
        const text = response.text();
        
        const updatedHistory = await chat.getHistory();
        // Remove o prompt de sistema para não poluir o banco
        const cleanHistoryToSave = updatedHistory.slice(2);
        
        await kv.set(sessionId, cleanHistoryToSave);
        
        res.status(200).json({ response: text });

    } catch (error) {
        console.error("ERRO API:", error);
        res.status(500).json({ error: 'Erro interno', details: error.message });
    }
};
