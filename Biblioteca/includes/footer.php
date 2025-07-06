<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

function footer()
{
    echo '<footer>';
    echo '<p>&copy; ' . date('Y') . ' Biblioteca. Todos os direitos reservados.</p>';
    echo '</footer>';
};
?>