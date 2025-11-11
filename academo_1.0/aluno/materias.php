<?php
require_once __DIR__ . '/../db.php';
$aluno_id = (int)$_SESSION['usuario_id'];

$sql = "SELECT d.title AS disciplina_titulo, p.name AS professor_nome FROM enrollments e JOIN classes c ON e.class_id = c.id_classes JOIN disciplines d ON c.discipline_id = d.id_disciplines JOIN users p ON c.professor_id = p.id_users WHERE e.student_id = ? AND e.status = 'matriculado' ORDER BY d.title";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $aluno_id);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<h1>Minhas Matérias</h1>
<div class="box">
  <?php if (empty($rows)): ?>
    <p>Nenhuma matéria encontrada.</p>
  <?php else: ?>
    <table>
      <thead><tr><th>Matéria</th><th>Professor</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['disciplina_titulo']) ?></td>
            <td><?= htmlspecialchars($r['professor_nome']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>