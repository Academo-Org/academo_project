<?php
require_once __DIR__ . '/../db.php';

$owner_id = $_SESSION['usuario_id'];

// Lógica para ativar/desativar usuário (com proteção de dono)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id_to_update = (int)$_POST['user_id'];
    $new_status = $_POST['action'] === 'desativar' ? 'inativo' : 'ativo';
    
    // Verifica se o usuário pertence a este coordenador antes de alterar
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id_users = ? AND owner_id = ?");
    $stmt->bind_param('sii', $new_status, $user_id_to_update, $owner_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: ?page=gerenciar_usuarios&success=1");
    exit;
}

// Busca APENAS usuários criados por este coordenador
$stmt = $conn->prepare("SELECT id_users, name, email, login, role_id, status FROM users WHERE role_id IN (2, 3) AND owner_id = ? ORDER BY name");
$stmt->bind_param('i', $owner_id);
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<style>
    .action-buttons { display: flex; gap: 8px; align-items: center; }
    .action-buttons form { margin: 0; }
    .action-buttons a, .action-buttons button { padding: 6px 12px; font-size: 14px; text-decoration: none; color: white; border-radius: 4px; border: none; cursor: pointer; }
</style>

<h1>Gerenciar Usuários</h1>
<div class="box">
    <h2>Seus Alunos e Professores</h2>
    <?php if(isset($_GET['success'])) echo "<p style='color:green; font-weight:bold;'>Status atualizado com sucesso!</p>"; ?>
    
    <?php if (empty($users)): ?>
        <p>Você ainda não cadastrou nenhum usuário.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Login</th>
                    <th>Email</th>
                    <th>Função</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['login']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= $user['role_id'] == 2 ? 'Professor' : 'Aluno' ?></td>
                    <td><?= ucfirst($user['status']) ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="?page=editar_usuario&id=<?= $user['id_users'] ?>" style="background-color:#007bff;">Editar</a>
                            
                            <form method="POST" action="?page=gerenciar_usuarios">
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
    <?php endif; ?>
</div>