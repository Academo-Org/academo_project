<?php
session_start();
include '../db.php';
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'aluno') {
    header("Location: ../index.php");
    exit;
}

$id = $_SESSION['usuario_id'];
$result = mysqli_query($conn, "SELECT * FROM materias WHERE aluno_id='$id'");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Minhas Matérias</title>
</head>
<body>
<h1>Minhas Matérias</h1>
<table border="1" cellpadding="10">
  <tr><th>Matéria</th><th>Professor</th></tr>
  <?php while ($row = mysqli_fetch_assoc($result)) { ?>
  <tr>
    <td><?= $row['nome'] ?></td>
    <td><?= $row['professor'] ?></td>
  </tr>
  <?php } ?>
</table>

<p><a href="../aluno_dashboard.php">Voltar</a></p>
</body>
</html>

