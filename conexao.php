<?php
$host = 'localhost'; // ou o host do seu servidor de BD
$db_name = 'biblioteca_bd';
$username = 'root'; // seu usuário do BD
$password = 'root'; // sua senha do BD

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    // Configura o PDO para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Em produção, não exiba detalhes do erro. Apenas logue.
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}
?>