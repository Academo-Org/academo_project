<?php
require_once __DIR__ . '/../db.php';
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$erro = ''; $sucesso = '';

// Lógica para salvar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_usuario'])) {
    $name = trim($_POST['name']);
    $login = trim($_POST['login']);
    $email = trim($_POST['email']);
    
    // Validações básicas
    if (!$name || !$login || !$email) {
        $erro = "Todos os campos são obrigatórios.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, login = ?, email = ? WHERE id_users = ? AND role_id = 4"); // role_id=4 garante que só edite coordenadores
        $stmt->bind_param('sssi', $name, $login, $email, $user_id);
        if($stmt->execute()){
            $sucesso = "Coordenador atualizado com sucesso!";
        } else {
            $erro = "Erro ao atualizar. Verifique se o login ou email já não está em uso.";
        }
        $stmt->close();
    }
}

// Busca dados do usuário
$stmt = $conn->prepare("SELECT name, login, email FROM users WHERE id_users = ? AND role_id = 4");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$user) {
    echo "<div class='box'><h1>Erro</h1><p>Coordenador não encontrado.</p></div>";
    exit; // Para a execução se não achar o usuário
}
?>
<h1>Editar Coordenador</h1>
<p><a href="?page=gerenciar_coordenacao"> &larr; Voltar para a lista</a></p>

<div class="box">
    <h2>Editando: <?= htmlspecialchars($user['name']) ?></h2>
    <?php if ($sucesso) echo "<p style='color:green;font-weight:bold;'>$sucesso</p>"; ?>
    <?php if ($erro) echo "<p style='color:red;font-weight:bold;'>$erro</p>"; ?>
    
    <form method="POST" action="?page=editar_coordenacao&id=<?= $user_id ?>">
        <label style="display:block; margin:10px 0 5px;">Nome:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required style="width:100%; padding: 8px;">
        
        <label style="display:block; margin:10px 0 5px;">Login:</label>
        <input type="text" name="login" value="<?= htmlspecialchars($user['login']) ?>" required style="width:100%; padding: 8px;">
        
        <label style="display:block; margin:10px 0 5px;">Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required style="width:100%; padding: 8px;">
        
        <button type="submit" name="salvar_usuario" style="margin-top:15px;">Salvar Alterações</button>
    </form>
</div>  