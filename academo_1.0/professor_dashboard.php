<?php
session_start();
if (empty($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'professor') {
    header("Location: index.php");
    exit;
}
$page = $_GET['page'] ?? 'inicio';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Painel do Professor — Academo</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  
  <style>
    /* CSS principal (baseado no indexProfessor.html e corrigido) */
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
      /* display: flex; FOI REMOVIDO PARA O CHAT FLUTUAR */
      min-height: 100dvh;
      color: var(--ink);
      background: #fff;
    }

    /* ===== Nova Sidebar (do indexProfessor.html) ===== */
    .sidebar {
      width: 80px;
      background: var(--teal);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 40px;
      padding: 20px 0;
      position: fixed;
      inset: 0 auto 0 0;
      z-index: 100;
    }
    .sidebar .profile i {
      font-size: 50px;
      color: #cfd8dc;
    }
    .sidebar nav {
      display: flex;
      flex-direction: column;
      gap: 30px;
    }
    .sidebar nav a {
      color: #fff;
      font-size: 28px;
      text-decoration: none;
      opacity: 0.95;
      transition: transform 0.2s, opacity 0.2s;
    }
    .sidebar nav a:hover {
      transform: scale(1.12);
      opacity: 1;
    }
    .sidebar nav a.active {
      text-shadow: 0 0 12px rgba(255, 255, 255, 0.45);
      transform: scale(1.1);
    }
    .sidebar .logout-link {
        color:white; 
        font-size: 24px; 
        margin-top: auto; 
        margin-bottom: 20px;
        text-decoration: none;
    }

    /* ===== Conteúdo Principal ===== */
    .content {
      margin-left: 80px;
      width: calc(100% - 80px);
      padding: 30px 40px;
      overflow-y: auto;
      height: 100vh; 
    }
    
    /* Estilos genéricos para o conteúdo (box, tabelas, etc.) */
    .box { border: 1px solid #e0e0e0; padding: 25px; border-radius: 8px; margin-bottom: 25px; background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    h1 { color: #1d888b; font-weight: 600; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: 0; }
    h2, h3 { color: #166d6f; border-bottom: 1px solid #eee; padding-bottom: 8px;}
    a { text-decoration: none; color: #1d888b; font-weight: bold;}
    table { width:100%; border-collapse:collapse; margin-top: 20px; }
    th { background-color: #f2f5f8; padding: 12px; text-align: left; font-weight: 600; }
    td { border-top: 1px solid #e5e5e5; padding: 12px; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    input[type=text], input[type=number], input[type=datetime-local], select { width: 100%; padding: 8px; box-sizing: border-box; border-radius: 4px; border: 1px solid #ccc;}
    button { background-color: #1d888b; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-top: 10px;}
    button:hover { background-color: #166d6f; }
    .msg-ok { color: green; font-weight: bold; }

    /* =========================== */
    /* ===== CSS DO CHATBOT ===== */
    /* (Copiado do aluno_dashboard.php) */
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
    #chat-container {
      position: fixed;
      bottom: 100px;
      right: 25px;
      width: 450px; 
      height: 600px;
      border: 1px solid var(--line);
      border-radius: 8px;
      background-color: #ffffff;
      /* O 'display: flex;' é controlado pelas classes .hidden/.visible */
      flex-direction: column;
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

  </style>
</head>

<body>
  <aside class="sidebar">
    <div class="profile">
      <i class="fa-solid fa-user-tie"></i>
    </div>
    <nav>
      <a href="?page=inicio" title="Início" class="<?= ($page === 'inicio') ? 'active' : '' ?>">
        <i class="fa-solid fa-house"></i>
      </a>
      <a href="?page=marcar_presenca" title="Chamada" class="<?= ($page === 'marcar_presenca') ? 'active' : '' ?>">
        <i class="fa-solid fa-clipboard-check"></i>
      </a>
      <a href="?page=enviar_notas" title="Lançar Notas" class="<?= ($page === 'enviar_notas') ? 'active' : '' ?>">
        <i class="fa-solid fa-pen-to-square"></i>
      </a>
      <a href="?page=gerenciar_tarefas" title="Gerenciar Tarefas" class="<?= ($page === 'gerenciar_tarefas') ? 'active' : '' ?>">
        <i class="fa-solid fa-file-pen"></i>
      </a>
      <a href="?page=avaliar_tarefas" title="Avaliar Tarefas" class="<?= ($page === 'avaliar_tarefas') ? 'active' : '' ?>">
        <i class="fa-solid fa-list-check"></i>
      </a>
    </nav>
    <a href="logout.php" title="Sair" class="logout-link">
        <i class="fa-solid fa-door-open"></i>
    </a>
  </aside>

  <main class="content">
      <?php
      // Lógica de roteamento PHP mantida
      $allowed_pages = ['inicio', 'enviar_notas', 'marcar_presenca', 'gerenciar_tarefas', 'avaliar_tarefas'];
      
      if (in_array($page, $allowed_pages)) {
          $page_path = __DIR__ . "/professor/{$page}.php";
          if (file_exists($page_path)) {
              include $page_path;
          } else {
              echo "<h1>Erro: Página não encontrada.</h1>";
          }
      } else {
          include __DIR__ . '/professor/inicio.php';
      }
      ?>
  </main>

  <button id="academo-chat-button" class="chat-fab" title="Abrir Chat Academo">
    <i class="fa-solid fa-comment-dots"></i>
  </button>
  
  <div id="chat-container" class="hidden">
      <div id="chat-window">
          <div class="message received">Olá! Sou o Academo. Como posso ajudar...</div>
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
    
    // Pega dados da sessão do PHP para enviar no contexto
    const userRole = '<?= htmlspecialchars($_SESSION['usuario_tipo']) ?>'; // Será 'professor'
    const userName = '<?= htmlspecialchars($_SESSION['usuario_nome']) ?>'; // Nome do professor
    const sessionId = 'chat_session_<?= htmlspecialchars($_SESSION['usuario_id']) ?>_${Date.now()}';

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
            // URL da sua API Vercel
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