<?php

function includeHeader() {
    echo '<header>';
        echo '<nav>';
            echo '<ul class="main-menu">';
                echo '<li><a href="index.php">Início</a></li>';
                echo '<li><a href="livros.php">Livros</a></li>';
                echo '<li><a href="autores.php">Autores</a></li>';
                echo '<li><a href="editoras.php">Editoras</a></li>';
            echo '</ul>';
            echo '<ul class="user-menu">';
                echo '<li><a href="php/cadastro.php">Cadastrar</a></li>';
                echo '<li><a href="php/login.php">Login</a></li>';
                echo '<li><a href="php/logout.php">Sair</a></li>';
            echo '</ul>';
        echo '</nav>';
    echo '</header>';
};

includeHeader();
?>