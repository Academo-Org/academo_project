<?php
// A sessão já foi verificada no arquivo 'professor_dashboard.php'
require_once __DIR__ . '/../db.php'; 

$professor_id = $_SESSION['usuario_id'];
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$msg = '';

// --- Lógica PHP (sem alterações) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['presencas'])) {
    $class_id_post = (int)$_POST['class_id'];
    $topic = trim($_POST['topic'] ?? 'Aula do dia ' . date('d/m/Y'));
    $lesson_sql = "INSERT INTO lessons (class_id, professor_id, lesson_date, topic) VALUES (?, ?, NOW(), ?)";
    $lesson_stmt = $conn->prepare($lesson_sql);
    $lesson_stmt->bind_param("iis", $class_id_post, $professor_id, $topic);
    $lesson_stmt->execute();
    $lesson_id = $conn->insert_id;
    $lesson_stmt->close();
    $attendance_sql = "INSERT INTO attendance (lesson_id, student_id, status, period_number, recorded_by) VALUES (?, ?, ?, ?, ?)";
    $attendance_stmt = $conn->prepare($attendance_sql);
    foreach ($_POST['presencas'] as $student_id => $periods) {
        foreach ($periods as $period_number => $status) {
            $attendance_stmt->bind_param("iisii", $lesson_id, $student_id, $status, $period_number, $professor_id);
            $attendance_stmt->execute();
        }
    }
    $attendance_stmt->close();
    header("Location: professor_dashboard.php?page=marcar_presenca&success=1");
    exit;
}
if(isset($_GET['success'])) {
    $msg = "Presenças registradas com sucesso!";
}
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
// --- Fim da Lógica PHP ---
?>

<style>
    /* Estilo para a seleção de turma (Passo 1) */
    .class-selector {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }
    .class-button {
        display: block;
        padding: 20px 15px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
        color: var(--teal);
        transition: all 0.2s ease;
        font-size: 1.1em;
    }
    .class-button:hover {
        background-color: #f1f1f1;
        border-color: #ccc;
    }
    .class-button.active {
        background-color: var(--teal);
        color: white;
        border-color: var(--teal);
        box-shadow: 0 4px 10px rgba(32, 132, 137, 0.3);
        transform: translateY(-2px);
    }
    /* Tabela de presença */
    .presence-table th, .presence-table td {
        text-align: center;
        padding: 8px; /* Mais compacto para a grade */
    }
    .presence-table th:first-child, .presence-table td:first-child {
        text-align: left;
    }
    .presence-table select {
        padding: 6px;
        font-size: 0.9em;
    }
    .topic-input {
        margin-bottom: 20px;
    }
    .topic-input label {
        display: block;
        font-weight: 600;
        margin-bottom: 5px;
    }
</style>

<h1>Marcar Presença</h1>
<?php if ($msg) echo "<p class='msg-ok'>$msg</p>"; ?>

<div class="box">
    <h3>Passo 1: Selecione a Turma para a Aula de Hoje</h3>
    <div class="class-selector">
    <?php foreach ($professor_classes as $class): ?>
        <a href="?page=marcar_presenca&class_id=<?= $class['id_classes'] ?>" class="class-button <?= $class_id == $class['id_classes'] ? 'active' : '' ?>">
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
        <div class="topic-input">
            <label for="topic">Tópico do encontro de hoje:</label>
            <input type="text" id="topic" name="topic" placeholder="Ex: Revisão para a Prova" required>
        </div>
        
        <table class="presence-table">
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