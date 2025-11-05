<?php
require_once __DIR__ . '/../db.php';
$professor_id = (int)$_SESSION['usuario_id'];
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$erro = ''; $sucesso = '';

// Lógica para criar nova tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_tarefa'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $class_id_post = (int)$_POST['class_id'];

    if ($title && $due_date && $class_id_post) {
        $stmt = $conn->prepare("INSERT INTO assignments (class_id, title, description, due_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isss', $class_id_post, $title, $description, $due_date);
        if ($stmt->execute()) {
            $sucesso = "Tarefa criada com sucesso!";
        } else {
            $erro = "Erro ao criar tarefa.";
        }
        $stmt->close();
    } else {
        $erro = "Título e data de entrega são obrigatórios.";
    }
}

// Busca turmas e tarefas existentes
$classes = $conn->query("SELECT c.id_classes, d.title FROM classes c JOIN disciplines d ON c.discipline_id = d.id_disciplines WHERE c.professor_id = $professor_id")->fetch_all(MYSQLI_ASSOC);
$assignments = [];
if ($class_id) {
    $assignments = $conn->query("SELECT id_assignments, title, due_date FROM assignments WHERE class_id = $class_id ORDER BY due_date DESC")->fetch_all(MYSQLI_ASSOC);
}
?>
<h1>Gerenciar Tarefas</h1>
<?php if ($sucesso) echo "<p style='color:green;font-weight:bold;'>$sucesso</p>"; ?>
<?php if ($erro) echo "<p style='color:red;font-weight:bold;'>$erro</p>"; ?>

<div class="box">
    <h3>Selecione a Turma</h3>
    <form method="GET" action="">
        <input type="hidden" name="page" value="gerenciar_tarefas">
        <select name="class_id" onchange="this.form.submit()" style="width:100%; padding: 8px;">
            <option value="">Selecione...</option>
            <?php foreach($classes as $class): ?>
                <option value="<?= $class['id_classes'] ?>" <?= $class_id == $class['id_classes'] ? 'selected' : ''?>><?= htmlspecialchars($class['title']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<?php if($class_id): ?>
<div class="box">
    <h3>Criar Nova Tarefa</h3>
    <form method="POST" action="?page=gerenciar_tarefas&class_id=<?= $class_id ?>">
        <input type="hidden" name="class_id" value="<?= $class_id ?>">
        <label>Título:</label>
        <input type="text" name="title" required style="width:100%; padding: 8px;">
        <label>Descrição:</label>
        <textarea name="description" rows="3" style="width:100%; padding: 8px;"></textarea>
        <label>Data de Entrega:</label>
        <input type="datetime-local" name="due_date" required style="width:100%; padding: 8px;">
        <button type="submit" name="criar_tarefa">Criar Tarefa</button>
    </form>
</div>

<div class="box">
    <h3>Tarefas Criadas</h3>
    <table>
        <thead><tr><th>Título</th><th>Data de Entrega</th></tr></thead>
        <tbody>
            <?php foreach($assignments as $assignment): ?>
            <tr>
                <td><?= htmlspecialchars($assignment['title']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($assignment['due_date'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>