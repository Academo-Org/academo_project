<?php
session_start();
include '../db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'professor') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aluno_id = $_POST['aluno_id'];
    $disciplina = $_POST['disciplina'];
    $nota = $_POST['nota'];

    $sql = "INSERT INTO notas (aluno_id, disciplina, nota) VALUES ('$aluno_id', '$disciplina', '$nota')";
    if (mysqli_query($conn, $sql)) {
        $msg = "Nota lançada com sucesso!";
    } else {
        $msg = "Erro ao lançar nota.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Lançar Notas</title>
</head>
<body>
<h1>Lançar Notas</h1>

<?php if (isset($msg)) echo "<p>$msg</p>"; ?>

<form method="post">
  ID do Aluno: <input type="number" name="aluno_id" required><br><br>
  Disciplina: <input type="text" name="disciplina" required><br><br>
  Nota: <input type="number" step="0.01" name="nota" required><br><br>
  <button type="submit">Salvar</button>
</form>

<p><a href="../professor_dashboard.php">Voltar</a></p>
</body>
</html>
