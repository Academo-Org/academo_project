import { kv } from '@vercel/kv';
import { GoogleGenerativeAI } from '@google/generative-ai';

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

export default async function handler(req, res) {
    if (req.method !== 'POST') {
        return res.status(405).json({ error: 'Método não permitido' });
    }

    try {
        const { sessionId, message } = req.body;

        if (!sessionId || !message) {
            return res.status(400).json({ error: 'sessionId e message são obrigatórios.' });
        }

        const history = await kv.get(sessionId) || [];
        history.push({ role: 'user', parts: [{ text: message }] });

        const model = genAI.getGenerativeModel({ model: 'gemini-1.5-flash' });
        const chat = model.startChat({ history });
        
        const result = await chat.sendMessage(message);
        const response = await result.response;
        const text = response.text();

        history.push({ role: 'model', parts: [{ text }] });
        await kv.set(sessionId, history);

        res.status(200).json({ response: text });

    } catch (error) {
        console.error('Erro na função da API:', error);
        res.status(500).json({ error: 'Falha ao processar a requisição', details: error.message });
    }
}
