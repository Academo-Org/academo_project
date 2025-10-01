// Usando 'require' em vez de 'import'
const { kv } = require('@vercel/kv');
const { GoogleGenerativeAI } = require('@google/generative-ai');

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

// Usando 'module.exports' para exportar a função principal
module.exports = async (req, res) => {
    console.log("LOG 1: Função iniciada.");
    try {
        const { sessionId, message } = req.body;
        console.log(`LOG 2: Recebido sessionId: ${sessionId}`);

        if (!sessionId || !message) {
            console.error("ERRO: sessionId ou message faltando.");
            return res.status(400).json({ error: 'sessionId e message são obrigatórios.' });
        }

        const history = await kv.get(sessionId) || [];
        console.log(`LOG 3: Histórico recuperado do KV. Tamanho: ${history.length}`);

        const model = genAI.getGenerativeModel({ model: 'gemini-2.5-pro' });
        const chat = model.startChat({ history });
        
        console.log("LOG 4: Enviando nova mensagem para a API Gemini...");
        const result = await chat.sendMessage(message);
        const response = await result.response;
        const text = response.text();
        console.log("LOG 5: Resposta recebida da API Gemini.");

        const updatedHistory = [
            ...history,
            { role: 'user', parts: [{ text: message }] },
            { role: 'model', parts: [{ text }] }
        ];

        await kv.set(sessionId, updatedHistory);
        console.log(`LOG 6: Histórico atualizado salvo no KV. Novo tamanho: ${updatedHistory.length}`);

        // Verifica o erro que está acontecendo
        const verificationRead = await kv.get(sessionId);
        console.log(`LOG 6.5: Lendo de volta o que foi salvo. Tamanho recuperado: ${verificationRead ? verificationRead.length : 'null'}`);
        
        res.status(200).json({ response: text });
        console.log("LOG 7: Resposta enviada para o frontend com sucesso.");

    } catch (error) {
        console.error("ERRO NO BLOCO CATCH:", error);
        res.status(500).json({ error: 'Falha ao processar a requisição', details: error.message });
    }
};
