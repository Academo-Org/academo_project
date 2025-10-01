import { kv } from '@vercel/kv';
import { GoogleGenerativeAI } from '@google/generative-ai';

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

export default async function handler(req, res) {
    console.log("LOG 1: Função iniciada.");
    try {
        const { sessionId, message } = req.body;
        console.log(`LOG 2: Recebido sessionId: ${sessionId}`);

        if (!sessionId || !message) {
            console.error("ERRO: sessionId ou message faltando.");
            return res.status(400).json({ error: 'sessionId e message são obrigatórios.' });
        }

        // Pega o histórico antes de adicionar a nova mensagem
        const history = await kv.get(sessionId) || [];
        console.log(`LOG 3: Histórico recuperado do KV. Tamanho: ${history.length}`);

        // Inicia o chat com o histórico antigo
        const model = genAI.getGenerativeModel({ model: 'gemini-2.5-pro' }); // Usando gemini-2.5-pro
        const chat = model.startChat({ history });
        
        console.log("LOG 4: Enviando nova mensagem para a API Gemini...");
        const result = await chat.sendMessage(message);
        const response = await result.response;
        const text = response.text();
        console.log("LOG 5: Resposta recebida da API Gemini.");

        // Atualiza o histórico com a pergunta do usuário e a resposta da IA
        const updatedHistory = [
            ...history,
            { role: 'user', parts: [{ text: message }] },
            { role: 'model', parts: [{ text }] }
        ];

        // Salva o histórico completo e atualizado
        await kv.set(sessionId, updatedHistory);
        console.log(`LOG 6: Histórico atualizado salvo no KV. Novo tamanho: ${updatedHistory.length}`);

        res.status(200).json({ response: text });
        console.log("LOG 7: Resposta enviada para o frontend com sucesso.");

    } catch (error) {
        console.error("ERRO NO BLOCO CATCH:", error);
        res.status(500).json({ error: 'Falha ao processar a requisição', details: error.message });
    }
}