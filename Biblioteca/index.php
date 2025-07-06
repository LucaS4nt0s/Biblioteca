<?php

session_start();

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <title>Biblioteca</title>
</head>
<body>
    <?php if(isset($_SESSION['user_id'])) { require_once './includes/headerLogado.php'; } else { require_once './includes/header.php'; } ?>
    <main>
        <h1>Bem-vindo à Biblioteca</h1>
        <p>Esta é a página inicial do sistema de gerenciamento de biblioteca.</p>
        <p>Para utilizar o sistema e editar as informações como editar a seção de livros, autores e categorias você deve estar logado como administrador.</p>
        <p>Para fins de testes utilize o usuário <strong>admin</strong> com a senha <strong>admin123</strong>.</p>
        <p>Você só poderá cadastrar funcionários se estiver logado como administrador.</p>
        <p>Use o menu acima para navegar entre as seções do sistema.</p>
    </main>
    <?php require_once './includes/footer.php'; ?>
</body>
</html>