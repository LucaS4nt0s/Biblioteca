<?php
// Inicia a sessão para que possamos armazenar que o usuário está logado
session_start();

// Se o usuário já tem uma sessão ativa, redireciona para a página principal
if (isset($_SESSION['user_cpf'])) {
    header("Location: index.php");
    exit();
}

// Inclui o arquivo de conexão com o banco de dados
require_once 'conexao.php';
$error = '';

// Verifica se o formulário foi enviado (método POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    // Validação simples para garantir que os campos não estão vazios
    if (empty($email) || empty($senha)) {
        $error = "E-mail e senha são obrigatórios.";
    } else {
        // Prepara a consulta SQL para buscar o usuário pelo e-mail
        $sql = "SELECT CPF, Nome, Senha, Tipo FROM Usuarios WHERE Email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        // Verifica se encontrou exatamente um usuário
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // A MÁGICA ACONTECE AQUI: Verifica se a senha digitada corresponde à senha criptografada no banco
            if (password_verify($senha, $user['Senha'])) {
                // Senha correta! Cria as variáveis da sessão para "lembrar" do usuário
                $_SESSION['user_cpf'] = $user['CPF'];
                $_SESSION['user_nome'] = $user['Nome'];
                $_SESSION['user_tipo'] = $user['Tipo'];

                // Redireciona para a página principal do sistema
                header("Location: index.php");
                exit();
            } else {
                // Senha incorreta
                $error = "E-mail ou senha inválidos.";
            }
        } else {
            // Nenhum usuário encontrado com o e-mail fornecido
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