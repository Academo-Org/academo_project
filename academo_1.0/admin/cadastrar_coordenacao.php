<?php
require_once __DIR__ . '/../db.php';
$erro = ''; $sucesso = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_coord'])) {
    $login = trim($_POST['login'] ?? '');
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $role_id = 4; // Fixo para Coordenação

    if (!$login || !$email || !$senha || !$name) {
        $erro = "Todos os campos são obrigatórios.";
    } else {
        $stmt_check = $conn->prepare("SELECT id_users FROM users WHERE login = ? OR email = ?");
        $stmt_check->bind_param("ss", $login, $email);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            $erro = "Login ou email já cadastrado.";
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt_insert = $conn->prepare("INSERT INTO users (login, password_hash, name, email, role_id) VALUES (?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("ssssi", $login, $hash, $name, $email, $role_id);
            if ($stmt_insert->execute()) {
                $sucesso = "Usuário da coordenação cadastrado com sucesso!";
            } else {
                $erro = "Erro ao cadastrar usuário: " . $stmt_insert->error;
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
?>
<h1>Cadastrar Coordenação</h1>
<div class="box">
    <h2>Novo Usuário da Coordenação</h2>
    <?php if ($erro) echo "<p style='color:#a00; font-weight:bold;'>" . htmlspecialchars($erro) . "</p>"; ?>
    <?php if ($sucesso) echo "<p style='color:green; font-weight:bold;'>" . htmlspecialchars($sucesso) . "</p>"; ?>
    <form method="post" action="?page=cadastrar_coordenacao">
        <label for="name">Nome Completo:</label>
        <input type="text" id="name" name="name" required style="width:100%; padding: 8px;">
        <label for="login">Login:</label>
        <input type="text" id="login" name="login" required style="width:100%; padding: 8px;">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required style="width:100%; padding: 8px;">
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required style="width:100%; padding: 8px;">
        <button type="submit" name="cadastrar_coord">Cadastrar</button>
    </form>
</div>