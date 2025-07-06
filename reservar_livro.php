<?php
session_start();
require_once 'conexao.php';

// Ativa a exibição de todos os erros do PHP para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Validação de login e ID do livro
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
    // Inicia uma transação para garantir que ambas as operações funcionem ou nenhuma delas
    $pdo->beginTransaction();

    // 1. Verifica se o livro está realmente disponível
    $stmt_check = $pdo->prepare("SELECT Disponivel FROM Livros WHERE ID_Livro = ?");
    $stmt_check->execute([$id_livro]);
    $livro = $stmt_check->fetch();

    if ($livro && $livro['Disponivel']) {
        // 2. Insere o registro na tabela de aluguel.
        // O Trigger 'tg_after_insert_aluguel' que criamos no banco de dados
        // irá cuidar automaticamente de atualizar a tabela Livros, marcando-o como indisponível.
        $stmt_insert = $pdo->prepare("INSERT INTO Aluguel (CPF, ID_Livro, Data_Saida) VALUES (?, ?, CURDATE())");
        $stmt_insert->execute([$cpf_usuario, $id_livro]);
        
        // Se o insert funcionou, confirma a transação
        $pdo->commit();
        header("Location: index.php?status=success&msg=Livro reservado com sucesso!");

    } else {
        // Se o livro já não estava disponível, desfaz a transação
        $pdo->rollBack();
        header("Location: index.php?status=error&msg=Este livro não está mais disponível.");
    }
} catch (PDOException $e) {
    // Se ocorrer qualquer erro no banco de dados, desfaz a transação
    $pdo->rollBack();
    
    // --- MUDANÇA PARA DEBUG ---
    // Em vez de redirecionar com uma mensagem genérica, vamos exibir o erro real na tela.
    echo "<h1>Erro Crítico no Banco de Dados!</h1>";
    echo "<p>Ocorreu um erro que impediu a reserva. Por favor, copie a mensagem de erro detalhada abaixo e me envie:</p>";
    echo "<hr>";
    echo "<pre>";
    echo "<strong>Mensagem do Erro:</strong> " . $e->getMessage() . "\n\n";
    echo "<strong>Código do Erro:</strong> " . $e->getCode() . "\n";
    echo "<strong>Arquivo:</strong> " . $e->getFile() . " (Linha: " . $e->getLine() . ")\n";
    echo "<strong>Rastro do Erro (Trace):</strong>\n" . $e->getTraceAsString();
    echo "</pre>";
    die(); // Para o script aqui para que possamos ver o erro
}
exit();
?>