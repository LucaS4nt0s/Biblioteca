<?php
require_once 'conexao.php';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Coletar e limpar os dados do formulário
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);
    $endereco = trim($_POST['endereco']);
    $telefone = trim($_POST['telefone']);

    // 2. Validação básica
    if (empty($nome) || empty($cpf) || empty($email) || empty($senha) || empty($endereco) || empty($telefone)) {
        $errors[] = "Todos os campos são obrigatórios.";
    }

    // 3. Verificar se CPF ou Email já existem
    $stmt = $pdo->prepare("SELECT CPF FROM Usuarios WHERE CPF = ? OR Email = ?");
    $stmt->execute([$cpf, $email]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "CPF ou E-mail já cadastrado no sistema.";
    }

    // 4. Se não houver erros, prossiga com a inserção
    if (empty($errors)) {
        // Criptografar a senha - NUNCA SALVE SENHAS EM TEXTO PURO!
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO Usuarios (CPF, Nome, Email, Senha, Endereco, Telefone, Tipo) VALUES (?, ?, ?, ?, ?, ?, 'Cliente')";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$cpf, $nome, $email, $senha_hash, $endereco, $telefone]);
            // Redireciona para o login com uma mensagem de sucesso
            header("Location: login.php?status=success");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Erro ao cadastrar usuário: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Biblioteca Central</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Crie sua Conta</h2>
        <p>Junte-se à nossa comunidade de leitores.</p>
        
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="cadastro.php" method="POST">
            <input type="text" name="nome" placeholder="Nome Completo" required>
            <input type="text" name="cpf" placeholder="CPF (xxx.xxx.xxx-xx)" required>
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <input type="text" name="endereco" placeholder="Endereço Completo" required>
            <input type="text" name="telefone" placeholder="Telefone" required>
            <button type="submit">Cadastrar</button>
        </form>
        <div class="form-link">
            <p>Já tem uma conta? <a href="login.php">Faça login aqui</a>.</p>
        </div>
    </div>
</body>
</html>