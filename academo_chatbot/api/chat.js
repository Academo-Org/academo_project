const { kv } = require('@vercel/kv');
const { GoogleGenerativeAI } = require('@google/generative-ai');

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

module.exports = async (req, res) => {
    try {
        const { sessionId, message } = req.body;
        const history = await kv.get(sessionId) || [];

        const model = genAI.getGenerativeModel({ model: 'gemini-2.5-pro' });
        const chat = model.startChat({ history });
        
        const result = await chat.sendMessage(message);
        const response = await result.response;
        const text = response.text();

        // Pega o histórico final e correto diretamente da sessão de chat
        const updatedHistory = await chat.getHistory();

        // Salva o histórico correto no banco de dados
        await kv.set(sessionId, updatedHistory);
        
        console.log(`Histórico salvo com ${updatedHistory.length} partes.`);

        res.status(200).json({ response: text });

    } catch (error) {
        console.error("ERRO NO BLOCO CATCH:", error);
        res.status(500).json({ error: 'Falha ao processar a requisição', details: error.message });
    }
};
