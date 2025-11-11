<?php
require_once __DIR__ . '/../db.php';
$aluno_id = (int)$_SESSION['usuario_id'];

// SQL ATUALIZADO: Busca também o ID da turma (class_id)
$sql = "
    SELECT 
        d.title AS disciplina_titulo, 
        p.name AS professor_nome,
        c.id_classes 
    FROM enrollments AS e
    JOIN classes AS c ON e.class_id = c.id_classes
    JOIN disciplines AS d ON c.discipline_id = d.id_disciplines
    JOIN users AS p ON c.professor_id = p.id_users 
    WHERE e.student_id = ? AND e.status = 'matriculado' 
    ORDER BY d.title
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $aluno_id);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<style>
    .grid {
      margin: 18px auto 10px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* Responsivo */
      gap: 30px;
    }
    .card {
      background: #fff;
      border: 2px solid var(--teal);
      border-radius: 18px;
      padding: 22px;
      display: flex;
      flex-direction: column;
      gap: 14px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .card-title {
      color: var(--teal);
      font-weight: 700;
      font-size: 1.25em;
      text-decoration: none;
      padding-bottom: 10px;
      margin-bottom: 5px;
    }
    .card-prof {
      font-size: 0.9em;
      color: #555;
    }
    .card-prof strong {
      color: #333;
    }
</style>

<h1>Minhas Matérias</h1>

<div class="grid">
  <?php if (empty($rows)): ?>
    <p>Nenhuma matéria encontrada.</p>
  <?php else: ?>
    <?php foreach ($rows as $r): ?>
      <a href="?page=detalhe_materia&class_id=<?= $r['id_classes'] ?>" class="card" style="text-decoration:none">
        <div class="card-title"><?= htmlspecialchars($r['disciplina_titulo']) ?></div>
        <p class="card-prof">
            <strong>Professor(a):</strong> <?= htmlspecialchars($r['professor_nome']) ?>
        </p>
      </a>
    <?php endforeach; ?>
  <?php endif; ?>
</div>