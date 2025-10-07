<?php
session_start();
include '../db.php';
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'professor') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aluno_id = $_POST['aluno_id'];
    $status = $_POST['status'];
    $data = date('Y-m-d');

    $sql = "INSERT INTO presenca (aluno_id, data, status) VALUES ('$aluno_id', '$data', '$status')";
    if (mysqli_query($conn, $sql)) {
        $msg = "Presença registrada!";
    } else {
        $msg = "Erro ao registrar.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Marcar Presença</title>
</head>
<body>
<h1>Marcar Presença</h1>
<?php if (isset($msg)) echo "<p>$msg</p>"; ?>

<form method="post">
  ID do Aluno: <input type="number" name="aluno_id" required><br><br>
  Presente? 
  <select name="status">
    <option value="Presente">Presente</option>
    <option value="Faltou">Faltou</option>
  </select><br><br>
  <button type="submit">Registrar</button>
</form>

<p><a href="../professor_dashboard.php">Voltar</a></p>
</body>
</html>
