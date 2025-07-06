<?php
// Inicia a sessão para verificar o login
session_start();

// VERIFICAÇÃO DE LOGIN: Se o usuário não estiver logado, redireciona para a página de login.
if (!isset($_SESSION['user_cpf'])) {
    header("Location: login.php");
    exit(); // Encerra o script para garantir que o resto do código não seja executado
}

// LÓGICA DA BUSCA VAZIA: Se o campo 'busca' foi enviado, mas está vazio, redireciona para a página limpa
if (isset($_GET['busca']) && trim($_GET['busca']) === '') {
    header("Location: index.php");
    exit();
}

require_once 'conexao.php';

// Lógica de busca
$search_term = $_GET['busca'] ?? '';
// Define a base da cláusula WHERE para sempre filtrar por livros ativos
$base_where = "WHERE l.Ativo = TRUE";
$search_query = "";

if (!empty($search_term)) {
    // Se houver uma busca, adiciona a condição de busca à cláusula base
    $search_query = " AND (l.Titulo LIKE :busca OR a.Nome LIKE :busca OR e.Nome LIKE :busca OR u.Nome LIKE :busca)";
}

// ===== CONSULTA SQL FINAL E COMPLETA =====
$sql = "SELECT
            l.ID_Livro, l.Titulo, l.Disponivel, l.Categoria, l.Estante,
            a.Nome AS Autor,
            e.Nome AS Editora,
            u.Nome AS Nome_Cliente
        FROM Livros AS l
        JOIN Autores AS a ON l.ID_Autor = a.ID_Autor
        JOIN Editoras AS e ON l.ID_Editora = e.ID_Editora
        LEFT JOIN Aluguel AS al ON l.ID_Livro = al.ID_Livro AND al.Data_Devolucao IS NULL
        LEFT JOIN Usuarios AS u ON al.CPF = u.CPF
        $base_where
        $search_query
        ORDER BY l.Titulo ASC";

$stmt = $pdo->prepare($sql);
if (!empty($search_term)) {
    $stmt->bindValue(':busca', '%' . $search_term . '%');
}
$stmt->execute();
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Biblioteca</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">Biblioteca Central</a>
                <nav class="main-nav">
                    <ul>
                        <?php if (isset($_SESSION['user_nome'])): ?>
                            <li><span>Olá, <?= htmlspecialchars($_SESSION['user_nome']) ?>!</span></li>
                            <li><a href="logout.php">Sair</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="search-section">
                <h2>Consulte nosso acervo</h2>
                <form method="GET" action="index.php" class="search-form">
                    <input type="text" name="busca" placeholder="Buscar por título, autor ou editora..." value="<?= htmlspecialchars($search_term) ?>">
                    <button type="submit">Buscar</button>
                </form>
            </div>
            
            <div class="admin-actions">
                <?php if (in_array($_SESSION['user_tipo'], ['Funcionario', 'Admin'])): ?>
                    <a href="adicionar_livro.php" class="btn btn-add">Adicionar Novo Livro</a>
                <?php endif; ?>
                <?php if ($_SESSION['user_tipo'] === 'Admin'): ?>
                     <a href="criar_usuario.php" class="btn btn-edit">Criar Novo Usuário</a>
                <?php endif; ?>
            </div>

            <?php if(isset($_GET['status']) && isset($_GET['msg'])): ?>
                <div class="<?= $_GET['status'] == 'success' ? 'success-message' : 'error-messages' ?>">
                    <p><?= htmlspecialchars($_GET['msg']) ?></p>
                </div>
            <?php endif; ?>

            <table class="library-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Editora</th>
                        <th>Categoria</th>
                        <th>Localização</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($livros) > 0): ?>
                        <?php foreach ($livros as $livro): ?>
                            <tr>
                                <td><?= htmlspecialchars($livro['Titulo']) ?></td>
                                <td><?= htmlspecialchars($livro['Autor']) ?></td>
                                <td><?= htmlspecialchars($livro['Editora']) ?></td>
                                <td><?= htmlspecialchars($livro['Categoria']) ?></td>
                                <td><?= htmlspecialchars($livro['Estante']) ?></td>
                                <td>
                                    <?php if ($livro['Disponivel']): ?>
                                        <span class="status-disponivel">Disponível</span>
                                    <?php else: ?>
                                        <span class="status-alugado">
                                            Alugado por: <?= htmlspecialchars($livro['Nome_Cliente'] ?? 'N/A') ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions-cell">
                                    <?php
                                    $has_actions = false;
                                    if ($livro['Disponivel']) {
                                        echo '<a href="reservar_livro.php?id=' . $livro['ID_Livro'] . '" class="btn btn-reserve">Reservar</a>';
                                        $has_actions = true;
                                    } else {
                                        if (in_array($_SESSION['user_tipo'], ['Funcionario', 'Admin'])) {
                                            echo '<a href="devolver_livro.php?id=' . $livro['ID_Livro'] . '" class="btn btn-return">Devolver</a>';
                                            $has_actions = true;
                                        }
                                    }
                                    
                                    if ($_SESSION['user_tipo'] === 'Admin') {
                                        echo '<a href="editar_livro.php?id=' . $livro['ID_Livro'] . '" class="btn btn-edit">Editar</a>';
                                        echo '<a href="excluir_livro.php?id=' . $livro['ID_Livro'] . '" class="btn btn-delete" onclick="return confirm(\'Tem certeza que deseja remover este livro do acervo?\')">Excluir</a>';
                                        $has_actions = true;
                                    }

                                    if (!$has_actions) {
                                        echo '&nbsp;';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center;">Nenhum livro encontrado no acervo.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; <?= date('Y') ?> Biblioteca Central. Todos os direitos reservados.</p>
                <div class="footer-info">
                    <p>Projeto Interdisciplinar - Banco de Dados I e Programação Web II</p>
                    <p>Nomes: Celso Junior, Kauan Simão, Luca Samuel, Maria Eduarda.</p>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>