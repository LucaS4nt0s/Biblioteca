<?php
session_start(); // Inicia a sessão NO TOPO do arquivo

// Se o usuário já estiver logado, redireciona para a página principal
if (isset($_SESSION['user_cpf'])) {
    header("Location: index.php");
    exit();
}

require_once 'conexao.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (empty($email) || empty($senha)) {
        $error = "E-mail e senha são obrigatórios.";
    } else {
        $sql = "SELECT CPF, Nome, Senha, Tipo FROM Usuarios WHERE Email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Verifica se a senha fornecida corresponde ao hash no banco
            if (password_verify($senha, $user['Senha'])) {
                // Senha correta, cria a sessão
                $_SESSION['user_cpf'] = $user['CPF'];
                $_SESSION['user_nome'] = $user['Nome'];
                $_SESSION['user_tipo'] = $user['Tipo'];
                header("Location: index.php");
                exit();
            } else {
                $error = "E-mail ou senha inválidos.";
            }
        } else {
            $error = "E-mail ou senha inválidos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Biblioteca Central</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        <p>Bem-vindo de volta! Acesse sua conta.</p>

        <?php if (!empty($error)): ?>
            <div class="error-messages">
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="success-message">
                <p>Cadastro realizado com sucesso! Faça seu login.</p>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
        <div class="form-link">
            <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se aqui</a>.</p>
        </div>
    </div>
</body>
</html>