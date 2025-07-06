<?php
session_start();
require_once 'conexao.php';

// Proteção de página: Apenas Admins podem excluir
if (!isset($_SESSION['user_cpf']) || $_SESSION['user_tipo'] !== 'Admin') {
    die("Acesso negado.");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?status=error&msg=ID inválido.");
    exit();
}

$id_livro = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM Livros WHERE ID_Livro = ?");
    $stmt->execute([$id_livro]);
    header("Location: index.php?status=success&msg=Livro excluído com sucesso.");
} catch (PDOException $e) {
    // Se o livro estiver em um aluguel, a exclusão falhará por causa da chave estrangeira.
    // Isso é bom, pois impede a exclusão de livros alugados.
    header("Location: index.php?status=error&msg=Não foi possível excluir o livro. Verifique se ele não está alugado.");
}
exit();
?>