<?php
require_once __DIR__ . '/../db.php';
$professor_id = (int)$_SESSION['usuario_id'];
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$assignment_id = isset($_GET['assignment_id']) ? (int)$_GET['assignment_id'] : 0;
$erro = ''; $sucesso = '';

// Lógica para salvar avaliação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['avaliar_submission'])) {
    $submission_id = (int)$_POST['submission_id'];
    $grade = (float)$_POST['grade'];
    $feedback = trim($_POST['feedback']);

    $stmt = $conn->prepare("UPDATE submissions SET grade = ?, feedback = ? WHERE idsubmissions = ?");
    $stmt->bind_param('dsi', $grade, $feedback, $submission_id);
    if($stmt->execute()){
        $sucesso = "Avaliação salva com sucesso!";
    } else {
        $erro = "Erro ao salvar avaliação.";
    }
    $stmt->close();
}

// Busca de dados
$classes = $conn->query("SELECT c.id_classes, d.title FROM classes c JOIN disciplines d ON c.discipline_id = d.id_disciplines WHERE c.professor_id = $professor_id")->fetch_all(MYSQLI_ASSOC);
$assignments = [];
if ($class_id) {
    $assignments = $conn->query("SELECT id_assignments, title FROM assignments WHERE class_id = $class_id ORDER BY due_date DESC")->fetch_all(MYSQLI_ASSOC);
}
$submissions = [];
if ($assignment_id) {
    $stmt = $conn->prepare("SELECT s.idsubmissions, u.name, s.submitted_at, s.file_name, s.grade, s.feedback FROM submissions s JOIN users u ON s.student_id = u.id_users WHERE s.assignment_id = ? ORDER BY u.name");
    $stmt->bind_param('i', $assignment_id);
    $stmt->execute();
    $submissions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>
<h1>Avaliar Tarefas</h1>
<?php if ($sucesso) echo "<p style='color:green;font-weight:bold;'>$sucesso</p>"; ?>
<?php if ($erro) echo "<p style='color:red;font-weight:bold;'>$erro</p>"; ?>

<div class="box">
    <h3>Passo 1: Selecione a Turma e a Tarefa</h3>
    <form method="GET" action="">
        <input type="hidden" name="page" value="avaliar_tarefas">
        <label>Turma:</label>
        <select name="class_id" onchange="this.form.submit()" style="width:100%; padding: 8px;">
            <option value="">Selecione...</option>
            <?php foreach($classes as $class): ?>
                <option value="<?= $class['id_classes'] ?>" <?= $class_id == $class['id_classes'] ? 'selected' : ''?>><?= htmlspecialchars($class['title']) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if($class_id): ?>
        <label>Tarefa:</label>
        <select name="assignment_id" onchange="this.form.submit()" style="width:100%; padding: 8px;">
            <option value="">Selecione...</option>
            <?php foreach($assignments as $assignment): ?>
                <option value="<?= $assignment['id_assignments'] ?>" <?= $assignment_id == $assignment['id_assignments'] ? 'selected' : ''?>><?= htmlspecialchars($assignment['title']) ?></option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
    </form>
</div>

<?php if($assignment_id): ?>
<div class="box">
    <h3>Envios Recebidos</h3>
    <table>
        <thead><tr><th>Aluno</th><th>Data de Envio</th><th>Arquivo</th><th>Nota</th><th>Feedback</th><th>Ação</th></tr></thead>
        <tbody>
            <?php foreach($submissions as $sub): ?>
            <tr>
                <form method="POST" action="?page=avaliar_tarefas&class_id=<?=$class_id?>&assignment_id=<?=$assignment_id?>">
                    <input type="hidden" name="submission_id" value="<?= $sub['idsubmissions'] ?>">
                    <td><?= htmlspecialchars($sub['name']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($sub['submitted_at'])) ?></td>
                    <td><?= htmlspecialchars($sub['file_name']) ?></td>
                    <td><input type="number" step="0.1" name="grade" value="<?= htmlspecialchars($sub['grade']) ?>" style="width:70px;"></td>
                    <td><input type="text" name="feedback" value="<?= htmlspecialchars($sub['feedback']) ?>"></td>
                    <td><button type="submit" name="avaliar_submission">Salvar</button></td>
                </form>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>