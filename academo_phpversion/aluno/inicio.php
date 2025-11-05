<?php
require_once __DIR__ . '/../db.php';
$aluno_id = (int)$_SESSION['usuario_id'];

// Buscar informações de perfil do usuário
$stmt_user = $conn->prepare("SELECT email, created_at FROM users WHERE id_users = ?");
$stmt_user->bind_param('i', $aluno_id);
$stmt_user->execute();
$user_info = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

// Buscar resumo acadêmico
$stmt_materias = $conn->prepare("SELECT COUNT(*) as total FROM enrollments WHERE student_id = ? AND status = 'matriculado'");
$stmt_materias->bind_param('i', $aluno_id);
$stmt_materias->execute();
$total_materias = $stmt_materias->get_result()->fetch_assoc()['total'];
$stmt_materias->close();

$stmt_notas = $conn->prepare("SELECT COUNT(*) as total, AVG(score) as media FROM grades WHERE student_id = ?");
$stmt_notas->bind_param('i', $aluno_id);
$stmt_notas->execute();
$resumo_notas = $stmt_notas->get_result()->fetch_assoc();
$stmt_notas->close();
?>

<h1>Painel Inicial</h1>

<div class="box">
    <h2>Resumo Acadêmico</h2>
    <div class="summary-grid">
        <div class="summary-item">
            <h3>Matérias Matriculadas</h3>
            <p><?= $total_materias ?></p>
        </div>
        <div class="summary-item">
            <h3>Avaliações Concluídas</h3>
            <p><?= $resumo_notas['total'] ?></p>
        </div>
        <div class="summary-item">
            <h3>Média Geral</h3>
            <p><?= number_format($resumo_notas['media'] ?? 0, 2, ',', '.') ?></p>
        </div>
    </div>
</div>

<div class="box">
    <h2>Meu Perfil</h2>
    <ul class="profile-list">
        <li><strong>Nome Completo:</strong> <?= htmlspecialchars($_SESSION['usuario_nome']); ?></li>
        <li><strong>Login:</strong> <?= htmlspecialchars($_SESSION['usuario_login']); ?></li>
        <li><strong>Email:</strong> <?= htmlspecialchars($user_info['email']); ?></li>
        <li><strong>Aluno desde:</strong> <?= date('d/m/Y', strtotime($user_info['created_at'])); ?></li>
    </ul>
</div>