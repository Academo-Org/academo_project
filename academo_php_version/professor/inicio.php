<?php
require_once __DIR__ . '/../db.php';
$professor_id = (int)$_SESSION['usuario_id'];

// Buscar resumo de turmas e alunos do professor
$stmt = $conn->prepare("
    SELECT 
        COUNT(DISTINCT c.id_classes) as total_turmas,
        COUNT(e.id_enrollments) as total_alunos
    FROM classes c
    LEFT JOIN enrollments e ON c.id_classes = e.class_id
    WHERE c.professor_id = ?
");
$stmt->bind_param('i', $professor_id);
$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<h1>Painel do Professor</h1>

<div class="box">
    <h2>Bem-vindo(a), <?= htmlspecialchars($_SESSION['usuario_nome']); ?>!</h2>
    <p>Utilize o menu à esquerda para navegar pelas suas ferramentas de ensino. Você pode lançar notas para atividades e registrar a presença dos seus alunos em cada aula.</p>
</div>

<div class="box">
    <h2>Seu Resumo</h2>
    <div style="display: flex; gap: 20px; text-align: center;">
        <div style="flex: 1; padding: 20px; background-color: #f9f9f9; border-radius: 5px;">
            <h3 style="margin-top:0;">Turmas Ativas</h3>
            <p style="font-size: 2em; font-weight: bold; margin-bottom:0;"><?= $summary['total_turmas'] ?? 0 ?></p>
        </div>
        <div style="flex: 1; padding: 20px; background-color: #f9f9f9; border-radius: 5px;">
            <h3 style="margin-top:0;">Total de Alunos</h3>
            <p style="font-size: 2em; font-weight: bold; margin-bottom:0;"><?= $summary['total_alunos'] ?? 0 ?></p>
        </div>
    </div>
</div>