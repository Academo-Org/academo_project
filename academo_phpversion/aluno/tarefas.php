<?php
require_once __DIR__ . '/../db.php';
$aluno_id = (int)$_SESSION['usuario_id'];
$erro = ''; $sucesso = '';

// Lógica de envio de arquivo (simulada)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_tarefa'])) {
    $assignment_id = (int)$_POST['assignment_id'];
    if (isset($_FILES['arquivo_tarefa']) && $_FILES['arquivo_tarefa']['error'] == 0) {
        $file_name = basename($_FILES['arquivo_tarefa']['name']);
        
        // Em um projeto real, você moveria o arquivo para uma pasta segura
        // move_uploaded_file($_FILES['arquivo_tarefa']['tmp_name'], 'caminho/seguro/' . $file_name);
        
        $stmt = $conn->prepare("INSERT INTO submissions (assignment_id, student_id, file_path, file_name) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE file_path=VALUES(file_path), file_name=VALUES(file_name), submitted_at=NOW()");
        $mock_path = 'uploads/' . $file_name; // Simulação de caminho
        $stmt->bind_param('iiss', $assignment_id, $aluno_id, $mock_path, $file_name);
        if ($stmt->execute()) {
            $sucesso = "Tarefa enviada com sucesso!";
        } else {
            $erro = "Erro ao enviar tarefa.";
        }
        $stmt->close();
    } else {
        $erro = "Houve um erro no upload do arquivo.";
    }
}

// Busca tarefas do aluno
$sql = "SELECT a.id_assignments, d.title as discipline_title, a.title, a.description, a.due_date, s.submitted_at 
        FROM assignments a 
        JOIN classes c ON a.class_id = c.id_classes
        JOIN disciplines d ON c.discipline_id = d.id_disciplines
        JOIN enrollments e ON e.class_id = c.id_classes
        LEFT JOIN submissions s ON s.assignment_id = a.id_assignments AND s.student_id = e.student_id
        WHERE e.student_id = ? 
        ORDER BY a.due_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $aluno_id);
$stmt->execute();
$assignments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<h1>Minhas Tarefas</h1>
<?php if ($sucesso) echo "<p style='color:green;font-weight:bold;'>$sucesso</p>"; ?>
<?php if ($erro) echo "<p style='color:red;font-weight:bold;'>$erro</p>"; ?>

<div class="box">
    <h2>Lista de Tarefas</h2>
    <table>
        <thead><tr><th>Disciplina</th><th>Título</th><th>Entrega</th><th>Status</th><th>Ação</th></tr></thead>
        <tbody>
            <?php foreach($assignments as $task): ?>
            <tr>
                <td><?= htmlspecialchars($task['discipline_title']) ?></td>
                <td><?= htmlspecialchars($task['title']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($task['due_date'])) ?></td>
                <td><?= $task['submitted_at'] ? 'Enviado' : 'Pendente' ?></td>
                <td>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="assignment_id" value="<?= $task['id_assignments'] ?>">
                        <input type="file" name="arquivo_tarefa" required>
                        <button type="submit" name="enviar_tarefa">Enviar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>