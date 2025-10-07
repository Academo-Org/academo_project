<?php
session_start();
include 'db.php';

if (isset($_POST['cadastrar'])) {
    $login = $_POST['login'];
    $name = $_POST['name'];
    $matricula = $_POST['matricula']; 
    $email = $_POST['email'];
    $password_hash = $_POST['senha']; 
    
    $result = $conn->query("SELECT * FROM users WHERE login='$login' OR email='$email'");
    if ($result->num_rows > 0) {
        $erro = "Login ou email já cadastrado!";
    } else {
        $conn->query("INSERT INTO users (login, password_hash, name, matricula, email, role_id, created_at, updated_at) 
                      VALUES ('$login', '$password_hash', '$name', '$matricula', '$email', 1, NOW(), NOW())");
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Criar Conta — Academo</title>
  <style>
    body, html { margin:0; padding:0; height:100%; font-family: Arial, sans-serif; }
    body { display:flex; height:100vh; flex-direction: row; position: relative; overflow: hidden; }
    body::after { content: ""; position: absolute; bottom: -100px; right: -100px; width:500px; height:500px;
                  background: url("pattern.svg") no-repeat center; background-size: contain; 
                  transform: rotate(-45deg); opacity: 0.25; pointer-events: none; z-index:0; }
    .left-white { width:40%; background-color:#ffffff; display:flex; align-items:center; justify-content:center; padding:20px; z-index:1; }
    .left-white img { max-width:350px; width:80%; height:auto; }
    .right-color { width:60%; background-color:#1d888b; display:flex; align-items:center; justify-content:center; padding:20px; position:relative; z-index:1; }
    .cadastro-box { background:#fff; padding:50px; border-radius:16px; box-shadow:0px 6px 16px rgba(0,0,0,0.25);
                    width:420px; max-width:90%; text-align:center; position:relative; z-index:2; }
    .cadastro-box h2 { margin-bottom:25px; color:#1d888b; font-size:26px; }
    .cadastro-box input { width:100%; padding:14px; margin:10px 0; border:1px solid #ccc; border-radius:8px; font-size:16px; }
    .btn { background:#1d888b; color:#fff; border:none; padding:14px; cursor:pointer; border-radius:8px; width:100%; font-size:18px; font-weight:bold; }
    .btn:hover { background:#166d6f; }
    .msg { margin-bottom:15px; }
    @media (max-width:900px) { body{flex-direction:column;} .left-white,.right-color{width:100%; height:50vh;} .left-white img{max-width:220px;} .cadastro-box{width:90%; padding:30px;} body::after{width:250px;height:250px;bottom:10px;right:10px;} }
    @media (max-width:500px) { .cadastro-box{padding:20px;} .cadastro-box h2{font-size:22px;} .cadastro-box input,.btn{font-size:14px; padding:12px;} }
  </style>
</head>
<body>
  <div class="left-white">
    <img src="assets/Academo.jpeg" alt="Academo Logo" />
  </div>

  <div class="right-color">
    <div class="cadastro-box">
      <h2>Criar Conta</h2>

      <?php
      if (isset($erro)) echo "<p class='msg' style='color:red;'>$erro</p>";
      ?>

      <form method="post" action="">
        <input type="text" name="login" placeholder="Usuário" required />
        <input type="text" name="name" placeholder="Nome completo" required />
        <input type="text" name="matricula" placeholder="Matrícula" /> <!-- novo campo -->
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="senha" placeholder="Senha" required />
        <button type="submit" name="cadastrar" class="btn">Cadastrar</button>
      </form>

      <p style="margin-top:15px;">
        <a href="index.php" style="color:#1d888b;">Voltar para login</a>
      </p>
    </div>
  </div>
</body>
</html>