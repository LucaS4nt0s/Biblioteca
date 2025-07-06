<?php
session_start();
require_once 'conexao.php';

// Proteção de página: Apenas Funcionários e Admins podem acessar
if (!isset($_SESSION['user_cpf']) || !in_array($_SESSION['user_tipo'], ['Funcionario', 'Admin'])) {
    die("Acesso negado. Você não tem permissão para acessar esta página.");
}

$errors = [];

// Lógica para processar o formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta dos dados do formulário do livro
    $titulo = trim($_POST['titulo']);
    $data_publicacao = trim($_POST['data_publicacao']);
    $categoria = trim($_POST['categoria']);
    $estante = trim($_POST['estante']);
    
    // Coleta dos dados do autor
    $id_autor_selecionado = $_POST['id_autor'];
    $novo_autor_nome = trim($_POST['novo_autor_nome']);
    $novo_autor_nascimento = trim($_POST['novo_autor_nascimento']);
    $novo_autor_nacionalidade = trim($_POST['novo_autor_nacionalidade']);

    // Coleta dos dados da editora
    $id_editora_selecionada = $_POST['id_editora'];
    $nova_editora_nome = trim($_POST['nova_editora_nome']);
    $nova_editora_endereco = trim($_POST['nova_editora_endereco']);
    $nova_editora_telefone = trim($_POST['nova_editora_telefone']);

    $id_autor_final = null;
    $id_editora_final = null;

    try {
        // --- LÓGICA DO AUTOR ---
        if (!empty($novo_autor_nome)) {
            // Se um novo autor foi digitado, insere ele primeiro
            if(empty($novo_autor_nascimento) || empty($novo_autor_nacionalidade)) {
                $errors[] = "Para cadastrar um novo autor, o nome, data de nascimento e nacionalidade são obrigatórios.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO Autores (Nome, Data_Nascimento, Nacionalidade) VALUES (?, ?, ?)");
                $stmt->execute([$novo_autor_nome, $novo_autor_nascimento, $novo_autor_nacionalidade]);
                $id_autor_final = $pdo->lastInsertId(); // Pega o ID do autor que acabamos de criar
            }
        } else {
            // Se não, usa o autor selecionado no dropdown
            $id_autor_final = $id_autor_selecionado;
        }

        // --- LÓGICA DA EDITORA ---
        if (!empty($nova_editora_nome)) {
            // Se uma nova editora foi digitada, insere ela
             if(empty($nova_editora_endereco) || empty($nova_editora_telefone)) {
                $errors[] = "Para cadastrar uma nova editora, o nome, endereço e telefone são obrigatórios.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO Editoras (Nome, Endereco, Telefone) VALUES (?, ?, ?)");
                $stmt->execute([$nova_editora_nome, $nova_editora_endereco, $nova_editora_telefone]);
                $id_editora_final = $pdo->lastInsertId(); // Pega o ID da editora que acabamos de criar
            }
        } else {
            // Se não, usa a editora selecionada no dropdown
            $id_editora_final = $id_editora_selecionada;
        }

        // --- INSERÇÃO DO LIVRO ---
        if (empty($errors) && !empty($id_autor_final) && !empty($id_editora_final)) {
            $sql = "INSERT INTO Livros (Titulo, Data_Publicacao, Categoria, Estante, ID_Editora, ID_Autor) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $data_publicacao, $categoria, $estante, $id_editora_final, $id_autor_final]);

            header("Location: index.php?status=success&msg=Livro adicionado com sucesso!");
            exit();
        } else {
             if(empty($id_autor_final)) $errors[] = "Você deve selecionar ou cadastrar um autor.";
             if(empty($id_editora_final)) $errors[] = "Você deve selecionar ou cadastrar uma editora.";
        }

    } catch (PDOException $e) {
        $errors[] = "Erro de banco de dados: " . $e->getMessage();
    }
}

// Lógica para buscar autores e editoras existentes para os dropdowns
$autores = $pdo->query("SELECT ID_Autor, Nome FROM Autores ORDER BY Nome")->fetchAll(PDO::FETCH_ASSOC);
$editoras = $pdo->query("SELECT ID_Editora, Nome FROM Editoras ORDER BY Nome")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Livro - Biblioteca Central</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Adicionar Novo Livro ao Acervo</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?><p><?= htmlspecialchars($error) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="adicionar_livro.php" method="POST" class="book-form">
            <fieldset>
                <legend>Informações do Livro</legend>
                <input type="text" name="titulo" placeholder="Título do Livro" required>
                <label for="data_publicacao">Data de Publicação:</label>
                <input type="date" id="data_publicacao" name="data_publicacao" required>
                <input type="text" name="categoria" placeholder="Categoria" required>
                <input type="text" name="estante" placeholder="Localização (Estante)" required>
            </fieldset>

            <fieldset>
                <legend>Autor</legend>
                <select name="id_autor">
                    <option value="">-- Selecione um Autor Existente --</option>
                    <?php foreach ($autores as $autor): ?>
                        <option value="<?= $autor['ID_Autor'] ?>"><?= htmlspecialchars($autor['Nome']) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="or-divider">OU</p>
                <input type="text" name="novo_autor_nome" placeholder="Nome do Novo Autor">
                <input type="text" name="novo_autor_nacionalidade" placeholder="Nacionalidade do Novo Autor">
                <label for="novo_autor_nascimento">Data de Nascimento do Novo Autor:</label>
                <input type="date" id="novo_autor_nascimento" name="novo_autor_nascimento">
            </fieldset>

            <fieldset>
                <legend>Editora</legend>
                <select name="id_editora">
                    <option value="">-- Selecione uma Editora Existente --</option>
                    <?php foreach ($editoras as $editora): ?>
                        <option value="<?= $editora['ID_Editora'] ?>"><?= htmlspecialchars($editora['Nome']) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="or-divider">OU</p>
                <input type="text" name="nova_editora_nome" placeholder="Nome da Nova Editora">
                <input type="text" name="nova_editora_endereco" placeholder="Endereço da Nova Editora">
                <input type="text" name="nova_editora_telefone" placeholder="Telefone da Nova Editora">
            </fieldset>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-add">Adicionar Livro</button>
                <a href="index.php" class="btn btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>