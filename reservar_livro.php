<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_cpf'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?status=error&msg=ID do livro inválido.");
    exit();
}

$id_livro = $_GET['id'];
$cpf_usuario = $_SESSION['user_cpf'];

try {
    // Usar transação para garantir a integridade dos dados
    $pdo->beginTransaction();

    // 1. Verifica se o livro está realmente disponível e o bloqueia para atualização
    $stmt = $pdo->prepare("SELECT Disponivel FROM Livros WHERE ID_Livro = ? FOR UPDATE");
    $stmt->execute([$id_livro]);
    $livro = $stmt->fetch();

    if ($livro && $livro['Disponivel']) {
        // 2. Marca o livro como indisponível
        $stmt_update = $pdo->prepare("UPDATE Livros SET Disponivel = FALSE WHERE ID_Livro = ?");
        $stmt_update->execute([$id_livro]);

        // 3. Insere o registro na tabela de aluguel
        $stmt_insert = $pdo->prepare("INSERT INTO Aluguel (CPF, ID_Livro, Data_Saida) VALUES (?, ?, CURDATE())");
        $stmt_insert->execute([$cpf_usuario, $id_livro]);
        
        // Se tudo deu certo, confirma a transação
        $pdo->commit();
        header("Location: index.php?status=success&msg=Livro reservado com sucesso!");
    } else {
        // Se o livro já não estava disponível, desfaz a transação
        $pdo->rollBack();
        header("Location: index.php?status=error&msg=Este livro não está mais disponível.");
    }
} catch (PDOException $e) {
    $pdo->rollBack();
    // Em um sistema real, você logaria o erro em vez de exibi-lo
    header("Location: index.php?status=error&msg=Ocorreu um erro ao processar sua reserva.");
}
exit();
?>