<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root123');
define('DB_NAME', 'biblioteca');

// Função para conectar ao banco
function conectarBanco() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Erro na conexão: " . $e->getMessage());
    }
}

// Função para criar o banco se não existir
function criarBanco() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Criar banco se não existir
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8 COLLATE utf8_general_ci");
        
        return true;
    } catch(PDOException $e) {
        die("Erro ao criar banco: " . $e->getMessage());
    }
}

// Função para criar as tabelas
function criarTabelas() {
    $pdo = conectarBanco();
    
    // Tabela pessoa
    $sql = "CREATE TABLE IF NOT EXISTS pessoa (
        cpf VARCHAR(11) PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        telefone VARCHAR(15),
        endereco VARCHAR(200),
        tipo ENUM('cliente', 'funcionario', 'admin') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    // Tabela cliente
    $sql = "CREATE TABLE IF NOT EXISTS cliente (
        cpf VARCHAR(11) PRIMARY KEY,
        email VARCHAR(120) UNIQUE NOT NULL,
        senha VARCHAR(255) NOT NULL,
        FOREIGN KEY (cpf) REFERENCES pessoa(cpf) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    
    // Tabela funcionario
    $sql = "CREATE TABLE IF NOT EXISTS funcionario (
        cpf VARCHAR(11) PRIMARY KEY,
        salario DECIMAL(10,2),
        funcao VARCHAR(50),
        senha VARCHAR(255) NOT NULL,
        FOREIGN KEY (cpf) REFERENCES pessoa(cpf) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    
    // Tabela admin
    $sql = "CREATE TABLE IF NOT EXISTS admin (
        cpf VARCHAR(11) PRIMARY KEY,
        senha VARCHAR(255) NOT NULL,
        FOREIGN KEY (cpf) REFERENCES pessoa(cpf) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    
    // Tabela editora
    $sql = "CREATE TABLE IF NOT EXISTS editora (
        id_editora INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        endereco VARCHAR(200),
        telefone VARCHAR(15),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    // Tabela autor
    $sql = "CREATE TABLE IF NOT EXISTS autor (
        id_autor INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        data_nascimento DATE,
        nacionalidade VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    // Tabela livro
    $sql = "CREATE TABLE IF NOT EXISTS livro (
        id_livro INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(200) NOT NULL,
        data_publicacao DATE,
        categoria VARCHAR(50),
        estante VARCHAR(20),
        disponivel BOOLEAN DEFAULT TRUE,
        id_editora INT,
        id_autor INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_editora) REFERENCES editora(id_editora),
        FOREIGN KEY (id_autor) REFERENCES autor(id_autor)
    )";
    $pdo->exec($sql);
    
    // Tabela aluguel
    $sql = "CREATE TABLE IF NOT EXISTS aluguel (
        id_aluguel INT AUTO_INCREMENT PRIMARY KEY,
        cpf VARCHAR(11) NOT NULL,
        id_livro INT NOT NULL,
        data_saida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data_devolucao TIMESTAMP NULL,
        devolvido BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (cpf) REFERENCES pessoa(cpf),
        FOREIGN KEY (id_livro) REFERENCES livro(id_livro)
    )";
    $pdo->exec($sql);
    
    return true;
}

// Função para inserir dados iniciais
function inserirDadosIniciais() {
    $pdo = conectarBanco();
    
    // Verificar se já existe admin
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pessoa WHERE tipo = 'admin'");
    $stmt->execute();
    
    if ($stmt->fetchColumn() == 0) {
        // Inserir admin padrão
        $stmt = $pdo->prepare("INSERT INTO pessoa (cpf, nome, tipo) VALUES (?, ?, ?)");
        $stmt->execute(['12345678901', 'Administrador', 'admin']);
        
        $stmt = $pdo->prepare("INSERT INTO admin (cpf, senha) VALUES (?, ?)");
        $stmt->execute(['12345678901', password_hash('admin123', PASSWORD_DEFAULT)]);
        
        // Inserir algumas editoras de exemplo
        $editoras = [
            ['Editora Saraiva', 'São Paulo, SP', '(11) 1234-5678'],
            ['Editora Globo', 'Rio de Janeiro, RJ', '(21) 8765-4321'],
            ['Editora Ática', 'São Paulo, SP', '(11) 5555-5555']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO editora (nome, endereco, telefone) VALUES (?, ?, ?)");
        foreach ($editoras as $editora) {
            $stmt->execute($editora);
        }
        
        // Inserir alguns autores de exemplo
        $autores = [
            ['Machado de Assis', '1839-06-21', 'Brasileira'],
            ['Clarice Lispector', '1920-12-10', 'Brasileira'],
            ['José Saramago', '1922-11-16', 'Portuguesa']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO autor (nome, data_nascimento, nacionalidade) VALUES (?, ?, ?)");
        foreach ($autores as $autor) {
            $stmt->execute($autor);
        }
    }
    
    return true;
}
?>

