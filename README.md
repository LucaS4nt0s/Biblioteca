
# 📖 Sistema de Gerenciamento de Biblioteca

Este é um projeto web completo para o gerenciamento de uma biblioteca, desenvolvido como parte do projeto interdisciplinar das disciplinas de Banco de Dados I e Programação Web II. O sistema permite o cadastro de usuários, livros, autores, editoras e o controle de aluguéis e devoluções de livros.

## ✨ Funcionalidades Principais

### Gerenciamento de Usuários

  - **Cadastro e Login:** Sistema de autenticação seguro com senhas criptografadas.
  - **Níveis de Acesso:** Hierarquia de permissões com três tipos de usuários:
      - **Cliente:** Pode visualizar o acervo e reservar livros disponíveis.
      - **Funcionário:** Pode realizar todas as ações de um cliente, além de adicionar novos livros e registrar a devolução de livros alugados.
      - **Admin:** Tem controle total sobre o sistema, incluindo editar e excluir livros, e criar novas contas de Funcionário ou Admin.

### Gerenciamento de Livros e Acervo

  - **Visualização e Busca:** Interface para listar todos os livros, com um campo de busca para filtrar por título, autor ou editora.
  - **CRUD de Livros:**
      - **Adicionar (Create):** Funcionários e Admins podem adicionar novos livros ao acervo.
      - **Editar (Update):** Admins podem editar as informações de um livro existente.
      - **Excluir (Delete):** Admins podem remover um livro do sistema.
  - **Cadastro Dinâmico:** Ao adicionar ou editar um livro, é possível cadastrar um novo autor ou editora diretamente no formulário.

### Sistema de Aluguel

  - **Reservar:** Usuários logados podem reservar um livro que esteja com o status "Disponível".
  - **Devolver:** Funcionários e Admins podem registrar a devolução de um livro, atualizando seu status para "Disponível" novamente.
  - **Consistência de Dados:** O sistema utiliza Triggers no banco de dados para garantir que o status de disponibilidade de um livro seja sempre atualizado automaticamente quando um aluguel ou devolução ocorre.

## 🚀 Tecnologias Utilizadas

  - **Backend:** PHP
  - **Frontend:** HTML5, CSS3
  - **Banco de Dados:** MySQL
  - **Servidor:** Apache (geralmente via XAMPP, WAMP ou MAMP)

## 📂 Estrutura do Projeto

```
/biblioteca-web/
|
|-- 📄 index.php             # Página principal do acervo
|-- 📄 login.php             # Página de login
|-- 📄 cadastro.php          # Página de cadastro de novos clientes
|-- 📄 logout.php            # Script para encerrar a sessão
|-- 📄 criar_usuario.php     # Painel do Admin para criar usuários (Admin/Funcionário)
|-- 📄 adicionar_livro.php     # Formulário para adicionar livros (Admin/Funcionário)
|-- 📄 editar_livro.php      # Formulário para editar livros (Admin)
|-- 📄 excluir_livro.php     # Script para processar a exclusão de livros (Admin)
|-- 📄 reservar_livro.php    # Script para processar a reserva de um livro
|-- 📄 devolver_livro.php    # Script para processar a devolução de um livro (Admin/Funcionário)
|-- 📄 conexao.php           # Script de conexão com o banco de dados
|-- 📄 database.sql          # Script SQL completo para criar o banco, tabelas e triggers
|-- 📂 css/
|   |-- 📄 style.css         # Folha de estilos principal
|
```

## ⚙️ Instalação e Configuração

Siga os passos abaixo para rodar o projeto localmente.

### Pré-requisitos

  - Um ambiente de servidor local como [XAMPP](https://www.apachefriends.org/index.html), WAMP ou MAMP.
  - Uma ferramenta de gerenciamento de banco de dados como phpMyAdmin (incluso no XAMPP) ou MySQL Workbench.

### Passos

1.  **Clone ou baixe o repositório** para a pasta principal do seu servidor web (geralmente `htdocs` no XAMPP).

2.  **Crie o Banco de Dados:**

      - Abra o phpMyAdmin ou sua ferramenta de preferência.
      - Crie um novo banco de dados chamado `biblioteca_bd`.
      - Selecione o banco `biblioteca_bd` e vá para a aba "Importar".
      - Escolha o arquivo `database.sql` do projeto e execute a importação. Isso criará todas as tabelas, triggers e inserirá os dados iniciais, incluindo o administrador padrão.

3.  **Configure a Conexão:**

      - Abra o arquivo `conexao.php`.
      - Verifique se os dados de conexão (`$host`, `$db_name`, `$username`, `$password`) correspondem à configuração do seu ambiente. O padrão para XAMPP geralmente está correto.

4.  **Acesse o Projeto:**

      - Inicie os serviços do Apache e MySQL no seu painel XAMPP.
      - Abra o seu navegador e acesse `http://localhost/biblioteca-web/` (ou o nome da pasta onde você salvou o projeto).

## 👨‍💻 Como Utilizar

Após a instalação, você pode acessar e administrar o sistema.

### Acessando com o Administrador Padrão

Para facilitar o primeiro acesso e o gerenciamento inicial, o sistema já vem com uma conta de administrador pré-configurada.

1.  Certifique-se de que você executou o script `database.sql` corretamente.
2.  Acesse a página de login.
3.  Utilize as seguintes credenciais para entrar:
      - **E-mail:** `admin@biblioteca.com`
      - **Senha:** `admin123`

O script `database.sql` já insere este usuário com a senha `admin123` devidamente criptografada, garantindo o acesso imediato com privilégios de administrador. Uma vez logado, você terá acesso aos botões "Adicionar Novo Livro", "Criar Novo Usuário", "Editar" e "Excluir".

### Criando um Novo Administrador ou Funcionário

Existem duas maneiras de criar novas contas com privilégios:

#### 1\. Pelo Painel do Admin (Recomendado)

  - Faça login com a conta de administrador padrão.
  - Na página inicial, clique no botão **"Criar Novo Usuário"**.
  - Preencha o formulário com os dados do novo usuário e, no campo "Tipo de Usuário", selecione `Admin` ou `Funcionario`.
  - Clique em "Criar Usuário". A conta será criada com o nível de permissão correto.

#### 2\. Manualmente (Alternativo)

Caso precise criar um admin sem usar a interface, siga estes passos:

  - **Passo 1: Cadastre o Usuário pelo Site:** Vá para a página `cadastro.php` e crie a conta normalmente. Ela será criada com o tipo `Cliente` por padrão, mas com a senha já criptografada.
  - **Passo 2: Promova o Usuário no Banco de Dados:** Abra sua ferramenta de banco de dados, localize o usuário recém-criado na tabela `Usuarios` e execute o seguinte comando SQL (substituindo pelo e-mail correto):
    ```sql
    UPDATE Usuarios
    SET Tipo = 'Admin'
    WHERE Email = 'email_do_novo_usuario@exemplo.com';
    ```
  - O usuário agora terá permissões de administrador.

## 👥 Autores

Este projeto foi desenvolvido por:

  * Carlos Barbosa [GitHub](https://github.com/c4rlosfb)
  * Celso Junior [GitHub](https://github.com/celsohd21)
  * Kauan Simão [GitHub](https://github.com/MariaEduardaBatt)
  * Luca Samuel [GitHub](https://github.com/LucaS4nt0s)
  * Maria Eduarda [GitHub](https://github.com/MariaEduardaBatt)
