<?php
session_start();
include '../db.php';
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'aluno') {
    header("Location: ../index.php");
    exit;
}

$id = $_SESSION['usuario_id'];
$result = mysqli_query($conn, "SELECT * FROM notas WHERE aluno_id='$id'");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Minhas Notas</title>
</head>
<body>
<h1>Minhas Notas</h1>
<table border="1" cellpadding="10">
  <tr><th>Disciplina</th><th>Nota</th></tr>
  <?php while ($row = mysqli_fetch_assoc($result)) { ?>
  <tr>
    <td><?= $row['disciplina'] ?></td>
    <td><?= $row['nota'] ?></td>
  </tr>
  <?php } ?>
</table>

<p><a href="../aluno_dashboard.php">Voltar</a></p>
</body>
</html>
