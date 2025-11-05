<?php
require_once __DIR__ . '/../db.php';
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$erro = ''; $sucesso = '';

// Lógica para salvar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_usuario'])) {
    $name = trim($_POST['name']);
    $login = trim($_POST['login']);
    $email = trim($_POST['email']);
    $stmt = $conn->prepare("UPDATE users SET name = ?, login = ?, email = ? WHERE id_users = ?");
    $stmt->bind_param('sssi', $name, $login, $email, $user_id);
    if($stmt->execute()){
        $sucesso = "Usuário atualizado com sucesso!";
    } else {
        $erro = "Erro ao atualizar. Verifique se o login ou email já não está em uso.";
    }
    $stmt->close();
}

// Busca dados do usuário
$stmt = $conn->prepare("SELECT name, login, email FROM users WHERE id_users = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if(!$user) die("Usuário não encontrado.");
?>
<h1>Editar Usuário</h1>
<div class="box">
    <h2>Editando: <?= htmlspecialchars($user['name']) ?></h2>
    <?php if ($sucesso) echo "<p style='color:green;font-weight:bold;'>$sucesso</p>"; ?>
    <?php if ($erro) echo "<p style='color:red;font-weight:bold;'>$erro</p>"; ?>
    <form method="POST" action="?page=editar_usuario&id=<?= $user_id ?>">
        <label>Nome:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required style="width:100%; padding: 8px;">
        <label>Login:</label>
        <input type="text" name="login" value="<?= htmlspecialchars($user['login']) ?>" required style="width:100%; padding: 8px;">
        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required style="width:100%; padding: 8px;">
        <button type="submit" name="salvar_usuario">Salvar Alterações</button>
    </form>
</div>