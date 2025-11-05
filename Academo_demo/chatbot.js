document.addEventListener('DOMContentLoaded', () => {

    // 1. LÓGICA PARA ABRIR/FECHAR O WIDGET
    const openChatButton = document.getElementById('academo-chat-button');
    const chatContainer = document.getElementById('chat-container');
    const chatIcon = openChatButton.querySelector('i'); 

    openChatButton.addEventListener('click', () => {
        const isHidden = chatContainer.classList.contains('hidden');
        chatContainer.classList.toggle('visible');
        chatContainer.classList.toggle('hidden');

        if (isHidden) {
            chatIcon.classList.remove('fa-comment-dots');
            chatIcon.classList.add('fa-xmark'); 
            openChatButton.setAttribute('title', 'Fechar Chat');
        } else {
            chatIcon.classList.remove('fa-xmark');
            chatIcon.classList.add('fa-comment-dots');
            openChatButton.setAttribute('title', 'Abrir Chat Academo');
        }
    });


    // 2. LÓGICA PRINCIPAL DO CHAT
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const chatWindow = document.getElementById('chat-window');
    const sessionId = `chat_session_${Date.now()}_${Math.random().toString(36).substring(2, 9)}`;

    sendButton.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    async function sendMessage() {
        const userMessage = messageInput.value.trim();
        if (!userMessage) return; 

        // --- PEGANDO O CONTEXTO ATUAL ---
        const currentPage = window.location.href; 
        const userRole = 'aluno'; // Simulando como "aluno"
        // --- FIM DA MUDANÇA ---

        displayMessage(userMessage, 'sent'); 
        messageInput.value = ''; 
        displayMessage('Digitando...', 'received', true); 

        try {
            const response = await fetch('https://academo-project.vercel.app/api/chat', { 
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    sessionId: sessionId,
                    message: userMessage,
                    // --- ENVIANDO O CONTEXTO QUE A API ESPERA ---
                    context: {
                        page: currentPage,
                        role: userRole 
                    }
                }),
            }); 

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || `Erro de servidor: ${response.status}`);
            }

            const data = await response.json();
            updateLastMessage(data.response, 'received'); 

        } catch (error) {
            console.error('Erro ao chamar a API:', error);
            updateLastMessage(`Desculpe, ocorreu um erro. Tente novamente.`, 'error');
        }
    }

    // 3. FUNÇÕES AUXILIARES (com Markdown)
    function displayMessage(message, type, isLoading = false) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', type);
        if (isLoading) {
            messageElement.id = 'loading-message'; 
        }

        if (type === 'sent') {
            messageElement.textContent = message; 
        } else {
            messageElement.innerHTML = marked.parse(message); 
        }

        chatWindow.appendChild(messageElement);
        chatWindow.scrollTop = chatWindow.scrollHeight; 
    }

    function updateLastMessage(newMessage, type) {
        const loadingElement = document.getElementById('loading-message');
        if (loadingElement) {
            loadingElement.id = ''; 
            loadingElement.className = `message ${type}`;
            loadingElement.innerHTML = marked.parse(newMessage);
        } else {
            displayMessage(newMessage, type);
        }
        chatWindow.scrollTop = chatWindow.scrollHeight; 
    }

});