create database biblioteca_bd;

use biblioteca_bd;

create table Usuarios(
CPF int primary key,
Nome varchar(100) not null,
Email varchar(100) not null unique,
Senha varchar(255) not null,
Tipo varchar(20) check (Tipo in ('Cliente', 'Funcionario', 'Admin')) not null default 'Cliente');

create table Funcionarios(
CPF int primary key,
Salario float not null, 
Funcao varchar(50) not null,
foreign key (CPF) references Usuarios(CPF) on delete cascade);

create table Editoras(
ID_Editora int primary key auto_increment,
Nome varchar(100) not null,
Endereco varchar(200) not null,
Telefone varchar(20) not null);

create table Autores(
ID_Autor int primary key auto_increment,
Nome varchar(100) not null,
Data_Nascimento date not null,
Nacionalidade varchar(50));

create table Livros(
ID_Livro int primary key auto_increment,
Titulo varchar(100) not null,
Data_Publicacao date not null,
Categoria varchar(100) not null,
Estante int not null,
ID_Editora int,
ID_Autor int,
foreign key (ID_Editora) references Editoras(ID_Editora) on delete cascade,
foreign key (ID_Autor) references Autores(ID_Autor) on delete cascade);

create table Aluguel(
ID_Aluguel int primary key auto_increment,
CPF varchar(11) not null,
ID_Livro int not null,
Data_Saida date default(current_timestamp()),
Data_Devolucao date default(null),
foreign key (CPF) references Usuarios(CPF) on delete cascade,
foreign key (ID_Livro) references Livros(ID_Livro) on delete cascade);

insert into Usuarios(CPF, Nome, Telefone, Endereco, Tipo) values 
('000.000.000', 'Admin', '31999999999', 'Rua 1', 'Admin'),
('111.111.111', 'Luca Samuel', '35999998888', 'Rua 2', 'Funcionario'),
('222.222.111', 'Maria Eduarda', '35988888888', 'Rua 3', 'Funcionario'),
('333.333.333', 'Celso Oliveira', '35977778888', 'Rua 4', 'Funcionario'),
('444.444.444', 'Kauan Simão', '35966668888', 'Rua 5', 'Cliente'),
('555.555.555', 'Carlos Felipe', '35955558888', 'Rua 6', 'Cliente'),
('666.666.666', 'João Guilherme', '35944448888', 'Rua 7', 'Cliente');

insert into Funcionarios(CPF, Salario, Funcao) values 
('111.111.111', 2500.00, 'Gerente'),
('222.222.111', 2000.00, 'Atendente'),
('333.333.333', 1800.00, 'Estoquista');
=
insert into Editoras(Nome, Endereco, Telefone) values 
('Suma', 'Av. Central, 1000', '31999998888'),
('Rocco', 'Rua das Flores, 200', '31988888888'),
('Companhia das Letras', 'Praça da Liberdade, 300', '31977778888');

insert into Autores(Nome, Data_Nascimento, Nacionalidade) values 
('Stephen King', '1947-09-21', 'Americano'),
('J.K. Rowling', '1990-02-02', 'Britânico'),
('Agatha Christie', '1975-03-03', 'Britânica');

insert into Livros(Titulo, Data_Publicacao, Categoria, Estante, ID_Editora, ID_Autor) values 
('O Iluminado', '1977-01-28', 'Terror', 1, 1, 1),
('Harry Potter e a Pedra Filosofal', '1997-06-26', 'Fantasia', 2, 2, 2),
('Assassinato no Expresso do Oriente', '1934-01-01', 'Mistério', 3, 3, 3);

insert into Aluguel(CPF, ID_Livro, Data_Saida) values 
('444.444.444', 1, '2023-10-01'),
('555.555.555', 2, '2023-10-02'),
('666.666.666', 3, '2023-10-03');

select * from Pessoas;
select * from Clientes;
select * from Funcionarios;
select * from Editoras;
select * from Autores;
select * from Livros;
select * from Aluguel;

-- Consultas para verificar os dados inseridos

-- Selecionar todas as pessoas que alugaram livros
select p.Nome, a.Titulo, al.Data_Saida
from Aluguel al
join Pessoas p on al.CPF = p.CPF
join Livros a on al.ID_Livro = a.ID_Livro;

-- Selecionar todos os livros alugados por um cliente específico
select l.Titulo, al.Data_Saida, al.Data_Devolucao
from Aluguel al
join Livros l on al.ID_Livro = l.ID_Livro
where al.CPF = '444.444.444';

-- Selecionar os livros escritos por um autor específico
select l.Titulo, a.Nome as Autor
from Livros l
join Autores a on l.ID_Autor = a.ID_Autor
where a.Nome = 'Stephen King';

-- Selecionar todos os livros de uma editora específica
select l.Titulo, e.Nome as Editora
from Livros l
join Editoras e on l.ID_Editora = e.ID_Editora
where e.Nome = 'Suma';

-- criar uma procedure para adicionar um novo cliente
delimiter //
create procedure AdicionarCliente(
    in p_CPF varchar(11),
    in p_Nome varchar(100),
    in p_Telefone varchar(15),
    in p_Endereco varchar(255),
    in p_Email varchar(100)
)
begin
    insert into Pessoas(CPF, Nome, Telefone, Endereco, Tipo) values (p_CPF, p_Nome, p_Telefone, p_Endereco, 'Cliente');
    insert into Clientes(CPF, Email) values (p_CPF, p_Email);
end //
delimiter ;

call AdicionarCliente('777.777.777', 'João Victor', '35933338888', 'Rua 8', 'joao.victor@email.com');
