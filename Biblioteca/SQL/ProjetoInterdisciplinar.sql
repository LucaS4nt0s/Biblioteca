-- Garante que começaremos do zero, apagando o banco de dados se ele já existir.
DROP DATABASE IF EXISTS biblioteca_bd;

-- Cria o banco de dados novamente
CREATE DATABASE biblioteca_bd;

-- Seleciona o banco de dados para uso
USE biblioteca_bd;

-- Criação das Tabelas (sem alterações aqui)
CREATE TABLE Usuarios(
    CPF VARCHAR(14) PRIMARY KEY,
    Nome VARCHAR(100) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Senha VARCHAR(255) NOT NULL,
    Endereco VARCHAR(255) NOT NULL,
    Telefone VARCHAR(20) NOT NULL,
    Tipo VARCHAR(20) CHECK (Tipo IN ('Cliente', 'Funcionario', 'Admin')) NOT NULL DEFAULT 'Cliente'
);

CREATE TABLE Funcionarios(
    CPF VARCHAR(14) PRIMARY KEY,
    Salario FLOAT NOT NULL,
    Funcao VARCHAR(50) NOT NULL,
    FOREIGN KEY (CPF) REFERENCES Usuarios(CPF) ON DELETE CASCADE
);

CREATE TABLE Editoras(
    ID_Editora INT PRIMARY KEY AUTO_INCREMENT,
    Nome VARCHAR(100) NOT NULL,
    Endereco VARCHAR(200) NOT NULL,
    Telefone VARCHAR(20) NOT NULL
);

CREATE TABLE Autores(
    ID_Autor INT PRIMARY KEY AUTO_INCREMENT,
    Nome VARCHAR(100) NOT NULL,
    Data_Nascimento DATE NOT NULL,
    Nacionalidade VARCHAR(50)
);

CREATE TABLE Livros(
    ID_Livro INT PRIMARY KEY AUTO_INCREMENT,
    Titulo VARCHAR(100) NOT NULL,
    Data_Publicacao DATE NOT NULL,
    Categoria VARCHAR(100) NOT NULL,
    Estante INT NOT NULL,
    ID_Editora INT,
    ID_Autor INT,
    FOREIGN KEY (ID_Editora) REFERENCES Editoras(ID_Editora) ON DELETE CASCADE,
    FOREIGN KEY (ID_Autor) REFERENCES Autores(ID_Autor) ON DELETE CASCADE
);

CREATE TABLE Aluguel(
    ID_Aluguel INT PRIMARY KEY AUTO_INCREMENT,
    CPF VARCHAR(14) NOT NULL,
    ID_Livro INT NOT NULL,
    Data_Saida DATE DEFAULT (CURDATE()),
    Data_Devolucao DATE DEFAULT NULL,
    FOREIGN KEY (CPF) REFERENCES Usuarios(CPF) ON DELETE CASCADE,
    FOREIGN KEY (ID_Livro) REFERENCES Livros(ID_Livro) ON DELETE CASCADE
);

-- Inserção de Dados (sem alterações aqui)
INSERT INTO Usuarios(CPF, Nome, Email, Senha, Endereco, Telefone, Tipo) VALUES 
('000.000.000-00', 'Admin', 'admin@email.com', 'senha123', 'Rua 1', '31999999999', 'Admin'),
('111.111.111-11', 'Luca Samuel', 'luca@email.com', 'senha123', 'Rua 2', '35999998888', 'Funcionario'),
('222.222.111-22', 'Maria Eduarda', 'maria@email.com', 'senha123', 'Rua 3', '35988888888', 'Funcionario'),
('333.333.333-33', 'Celso Oliveira', 'celso@email.com', 'senha123', 'Rua 4', '35977778888', 'Funcionario'),
('444.444.444-44', 'Kauan Simão', 'kauan@email.com', 'senha123', 'Rua 5', '35966668888', 'Cliente'),
('555.555.555-55', 'Carlos Felipe', 'carlos@email.com', 'senha123', 'Rua 6', '35955558888', 'Cliente'),
('666.666.666-66', 'João Guilherme', 'joao@email.com', 'senha123', 'Rua 7', '35944448888', 'Cliente');

INSERT INTO Funcionarios(CPF, Salario, Funcao) VALUES 
('111.111.111-11', 2500.00, 'Gerente'),
('222.222.111-22', 2000.00, 'Atendente'),
('333.333.333-33', 1800.00, 'Estoquista');

INSERT INTO Editoras(Nome, Endereco, Telefone) VALUES 
('Suma', 'Av. Central, 1000', '31999998888'),
('Rocco', 'Rua das Flores, 200', '31988888888'),
('Companhia das Letras', 'Praça da Liberdade, 300', '31977778888');

INSERT INTO Autores(Nome, Data_Nascimento, Nacionalidade) VALUES 
('Stephen King', '1947-09-21', 'Americano'),
('J.K. Rowling', '1965-07-31', 'Britânica'),
('Agatha Christie', '1890-09-15', 'Britânica');

INSERT INTO Livros(Titulo, Data_Publicacao, Categoria, Estante, ID_Editora, ID_Autor) VALUES 
('O Iluminado', '1977-01-28', 'Terror', 1, 1, 1),
('Harry Potter e a Pedra Filosofal', '1997-06-26', 'Fantasia', 2, 2, 2),
('Assassinato no Expresso do Oriente', '1934-01-01', 'Mistério', 3, 3, 3);

INSERT INTO Aluguel(CPF, ID_Livro, Data_Saida) VALUES 
('444.444.444-44', 1, '2023-10-01'),
('555.555.555-55', 2, '2023-10-02'),
('666.666.666-66', 3, '2023-10-03');

-- --- Procedure ---
-- Este bloco ainda pode precisar ser executado separadamente.
DELIMITER //
CREATE PROCEDURE AdicionarCliente(
    IN p_CPF VARCHAR(14),
    IN p_Nome VARCHAR(100),
    IN p_Email VARCHAR(100),
    IN p_Senha VARCHAR(255),
    IN p_Endereco VARCHAR(255),
    IN p_Telefone VARCHAR(20)
)
BEGIN
    INSERT INTO Usuarios(CPF, Nome, Email, Senha, Endereco, Telefone, Tipo) 
    VALUES (p_CPF, p_Nome, p_Email, p_Senha, p_Endereco, p_Telefone, 'Cliente');
END //
DELIMITER ;

-- --- Consultas e Chamada da Procedure ---
CALL AdicionarCliente('777.777.777-77', 'João Victor', 'joao.victor@email.com', 'senha456', 'Rua 8', '35933338888');

SELECT * FROM Usuarios;