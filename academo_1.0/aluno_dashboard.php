<?php
ob_start(); // Adicionado para previnir erros de "headers already sent"
session_start();
// Bloco de autenticação
if (empty($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'aluno') {
    header("Location: index.php");
    exit;
}
$page = $_GET['page'] ?? 'inicio';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel do Aluno — Academo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
  />
  <link rel="icon" href="assets/Academo.jpeg" type="image/png">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Arial", sans-serif;
    }
    :root {
      --teal: #208489;
      --purple: #6b3df2;
      --line: #e6e8ec;
      --soft: #f6f8fb;
      --ink: #333;
    }
    body {
      display: flex; 
      min-height: 100vh;
      color: var(--ink);
      background: #fff;
    }

    /* ===== Sidebar ===== */
    .sidebar {
      width: 80px;
      background: var(--teal);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 30px; 
      padding: 20px 0;
      position: fixed;
      inset: 0 auto 0 0;
      z-index: 100;
    }
    .sidebar .profile {
        text-align: center;
        color: white;
    }
    .sidebar .profile i {
      font-size: 40px; 
      color: #cfd8dc;
    }
    .sidebar .profile p {
        font-size: 12px;
        font-weight: 600;
        margin-top: 5px;
        max-width: 70px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .sidebar nav {
      display: flex;
      flex-direction: column;
      gap: 25px; 
    }
    .sidebar nav a {
      color: #fff;
      font-size: 26px; 
      text-decoration: none;
      opacity: 0.95;
      transition: transform 0.2s, opacity 0.2s;
      width: 40px;           
      text-align: center;    
    }
    .sidebar nav a:hover {
      transform: scale(1.12);
      opacity: 1;
    }
    .sidebar nav a.active {
      text-shadow: 0 0 12px rgba(255, 255, 255, 0.6);
      transform: scale(1.1);
      opacity: 1;
    }
    .sidebar .logout {
        margin-top: auto;
        color: white;
        text-decoration: none;
        font-size: 24px;
        opacity: 0.8;
        transition: all 0.2s;
    }
    .sidebar .logout:hover {
        opacity: 1;
        transform: scale(1.1);
    }

    /* ===== Conteúdo ===== */
    .main-content {
      margin-left: 80px; 
      width: calc(100% - 80px);
      padding: 30px; 
      overflow-y: auto;
      background-color: #f4f7f6; 
      min-height: 100vh; 
    }
    
    .box { 
      border: 1px solid #e0e0e0; 
      padding: 25px; 
      border-radius: 8px; 
      margin-bottom: 25px; 
      background-color: #fff; 
      box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
    }
    h1 { 
      color: #1d888b; 
      font-weight: 600; 
      border-bottom: 1px solid #eee; 
      padding-bottom: 10px; 
      margin-top: 0; 
    }
    h2, h3 { color: #166d6f; border-bottom: 1px solid #eee; padding-bottom: 8px;}
    
    /* CSS DO CHATBOT */
    
    #academo-chat-button {
      position: fixed;
      bottom: 25px;
      right: 25px;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background-color: var(--purple); 
      color: white;
      font-size: 24px;
      border: none;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      z-index: 999;
      transition: transform 0.2s;
    }
    #academo-chat-button:hover {
      transform: scale(1.1);
    }
    
    #chat-container {
      position: fixed;
      bottom: 100px;
      right: 25px;
      width: 450px; 
      height: 600px;
      border: 1px solid var(--line);
      border-radius: 8px;
      background-color: #ffffff;
      flex-direction: column;
      height: 600px;
      overflow: hidden;
      box-shadow: 0 6px 16px rgba(0,0,0,0.18);
      z-index: 1000;
    }
    .hidden {
      display: none;
    }
    .visible {
      display: flex;
    }
    #chat-window {
      flex-grow: 1;
      padding: 20px;
      overflow-y: auto;
      border-bottom: 1px solid var(--line);
      display: flex;
      flex-direction: column;
    }
    .message {
      margin-bottom: 15px;
      padding: 10px 15px;
      border-radius: 18px;
      max-width: 80%;
      line-height: 1.4;
    }
    .received {
      background-color: var(--soft);
      align-self: flex-start;
    }
    .sent {
      background-color: var(--purple);
      color: white;
      align-self: flex-end;
    }
    .error {
      background-color: #ffdddd;
      color: #d8000c;
      align-self: flex-start;
    }
    #input-container {
      display: flex;
      padding: 15px;
      border-top: 1px solid var(--line);
    }
    #message-input {
      flex-grow: 1;
      border: 1px solid var(--line);
      border-radius: 18px;
      padding: 10px 15px;
      font-size: 16px;
    }
    #send-button {
      border: none;
      background-color: var(--purple);
      color: white;
      padding: 10px 20px;
      border-radius: 18px;
      margin-left: 10px;
      cursor: pointer;
      font-size: 16px;
    }
    #send-button:hover {
      filter: brightness(1.1);
    }
    /* CSS DO CABEÇALHO DO CHAT */
    #chat-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
      background: var(--soft);
      border-bottom: 1px solid var(--line);
      border-radius: 8px 8px 0 0;
}
    #chat-header span { 
      font-weight: 600; 
      color: var(--teal); 
}
    #clear-chat-button {
      background: none !important; 
      border: none !important;
      color: #999 !important;
      font-size: 16px !important;
      cursor: pointer;
      padding: 5px !important;
      margin: 0 !important;
}
    #clear-chat-button:hover { 
      color: var(--purple) !important;
      background: none !important;
}
  </style>
