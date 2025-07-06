
# 📖 Sistema de Gerenciamento de Biblioteca

Este é um projeto web completo para o gerenciamento de uma biblioteca, desenvolvido como parte do projeto interdisciplinar das disciplinas de Banco de Dados I e Programação Web II. O sistema permite o cadastro de usuários, livros, autores, editoras e o controle de aluguéis e devoluções de livros.

## ✨ Funcionalidades Principais

### Gerenciamento de Usuários

  - **Cadastro e Login:** Sistema de autenticação seguro com senhas criptografadas.
  - **Níveis de Acesso:** Hierarquia de permissões com três tipos de usuários:
      - **Cliente:** Pode visualizar o acervo e reservar livros disponíveis.
      - **Funcionário:** Pode realizar todas as ações de um cliente, além de adicionar novos livros e registrar a devolução de livros alugados.
      - **Admin:** Tem controle total sobre o sistema, incluindo editar e excluir livros, e gerenciar os tipos de conta de outros usuários.

### Gerenciamento de Livros e Acervo

  - **Visualização e Busca:** Interface para listar todos os livros, com um campo de busca para filtrar por título, autor ou editora.
  - **CRUD de Livros:**
      - **Adicionar (Create):** Funcionários e Admins podem adicionar novos livros ao acervo.
      - **Editar (Update):** Admins podem editar as informações de um livro existente.
      - **Excluir (Delete):** Admins podem remover um livro do sistema.
  - **Cadastro Dinâmico:** Ao adicionar ou editar um livro, é possível cadastrar um novo autor ou editora diretamente no formulário, sem precisar sair da tela.

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
|-- 📄 adicionar_livro.php   # Formulário para adicionar livros (Admin/Funcionário)
|-- 📄 editar_livro.php      # Formulário para editar livros (Admin)
|-- 📄 excluir_livro.php     # Script para processar a exclusão de livros (Admin)
|-- 📄 reservar_livro.php    # Script para processar a reserva de um livro
|-- 📄 devolver_livro.php    # Script para processar a devolução de um livro (Admin/Funcionário)
|-- 📄 gerenciar_usuarios.php # (Opcional) Painel para o Admin gerenciar usuários
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
      - Escolha o arquivo `database.sql` do projeto e execute a importação. Isso criará todas as tabelas, triggers e inserirá os dados iniciais.

3.  **Configure a Conexão:**

      - Abra o arquivo `conexao.php`.
      - Verifique se os dados de conexão (`$host`, `$db_name`, `$username`, `$password`) correspondem à configuração do seu ambiente. O padrão para XAMPP geralmente está correto.

4.  **Acesse o Projeto:**

      - Inicie os serviços do Apache e MySQL no seu painel XAMPP.
      - Abra o seu navegador e acesse `http://localhost/biblioteca-web/` (ou o nome da pasta onde você salvou o projeto).

## 👨‍💻 Como Utilizar

### Criando a Primeira Conta de Administrador

O sistema não permite o cadastro público de administradores por segurança. Para criar sua primeira conta Admin:

1.  **Cadastre-se normalmente** através da página `cadastro.php`.
2.  **Abra o seu SGBD** (phpMyAdmin, por exemplo) e localize a tabela `usuarios`.
3.  **Encontre o usuário** que você acabou de criar e edite a coluna `Tipo`, alterando o valor de `Cliente` para `Admin`.
4.  Salve a alteração. Agora você pode fazer login com essa conta e terá acesso a todas as funcionalidades administrativas.

### Níveis de Permissão

  - **Faça login como `Admin`** para ver os botões de Adicionar, Editar e Excluir livros.
  - **Crie e faça login como `Funcionario`** para ver os botões de Adicionar e Devolver.
  - **Faça login como `Cliente`** para ver apenas a opção de Reservar livros disponíveis.

## 👥 Autores

Este projeto foi desenvolvido por:

  * Carlos Barbosa [GitHub](https://github.com/c4rlosfb)
  * Celso Junior [GitHub](https://github.com/celsohd21)
  * Kauan Simão [GitHub](https://github.com/MariaEduardaBatt)
  * Luca Samuel [GitHub](https://github.com/LucaS4nt0s)
  * Maria Eduarda [GitHub](https://github.com/MariaEduardaBatt)