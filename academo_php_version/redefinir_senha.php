<?php
session_start();
require_once 'db.php';

$token = $_GET['token'] ?? '';
$erro = '';
$sucesso = '';
$token_valido = false;

if (empty($token)) {
    $erro = "Token de redefinição não fornecido ou inválido.";
} else {
    // Verifica se o token existe e não expirou
    $stmt = $conn->prepare("SELECT id_users FROM users WHERE reset_token = ? AND token_expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $token_valido = true;
        $user = $result->fetch_assoc();
        $user_id = $user['id_users'];

        // Lógica para processar a nova senha
        if (isset($_POST['redefinir'])) {
            $nova_senha = $_POST['nova_senha'];
            $confirma_senha = $_POST['confirma_senha'];

            if (empty($nova_senha) || $nova_senha !== $confirma_senha) {
                $erro = "As senhas não coincidem ou estão vazias.";
            } elseif (strlen($nova_senha) < 6) {
                $erro = "A senha deve ter no mínimo 6 caracteres.";
            } else {
                // Tudo certo, vamos atualizar a senha
                $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                
                // Nulifica o token para que não possa ser usado novamente
                $update_stmt = $conn->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, token_expires_at = NULL WHERE id_users = ?");
                $update_stmt->bind_param("si", $hash, $user_id);
                $update_stmt->execute();

                $sucesso = "Senha redefinida com sucesso! Você já pode fazer login com a nova senha.";
                $token_valido = false; // Esconde o formulário após o sucesso
            }
        }
    } else {
        $erro = "Token inválido, expirado ou já utilizado. Por favor, solicite uma nova recuperação.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redefinir Senha — Academo</title>
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
            <h2>Redefinir Senha</h2>

            <?php if ($erro) echo "<p class='msg' style='color:red;'>$erro</p>"; ?>
            <?php if ($sucesso) echo "<p class='msg' style='color:green;'>$sucesso</p>"; ?>

            <?php if ($token_valido): ?>
            <form method="post" action="">
                <input type="password" name="nova_senha" placeholder="Digite sua nova senha" required>
                <input type="password" name="confirma_senha" placeholder="Confirme a nova senha" required>
                <button type="submit" name="redefinir" class="btn">Salvar Nova Senha</button>
            </form>
            <?php endif; ?>

            <p style="margin-top:15px;"><a href="index.php" style="color:#1d888b;">Voltar para login</a></p>
        </div>
    </div>
</body>
</html>