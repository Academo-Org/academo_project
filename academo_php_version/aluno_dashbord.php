<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'aluno') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel do Aluno — Academo</title>
  <style>
    body { font-family: Arial; background: #f2f2f2; margin: 0; padding: 0; }
    header { background: #1d888b; color: #fff; padding: 20px; text-align: center; }
    main { padding: 30px; text-align: center; }
    a {
      display: inline-block;
      margin: 15px;
      padding: 15px 25px;
      background: #1d888b;
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      transition: 0.3s;
    }
    a:hover { background: #166d6f; }
    a.logout { background: red; }
  </style>
</head>
<body>
  <header>
    <h1>Bem-vindo(a), <?php echo $_SESSION['usuario_nome']; ?>!</h1>
    <a href="logout.php" class="logout">Sair</a>
  </header>

  <main>
    <h2>O que deseja acessar?</h2>
    <a href="aluno/ver_notas.php">📊 Ver Notas</a>
    <a href="aluno/ver_presenca.php">📋 Ver Presença</a>
    <a href="aluno/materias.php">📚 Minhas Matérias</a>
    <a href="aluno/chat.php">💬 Chatbot</a>
  </main>
</body>
</html>
