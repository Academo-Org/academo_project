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
  <meta charset="UTF-8">
  <title>Painel do Professor — Academo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* Seu CSS completo aqui... */
    body, html { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7f6; height: 100%; color: #333; }
    .container { display: flex; height: 100vh; }
    .sidebar { width: 250px; background-color: #1d888b; color: white; padding: 20px; display: flex; flex-direction: column; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }
    .main-content { flex-grow: 1; padding: 30px; overflow-y: auto; }
    .sidebar h2 { margin: 0 0 10px 0; text-align: center; border-bottom: 1px solid #4dbfbf; padding-bottom: 15px; font-weight: 600; }
    .sidebar .user-info { text-align: center; margin-bottom: 30px; }
    .sidebar .user-info p { margin: 4px 0; opacity: 0.9; }
    .sidebar nav a { color: white; text-decoration: none; display: block; padding: 12px 15px; border-radius: 5px; margin-bottom: 8px; font-weight: 500; transition: background-color 0.2s; }
    .sidebar nav a:hover, .sidebar nav a.active { background-color: #166d6f; }
    .sidebar .logout { margin-top: auto; }
    .box { border: 1px solid #e0e0e0; padding: 25px; border-radius: 8px; margin-bottom: 25px; background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    h1 { color: #1d888b; font-weight: 600; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: 0; }
  </style>
</head>
<body>
  <div class="container">
    <aside class="sidebar">
        <h2>Academo</h2>
        <div class="user-info">
            <p><strong><?= htmlspecialchars($_SESSION['usuario_nome']); ?></strong></p>
            <p>Professor</p>
        </div>
        <nav>
            <a href="?page=inicio" class="<?= ($page === 'inicio') ? 'active' : '' ?>">🏠 Início</a>
            <a href="?page=enviar_notas" class="<?= ($page === 'enviar_notas') ? 'active' : '' ?>">📊 Lançar Notas</a>
            <a href="?page=marcar_presenca" class="<?= ($page === 'marcar_presenca') ? 'active' : '' ?>">📋 Marcar Presença</a>
            <a href="?page=gerenciar_tarefas" class="<?= ($page === 'gerenciar_tarefas') ? 'active' : '' ?>">📝 Gerenciar Tarefas</a>
            <a href="?page=avaliar_tarefas" class="<?= ($page === 'avaliar_tarefas') ? 'active' : '' ?>">✅ Avaliar Tarefas</a>
        </nav>
        <a href="logout.php" class="logout">🚪 Sair</a>
    </aside>

    <main class="main-content">
        <?php
        // CORREÇÃO: Novas páginas adicionadas à lista
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
  </div>
</body>
</html>