-- Garante que o ambiente esteja limpo, apagando o banco de dados se ele já existir.
DROP DATABASE IF EXISTS biblioteca_bd;
CREATE DATABASE biblioteca_bd;
USE biblioteca_bd;

-- Tabela Usuarios (baseada na entidade Pessoa [cite: 4, 5])
-- Simplifica a especialização de Cliente/Funcionário  usando um campo 'Tipo'.
CREATE TABLE Usuarios(
    CPF VARCHAR(14) PRIMARY KEY,
    Nome VARCHAR(100) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Senha VARCHAR(255) NOT NULL, -- Essencial para um sistema web
    Endereco VARCHAR(255) NOT NULL,
    Telefone VARCHAR(20) NOT NULL,
    Tipo VARCHAR(20) CHECK (Tipo IN ('Cliente', 'Funcionario', 'Admin')) NOT NULL DEFAULT 'Cliente'
);

-- Tabela Funcionarios (especialização de Pessoa/Usuarios [cite: 10, 11])
CREATE TABLE Funcionarios(
    CPF VARCHAR(14) PRIMARY KEY,
    Salario FLOAT NOT NULL,
    Funcao VARCHAR(50) NOT NULL,
    FOREIGN KEY (CPF) REFERENCES Usuarios(CPF) ON DELETE CASCADE
);

-- Tabela Editoras [cite: 13, 16]
CREATE TABLE Editoras(
    ID_Editora INT PRIMARY KEY AUTO_INCREMENT,
    Nome VARCHAR(100) NOT NULL,
    Endereco VARCHAR(200) NOT NULL,
    Telefone VARCHAR(20) NOT NULL
);

-- Tabela Autores [cite: 14, 18]
CREATE TABLE Autores(
    ID_Autor INT PRIMARY KEY AUTO_INCREMENT,
    Nome VARCHAR(100) NOT NULL,
    Data_Nascimento DATE NOT NULL,
    Nacionalidade VARCHAR(50)
);

-- Tabela Livros [cite: 15, 20]
CREATE TABLE Livros(
    ID_Livro INT PRIMARY KEY AUTO_INCREMENT,
    Titulo VARCHAR(100) NOT NULL,
    Data_Publicacao DATE NOT NULL,
    Categoria VARCHAR(100) NOT NULL,
    Estante VARCHAR(10) NOT NULL,
    ID_Editora INT,
    ID_Autor INT,
    Disponivel BOOLEAN DEFAULT TRUE, -- Campo extra útil para controle de aluguel
    FOREIGN KEY (ID_Editora) REFERENCES Editoras(ID_Editora),
    FOREIGN KEY (ID_Autor) REFERENCES Autores(ID_Autor)
);

-- Tabela Aluguel [cite: 22, 23]
CREATE TABLE Aluguel(
    ID_Aluguel INT PRIMARY KEY AUTO_INCREMENT,
    CPF VARCHAR(14) NOT NULL,
    ID_Livro INT NOT NULL,
    Data_Saida DATE NOT NULL,
    Data_Devolucao DATE,
    FOREIGN KEY (CPF) REFERENCES Usuarios(CPF),
    FOREIGN KEY (ID_Livro) REFERENCES Livros(ID_Livro)
);

-- INSERÇÃO DE DADOS (Mínimo 3 tuplas por tabela)
INSERT INTO Usuarios (CPF, Nome, Email, Senha, Endereco, Telefone, Tipo) VALUES
('111.111.111-11', 'Maria Joaquina', 'maria@email.com', '123', 'Rua 1, 10', '(35) 99988-7766', 'Cliente'),
('222.222.222-22', 'Luca Santos', 'luca@email.com', '123', 'Rua 2, 20', '(35) 98866-3344', 'Funcionario'),
('333.333.333-33', 'Celso Junior', 'celso@email.com', '123', 'Rua 3, 30', '(35) 98877-1122', 'Cliente'),
('444.444.444-44', 'Kauan Simão', 'kauan@email.com', '123', 'Rua 4, 40', '(35) 91122-3344', 'Cliente');

