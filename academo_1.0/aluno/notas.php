<?php
require_once __DIR__ . '/../db.php';
$aluno_id = (int)$_SESSION['usuario_id'];

$sql = "SELECT d.title AS disciplina_titulo, gi.title AS avaliacao_titulo, g.score AS nota, g.feedback AS observacao FROM grades AS g JOIN grade_items AS gi ON g.grade_item_id = gi.id_grade_items JOIN classes AS c ON gi.class_id = c.id_classes JOIN disciplines AS d ON c.discipline_id = d.id_disciplines WHERE g.student_id = ? ORDER BY d.title, gi.title";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $aluno_id);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<h1>Minhas Notas</h1>
<div class="box">
  <?php if (empty($rows)): ?>
    <p>Nenhuma nota encontrada.</p>
  <?php else: ?>
    <table>
      <thead><tr><th>Disciplina</th><th>Avaliação</th><th>Nota</th><th>Observações</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['disciplina_titulo']) ?></td>
            <td><?= htmlspecialchars($r['avaliacao_titulo']) ?></td>
            <td class="<?= (float)$r['nota'] >= 6 ? 'nota-boa' : 'nota-ruim' ?>"><?= htmlspecialchars(number_format($r['nota'], 2, ',', '.')) ?></td>
            <td><?= htmlspecialchars($r['observacao'] ?? 'Nenhuma') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>