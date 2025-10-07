const { kv } = require('@vercel/kv');
const { GoogleGenerativeAI } = require('@google/generative-ai');

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

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

        const historyFromDB = await kv.get(sessionId) || [];

        // Histórico para a API que sempre começa com a instrução de sistema
        const historyForAPI = [
            systemInstruction.parts[0], // Apenas a parte da instrução
            ...historyFromDB
        ];
        
        // O modelo não precisa mais do parâmetro systemInstruction
        const model = genAI.getGenerativeModel({ model: 'gemini-2.5-pro' }); 

        // Inicia o chat com o histórico "injetado"
        const chat = model.startChat({ history: historyForAPI });
        const result = await chat.sendMessage(message);
        const response = await result.response;
        const text = response.text();

        // Salva no banco o histórico original, sem a instrução do sistema,
        // para não poluir o banco de dados com a mesma instrução repetidamente.
        const updatedHistoryForDB = await chat.getHistory();
        
        // Remove a instrução do sistema antes de salvar
        const cleanHistory = updatedHistoryForDB.slice(1);
        
        await kv.set(sessionId, cleanHistory);
        
        res.status(200).json({ response: text });

    } catch (error) {
        console.error("ERRO NO BLOCO CATCH:", error);
        res.status(500).json({ error: 'Falha ao processar a requisição', details: error.message });
    }
};
