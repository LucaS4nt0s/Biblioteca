<?php

function navBar()
{
    echo '<nav class="navbar">';
    echo '<ul>';
    echo '<li><a href="index.php">Início</a></li>';
    echo '<li><a href="livros.php">Livros</a></li>';
    echo '<li><a href="autores.php">Autores</a></li>';
    echo '<li><a href="editoras.php">Editoras</a></li>';
    echo '<li><a href="cadastro.php">Cadastro</a></li>';
    echo '</ul>';
    echo '</nav>';
};
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Biblioteca'; ?></title>
    </head>
<body>
    <?php navBar(); // A navegação é parte do header visual ?>