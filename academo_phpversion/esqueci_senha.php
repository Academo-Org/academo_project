<?php
session_start();
require_once 'db.php';

// Importa as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carrega o autoloader do Composer
require 'vendor/autoload.php';

$erro = '';
$sucesso = '';
// A variável $link_recuperacao ainda é usada, mas não mais exibida na tela
$link_recuperacao = ''; 

if (isset($_POST['recuperar'])) {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Por favor, insira um email válido.";
    } else {
        $stmt = $conn->prepare("SELECT id_users FROM users WHERE email = ? AND status = 'ativo'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $token = bin2hex(random_bytes(32));
            $expires_at = date("Y-m-d H:i:s", strtotime('+1 hour'));

            $update_stmt = $conn->prepare("UPDATE users SET reset_token = ?, token_expires_at = ? WHERE email = ?");
            $update_stmt->bind_param("sss", $token, $expires_at, $email);
            
            if($update_stmt->execute()) {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'academo.dev@gmail.com';
                    $mail->Password   = 'tgbq evqv hrff leay';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port       = 465;
                    $mail->CharSet    = 'UTF-8';

                    $mail->setFrom('academo.dev@gmail.com', 'Sistema Academo');
                    $mail->addAddress($email);

                    $base_url = "http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                    $link_recuperacao = $base_url . "/redefinir_senha.php?token=" . $token;

                    $mail->isHTML(true);
                    $mail->Subject = 'Recuperação de Senha - Sistema Academo';
                    $mail->Body    = "Olá,<br><br>Recebemos uma solicitação para redefinir sua senha no Sistema Academo. Clique no link abaixo para criar uma nova senha:<br><br><a href='$link_recuperacao'>Redefinir Minha Senha</a><br><br>Se você não solicitou isso, por favor, ignore este email.<br><br>Atenciosamente,<br>Equipe Academo";
                    $mail->AltBody = "Para redefinir sua senha, copie e cole este link no seu navegador: $link_recuperacao";

                    $mail->send();
                } catch (Exception $e) {
                    // Em ambiente de produção, seria ideal gravar o erro em um log, não mostrar ao usuário
                    $erro = "A mensagem não pôde ser enviada. Contate o suporte."; 
                }
            }
        }
        // A mensagem de sucesso é a mesma em todos os casos por segurança
        $sucesso = "Se um usuário com este email existir em nosso sistema, um link de recuperação foi enviado.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Esqueci a Senha — Academo</title>
    <style>
        body, html { margin: 0; padding: 0; height: 100%; font-family: Arial, sans-serif; }
        body { display: flex; height: 100vh; flex-direction: row; }
        .left-white { width: 40%; background-color: #ffffff; display: flex; align-items: center; justify-content: center; }
        .left-white img { max-width: 350px; width: 80%; height: auto; }
        .right-color { width: 60%; background-color: #1d888b; display: flex; align-items: center; justify-content: center; }
        .recuperar-box { background: #fff; padding: 50px; border-radius: 16px; box-shadow: 0 6px 16px rgba(0,0,0,0.25); width: 420px; max-width: 90%; text-align: center; }
        .recuperar-box h2 { margin-bottom: 25px; color: #1d888b; }
        .recuperar-box input { width: 100%; padding: 14px; margin: 10px 0; border: 1px solid #ccc; border-radius: 8px; font-size: 16px; box-sizing: border-box;}
        .btn { background: #1d888b; color: #fff; border: none; padding: 14px; cursor: pointer; border-radius: 8px; width: 100%; font-size: 18px; font-weight: bold; }
        .msg { margin-bottom: 15px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="left-white">
        <img src="assets/Academo.jpeg" alt="Academo Logo">
    </div>

    <div class="right-color">
        <div class="recuperar-box">
            <h2>Esqueci a Senha</h2>

            <?php if ($erro) echo "<p class='msg' style='color:red;'>".htmlspecialchars($erro)."</p>"; ?>
            <?php if ($sucesso) echo "<p class='msg' style='color:green;'>".htmlspecialchars($sucesso)."</p>"; ?>
            
            <form method="post" action="">
                <input type="email" name="email" placeholder="Digite seu email" required>
                <button type="submit" name="recuperar" class="btn">Recuperar</button>
            </form>

            <p style="margin-top:15px;"><a href="index.php" style="color:#1d888b;">Voltar para login</a></p>
        </div>
    </div>
</body>
</html>