<?php
session_start();
require_once 'conexao.php';

// PASSO 1: SEGURANÇA - Apenas Admins podem editar
if (!isset($_SESSION['user_cpf']) || $_SESSION['user_tipo'] !== 'Admin') {
    die("Acesso negado. Apenas administradores podem editar livros.");
}

$errors = [];

// PASSO 2: LÓGICA DE ATUALIZAÇÃO (QUANDO O FORMULÁRIO É ENVIADO)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta dos dados do livro
    $id_livro = $_POST['id_livro'];
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
            if(empty($novo_autor_nascimento) || empty($novo_autor_nacionalidade)) {
                $errors[] = "Para cadastrar um novo autor, o nome, data de nascimento e nacionalidade são obrigatórios.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO Autores (Nome, Data_Nascimento, Nacionalidade) VALUES (?, ?, ?)");
                $stmt->execute([$novo_autor_nome, $novo_autor_nascimento, $novo_autor_nacionalidade]);
                $id_autor_final = $pdo->lastInsertId();
            }
        } else {
            $id_autor_final = $id_autor_selecionado;
        }

        // --- LÓGICA DA EDITORA ---
        if (!empty($nova_editora_nome)) {
             if(empty($nova_editora_endereco) || empty($nova_editora_telefone)) {
                $errors[] = "Para cadastrar uma nova editora, o nome, endereço e telefone são obrigatórios.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO Editoras (Nome, Endereco, Telefone) VALUES (?, ?, ?)");
                $stmt->execute([$nova_editora_nome, $nova_editora_endereco, $nova_editora_telefone]);
                $id_editora_final = $pdo->lastInsertId();
            }
        } else {
            $id_editora_final = $id_editora_selecionada;
        }

        // --- ATUALIZAÇÃO DO LIVRO ---
        if (empty($errors) && !empty($id_autor_final) && !empty($id_editora_final)) {
            $sql = "UPDATE Livros SET 
                        Titulo = ?, Data_Publicacao = ?, Categoria = ?, 
                        Estante = ?, ID_Editora = ?, ID_Autor = ? 
                    WHERE ID_Livro = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $data_publicacao, $categoria, $estante, $id_editora_final, $id_autor_final, $id_livro]);

            header("Location: index.php?status=success&msg=Livro atualizado com sucesso!");
            exit();
        } else {
             if(empty($id_autor_final)) $errors[] = "Você deve selecionar ou cadastrar um autor.";
             if(empty($id_editora_final)) $errors[] = "Você deve selecionar ou cadastrar uma editora.";
        }

    } catch (PDOException $e) {
        $errors[] = "Erro de banco de dados: " . $e->getMessage();
    }
}

// PASSO 3: LÓGICA PARA BUSCAR DADOS (QUANDO A PÁGINA É CARREGADA)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?status=error&msg=ID do livro inválido.");
    exit();
}
$id_livro = $_GET['id'];

// Busca os dados do livro específico que será editado
$stmt = $pdo->prepare("SELECT * FROM Livros WHERE ID_Livro = ?");
$stmt->execute([$id_livro]);
$livro = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$livro) {
    header("Location: index.php?status=error&msg=Livro não encontrado.");
    exit();
}

// Busca todos os autores e editoras para preencher os dropdowns
$autores = $pdo->query("SELECT ID_Autor, Nome FROM Autores ORDER BY Nome")->fetchAll(PDO::FETCH_ASSOC);
$editoras = $pdo->query("SELECT ID_Editora, Nome FROM Editoras ORDER BY Nome")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Livro - Biblioteca Central</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Editar Livro: <?= htmlspecialchars($livro['Titulo']) ?></h2>

        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?><p><?= htmlspecialchars($error) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="editar_livro.php" method="POST" class="book-form">
            <input type="hidden" name="id_livro" value="<?= $livro['ID_Livro'] ?>">

            <fieldset>
                <legend>Informações do Livro</legend>
                <label for="titulo">Título do Livro:</label>
                <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($livro['Titulo']) ?>" required>
                
                <label for="data_publicacao">Data de Publicação:</label>
                <input type="date" id="data_publicacao" name="data_publicacao" value="<?= htmlspecialchars($livro['Data_Publicacao']) ?>" required>
                
                <label for="categoria">Categoria:</label>
                <input type="text" id="categoria" name="categoria" value="<?= htmlspecialchars($livro['Categoria']) ?>" required>
                
                <label for="estante">Localização (Estante):</label>
                <input type="text" id="estante" name="estante" value="<?= htmlspecialchars($livro['Estante']) ?>" required>
            </fieldset>

            <fieldset>
                <legend>Autor</legend>
                <select name="id_autor">
                    <option value="">-- Selecione um Autor Existente --</option>
                    <?php foreach ($autores as $autor): ?>
                        <option value="<?= $autor['ID_Autor'] ?>" <?= ($autor['ID_Autor'] == $livro['ID_Autor']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($autor['Nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="or-divider">OU CADASTRE UM NOVO</p>
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
                        <option value="<?= $editora['ID_Editora'] ?>" <?= ($editora['ID_Editora'] == $livro['ID_Editora']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($editora['Nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="or-divider">OU CADASTRE UMA NOVA</p>
                <input type="text" name="nova_editora_nome" placeholder="Nome da Nova Editora">
                <input type="text" name="nova_editora_endereco" placeholder="Endereço da Nova Editora">
                <input type="text" name="nova_editora_telefone" placeholder="Telefone da Nova Editora">
            </fieldset>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-add">Salvar Alterações</button>
                <a href="index.php" class="btn btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>