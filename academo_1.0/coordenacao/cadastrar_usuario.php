<?php
require_once __DIR__ . '/../db.php';
$erro = '';
$sucesso = '';
$owner_id = $_SESSION['usuario_id']; // O ID do coordenador logado

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_usuario'])) {
    $login = trim($_POST['login'] ?? '');
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $role_id = (int)($_POST['role_id'] ?? 0);

    if (!$login || !$email || !$senha || !$name || !in_array($role_id, [2, 3])) {
        $erro = "Todos os campos são obrigatórios e o tipo de usuário deve ser válido.";
    } else {
        $stmt_check = $conn->prepare("SELECT id_users FROM users WHERE login = ? OR email = ?");
        $stmt_check->bind_param("ss", $login, $email);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            $erro = "Login ou email já cadastrado.";
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            
            // MUDANÇA AQUI: Inserimos o owner_id
            $stmt_insert = $conn->prepare("INSERT INTO users (login, password_hash, name, email, role_id, owner_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("ssssii", $login, $hash, $name, $email, $role_id, $owner_id);
            
            if ($stmt_insert->execute()) {
                $sucesso = "Usuário cadastrado e vinculado à sua coordenação!";
            } else {
                $erro = "Erro ao cadastrar usuário: " . $stmt_insert->error;
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
?>
<h1>Cadastrar Novo Usuário</h1>
<div class="box">
    <h2>Cadastrar Aluno ou Professor</h2>
    
    <?php if ($erro) echo "<p style='color:#a00; font-weight:bold;'>" . htmlspecialchars($erro) . "</p>"; ?>
    <?php if ($sucesso) echo "<p style='color:green; font-weight:bold;'>" . htmlspecialchars($sucesso) . "</p>"; ?>
    
    <form method="post" action="?page=cadastrar_usuario">
        <label for="name">Nome Completo:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="login">Login:</label>
        <input type="text" id="login" name="login" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>
        
        <label for="role_id">Tipo de Usuário:</label>
        <select id="role_id" name="role_id" required>
            <option value="">Selecione...</option>
            <option value="3">Aluno</option>
            <option value="2">Professor</option>
        </select>
        
        <button type="submit" name="cadastrar_usuario">Cadastrar Usuário</button>
    </form>
</div>