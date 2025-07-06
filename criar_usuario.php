<?php
session_start();
require_once 'conexao.php';

// SEGURANÇA: Apenas usuários do tipo 'Admin' podem acessar esta página
if (!isset($_SESSION['user_cpf']) || $_SESSION['user_tipo'] !== 'Admin') {
    die("Acesso negado. Apenas administradores podem criar novos usuários.");
}

$errors = [];
$success_msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta dos dados
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);
    $endereco = trim($_POST['endereco']);
    $telefone = trim($_POST['telefone']);
    $tipo = $_POST['tipo']; // Pega o tipo do formulário

    // Validação
    if (!in_array($tipo, ['Funcionario', 'Admin'])) {
        $errors[] = "Tipo de usuário inválido.";
    }
    // ... (outras validações como de campos vazios, CPF/Email duplicado, etc.)

    if (empty($errors)) {
        // Criptografa a senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // Insere o novo usuário no banco com o tipo especificado
        $sql = "INSERT INTO Usuarios (CPF, Nome, Email, Senha, Endereco, Telefone, Tipo) VALUES (?, ?, ?, ?, ?, ?, ?)";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$cpf, $nome, $email, $senha_hash, $endereco, $telefone, $tipo]);
            $success_msg = "Usuário '" . htmlspecialchars($nome) . "' criado com sucesso como " . htmlspecialchars($tipo) . "!";
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) { // Código de erro para entrada duplicada
                $errors[] = "Erro: CPF ou E-mail já existe no sistema.";
            } else {
                $errors[] = "Erro ao cadastrar usuário: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Novo Usuário - Painel Admin</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Criar Novo Usuário</h2>
        <p>Painel exclusivo para administradores.</p>
        
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?><p><?= htmlspecialchars($error) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_msg)): ?>
            <div class="success-message">
                <p><?= htmlspecialchars($success_msg) ?></p>
            </div>
        <?php endif; ?>

        <form action="criar_usuario.php" method="POST">
            <input type="text" name="nome" placeholder="Nome Completo" required>
            <input type="text" name="cpf" placeholder="CPF (xxx.xxx.xxx-xx)" required>
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="senha" placeholder="Senha Provisória" required>
            <input type="text" name="endereco" placeholder="Endereço Completo" required>
            <input type="text" name="telefone" placeholder="Telefone" required>
            
            <label for="tipo" style="text-align: left; margin-top: 10px;">Tipo de Usuário:</label>
            <select name="tipo" id="tipo" required>
                <option value="Funcionario">Funcionário</option>
                <option value="Admin">Admin</option>
            </select>
            
            <button type="submit" style="margin-top: 20px;">Criar Usuário</button>
        </form>
        <div class="form-link">
            <p><a href="index.php">Voltar para o acervo</a></p>
        </div>
    </div>
</body>
</html>