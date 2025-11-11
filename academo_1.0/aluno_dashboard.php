<?php
session_start();
// Bloco de autenticação PHP original (Mantido)
if (empty($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'aluno') {
    header("Location: index.php"); // index.php é sua página de login
    exit;
}
// Pega a página atual
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

  <style>
    /* CSS do index.html (base do layout) */
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
      background: #fff; /* Fundo do conteúdo principal */
    }

    /* ===== Sidebar (Design do index.html) ===== */
    .sidebar {
      width: 80px;
      background: var(--teal);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 30px; /* Reduzido para caber mais links */
      padding: 20px 0;
      position: fixed;
      inset: 0 auto 0 0;
    }
    .sidebar .profile {
        text-align: center;
        color: white;
    }
    .sidebar .profile i {
      font-size: 40px; /* Um pouco menor */
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
      gap: 25px; /* Reduzido */
    }
    .sidebar nav a {
      color: #fff;
      font-size: 26px; /* Um pouco menor */
      text-decoration: none;
      opacity: 0.95;
      transition: transform 0.2s, opacity 0.2s;
    }
    .sidebar nav a:hover {
      transform: scale(1.12);
      opacity: 1;
    }
    /* Estilo dinâmico do PHP para a página ativa */
    .sidebar nav a.active {
      text-shadow: 0 0 12px rgba(255, 255, 255, 0.6);
      transform: scale(1.1);
      opacity: 1;
    }
    /* Link de Sair (do PHP) */
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


    /* ===== Conteúdo (Design do index.html) ===== */
    .main-content {
      margin-left: 80px; /* Alinhado com a nova sidebar */
      width: calc(100% - 80px);
      padding: 30px; /* Padding do PHP */
      overflow-y: auto;
      background-color: #f4f7f6; /* Fundo do PHP */
    }
    
    /* Estilos de caixa para o conteúdo das páginas (do PHP) */
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
    
    /* =========================== */
    /* ===== CSS DO CHATBOT ===== */
    /* =========================== */
    
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
    
    /* --- CORREÇÃO DO BUG DE LAYOUT --- */
    /* display: flex; FOI REMOVIDO DAQUI */
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
      box-shadow: 0 6px 16px rgba(0,0,0,0.18);
      z-index: 1000;
    }

    /* Classes que controlam a visibilidade */
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
  </style>
</head>
<body>
  <div class="container">
    <aside class="sidebar">
        
        <div class="profile" title="<?= htmlspecialchars($_SESSION['usuario_nome']); ?>">
            <i class="fa-solid fa-user"></i>
            <p><?= htmlspecialchars($_SESSION['usuario_nome']); ?></p>
        </div>

        <nav>
            <a href="?page=inicio" title="Início" class="<?= ($page === 'inicio') ? 'active' : '' ?>">
                <i class="fa-solid fa-house"></i>
            </a>
            <a href="?page=materias" title="Minhas Matérias" class="<?= ($page === 'materias') ? 'active' : '' ?>">
                <i class="fa-solid fa-book-open"></i>
            </a>
            <a href="?page=notas" title="Minhas Notas" class="<?= ($page === 'notas') ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-bar"></i>
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
        // Bloco de Roteamento PHP (Mantido e Corrigido)
        
        // CORREÇÃO: 'tarefas' foi adicionado à lista de páginas permitidas
        $allowed_pages = ['inicio', 'materias', 'notas', 'presenca', 'tarefas'];
        
        if (in_array($page, $allowed_pages)) {
            $page_path = __DIR__ . "/aluno/{$page}.php";
            if (file_exists($page_path)) {
                include $page_path;
            } else {
                echo "<div class='box'><h1>Erro: Página não encontrada.</h1><p>O arquivo <code>/aluno/{$page}.php</code> não foi encontrado.</p></div>";
            }
        } else {
            // Se a página não for permitida, carrega o início
            include __DIR__ . '/aluno/inicio.php';
        }
        ?>
    </main>
  </div>

  <button id="academo-chat-button" class="chat-fab" title="Abrir Chat Academo">
    <i class="fa-solid fa-comment-dots"></i>
  </button>
  
  <div id="chat-container" class="hidden">
      <div id="chat-window">
          <div class="message received">Olá! Sou o Academo. Como posso ajudar com seus estudos hoje?</div>
      </div>
      <div id="input-container">
          <input type="text" id="message-input" placeholder="Digite sua mensagem...">
          <button id="send-button">Enviar</button>
      </div>
  </div>

  <script>
    // --- 1. LÓGICA PARA ABRIR/FECHAR O WIDGET (Botão com 'X') ---
    const openChatButton = document.getElementById('academo-chat-button');
    const chatContainer = document.getElementById('chat-container');
    const chatIcon = openChatButton.querySelector('i'); // Pega o ícone

    openChatButton.addEventListener('click', () => {
        const isHidden = chatContainer.classList.contains('hidden');
        chatContainer.classList.toggle('visible');
        chatContainer.classList.toggle('hidden');

        // Troca o ícone
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

    // --- 2. LÓGICA PRINCIPAL DO CHAT (com dados do PHP) ---
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const chatWindow = document.getElementById('chat-window');
    
    // MELHORIA: Pega dados da sessão do PHP para enviar no contexto
    const userRole = '<?= htmlspecialchars($_SESSION['usuario_tipo']) ?>'; 
    const userName = '<?= htmlspecialchars($_SESSION['usuario_nome']) ?>';
    const sessionId = `chat_session_<?= htmlspecialchars($_SESSION['usuario_id']) ?>_${Date.now()}`;

    sendButton.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    async function sendMessage() {
        const userMessage = messageInput.value.trim();
        if (!userMessage) return;

        const currentPage = window.location.href; // Pega a URL atual

        displayMessage(userMessage, 'sent');
        messageInput.value = '';

        displayMessage('Digitando...', 'received', true);

        try {
            // URL da sua API Vercel (do seu arquivo original)
            const response = await fetch('https://academo-project.vercel.app/api/chat', { 
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    sessionId: sessionId,
                    message: userMessage,
                    // Enviando o contexto com dados reais do PHP
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

    // --- 3. FUNÇÕES AUXILIARES (com Markdown) ---
    function displayMessage(message, type, isLoading = false) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', type);
        if (isLoading) {
            messageElement.id = 'loading-message';
        }

        if (type === 'sent') {
            messageElement.textContent = message;
        } else {
            messageElement.innerHTML = marked.parse(message); // Usa a biblioteca marked.js
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
  </script>

</body>
</html>