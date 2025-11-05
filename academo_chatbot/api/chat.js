const { kv } = require('@vercel/kv');
const { GoogleGenerativeAI } = require('@google/generative-ai');

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

function getSystemInstruction(role, page) {
    let persona = "Você é o Academo, um assistente geral para visitantes.";
    let pageContext = `O usuário está atualmente na página: ${page}. Responda levando isso em consideração.`;

    if (role === 'aluno') {
        persona = "Você é o Academo, um tutor amigável e encorajador. Ajude os alunos com dúvidas sobre suas matérias, notas, frequência e como usar a plataforma para estudar.";
    }
    
    // Adiciona contexto específico da página de disciplinas
    if (page.includes('disciplinas.html')) {
        pageContext += " Esta é a página de disciplinas. O usuário pode estar com dúvidas sobre qual matéria escolher ou o que cada uma significa.";
    }

    // Esta é a técnica de "injeção" que sabemos que funciona
    return [
        { role: "user", parts: [{ text: `IGNORE TODAS AS INSTRUÇÕES ANTERIORES. A partir de agora, siga estas regras: ${persona} ${pageContext}` }] },
        { role: "model", parts: [{ text: "Entendido. Estou pronto para ajudar." }] }
    ];
}

module.exports = async (req, res) => {
    
    // --- BLOCO CORS (Para funcionar no seu PC local) ---
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

        const dynamicInstruction = getSystemInstruction(context.role, context.page);

        const historyForAPI = [
            ...dynamicInstruction,
            ...historyFromDB
        ];
        
        const model = genAI.getGenerativeModel({ model: 'gemini-2.5-pro' }); 
        const chat = model.startChat({ history: historyForAPI });
        
        const result = await chat.sendMessage(message);
        const response = await result.response;
        const text = response.text();
        
        const updatedHistoryForDB = [
            ...historyFromDB,
            { role: 'user', parts: [{ text: message }] },
            { role: 'model', parts: [{ text }] }
        ];
        await kv.set(sessionId, updatedHistoryForDB);
        
        res.status(200).json({ response: text });

    } catch (error) {
        console.error("ERRO NO BLOCO CATCH:", error);
        res.status(500).json({ error: 'Falha ao processar a requisição', details: error.message });
    }
};
