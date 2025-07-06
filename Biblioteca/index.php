<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

include 'includes/header.php';
include 'includes/footer.php';

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca</title>
</head>
<body>
    <?php navBar(); ?>
    <h1>Bem-vindo à Biblioteca</h1>
    <p>Esta é a página inicial do sistema de gerenciamento de biblioteca.</p>
    <p>Você está logado como: <?php echo htmlspecialchars($_SESSION['usuario']); ?></p>
    <?php footer(); ?>
</body>
</html>