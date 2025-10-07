<?php
session_start();
include 'db.php'; 
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Academo — Página Inicial</title>
<style>
body, html { margin:0; padding:0; font-family: Arial, sans-serif; height:100%; }
body { display:flex; height:100vh; overflow:hidden; }
.left-img { width:50%; background-color:#fff; display:flex; align-items:center; justify-content:center; }
.left-img img { max-width:80%; height:auto; }
.right-panel { width:50%; background-color:#1d888b; display:flex; align-items:center; justify-content:center; position:relative; }
.panel-box { background:#fff; padding:40px; border-radius:16px; width:80%; text-align:center; box-shadow:0 6px 16px rgba(0,0,0,0.25); }
.panel-box h1 { color:#1d888b; margin-bottom:20px; }
.btn { background:#1d888b; color:#fff; border:none; padding:14px; border-radius:8px; font-size:18px; font-weight:bold; cursor:pointer; text-decoration:none; display:block; margin:10px 0; transition: background 0.3s; }
.btn:hover { background:#166d6f; }
.msg { color:green; margin-bottom:15px; }
@media (max-width:900px) { body { flex-direction:column; } .left-img,.right-panel { width:100%; height:50vh; } .panel-box { width:90%; padding:30px; } }
@media (max-width:500px) { .panel-box { padding:20px; } .panel-box h1 { font-size:22px; } .btn { font-size:16px; padding:12px; } }
</style>
</head>
<body>

<div class="left-img">
    <img src="assets/Academo.jpeg" alt="Academo Logo">
</div>

<div class="right-panel">
    <div class="panel-box">
        <h1>Bem-vindo ao Academo</h1>

        <?php if(isset($_SESSION['mensagem'])): ?>
            <p class="msg"><?= $_SESSION['mensagem']; ?></p>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>

        <a href="login.php" class="btn">Login</a>
        <a href="cadastro.php" class="btn">Cadastrar</a>
    </div>
</div>

</body>
</html>