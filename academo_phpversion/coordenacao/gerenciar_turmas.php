<?php
require_once __DIR__ . '/../db.php';
$erro = ''; $sucesso = '';

// Lógica para CRIAR uma nova turma (disciplina + classe)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_turma'])) {
    $title = trim($_POST['discipline_title']);
    $code = trim($_POST['discipline_code']);
    $semester_id = (int)$_POST['semester_id'];
    $periods = (int)$_POST['periods_per_session'];

    if (!$title || !$code || !$semester_id || !$periods) {
        $erro = "Todos os campos para criar a turma são obrigatórios.";
    } else {
        // 1. Insere a nova disciplina
        $stmt_disc = $conn->prepare("INSERT INTO disciplines (code, title) VALUES (?, ?)");
        $stmt_disc->bind_param('ss', $code, $title);
        if ($stmt_disc->execute()) {
            $new_discipline_id = $conn->insert_id;
            $stmt_disc->close();

            // 2. Cria a classe vinculada à disciplina
            $stmt_class = $conn->prepare("INSERT INTO classes (discipline_id, semester_id, periods_per_session) VALUES (?, ?, ?)");
            $stmt_class->bind_param('iii', $new_discipline_id, $semester_id, $periods);
            if ($stmt_class->execute()) {
                $sucesso = "Turma '$title' criada com sucesso!";
            } else {
                $erro = "Erro ao criar a classe: " . $stmt_class->error;
            }
            $stmt_class->close();
        } else {
            $erro = "Erro ao criar a disciplina (verifique se o código já existe): " . $stmt_disc->error;
        }
    }
}

// Lógica para ATUALIZAR o professor da turma
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_professor'])) {
    $class_id_to_update = (int)$_POST['class_id'];
    $new_prof_id = (int)$_POST['professor_id'];
    $stmt = $conn->prepare("UPDATE classes SET professor_id = ? WHERE id_classes = ?");
    $stmt->bind_param('ii', $new_prof_id, $class_id_to_update);
    if($stmt->execute()){
        $sucesso = "Professor da turma atualizado com sucesso!";
    } else {
        $erro = "Erro ao atualizar professor: " . $stmt->error;
    }
    $stmt->close();
}

// Busca dados para a página
$classes = $conn->query("SELECT c.id_classes, d.id_disciplines, d.title, u.name AS professor_name FROM classes c JOIN disciplines d ON c.discipline_id = d.id_disciplines LEFT JOIN users u ON c.professor_id = u.id_users ORDER BY d.title")->fetch_all(MYSQLI_ASSOC);
$professors = $conn->query("SELECT id_users, name FROM users WHERE role_id = 2 AND status = 'ativo' ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$semesters = $conn->query("SELECT id_semesters, name FROM semesters ORDER BY start_date DESC")->fetch_all(MYSQLI_ASSOC);
?>
<style>
    .action-buttons { display: flex; gap: 8px; align-items: center; }
    .action-buttons a, .action-buttons button { padding: 6px 12px; font-size: 14px; text-decoration: none; color: white; border-radius: 4px; border: none; cursor: pointer; }
</style>
<h1>Gerenciar Turmas</h1>

<div class="box">
    <h2>Criar Nova Turma</h2>
    <?php if ($erro) echo "<p style='color:#a00; font-weight:bold;'>" . htmlspecialchars($erro) . "</p>"; ?>
    <?php if ($sucesso) echo "<p style='color:green; font-weight:bold;'>" . htmlspecialchars($sucesso) . "</p>"; ?>
    <form method="POST" action="?page=gerenciar_turmas">
        <table>
            <tr>
                <td><label for="discipline_title">Nome da Nova Turma/Disciplina:</label></td>
                <td><input type="text" name="discipline_title" id="discipline_title" required></td>
            </tr>
            <tr>
                <td><label for="discipline_code">Código da Disciplina (único):</label></td>
                <td><input type="text" name="discipline_code" id="discipline_code" placeholder="Ex: MAT101" required></td>
            </tr>
            <tr>
                <td><label for="semester_id">Semestre:</label></td>
                <td>
                    <select name="semester_id" id="semester_id" required>
                        <option value="">Selecione...</option>
                        <?php foreach($semesters as $semester): ?>
                            <option value="<?= $semester['id_semesters'] ?>"><?= htmlspecialchars($semester['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="periods_per_session">Aulas por Encontro:</label></td>
                <td><input type="number" name="periods_per_session" id="periods_per_session" value="2" min="1" required></td>
            </tr>
        </table>
        <button type="submit" name="criar_turma">Criar Turma</button>
    </form>
</div>

<div class="box">
    <h2>Atribuir Professor e Editar Turmas</h2>
    <table>
        <thead><tr><th>Turma (Disciplina)</th><th>Professor Atual</th><th>Mudar Professor</th><th>Ações</th></tr></thead>
        <tbody>
            <?php foreach($classes as $class): ?>
            <tr>
                <td><?= htmlspecialchars($class['title']) ?></td>
                <td><?= htmlspecialchars($class['professor_name'] ?? 'Nenhum') ?></td>
                <td>
                    <form method="POST" action="?page=gerenciar_turmas">
                        <input type="hidden" name="class_id" value="<?= $class['id_classes'] ?>">
                        <select name="professor_id" required>
                            <option value="">Selecione um professor...</option>
                            <?php foreach($professors as $prof): ?>
                                <option value="<?= $prof['id_users'] ?>"><?= htmlspecialchars($prof['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="salvar_professor">Salvar</button>
                    </form>
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="?page=editar_turma&id=<?= $class['id_disciplines'] ?>" style="background-color:#007bff;">Editar</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>