<?php
require_once __DIR__ . '/../db.php';
$aluno_id = (int)$_SESSION['usuario_id'];
// Pega o ID da turma pela URL
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;

if ($class_id === 0) {
    echo "<h1>Erro</h1><p>Turma não especificada.</p>";
    exit;
}

// 1. Busca o nome da disciplina para o título
$stmt_title = $conn->prepare("SELECT d.title FROM disciplines d JOIN classes c ON d.id_disciplines = c.discipline_id WHERE c.id_classes = ?");
$stmt_title->bind_param('i', $class_id);
$stmt_title->execute();
$disciplina = $stmt_title->get_result()->fetch_assoc();
$stmt_title->close();

// 2. Busca o RESUMO de notas para esta matéria
$stmt_summary = $conn->prepare("
    SELECT COUNT(g.id_grades) as total_notas, AVG(g.score) as media_materia
    FROM grades AS g
    JOIN grade_items AS gi ON g.grade_item_id = gi.id_grade_items
    WHERE g.student_id = ? AND gi.class_id = ?
");
$stmt_summary->bind_param('ii', $aluno_id, $class_id);
$stmt_summary->execute();
$resumo_materia = $stmt_summary->get_result()->fetch_assoc();
$stmt_summary->close();

// 3. Busca a LISTA de notas para esta matéria
$sql_grades = "
    SELECT 
        gi.title AS avaliacao_titulo,
        g.score AS nota,
        g.feedback AS observacao
    FROM grades AS g
    JOIN grade_items AS gi ON g.grade_item_id = gi.id_grade_items
    WHERE g.student_id = ? AND gi.class_id = ?
    ORDER BY gi.title
";
$stmt_grades = $conn->prepare($sql_grades);
$stmt_grades->bind_param('ii', $aluno_id, $class_id);
$stmt_grades->execute();
$rows = $stmt_grades->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_grades->close();
?>

<style>
    /* Estilos para o link de "Voltar" */
    .back-link {
        display: inline-block;
        margin-bottom: 25px;
        font-size: 1em;
        font-weight: 600;
        color: #555;
        text-decoration: none;
    }
    .back-link:hover {
        color: var(--teal);
    }

    /* Estilos para os cartões de resumo (do inicio.php) */
    .summary-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }
    .summary-item {
      background-color: #f9f9f9;
      border: 1px solid #e0e0e0;
      border-left: 5px solid var(--teal);
      padding: 25px;
      border-radius: 5px;
      text-align: center;
    }
    .summary-item h3 {
      margin-top: 0;
      margin-bottom: 10px;
      color: #333;
      border: none;
      font-size: 1em;
    }
    .summary-item p {
      font-size: 2.5em;
      font-weight: 600;
      color: var(--teal);
      margin: 0;
    }
    
    /* Estilos para as notas (verde/vermelho) */
    .nota-boa { color: #28a745; font-weight: bold; }
    .nota-ruim { color: #dc3545; font-weight: bold; }

    /* Deixa a tabela mais espaçada */
    table th, table td {
        padding: 14px; /* Mais padding */
    }
    table td:nth-child(2) { /* Centraliza a nota */
        text-align: center;
    }
</style>

<a href="?page=materias" class="back-link">
    &larr; Voltar para Todas as Matérias
</a>

<h1><?= htmlspecialchars($disciplina['title'] ?? 'Matéria') ?></h1>

<div class="box">
    <h2>Resumo da Matéria</h2>
    <div class="summary-grid">
        <div class="summary-item">
            <h3>Média na Matéria</h3>
            <p><?= number_format($resumo_materia['media_materia'] ?? 0, 2, ',', '.') ?></p>
        </div>
        <div class="summary-item">
            <h3>Avaliações Lançadas</h3>
            <p><?= $resumo_materia['total_notas'] ?></p>
        </div>
    </div>
</div>

<div class="box">
  <h2>Notas Detalhadas</h2>
  <?php if (empty($rows)): ?>
    <p>Nenhuma nota lançada para esta matéria ainda.</p>
  <?php else: ?>
    <table>
      <thead><tr><th>Avaliação</th><th>Nota</th><th>Observações</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['avaliacao_titulo']) ?></td>
            <td class="<?= (float)$r['nota'] >= 6 ? 'nota-boa' : 'nota-ruim' ?>">
                <?= htmlspecialchars(number_format($r['nota'], 2, ',', '.')) ?>
            </td>
            <td><?= htmlspecialchars($r['observacao'] ?? 'Nenhuma') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>