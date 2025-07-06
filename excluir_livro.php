<?php
session_start();
require_once 'conexao.php';

// Proteção de página: Apenas Admins podem realizar esta ação
if (!isset($_SESSION['user_cpf']) || $_SESSION['user_tipo'] !== 'Admin') {
    die("Acesso negado.");
}

// Validação do ID do livro
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?status=error&msg=" . urlencode("ID do livro inválido."));
    exit();
}

$id_livro = $_GET['id'];

try {
    // PASSO 1: VERIFICA SE O LIVRO NÃO ESTÁ ATUALMENTE ALUGADO
    $stmt_check = $pdo->prepare("SELECT Disponivel FROM Livros WHERE ID_Livro = ?");
    $stmt_check->execute([$id_livro]);
    $livro = $stmt_check->fetch();

    if ($livro && $livro['Disponivel'] == TRUE) {
        // Se o livro está disponível (Disponivel = TRUE), podemos "excluí-lo" (desativá-lo)
        
        // PASSO 2: ATUALIZA O STATUS PARA INATIVO (EXCLUSÃO LÓGICA)
        $stmt_update = $pdo->prepare("UPDATE Livros SET Ativo = FALSE WHERE ID_Livro = ?");
        $stmt_update->execute([$id_livro]);
        
        header("Location: index.php?status=success&msg=" . urlencode("Livro removido do acervo com sucesso."));

    } else {
        // Se o livro não está disponível, ele está alugado. Não pode ser excluído.
        header("Location: index.php?status=error&msg=" . urlencode("Ação negada: Não é possível remover um livro que está atualmente alugado."));
    }

} catch (PDOException $e) {
    header("Location: index.php?status=error&msg=" . urlencode("Ocorreu um erro no banco de dados."));
}
exit();
?>