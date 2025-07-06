<?php
session_start();
require_once 'config/database.php';

// Se já estiver logado, redirecionar para dashboard apropriado
if (isset($_SESSION['user_cpf'])) {
    $tipo = $_SESSION['user_tipo'];
    switch ($tipo) {
        case 'admin':
            header('Location: admin/dashboard.php');
            break;
        case 'funcionario':
            header('Location: funcionario/dashboard.php');
            break;
        case 'cliente':
            header('Location: cliente/dashboard.php');
            break;
    }
    exit;
}

$erro = '';
$sucesso = '';

// Processar login
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'login') {
    $cpf = preg_replace('/\D/', '', $_POST['cpf']);
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];
    
    if (empty($cpf) || empty($senha) || empty($tipo)) {
        $erro = 'Todos os campos são obrigatórios';
    } else {
        $pdo = conectarBanco();
        
        // Verificar se a pessoa existe
        $stmt = $pdo->prepare("SELECT * FROM pessoa WHERE cpf = ? AND tipo = ?");
        $stmt->execute([$cpf, $tipo]);
        $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($pessoa) {
            $senhaCorreta = false;
            
            // Verificar senha baseado no tipo
            switch ($tipo) {
                case 'cliente':
                    $stmt = $pdo->prepare("SELECT senha FROM cliente WHERE cpf = ?");
                    $stmt->execute([$cpf]);
                    $dadosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($dadosUsuario && password_verify($senha, $dadosUsuario['senha'])) {
                        $senhaCorreta = true;
                    }
                    break;
                    
                case 'funcionario':
                    $stmt = $pdo->prepare("SELECT senha FROM funcionario WHERE cpf = ?");
                    $stmt->execute([$cpf]);
                    $dadosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($dadosUsuario && password_verify($senha, $dadosUsuario['senha'])) {
                        $senhaCorreta = true;
                    }
                    break;
                    
                case 'admin':
                    $stmt = $pdo->prepare("SELECT senha FROM admin WHERE cpf = ?");
                    $stmt->execute([$cpf]);
                    $dadosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($dadosUsuario && password_verify($senha, $dadosUsuario['senha'])) {
                        $senhaCorreta = true;
                    }
                    break;
            }
            
            if ($senhaCorreta) {
                $_SESSION['user_cpf'] = $cpf;
                $_SESSION['user_tipo'] = $tipo;
                $_SESSION['user_nome'] = $pessoa['nome'];
                
                // Redirecionar para dashboard apropriado
                switch ($tipo) {
                    case 'admin':
                        header('Location: admin/dashboard.php');
                        break;
                    case 'funcionario':
                        header('Location: funcionario/dashboard.php');
                        break;
                    case 'cliente':
                        header('Location: cliente/dashboard.php');
                        break;
                }
                exit;
            } else {
                $erro = 'Senha incorreta';
            }
        } else {
            $erro = 'Usuário não encontrado ou tipo incorreto';
        }
    }
}

// Processar registro de cliente
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'register') {
    $cpf = preg_replace('/\D/', '', $_POST['cpf']);
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    if (empty($cpf) || empty($nome) || empty($email) || empty($senha)) {
        $erro = 'Campos obrigatórios: CPF, Nome, Email e Senha';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'Senhas não coincidem';
    } elseif (strlen($cpf) !== 11) {
        $erro = 'CPF deve ter 11 dígitos';
    } else {
        try {
            $pdo = conectarBanco();
            $pdo->beginTransaction();
            
            // Verificar se CPF já existe
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM pessoa WHERE cpf = ?");
            $stmt->execute([$cpf]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("CPF já cadastrado");
            }
            
            // Verificar se email já existe
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM cliente WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Email já cadastrado");
            }
            
            // Inserir pessoa
            $stmt = $pdo->prepare("INSERT INTO pessoa (cpf, nome, telefone, endereco, tipo) VALUES (?, ?, ?, ?, 'cliente')");
            $stmt->execute([$cpf, $nome, $telefone, $endereco]);
            
            // Inserir cliente
            $stmt = $pdo->prepare("INSERT INTO cliente (cpf, email, senha) VALUES (?, ?, ?)");
            $stmt->execute([$cpf, $email, password_hash($senha, PASSWORD_DEFAULT)]);
            
            $pdo->commit();
            $sucesso = 'Cadastro realizado com sucesso! Faça login para continuar.';
            
        } catch (Exception $e) {
            $pdo->rollback();
            $erro = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Biblioteca</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card fade-in">
            <h1 class="login-title">📚 Biblioteca</h1>
            <p class="login-subtitle">Sistema de Gerenciamento</p>
            
            <?php if ($erro): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>
            
            <?php if ($sucesso): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($sucesso); ?></div>
            <?php endif; ?>
            
            <!-- Tabs para Login e Registro -->
            <div class="tabs mb-3">
                <button class="btn btn-primary" onclick="showTab('login')" id="login-tab">Login</button>
                <button class="btn btn-secondary" onclick="showTab('register')" id="register-tab">Cadastrar</button>
            </div>
            
            <!-- Formulário de Login -->
            <div id="login-form">
                <form method="POST">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="form-group">
                        <label class="form-label">CPF:</label>
                        <input type="text" name="cpf" class="form-input" required maxlength="14" placeholder="000.000.000-00">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Senha:</label>
                        <input type="password" name="senha" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Tipo de Usuário:</label>
                        <select name="tipo" class="form-select" required>
                            <option value="">Selecione...</option>
                            <option value="cliente">Cliente</option>
                            <option value="funcionario">Funcionário</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Entrar</button>
                </form>
            </div>
            
            <!-- Formulário de Registro -->
            <div id="register-form" class="hidden">
                <form method="POST">
                    <input type="hidden" name="action" value="register">
                    
                    <div class="form-group">
                        <label class="form-label">CPF:</label>
                        <input type="text" name="cpf" class="form-input" required maxlength="14" placeholder="000.000.000-00">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Nome Completo:</label>
                        <input type="text" name="nome" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Telefone:</label>
                        <input type="text" name="telefone" class="form-input" placeholder="(00) 00000-0000">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Endereço:</label>
                        <input type="text" name="endereco" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email:</label>
                        <input type="email" name="email" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Senha:</label>
                        <input type="password" name="senha" class="form-input" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Confirmar Senha:</label>
                        <input type="password" name="confirmar_senha" class="form-input" required minlength="6">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Cadastrar</button>
                </form>
            </div>
            
            <div class="mt-4 text-center">
                <small style="color: #666;">
                    <strong>Admin padrão:</strong> CPF: 123.456.789-01, Senha: admin123
                </small>
            </div>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
    <script>
        function showTab(tab) {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const loginTab = document.getElementById('login-tab');
            const registerTab = document.getElementById('register-tab');
            
            if (tab === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                loginTab.className = 'btn btn-primary';
                registerTab.className = 'btn btn-secondary';
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                loginTab.className = 'btn btn-secondary';
                registerTab.className = 'btn btn-primary';
            }
        }
    </script>
</body>
</html>