</head>
<body data-user-role="<?= htmlspecialchars($_SESSION['usuario_tipo']) ?>">
    <aside class="sidebar">
        
        <div class="profile">
            <i class="fa-solid fa-user"></i>
        </div>

        <nav>
            <a href="?page=inicio" title="Início" class="<?= ($page === 'inicio') ? 'active' : '' ?>">
                <i class="fa-solid fa-house"></i>
            </a>
            <a href="?page=materias" title="Minhas Matérias & Notas" class="<?= ($page === 'materias' || $page === 'detalhe_materia') ? 'active' : '' ?>">
                <i class="fa-solid fa-book-open"></i>
            </a>
            <a href="?page=presenca" title="Minhas Presenças" class="<?= ($page === 'presenca') ? 'active' : '' ?>">
                <i class="fa-solid fa-clipboard-check"></i>
            </a>
            <a href="?page=tarefas" title="Minhas Tarefas" class="<?= ($page === 'tarefas') ? 'active' : '' ?>">
                <i class="fa-solid fa-list-check"></i>
            </a>
        </nav>

        <a href="logout.php" class="logout" title="Sair">
            <i class="fa-solid fa-door-open"></i>
        </a>
    </aside>

    <main class="main-content">
        <?php
        $allowed_pages = ['inicio', 'materias', 'presenca', 'tarefas', 'detalhe_materia'];
        
        if (in_array($page, $allowed_pages)) {
            $page_path = __DIR__ . "/aluno/{$page}.php"; 
            if (file_exists($page_path)) {
                include $page_path;
            } else {
                echo "<div class='box'><h1>Erro: Página não encontrada.</h1><p>O arquivo <code>/aluno/{$page}.php</code> não foi encontrado.</p></div>";
            }
        } else {
            include __DIR__ . '/aluno/inicio.php';
        }
        ?>
    </main>
  
  <button id="academo-chat-button" class="chat-fab" title="Abrir Chat Academo">
    <i class="fa-solid fa-comment-dots"></i>
</button>
 
