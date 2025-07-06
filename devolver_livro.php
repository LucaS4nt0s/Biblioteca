<?php
session_start();
require_once 'conexao.php';

// PASSO 1: SEGURANÇA - Verifica se o usuário está logado e tem permissão
if (!isset($_SESSION['user_cpf']) || !in_array($_SESSION['user_tipo'], ['Funcionario', 'Admin'])) {
    // Se não tiver permissão, encerra o script com uma mensagem de erro.
    die("Acesso negado. Você não tem permissão para realizar esta operação.");
}

// PASSO 2: VALIDAÇÃO - Verifica se o ID do livro foi enviado e é um número
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?status=error&msg=ID do livro inválido.");
    exit();
}

$id_livro = $_GET['id'];

// PASSO 3: LÓGICA DE DEVOLUÇÃO
try {
    // Atualiza o registro de aluguel ATIVO (onde a data de devolução é NULA)
    // para registrar a data de hoje como a data de devolução.
    $sql = "UPDATE Aluguel SET Data_Devolucao = CURDATE() WHERE ID_Livro = ? AND Data_Devolucao IS NULL";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_livro]);

    // Verifica se alguma linha foi realmente atualizada
    if ($stmt->rowCount() > 0) {
        // Se a atualização funcionou, redireciona com mensagem de sucesso.
        // O TRIGGER no banco de dados cuidará de mudar o status do livro para 'Disponível'.
        header("Location: index.php?status=success&msg=Livro devolvido com sucesso!");
    } else {
        // Se nenhuma linha foi afetada, significa que não havia um aluguel ativo para este livro.
        header("Location: index.php?status=error&msg=Não foi possível encontrar um aluguel ativo para este livro.");
    }

} catch (PDOException $e) {
    // Em caso de erro no banco, redireciona com mensagem genérica.
    header("Location: index.php?status=error&msg=Ocorreu um erro ao processar a devolução.");
}
exit();
?>