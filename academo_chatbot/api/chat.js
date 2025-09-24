import { GoogleGenerativeAI } from "@google/generative-ai";

export default async function handler(request, response) {
  // Configuração da API Key
  const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);
  const model = genAI.getGenerativeModel({ model: "gemini-2.5-flash-image-preview" });

  try {
    const chatMessage = request.body.message;

    // Adicione esta linha para ver a mensagem que a função está recebendo
    console.log("Mensagem recebida do front-end:", chatMessage);

    const result = await model.generateContent(chatMessage);
    const apiResponse = result.response;

    // Adicione esta linha para ver a resposta que a Google devolveu
    console.log("Resposta da Google:", apiResponse);

    // Verifique se a resposta tem texto
    const textResponse = apiResponse.text();
    console.log("Texto extraído:", textResponse);

    response.status(200).json({ response: textResponse });

  } catch (error) {
    console.error("Erro na função de chat:", error);
    response.status(500).json({ error: "Ocorreu um erro na API." });
  }
}