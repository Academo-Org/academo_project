<?php
session_start();
include 'db.php'; 
?> 

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login — Academo</title>
  <style>
    body, html { margin: 0; padding: 0; height: 100%; font-family: Arial, sans-serif; }
    body { display: flex; height: 100vh; flex-direction: row; position: relative; overflow: hidden; }
    body::after { content: ""; position: absolute; bottom: -100px; right: -100px; width: 500px; height: 500px; background: url("pattern.svg") no-repeat center; background-size: contain; transform: rotate(-45deg); opacity: 0.25; pointer-events: none; z-index: 0; }
    .left-white { width: 40%; background-color: #ffffff; display: flex; align-items: center; justify-content: center; padding: 20px; z-index: 1; }
    .left-white img { max-width: 350px; width: 80%; height: auto; }
    .right-color { width: 60%; background-color: #1d888b; display: flex; align-items: center; justify-content: center; padding: 20px; position: relative; z-index: 1; }
    .login-box { background: #fff; padding: 50px; border-radius: 16px; box-shadow: 0px 6px 16px rgba(0, 0, 0, 0.25); width: 420px; max-width: 90%; text-align: center; position: relative; z-index: 2; }
    .login-box h2 { margin-bottom: 25px; color: #1d888b; font-size: 26px; }
    .login-box input { width: 100%; padding: 14px; margin: 10px 0; border: 1px solid #ccc; border-radius: 8px; font-size: 16px; }
    .btn { background: #1d888b; color: #fff; border: none; padding: 14px; cursor: pointer; border-radius: 8px; width: 100%; font-size: 18px; font-weight: bold; }
    .btn:hover { background: #166d6f; }
    @media (max-width: 900px) { body { flex-direction: column; } .left-white, .right-color { width: 100%; height: 50vh; } .left-white img { max-width: 220px; } .login-box { width: 90%; padding: 30px; } body::after { width: 250px; height: 250px; bottom: 10px; right: 10px; } }
    @media (max-width: 500px) { .login-box { padding: 20px; } .login-box h2 { font-size: 22px; } .login-box input, .btn { font-size: 14px; padding: 12px; } }
  </style>
</head>
<body>
  <div class="left-white">
    <img src="assets/Academo.jpeg" alt="Academo Logo" />
  </div>

  <div class="right-color">
    <div class="login-box">
      <h2>Login:</h2>

      <?php
      if (isset($_POST['entrar'])) {
          $email = trim($_POST['email']);
          $senha = trim($_POST['senha']);

          
          $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
          $stmt->bind_param("s", $email);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
              $usuario = $result->fetch_assoc();

              
              if (password_verify($senha, $usuario['password_hash'])) {
                  $_SESSION['usuario_id'] = $usuario['id_users'];
                  $_SESSION['usuario_nome'] = $usuario['name'];
                  $_SESSION['usuario_tipo'] = $usuario['tipo'];

                  
                  if ($usuario['tipo'] == 'professor') {
                      header("Location: professor_dashboard.php");
                  } else {
                      header("Location: aluno_dashboard.php");
                  }
                  exit;
              } else {
                  echo "<div style='color:red; margin-bottom:10px;'>Senha incorreta.</div>";
              }
          } else {
              echo "<div style='color:red; margin-bottom:10px;'>Usuário não encontrado.</div>";
          }
      }
      ?>

      <form method="post" action="">
        <input name="email" type="email" placeholder="Email" required />
        <input name="senha" type="password" placeholder="Senha" required />
        <button type="submit" name="entrar" class="btn">Entrar</button>
      </form>

      <p style="margin-top:15px;">
        <a href="esqueci_senha.php">Esqueci a senha</a> •
        <a href="cadastro.php">Criar conta</a>
      </p>
    </div>
  </div>
</body>
</html>