<div id="chat-container" class="hidden">
    <div id="chat-header">
        <span>Chat Academo</span>
        <button id="clear-chat-button" title="Limpar conversa">
            <i class="fa-solid fa-trash"></i>
        </button>
    </div>
    <div id="chat-window">
    </div>
    <div id="input-container">
        <input type="text" id="message-input" placeholder="Digite sua mensagem...">
        <button id="send-button">Enviar</button>
    </div>
</div>

  <script>
    // FUNÇÃO DE LÓGICA DO LOCALSTORAGE 
    function getOrCreateSessionId(userId) {
        let storedId = localStorage.getItem('academo_session_id');
        let baseId = `chat_session_${userId}`;
        if (!storedId || !storedId.startsWith(baseId)) {
            let newId = `${baseId}_${Date.now()}`;
            localStorage.setItem('academo_session_id', newId);
            console.log("Nova sessão de chat criada:", newId);
            return newId;
        }
        console.log("Sessão de chat recuperada:", storedId);
        return storedId;
    }

    // 1. LÓGICA PARA ABRIR/FECHAR O WIDGET
    const openChatButton = document.getElementById('academo-chat-button');
    const chatContainer = document.getElementById('chat-container');
    const chatIcon = openChatButton.querySelector('i');
    const clearChatButton = document.getElementById('clear-chat-button');

    // Botão de limpar o chat
    clearChatButton.addEventListener('click', () => {
        if (confirm("Tem certeza que deseja apagar todo o histórico desta conversa?")) {
            // 1. Destrói a chave salva no "cofre" do navegador
            localStorage.removeItem('academo_session_id');
            // 2. Recarrega a página para iniciar uma nova sessão
            location.reload();
        }
    });

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
    
    // Pega dados da sessão do PHP
    const userRole = '<?= htmlspecialchars($_SESSION['usuario_tipo']) ?>'; 
    const userName = '<?= htmlspecialchars($_SESSION['usuario_nome']) ?>';
    const phpSessionId = '<?= htmlspecialchars($_SESSION['usuario_id']) ?>';
    
    const sessionId = getOrCreateSessionId(phpSessionId);

    sendButton.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Função para carregar o histórico
    async function loadHistory() {
        console.log("(Frontend) Carregando histórico para:", sessionId);
        chatWindow.innerHTML = '';
        displayMessage('Carregando histórico...', 'received', true); 

        try {
            const response = await fetch('https://academo-project.vercel.app/api/chat', { 
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    sessionId: sessionId,
                    message: "__GET_HISTORY__", // usa o history de backend
                    context: { page: window.location.href, role: userRole, name: userName }
                }),
            });

            if (!response.ok) throw new Error('Falha ao carregar histórico');

            const data = await response.json();
            
            const loading = document.getElementById('loading-message');
            if (loading) loading.remove();

            if (data.history && data.history.length > 0) {
                data.history.forEach(msg => {
                    const type = msg.role === 'user' ? 'sent' : 'received';
                    displayMessage(msg.parts[0].text, type);
                });
                // Faz com que espere 0 milissegundos antes de scrollar
                setTimeout(() => {
                    chatWindow.scrollTop = chatWindow.scrollHeight;
                }, 0); 

            } else {
                // Se não tiver histórico, mostra a saudação
                displayMessage('Olá! Sou o Academo. Como posso ajudar?', 'received');
            }

        } catch (error) {
            console.error('Erro ao carregar histórico:', error);
            updateLastMessage(`Não consegui carregar seu histórico. Começando uma nova conversa.`, 'error');
        }
    }

    async function sendMessage() {
        const userMessage = messageInput.value.trim();
        if (!userMessage) return;

        const currentPage = window.location.href; 
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
                    context: {
                        page: currentPage,
                        role: userRole,
                        name: userName 
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
            updateLastMessage(`Desculpe, ocorreu um erro de conexão. Tente novamente.`, 'error');
        }
    }

    // 3. FUNÇÕES AUXILIARES 
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
    }

    loadHistory();
  </script>

</body>
</html>