<?php
require_once __DIR__ . '/../db.php';
// Lógica para ativar/desativar usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id_to_update = (int)$_POST['user_id'];
    $new_status = $_POST['action'] === 'desativar' ? 'inativo' : 'ativo';
    if ($user_id_to_update != 1) {
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id_users = ?");
        $stmt->bind_param('si', $new_status, $user_id_to_update);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: ?page=gerenciar_coordenacao&success=1");
    exit;
}
$users = $conn->query("SELECT id_users, name, email, login, status FROM users WHERE role_id = 4 ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>
<style>
    .action-buttons { display: flex; gap: 8px; align-items: center; }
    .action-buttons form { margin: 0; }
    .action-buttons a, .action-buttons button { padding: 6px 12px; font-size: 14px; text-decoration: none; color: white; border-radius: 4px; border: none; cursor: pointer; }
</style>

<h1>Gerenciar Coordenadores</h1>
<div class="box">
    <h2>Usuários da Coordenação</h2>
    <?php if(isset($_GET['success'])) echo "<p style='color:green; font-weight:bold;'>Status atualizado com sucesso!</p>"; ?>
    <table>
        <thead><tr><th>Nome</th><th>Login</th><th>Email</th><th>Status</th><th>Ações</th></tr></thead>
        <tbody>
            <?php foreach($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['login']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= ucfirst($user['status']) ?></td>
                <td>
                    <div class="action-buttons">
                        <a href="?page=editar_coordenacao&id=<?= $user['id_users'] ?>" style="background-color:#007bff;">Editar</a>
                        
                        <form method="POST" action="?page=gerenciar_coordenacao">
                            <input type="hidden" name="user_id" value="<?= $user['id_users'] ?>">
                            <?php if ($user['status'] === 'ativo'): ?>
                                <button type="submit" name="action" value="desativar" style="background-color:#dc3545;">Desativar</button>
                            <?php else: ?>
                                <button type="submit" name="action" value="reativar" style="background-color:#28a745;">Reativar</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>