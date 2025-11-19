<?php
require_once __DIR__ . '/../db.php';
$erro = ''; $sucesso = '';
$owner_id = $_SESSION['usuario_id'];

// CRIAR TURMA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_turma'])) {
    $title = trim($_POST['discipline_title']);
    $code = trim($_POST['discipline_code']);
    $semester_id = (int)$_POST['semester_id'];
    $periods = (int)$_POST['periods_per_session'];

    if (!$title || !$code || !$semester_id || !$periods) {
        $erro = "Todos os campos são obrigatórios.";
    } else {
        $stmt_disc = $conn->prepare("INSERT INTO disciplines (code, title) VALUES (?, ?)");
        $stmt_disc->bind_param('ss', $code, $title);
        if ($stmt_disc->execute()) {
            $new_discipline_id = $conn->insert_id;
            $stmt_disc->close();

            // MUDANÇA: Salva o owner_id na turma
            $stmt_class = $conn->prepare("INSERT INTO classes (discipline_id, semester_id, periods_per_session, owner_id) VALUES (?, ?, ?, ?)");
            $stmt_class->bind_param('iiii', $new_discipline_id, $semester_id, $periods, $owner_id);
            
            if ($stmt_class->execute()) {
                $sucesso = "Turma criada com sucesso!";
            } else {
                $erro = "Erro ao criar classe.";
            }
            $stmt_class->close();
        } else {
            $erro = "Erro ao criar disciplina (código já existe?).";
        }
    }
}

// ATUALIZAR PROFESSOR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_professor'])) {
    $class_id_to_update = (int)$_POST['class_id'];
    $new_prof_id = (int)$_POST['professor_id'];
    // Garante que só altera turmas deste dono
    $stmt = $conn->prepare("UPDATE classes SET professor_id = ? WHERE id_classes = ? AND owner_id = ?");
    $stmt->bind_param('iii', $new_prof_id, $class_id_to_update, $owner_id);
    if($stmt->execute()){ $sucesso = "Professor atualizado!"; } 
    else { $erro = "Erro ao atualizar."; }
    $stmt->close();
}

// BUSCAR DADOS (Filtrados por owner_id)
// 1. Turmas deste coordenador
$stmt = $conn->prepare("
    SELECT c.id_classes, d.id_disciplines, d.title, u.name AS professor_name 
    FROM classes c 
    JOIN disciplines d ON c.discipline_id = d.id_disciplines 
    LEFT JOIN users u ON c.professor_id = u.id_users 
    WHERE c.owner_id = ? 
    ORDER BY d.title
");
$stmt->bind_param('i', $owner_id);
$stmt->execute();
$classes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 2. Professores deste coordenador
$stmt = $conn->prepare("SELECT id_users, name FROM users WHERE role_id = 2 AND status = 'ativo' AND owner_id = ? ORDER BY name");
$stmt->bind_param('i', $owner_id);
$stmt->execute();
$professors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$semesters = $conn->query("SELECT id_semesters, name FROM semesters ORDER BY start_date DESC")->fetch_all(MYSQLI_ASSOC);
?>
<style> .action-buttons { display: flex; gap: 8px; } .action-buttons a, .action-buttons button { padding: 6px 12px; text-decoration: none; color: white; border-radius: 4px; border: none; cursor: pointer; } </style>
<h1>Gerenciar Turmas</h1>
<div class="box">
    <h2>Criar Nova Turma</h2>
    <?php if ($erro) echo "<p style='color:red;font-weight:bold;'>$erro</p>"; ?>
    <?php if ($sucesso) echo "<p style='color:green;font-weight:bold;'>$sucesso</p>"; ?>
    <form method="POST" action="?page=gerenciar_turmas">
        <table>
            <tr><td><label>Nome:</label></td><td><input type="text" name="discipline_title" required></td></tr>
            <tr><td><label>Código:</label></td><td><input type="text" name="discipline_code" required></td></tr>
            <tr><td><label>Semestre:</label></td><td><select name="semester_id" required><option value="">...</option><?php foreach($semesters as $s): ?><option value="<?=$s['id_semesters']?>"><?=$s['name']?></option><?php endforeach; ?></select></td></tr>
            <tr><td><label>Aulas/Dia:</label></td><td><input type="number" name="periods_per_session" value="2" min="1" required></td></tr>
        </table>
        <button type="submit" name="criar_turma">Criar Turma</button>
    </form>
</div>

<div class="box">
    <h2>Suas Turmas</h2>
    <table>
        <thead><tr><th>Turma</th><th>Professor</th><th>Ações</th></tr></thead>
        <tbody>
            <?php foreach($classes as $class): ?>
            <tr>
                <td><?= htmlspecialchars($class['title']) ?></td>
                <td>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="class_id" value="<?= $class['id_classes'] ?>">
                        <select name="professor_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach($professors as $prof): ?>
                                <option value="<?= $prof['id_users'] ?>" <?= ($class['professor_name'] == $prof['name']) ? 'selected' : '' ?>><?= htmlspecialchars($prof['name']) ?></option>
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