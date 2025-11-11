<?php
ob_start();
session_start();
require_once 'db.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entrar'])) {
    $login_or_email = trim($_POST['login'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!$login_or_email || !$senha) {
        $erro = "Preencha login/email e senha.";
    } else {
        $stmt = $conn->prepare("SELECT id_users, login, name, password_hash, role_id FROM users WHERE (login = ? OR email = ?) AND status = 'ativo' LIMIT 1");
        
        if ($stmt) {
            $stmt->bind_param("ss", $login_or_email, $login_or_email);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res && $res->num_rows === 1) {
                $row = $res->fetch_assoc();
                if (password_verify($senha, $row['password_hash'])) {
                    session_regenerate_id(true);
                    $_SESSION['usuario_id']    = $row['id_users'];
                    $_SESSION['usuario_login'] = $row['login'];
                    $_SESSION['usuario_nome']  = $row['name'];
                    $_SESSION['role_id']       = (int)$row['role_id'];

                    // Define o tipo de usuário e redireciona para o painel correto
                    switch ($_SESSION['role_id']) {
                        case 1: $_SESSION['usuario_tipo'] = 'admin'; break;
                        case 2: $_SESSION['usuario_tipo'] = 'professor'; break;
                        case 3: $_SESSION['usuario_tipo'] = 'aluno'; break;
                        case 4: $_SESSION['usuario_tipo'] = 'coordenacao'; break;
                        default: $_SESSION['usuario_tipo'] = 'usuario';
                    }

                    $redirect = 'index.php'; // Fallback
                    if ($_SESSION['usuario_tipo'] === 'admin') $redirect = 'admin_dashboard.php';
                    if ($_SESSION['usuario_tipo'] === 'aluno') $redirect = 'aluno_dashboard.php';
                    if ($_SESSION['usuario_tipo'] === 'professor') $redirect = 'professor_dashboard.php';
                    if ($_SESSION['usuario_tipo'] === 'coordenacao') $redirect = 'coordenacao_dashboard.php';

                    header("Location: $redirect");
                    exit;
                }
            }
            $erro = "Login ou senha incorretos.";
            $stmt->close();
        } else {
            $erro = "Erro no servidor. Tente novamente.";
        }
    }
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Academo — Login</title>
<style>
    /* --- ESTILOS GERAIS --- */
    body, html { margin:0; padding:0; font-family: Arial, sans-serif; height:100%; transition: background-color 0.3s; }
    body { display:flex; height:100vh; overflow:hidden; }
    .left-img { width:50%; background-color:#fff; display:flex; align-items:center; justify-content:center; transition: background-color 0.3s; }
    .left-img img { max-width:80%; height:auto; transition: filter 0.3s; }
    .right-panel { width:50%; background-color:#1d888b; display:flex; align-items:center; justify-content:center; position:relative; transition: background-color 0.3s; }
    .panel-box { background:#fff; padding:40px; border-radius:16px; width:80%; text-align:center; box-shadow:0 6px 16px rgba(0,0,0,0.25); transition: background-color 0.3s, box-shadow 0.3s; }
    .panel-box h1 { color:#1d888b; margin-bottom:20px; transition: color 0.3s; }
    .err{color:#a00;margin-bottom:12px; text-align: left;}
    label{display:block;margin:8px 0 4px; text-align: left; font-weight: bold; color: #555; transition: color 0.3s;}
    input{width:100%;padding:10px;margin-bottom:12px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; transition: background-color 0.3s, border-color 0.3s, color 0.3s;}
    .btn { width: 100%; background:#1d888b; color:#fff; border:none; padding:14px; border-radius:8px; font-size:18px; font-weight:bold; cursor:pointer; text-decoration:none; display:block; margin:20px 0 10px; transition: background 0.3s; }
    .btn:hover { background:#166d6f; }
    .links a { color: #1d888b; text-decoration: none; }
    @media (max-width:900px) { body { flex-direction:column; } .left-img,.right-panel { width:100%; height:50vh; } .panel-box { width:90%; padding:30px; } }

    /* --- BOTÃO DARK MODE --- */
    .dark-mode-button {
        position: fixed; top: 20px; right: 20px; background-color: #fff; color: #333; border: 1px solid #ddd; border-radius: 50%; width: 45px; height: 45px; font-size: 20px; cursor: pointer; z-index: 1000; transition: background-color 0.3s, color 0.3s, transform 0.3s;
    }
    .dark-mode-button:hover { background-color: #f0f0f0; transform: scale(1.1); }
    body.dark-mode .dark-mode-button { background-color: #2c2c2c; color: #f1f1f1; border-color: #555; }

    /* --- ESTILOS DO MODO ESCURO --- */
    body.dark-mode .left-img { background-color: #121212; }
    body.dark-mode .left-img img { filter: invert(1) brightness(1.5); }
    body.dark-mode .right-panel { background-color: #1e1e1e; }
    body.dark-mode .panel-box { background-color: #2c2c2c; box-shadow: none; }
    body.dark-mode .panel-box h1,
    body.dark-mode .panel-box label { color: #ffffff; }
    body.dark-mode .panel-box input { background-color: #3c3c3c; border: 1px solid #555; color: #f1f1f1; }
    body.dark-mode .links a { color: #4dbfbf; }
</style>
</head>
<body>

<button id="darkModeToggle" class="dark-mode-button">🌙</button>

<div class="left-img">
    <img src="assets/Academo.jpeg" alt="Academo Logo">
</div>
<div class="right-panel">
    <div class="panel-box">
        <h1>Acesse o Sistema</h1>
        <?php if ($erro): ?>
            <div class="err"><?=htmlspecialchars($erro)?></div>
        <?php endif; ?>

        <form method="post" action="index.php">
            <label for="login">Login ou Email</label>
            <input id="login" name="login" required value="<?=isset($login_or_email)?htmlspecialchars($login_or_email):''?>">
            
            <label for="senha">Senha</label>
            <input id="senha" name="senha" type="password" required>
            
            <button type="submit" name="entrar" class="btn">Entrar</button>
        </form>
        
        <div class="links">
            <p><a href="esqueci_senha.php">Esqueci minha senha</a></p>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const body = document.body;
    const enableDarkMode = () => { body.classList.add('dark-mode'); darkModeToggle.textContent = '☀️'; localStorage.setItem('darkMode', 'enabled'); };
    const disableDarkMode = () => { body.classList.remove('dark-mode'); darkModeToggle.textContent = '🌙'; localStorage.setItem('darkMode', null); };
    if (localStorage.getItem('darkMode') === 'enabled') { enableDarkMode(); }
    darkModeToggle.addEventListener('click', () => { if (localStorage.getItem('darkMode') !== 'enabled') { enableDarkMode(); } else { disableDarkMode(); } });
});
</script>
</body>
</html>