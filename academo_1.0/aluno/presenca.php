<?php
require_once __DIR__ . '/../db.php';
$aluno_id = (int)$_SESSION['usuario_id'];
$selected_discipline_id = isset($_GET['discipline_id']) ? (int)$_GET['discipline_id'] : 0;

// 1. Busca as disciplinas para o filtro
$disciplines_sql = "SELECT DISTINCT d.id_disciplines, d.title FROM enrollments e JOIN classes c ON e.class_id = c.id_classes JOIN disciplines d ON c.discipline_id = d.id_disciplines WHERE e.student_id = ? AND e.status = 'matriculado' ORDER BY d.title";
$stmt_disciplines = $conn->prepare($disciplines_sql);
$stmt_disciplines->bind_param('i', $aluno_id);
$stmt_disciplines->execute();
$disciplines_list = $stmt_disciplines->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_disciplines->close();

// 2. Busca os dados de presença
$sql = "SELECT l.id_lessons, l.lesson_date, l.topic, d.title AS disciplina_titulo, a.status, a.period_number FROM attendance AS a JOIN lessons AS l ON a.lesson_id = l.id_lessons JOIN classes AS c ON l.class_id = c.id_classes JOIN disciplines AS d ON c.discipline_id = d.id_disciplines WHERE a.student_id = ?";
if ($selected_discipline_id > 0) { $sql .= " AND d.id_disciplines = ?"; }
$sql .= " ORDER BY l.lesson_date DESC, a.period_number ASC";
$stmt = $conn->prepare($sql);
if ($selected_discipline_id > 0) { $stmt->bind_param('ii', $aluno_id, $selected_discipline_id); } 
else { $stmt->bind_param('i', $aluno_id); }
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 3. Processa e agrupa os dados
$total_aulas = count($rows); $presencas = 0; $atrasos = 0;
foreach($rows as $row) { if ($row['status'] == 'presente') $presencas++; elseif ($row['status'] == 'atrasado') $atrasos++; }
$aulas_comparecidas = $presencas + $atrasos;
$percentual_presenca = $total_aulas > 0 ? round(($aulas_comparecidas / $total_aulas) * 100, 2) : 0;
$presencas_agrupadas = [];
foreach ($rows as $row) {
    $lesson_id = $row['id_lessons'];
    if (!isset($presencas_agrupadas[$lesson_id])) { $presencas_agrupadas[$lesson_id] = ['data_aula' => date('d/m/Y', strtotime($row['lesson_date'])), 'disciplina' => $row['disciplina_titulo'], 'topic' => $row['topic'], 'periodos' => []]; }
    $presencas_agrupadas[$lesson_id]['periodos'][] = ['numero' => $row['period_number'], 'status' => $row['status']];
}
?>
<style>
    .status-badge { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 0.9em; margin: 2px; color: white; }
    .status-presente { background-color: #28a745; } .status-ausente { background-color: #dc3545; }
    .status-atrasado { background-color: #ffc107; color: #333;} .status-justificado { background-color: #17a2b8; }
    .accordion-toggle { background-color: #1d888b; color: white; cursor: pointer; padding: 15px; width: 100%; border: none; text-align: left; font-size: 18px; transition: 0.4s; border-radius: 5px; }
    .accordion-toggle:after { content: '\\02795'; font-size: 13px; float: right; } .accordion-toggle.active:after { content: "\\2796"; }
    .accordion-content { padding: 0 18px; background-color: white; max-height: 0; overflow: hidden; transition: max-height 0.2s ease-out; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px;}
    .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; text-align: center; }
    .summary-item { background-color: #fff; padding: 15px; border-radius: 5px; border: 1px solid #eee; }
    .summary-item h3 { margin: 0 0 5px 0; color: #166d6f; } .summary-item p { font-size: 24px; font-weight: bold; margin: 0; }
</style>
<h1>Minhas Presenças</h1>
<div class="box">
    <h2>Filtrar por Matéria</h2>
    <form method="GET" action="">
        <input type="hidden" name="page" value="presenca">
        <select name="discipline_id" onchange="this.form.submit()" style="width: 100%; padding: 10px;">
            <option value="0">Ver Todas as Matérias</option>
            <?php foreach ($disciplines_list as $discipline): ?>
                <option value="<?= $discipline['id_disciplines'] ?>" <?= ($selected_discipline_id == $discipline['id_disciplines']) ? 'selected' : '' ?>><?= htmlspecialchars($discipline['title']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>
<div class="box">
    <h2>Resumo Geral</h2>
    <div class="summary-grid">
        <div class="summary-item"><h3>Aulas Comparecidas</h3><p><?= $aulas_comparecidas ?></p></div>
        <div class="summary-item"><h3>Total de Aulas</h3><p><?= $total_aulas ?></p></div>
        <div class="summary-item"><h3>Frequência</h3><p><?= $percentual_presenca ?>%</p></div>
    </div>
</div>
<button class="accordion-toggle">Ver/Ocultar Histórico Detalhado</button>
<div class="accordion-content">
  <?php if (empty($presencas_agrupadas)): ?>
    <p>Nenhum registro de presença encontrado para a seleção atual.</p>
  <?php else: ?>
    <table>
      <thead><tr><th>Data</th><th>Disciplina</th><th>Tópico</th><th>Detalhes</th></tr></thead>
      <tbody>
        <?php foreach ($presencas_agrupadas as $presenca): ?>
          <tr>
            <td><?= htmlspecialchars($presenca['data_aula']) ?></td>
            <td><?= htmlspecialchars($presenca['disciplina']) ?></td>
            <td><?= htmlspecialchars($presenca['topic']) ?></td>
            <td>
                <?php foreach ($presenca['periodos'] as $periodo): ?>
                    <span class="status-badge status-<?= htmlspecialchars($periodo['status']) ?>"><b>Aula <?= htmlspecialchars($periodo['numero']) ?>:</b> <?= htmlspecialchars(ucfirst($periodo['status'])) ?></span>
                <?php endforeach; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var acc = document.getElementsByClassName("accordion-toggle");
    for (var i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.maxHeight) { panel.style.maxHeight = null; } 
            else { panel.style.maxHeight = panel.scrollHeight + "px"; }
        });
    }
});
</script>