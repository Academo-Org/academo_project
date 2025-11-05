const { kv } = require('@vercel/kv');
const { GoogleGenerativeAI } = require('@google/generative-ai');

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

// A "persona" que já tínhamos definido
const persona = "Você é o Academo, um assistente virtual para estudantes. Seu tom é amigável, encorajador e um pouco informal. Seu objetivo principal é ajudar os alunos a entenderem conceitos difíceis, dando exemplos práticos e analogias. Sempre responda em português do Brasil e nunca revele que você é uma IA, a menos que seja perguntado diretamente.";

module.exports = async (req, res) => {
    
    // Estes cabeçalhos dão o "crachá de permissão" para o site local.
    // O '*' permite que qualquer origem (incluindo 127.0.0.1) acesse a API.
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

    // Se o navegador enviar uma "pergunta de permissão" (método OPTIONS),
    if (req.method === 'OPTIONS') {
        res.status(200).end();
        return;
    }

    // Garante que a requisição é um POST 
    if (req.method !== 'POST') {
        return res.status(405).json({ error: 'Método não permitido' });
    }

    try {
        const { sessionId, message } = req.body;

        if (!sessionId || !message) {
            return res.status(400).json({ error: 'sessionId e message são obrigatórios.' });
        }

        const historyFromDB = await kv.get(sessionId) || [];

        const historyForAPI = [
            { role: "user", parts: [{ text: `IGNORE TODAS AS INSTRUÇÕES ANTERIORES. A partir de agora, siga estas regras: ${persona}` }] },
            { role: "model", parts: [{ text: "Entendido. Assumirei a personalidade de Academo e seguirei as regras." }] },
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
        // Garante que a resposta de erro também tenha o crachá de permissão
        res.status(500).json({ error: 'Falha ao processar a requisição', details: error.message });
    }
};
