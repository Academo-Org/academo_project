<?php
require_once __DIR__ . '/../db.php';
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
// Lógica para adicionar/remover matrícula
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $class_id_post = (int)$_POST['class_id'];
    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO enrollments (student_id, class_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $student_id, $class_id_post);
    } elseif (isset($_POST['remove'])) {
        $stmt = $conn->prepare("DELETE FROM enrollments WHERE student_id = ? AND class_id = ?");
        $stmt->bind_param('ii', $student_id, $class_id_post);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: ?page=gerenciar_matriculas&class_id=$class_id_post");
    exit;
}
// Busca dados para a página
$classes = $conn->query("SELECT c.id_classes, d.title FROM classes c JOIN disciplines d ON c.discipline_id = d.id_disciplines ORDER BY d.title")->fetch_all(MYSQLI_ASSOC);
$enrolled_students = [];
$unenrolled_students = [];
if ($class_id) {
    // Alunos matriculados na turma
    $enrolled_stmt = $conn->prepare("SELECT u.id_users, u.name FROM users u JOIN enrollments e ON u.id_users = e.student_id WHERE e.class_id = ? AND u.status='ativo' ORDER BY u.name");
    $enrolled_stmt->bind_param('i', $class_id);
    $enrolled_stmt->execute();
    $enrolled_students = $enrolled_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $enrolled_stmt->close();
    // Alunos NÃO matriculados na turma
    $unenrolled_stmt = $conn->prepare("SELECT id_users, name FROM users WHERE role_id = 3 AND status='ativo' AND id_users NOT IN (SELECT student_id FROM enrollments WHERE class_id = ?) ORDER BY name");
    $unenrolled_stmt->bind_param('i', $class_id);
    $unenrolled_stmt->execute();
    $unenrolled_students = $unenrolled_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $unenrolled_stmt->close();
}
?>
<h1>Gerenciar Matrículas</h1>
<div class="box">
    <h3>Passo 1: Selecione a Turma</h3>
    <form method="GET" action="">
        <input type="hidden" name="page" value="gerenciar_matriculas">
        <select name="class_id" onchange="this.form.submit()">
            <option value="">Selecione uma turma...</option>
            <?php foreach($classes as $class): ?>
                <option value="<?= $class['id_classes'] ?>" <?= $class_id == $class['id_classes'] ? 'selected' : '' ?>><?= htmlspecialchars($class['title']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<?php if($class_id): ?>
<div style="display: flex; gap: 20px;">
    <div class="box" style="flex: 1;">
        <h3>Alunos Matriculados</h3>
        <table>
            <?php foreach($enrolled_students as $student): ?>
            <tr>
                <td><?= htmlspecialchars($student['name']) ?></td>
                <td>
                    <form method="POST" action="?page=gerenciar_matriculas&class_id=<?= $class_id ?>">
                        <input type="hidden" name="student_id" value="<?= $student['id_users'] ?>">
                        <input type="hidden" name="class_id" value="<?= $class_id ?>">
                        <button type="submit" name="remove" style="background-color:#dc3545;">Remover</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="box" style="flex: 1;">
        <h3>Alunos Disponíveis</h3>
        <table>
            <?php foreach($unenrolled_students as $student): ?>
            <tr>
                <td><?= htmlspecialchars($student['name']) ?></td>
                <td>
                    <form method="POST" action="?page=gerenciar_matriculas&class_id=<?= $class_id ?>">
                        <input type="hidden" name="student_id" value="<?= $student['id_users'] ?>">
                        <input type="hidden" name="class_id" value="<?= $class_id ?>">
                        <button type="submit" name="add" style="background-color:#28a745;">Adicionar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php endif; ?>