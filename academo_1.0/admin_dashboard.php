<?php
ob_start();
session_start();
if (empty($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: index.php");
    exit;
}
$page = $_GET['page'] ?? 'inicio'; 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel do Administrador — Academo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="icon" href="assets/Academo.jpeg" type="image/png">
  
  <style>
    /* CSS GLOBAL (Baseado no novo padrão) */
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Arial", sans-serif; }
    :root { --teal: #208489; --purple: #6b3df2; --line: #e6e8ec; --soft: #f6f8fb; --ink: #333; }
    body { min-height: 100dvh; color: var(--ink); background: #fff; }

    /* SIDEBAR 80px */
    .sidebar { width: 80px; background: var(--teal); display: flex; flex-direction: column; align-items: center; gap: 40px; padding: 20px 0; position: fixed; inset: 0 auto 0 0; z-index: 100; }
    .sidebar .profile i { font-size: 40px; color: #cfd8dc; }
    .sidebar nav { display: flex; flex-direction: column; gap: 30px; }
    .sidebar nav a { color: #fff; font-size: 28px; text-decoration: none; opacity: 0.95; transition: transform 0.2s; width: 40px; text-align: center; }
    .sidebar nav a:hover { transform: scale(1.12); opacity: 1; }
    .sidebar nav a.active { text-shadow: 0 0 12px rgba(255, 255, 255, 0.45); transform: scale(1.1); }
    .sidebar .logout { margin-top: auto; color: white; font-size: 24px; margin-bottom: 20px; text-decoration: none; }

    /* CONTEÚDO */
    .main-content { margin-left: 80px; width: calc(100% - 80px); padding: 30px 40px; overflow-y: auto; height: 100vh; background-color: #f4f7f6; }
    
    /* COMPONENTES */
    .box { border: 1px solid #e0e0e0; padding: 25px; border-radius: 8px; margin-bottom: 25px; background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    h1 { color: #1d888b; font-weight: 600; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: 0; }
    table { width:100%; border-collapse:collapse; margin-top: 20px; }
    th { background-color: #f2f5f8; padding: 12px; text-align: left; font-weight: 600; }
    td { border-top: 1px solid #e5e5e5; padding: 12px; }
    button { background-color: #1d888b; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-top: 10px;}
    button:hover { background-color: #166d6f; }
    a { text-decoration: none; color: #1d888b; font-weight: bold; }
    input[type=text], input[type=email], input[type=password], select { width: 100%; padding: 8px; box-sizing: border-box; border-radius: 4px; border: 1px solid #ccc;}
  </style>
</head>
<body>
  <div class="container">
    <aside class="sidebar">
        <div class="profile" title="Administrador">
            <i class="fa-solid fa-user-shield"></i>
        </div>
        <nav>
            <a href="?page=inicio" title="Início" class="<?= ($page === 'inicio') ? 'active' : '' ?>">
                <i class="fa-solid fa-house"></i>
            </a>
            <a href="?page=cadastrar_coordenacao" title="Cadastrar Coordenação" class="<?= ($page === 'cadastrar_coordenacao') ? 'active' : '' ?>">
                <i class="fa-solid fa-user-plus"></i>
            </a>
            <a href="?page=gerenciar_coordenacao" title="Gerenciar Coordenação" class="<?= ($page === 'gerenciar_coordenacao' || $page === 'editar_coordenacao') ? 'active' : '' ?>">
                <i class="fa-solid fa-users-gear"></i>
            </a>
        </nav>
        <a href="logout.php" class="logout" title="Sair">
            <i class="fa-solid fa-door-open"></i>
        </a>
    </aside>

    <main class="main-content">
        <?php
        // Adicionamos 'editar_coordenacao' à lista de permissões
        $allowed_pages = ['inicio', 'cadastrar_coordenacao', 'gerenciar_coordenacao', 'editar_coordenacao'];
        
        if (in_array($page, $allowed_pages)) {
            $page_path = __DIR__ . "/admin/{$page}.php";
            if (file_exists($page_path)) {
                include $page_path;
            } else {
                echo "<div class='box'><h1>Erro</h1><p>Página não encontrada.</p></div>";
            }
        } else {
            include __DIR__ . '/admin/inicio.php';
        }
        ?>
    </main>
  </div>
</body>
</html>