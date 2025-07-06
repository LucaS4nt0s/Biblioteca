<?php
session_start();
require_once 'conexao.php';

// Proteção de página: Apenas Funcionários e Admins podem acessar
if (!isset($_SESSION['user_cpf']) || !in_array($_SESSION['user_tipo'], ['Funcionario', 'Admin'])) {
    die("Acesso negado. Você não tem permissão para acessar esta página.");
}

// Lógica para buscar autores e editoras para os dropdowns
$autores = $pdo->query("SELECT ID_Autor, Nome FROM Autores ORDER BY Nome")->fetchAll(PDO::FETCH_ASSOC);
$editoras = $pdo->query("SELECT ID_Editora, Nome FROM Editoras ORDER BY Nome")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... Lógica para inserir o livro no banco (similar ao cadastro de usuário)
    // ... Redirecionar para index.php com mensagem de sucesso
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Livro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Adicionar Novo Livro ao Acervo</h2>
        <form action="adicionar_livro.php" method="POST">
            <input type="text" name="titulo" placeholder="Título do Livro" required>
            <input type="date" name="data_publicacao" required>
            <input type="text" name="categoria" placeholder="Categoria" required>
            <input type="text" name="estante" placeholder="Localização (Estante)" required>
            <select name="id_autor" required>
                <option value="">Selecione o Autor</option>
                <?php foreach ($autores as $autor): ?>
                    <option value="<?= $autor['ID_Autor'] ?>"><?= htmlspecialchars($autor['Nome']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="id_editora" required>
                <option value="">Selecione a Editora</option>
                <?php foreach ($editoras as $editora): ?>
                    <option value="<?= $editora['ID_Editora'] ?>"><?= htmlspecialchars($editora['Nome']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Adicionar Livro</button>
        </form>
    </div>
</body>
</html>