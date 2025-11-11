<?php
// A sessão já foi verificada no arquivo 'professor_dashboard.php'
require_once __DIR__ . '/../db.php'; 

$professor_id = $_SESSION['usuario_id'];
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$item_id = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;
$msg = '';

// --- Lógica PHP (sem alterações) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['criar_atividade'])) {
    $titulo = trim($_POST['titulo_atividade']);
    $peso = (float)($_POST['peso_atividade'] ?? 0);
    $nota_max = (float)($_POST['nota_max_atividade'] ?? 10.0);
    $class_id_post = (int)$_POST['class_id'];
    if (!empty($titulo) && $class_id_post > 0) {
        $sql = "INSERT INTO grade_items (class_id, title, weight, max_score) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isdd", $class_id_post, $titulo, $peso, $nota_max);
        $stmt->execute();
        $new_item_id = $conn->insert_id;
        $stmt->close();
        header("Location: professor_dashboard.php?page=enviar_notas&class_id=$class_id_post&item_id=$new_item_id");
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['notas'])) {
    $item_id_post = (int)$_POST['item_id'];
    $class_id_post = (int)$_POST['class_id'];
    $sql = "INSERT INTO grades (grade_item_id, student_id, score, feedback, graded_by) VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE score = VALUES(score), feedback = VALUES(feedback)";
    $stmt = $conn->prepare($sql);
    foreach ($_POST['notas'] as $student_id => $data) {
        $score = $data['score'];
        $feedback = trim($data['feedback']);
        if ($score !== '') {
            $score_decimal = (float)$score;
            $stmt->bind_param("iidsi", $item_id_post, $student_id, $score_decimal, $feedback, $professor_id);
            $stmt->execute();
        }
    }
    $stmt->close();
    header("Location: professor_dashboard.php?page=enviar_notas&class_id=$class_id_post&item_id=$item_id_post&success=1");
    exit;
}
if(isset($_GET['success'])) {
    $msg = "Notas salvas com sucesso!";
}
$classes_stmt = $conn->prepare("SELECT c.id_classes, d.title FROM classes c JOIN disciplines d ON c.discipline_id = d.id_disciplines WHERE c.professor_id = ?");
$classes_stmt->bind_param("i", $professor_id);
$classes_stmt->execute();
$professor_classes = $classes_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$classes_stmt->close();
$grade_items = [];
if ($class_id) {
    $items_stmt = $conn->prepare("SELECT id_grade_items, title FROM grade_items WHERE class_id = ? ORDER BY title");
    $items_stmt->bind_param("i", $class_id);
    $items_stmt->execute();
    $grade_items = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $items_stmt->close();
}
$students = [];
if ($class_id && $item_id) {
    $students_sql = "SELECT u.id_users, u.name, g.score, g.feedback FROM enrollments e JOIN users u ON e.student_id = u.id_users LEFT JOIN grades g ON g.student_id = u.id_users AND g.grade_item_id = ? WHERE e.class_id = ? AND e.status = 'matriculado' ORDER BY u.name";
    $students_stmt = $conn->prepare($students_sql);
    $students_stmt->bind_param("ii", $item_id, $class_id);
    $students_stmt->execute();
    $students = $students_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $students_stmt->close();
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
    /* Tabela de criação de atividade */
    .form-table {
        width: 100%;
        max-width: 600px; /* Limita a largura para melhor leitura */
    }
    .form-table td {
        border: none;
        padding: 8px 0;
    }
    .form-table label {
        font-weight: 600;
        padding-right: 15px;
    }
    /* Lista de atividades existentes */
    .activity-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .activity-link {
        padding: 8px 15px;
        background-color: #f0f0f0;
        border-radius: 20px;
        color: #555;
        font-weight: 600;
        font-size: 0.9em;
    }
    .activity-link:hover {
        background-color: #e0e0e0;
    }
    .activity-link.active {
        background-color: var(--purple);
        color: white;
    }
</style>

<h1>Lançar Notas</h1>
<?php if ($msg) echo "<p class='msg-ok'>$msg</p>"; ?>

<div class="box">
    <h3>Passo 1: Selecione a Turma</h3>
    <div class="class-selector">
    <?php foreach ($professor_classes as $class): ?>
        <a href="?page=enviar_notas&class_id=<?= $class['id_classes'] ?>" class="class-button <?= $class_id == $class['id_classes'] ? 'active' : '' ?>">
            <?= htmlspecialchars($class['title']) ?>
        </a>
    <?php endforeach; ?>
    </div>
</div>

<?php if ($class_id): ?>
<div class="box">
    <h3>Passo 2: Crie ou Selecione uma Atividade Avaliativa</h3>
    
    <h4>Criar Nova Atividade</h4>
    <form method="post" action="?page=enviar_notas" style="margin-bottom: 20px;">
        <input type="hidden" name="class_id" value="<?= $class_id ?>">
        <table class="form-table">
            <tr>
                <td><label for="titulo_atividade">Título:</label></td>
                <td><input type="text" id="titulo_atividade" name="titulo_atividade" placeholder="Ex: Prova 1" required></td>
            </tr>
            <tr>
                <td><label for="peso_atividade">Peso (opcional):</label></td>
                <td><input type="number" id="peso_atividade" name="peso_atividade" step="0.01" min="0" placeholder="Ex: 0.4"></td>
            </tr>
             <tr>
                <td><label for="nota_max_atividade">Nota Máxima:</label></td>
                <td><input type="number" id="nota_max_atividade" name="nota_max_atividade" step="0.01" min="0" value="10.00"></td>
            </tr>
        </table>
        <button type="submit" name="criar_atividade">Criar e Lançar Notas</button>
    </form>
    
    <?php if (!empty($grade_items)): ?>
        <hr style="margin: 25px 0;">
        <h4>Lançar Notas para Atividade Existente</h4>
        <div class="activity-list">
        <?php foreach ($grade_items as $item): ?>
            <a href="?page=enviar_notas&class_id=<?= $class_id ?>&item_id=<?= $item['id_grade_items'] ?>" class="activity-link <?= $item_id == $item['id_grade_items'] ? 'active' : '' ?>">
                <?= htmlspecialchars($item['title']) ?>
            </a>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($class_id && $item_id && !empty($students)): ?>
<div class="box">
    <h3>Passo 3: Lance as Notas e Observações</h3>
    <form method="post" action="?page=enviar_notas&class_id=<?= $class_id ?>&item_id=<?= $item_id ?>">
        <input type="hidden" name="class_id" value="<?= $class_id ?>">
        <input type="hidden" name="item_id" value="<?= $item_id ?>">
        <table>
            <thead><tr><th>Aluno</th><th>Nota</th><th>Observações</th></tr></thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['name']) ?></td>
                    <td><input type="number" step="0.01" min="0" max="10" name="notas[<?= $student['id_users'] ?>][score]" value="<?= htmlspecialchars($student['score'] ?? '') ?>"></td>
                    <td><input type="text" name="notas[<?= $student['id_users'] ?>][feedback]" value="<?= htmlspecialchars($student['feedback'] ?? '') ?>" placeholder="Opcional"></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit">Salvar Notas</button>
    </form>
</div>
<?php endif; ?>