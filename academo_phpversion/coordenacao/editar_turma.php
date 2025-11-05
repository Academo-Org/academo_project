<?php
require_once __DIR__ . '/../db.php';
$erro = ''; $sucesso = '';
$discipline_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($discipline_id <= 0) {
    echo "<h1>Erro</h1><p>ID da disciplina não fornecido ou inválido.</p>";
    exit;
}

// Lógica para salvar as alterações
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_disciplina'])) {
    $title = trim($_POST['title']);
    $code = trim($_POST['code']);
    if (!empty($title) && !empty($code)) {
        $stmt = $conn->prepare("UPDATE disciplines SET title = ?, code = ? WHERE id_disciplines = ?");
        $stmt->bind_param('ssi', $title, $code, $discipline_id);
        if ($stmt->execute()) {
            $sucesso = "Disciplina atualizada com sucesso!";
        } else {
            $erro = "Erro ao atualizar. O código da disciplina já pode estar em uso por outra matéria.";
        }
        $stmt->close();
    } else {
        $erro = "Título e código são obrigatórios.";
    }
}

// Busca os dados atuais da disciplina para preencher o formulário
$stmt_fetch = $conn->prepare("SELECT title, code FROM disciplines WHERE id_disciplines = ?");
$stmt_fetch->bind_param('i', $discipline_id);
$stmt_fetch->execute();
$discipline = $stmt_fetch->get_result()->fetch_assoc();
$stmt_fetch->close();

if (!$discipline) {
    echo "<h1>Erro</h1><p>Disciplina não encontrada.</p>";
    exit;
}
?>
<h1>Editar Turma / Disciplina</h1>
<p><a href="?page=gerenciar_turmas"> &larr; Voltar para a lista de turmas</a></p>

<div class="box">
    <h2>Editando: <?= htmlspecialchars($discipline['title']) ?></h2>
    
    <?php if ($erro) echo "<p style='color:#a00; font-weight:bold;'>" . htmlspecialchars($erro) . "</p>"; ?>
    <?php if ($sucesso) echo "<p style='color:green; font-weight:bold;'>" . htmlspecialchars($sucesso) . "</p>"; ?>

    <form method="POST" action="?page=editar_turma&id=<?= $discipline_id ?>">
        <label for="title" style="display:block; margin:10px 0 5px;">Título da Disciplina:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($discipline['title']) ?>" required style="width:100%; padding: 8px;">
        
        <label for="code" style="display:block; margin:10px 0 5px;">Código da Disciplina:</label>
        <input type="text" id="code" name="code" value="<?= htmlspecialchars($discipline['code']) ?>" required style="width:100%; padding: 8px;">
        
        <button type="submit" name="salvar_disciplina">Salvar Alterações</button>
    </form>
</div>