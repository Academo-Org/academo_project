const { kv } = require('@vercel/kv');
const { GoogleGenerativeAI } = require('@google/generative-ai');

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

// Define a personalidade
const systemInstruction = {
    role: "model",
    parts: [{ text: "Você é o Academo, um assistente virtual para estudantes. Seu tom é amigável, encorajador e um pouco informal. Seu objetivo principal é ajudar os alunos a entenderem conceitos difíceis, dando exemplos práticos e analogias. Sempre responda em português do Brasil." }],
};

module.exports = async (req, res) => {
    try {
        const { sessionId, message } = req.body;

        if (!sessionId || !message) {
            return res.status(400).json({ error: 'sessionId e message são obrigatórios.' });
        }

        const history = await kv.get(sessionId) || [];

        // Aplica a "personalidade" da IA
        const model = genAI.getGenerativeModel({
            model: 'gemini-2.5-pro', 
            systemInstruction: systemInstruction
        });

        const chat = model.startChat({ history });
        const result = await chat.sendMessage(message);
        const response = await result.response;
        const text = response.text();

        // Atualização do históricos
        const updatedHistory = await chat.getHistory();
        await kv.set(sessionId, updatedHistory);
        
        res.status(200).json({ response: text });

    } catch (error) {
        // Manterei apenas o ultimo console.error para possiveis falhas futuras
        console.error("ERRO NO BLOCO CATCH:", error);
        res.status(500).json({ error: 'Falha ao processar a requisição', details: error.message });
    }
};

