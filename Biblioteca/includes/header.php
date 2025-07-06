<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

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