INSERT INTO Funcionarios (CPF, Salario, Funcao) VALUES
('222.222.222-22', 3000.00, 'Atendente');

INSERT INTO Editoras (Nome, Endereco, Telefone) VALUES
('Suma', 'Av. Central, 1000', '(31) 99999-8888'),
('Rocco', 'Rua das Flores, 200', '(31) 98888-8888'),
('Companhia das Letras', 'Praça da Liberdade, 300', '(31) 97777-8888');

INSERT INTO Autores (Nome, Data_Nascimento, Nacionalidade) VALUES
('Artur Conan Doyle', '1859-05-22', 'Britânico'),
('J.K. Rowling', '1965-07-31', 'Britânica'),
('Agatha Christie', '1890-09-15', 'Britânica');

INSERT INTO Livros (Titulo, Data_Publicacao, Categoria, Estante, ID_Editora, ID_Autor) VALUES
('Um Estudo em Vermelho', '1887-11-01', 'Mistério', 'A1', 2),
('Harry Potter e a Pedra Filosofal', '1997-06-26', 'Fantasia', 'B2', 1, 2),
('Assassinato no Expresso do Oriente', '1934-01-01', 'Mistério', 'A1', 3, 3),
('O Cão dos Baskervilles', '1902-08-01', 'Mistério', 'A2', 2, 1);

INSERT INTO Aluguel (CPF, ID_Livro, Data_Saida, Data_Devolucao) VALUES
('111.111.111-11', 2, '2025-06-20', NULL),
('333.333.333-33', 3, '2025-05-10', '2025-06-05'),
('444.444.444-44', 1, '2025-07-01', NULL);

-- ===================================================================================
-- REQUISITOS DE CONSULTAS E PROCEDURES
-- ===================================================================================

-- 1. STORED PROCEDURE para registrar um novo aluguel
DELIMITER $$
CREATE PROCEDURE RegistrarAluguel(IN p_CPF VARCHAR(14), IN p_ID_Livro INT)
BEGIN
    DECLARE livro_disponivel BOOLEAN;
    SELECT Disponivel INTO livro_disponivel FROM Livros WHERE ID_Livro = p_ID_Livro;

    IF livro_disponivel = TRUE THEN
        INSERT INTO Aluguel (CPF, ID_Livro, Data_Saida) VALUES (p_CPF, p_ID_Livro, CURDATE());
        UPDATE Livros SET Disponivel = FALSE WHERE ID_Livro = p_ID_Livro;
        SELECT 'Aluguel registrado com sucesso!' AS Resultado;
    ELSE
        SELECT 'Erro: Livro não está disponível para aluguel.' AS Resultado;
    END IF;
END$$
DELIMITER ;

-- 2. AS 4 CONSULTAS OBRIGATÓRIAS

-- CONSULTA 1: Listar todos os livros de um autor específico. (Simples)
SELECT Titulo, Data_Publicacao FROM Livros
WHERE ID_Autor = (SELECT ID_Autor FROM Autores WHERE Nome = 'Agatha Christie');

-- CONSULTA 2: Listar livros e suas respectivas editoras (Usa JOIN)
SELECT
    l.Titulo,
    e.Nome AS Nome_Editora
FROM Livros l
JOIN Editoras e ON l.ID_Editora = e.ID_Editora;

-- CONSULTA 3: Listar todos os aluguéis ativos, mostrando nome do livro, nome do cliente e a data de saída. (Usa 3 tabelas e JOIN)
SELECT
    u.Nome AS Nome_Cliente,
    l.Titulo AS Titulo_Livro,
    al.Data_Saida
FROM Aluguel al
JOIN Usuarios u ON al.CPF = u.CPF
JOIN Livros l ON al.ID_Livro = l.ID_Livro
WHERE al.Data_Devolucao IS NULL;

-- CONSULTA 4: Contar quantos livros existem por categoria. (Usa Agregação)
SELECT
    Categoria,
    COUNT(ID_Livro) AS Quantidade
FROM Livros
GROUP BY Categoria
ORDER BY Quantidade DESC;