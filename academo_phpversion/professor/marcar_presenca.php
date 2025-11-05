<?php
// A sessão já foi verificada no arquivo 'professor_dashboard.php'
require_once __DIR__ . '/../db.php'; 

$professor_id = $_SESSION['usuario_id'];
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$msg = '';

// Lógica para salvar a presença
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['presencas'])) {
    $class_id_post = (int)$_POST['class_id'];
    $topic = trim($_POST['topic'] ?? 'Aula do dia ' . date('d/m/Y'));
    
    // 1. Cria o registro da aula (lesson)
    $lesson_sql = "INSERT INTO lessons (class_id, professor_id, lesson_date, topic) VALUES (?, ?, NOW(), ?)";
    $lesson_stmt = $conn->prepare($lesson_sql);
    $lesson_stmt->bind_param("iis", $class_id_post, $professor_id, $topic);
    $lesson_stmt->execute();
    $lesson_id = $conn->insert_id;
    $lesson_stmt->close();

    // 2. Insere a presença de cada aluno para essa aula
    $attendance_sql = "INSERT INTO attendance (lesson_id, student_id, status, period_number, recorded_by) VALUES (?, ?, ?, ?, ?)";
    $attendance_stmt = $conn->prepare($attendance_sql);
    
    foreach ($_POST['presencas'] as $student_id => $periods) {
        foreach ($periods as $period_number => $status) {
            $attendance_stmt->bind_param("iisii", $lesson_id, $student_id, $status, $period_number, $professor_id);
            $attendance_stmt->execute();
        }
    }
    $attendance_stmt->close();
    
    // CORREÇÃO: Redireciona para o dashboard com os parâmetros corretos
    header("Location: professor_dashboard.php?page=marcar_presenca&success=1");
    exit;
}

if(isset($_GET['success'])) {
    $msg = "Presenças registradas com sucesso!";
}

// Busca as turmas do professor
$classes_stmt = $conn->prepare("SELECT c.id_classes, d.title, c.periods_per_session FROM classes c JOIN disciplines d ON c.discipline_id = d.id_disciplines WHERE c.professor_id = ?");
$classes_stmt->bind_param("i", $professor_id);
$classes_stmt->execute();
$professor_classes = $classes_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$classes_stmt->close();

$students = [];
$current_class = null;
if ($class_id) {
    foreach($professor_classes as $class) {
        if ($class['id_classes'] == $class_id) {
            $current_class = $class;
            break;
        }
    }
    
    if ($current_class) {
        $students_sql = "SELECT u.id_users, u.name FROM enrollments e JOIN users u ON e.student_id = u.id_users WHERE e.class_id = ? AND e.status = 'matriculado' ORDER BY u.name";
        $students_stmt = $conn->prepare($students_sql);
        $students_stmt->bind_param("i", $class_id);
        $students_stmt->execute();
        $students = $students_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $students_stmt->close();
    }
}
?>
<h1>Marcar Presença</h1>
<?php if ($msg) echo "<p class='msg-ok'>$msg</p>"; ?>

<div class="box">
    <h3>Passo 1: Selecione a Turma para a Aula de Hoje</h3>
    <div class="link-list">
    <?php foreach ($professor_classes as $class): ?>
        <a href="?page=marcar_presenca&class_id=<?= $class['id_classes'] ?>" style="<?= $class_id == $class['id_classes'] ? 'background-color: #e0e0e0; padding: 2px 5px; border-radius: 4px;' : '' ?>">
            <?= htmlspecialchars($class['title']) ?>
        </a>
    <?php endforeach; ?>
    </div>
</div>

<?php if ($class_id && !empty($students) && $current_class): ?>
<div class="box">
    <h3>Passo 2: Registre as Presenças (Aula de: <?= date('d/m/Y') ?>)</h3>
    <form method="post" action="?page=marcar_presenca">
        <input type="hidden" name="class_id" value="<?= $class_id ?>">
        <p>
            <label for="topic">Tópico do encontro de hoje:</label><br>
            <input type="text" id="topic" name="topic" placeholder="Ex: Revisão para a Prova" required>
        </p>
        <table>
            <thead>
                <tr>
                    <th>Aluno</th>
                    <?php for ($i = 1; $i <= $current_class['periods_per_session']; $i++): ?>
                        <th>Aula <?= $i ?></th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['name']) ?></td>
                    <?php for ($i = 1; $i <= $current_class['periods_per_session']; $i++): ?>
                        <td>
                            <select name="presencas[<?= $student['id_users'] ?>][<?= $i ?>]">
                                <option value="presente" selected>P</option>
                                <option value="ausente">F</option>
                                <option value="atrasado">A</option>
                                <option value="justificado">J</option>
                            </select>
                        </td>
                    <?php endfor; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit">Salvar Presenças do Dia</button>
    </form>
</div>
<?php endif; ?>