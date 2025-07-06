<?php
session_start();
require_once '../config/database.php';

// Função para fazer login
function login($cpf, $senha, $tipo) {
    $pdo = conectarBanco();
    
    // Verificar se a pessoa existe
    $stmt = $pdo->prepare("SELECT * FROM pessoa WHERE cpf = ? AND tipo = ?");
    $stmt->execute([$cpf, $tipo]);
    $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pessoa) {
        return false;
    }
    
    // Verificar senha baseado no tipo
    $senhaCorreta = false;
    $dadosUsuario = null;
    
    switch ($tipo) {
        case 'cliente':
            $stmt = $pdo->prepare("SELECT * FROM cliente WHERE cpf = ?");
            $stmt->execute([$cpf]);
            $dadosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dadosUsuario && password_verify($senha, $dadosUsuario['senha'])) {
                $senhaCorreta = true;
            }
            break;
            
        case 'funcionario':
            $stmt = $pdo->prepare("SELECT * FROM funcionario WHERE cpf = ?");
            $stmt->execute([$cpf]);
            $dadosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dadosUsuario && password_verify($senha, $dadosUsuario['senha'])) {
                $senhaCorreta = true;
            }
            break;
            
        case 'admin':
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE cpf = ?");
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
        $_SESSION['user_dados'] = $dadosUsuario;
        return true;
    }
    
    return false;
}

// Função para logout
function logout() {
    session_destroy();
    header('Location: ../index.php');
    exit;
}

// Função para verificar se está logado
function isLoggedIn() {
    return isset($_SESSION['user_cpf']);
}

// Função para verificar tipo de usuário
function isAdmin() {
    return isset($_SESSION['user_tipo']) && $_SESSION['user_tipo'] === 'admin';
}

function isFuncionario() {
    return isset($_SESSION['user_tipo']) && $_SESSION['user_tipo'] === 'funcionario';
}

function isCliente() {
    return isset($_SESSION['user_tipo']) && $_SESSION['user_tipo'] === 'cliente';
}

// Função para proteger páginas
function protegerPagina($tiposPermitidos = []) {
    if (!isLoggedIn()) {
        header('Location: ../index.php?erro=acesso_negado');
        exit;
    }
    
    if (!empty($tiposPermitidos) && !in_array($_SESSION['user_tipo'], $tiposPermitidos)) {
        header('Location: ../index.php?erro=permissao_negada');
        exit;
    }
}

// Função para registrar cliente
function registrarCliente($cpf, $nome, $telefone, $endereco, $email, $senha) {
    $pdo = conectarBanco();
    
    try {
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
        return true;
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
}
?>

