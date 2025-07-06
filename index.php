<?php
// Inicia a sessão para verificar o login
session_start();

// VERIFICAÇÃO DE LOGIN: Se o usuário não estiver logado, redireciona para a página de login.
// Esta é a parte que protege a página.
if (!isset($_SESSION['user_cpf'])) {
    header("Location: login.php");
    exit(); // Encerra o script para garantir que o resto do código não seja executado
}

// O resto do código só será executado se o usuário estiver logado
require_once 'conexao.php';

// Lógica de busca
$search_term = $_GET['busca'] ?? ''; // Pega o termo de busca da URL, se existir
$search_query = "";

if (!empty($search_term)) {
    $search_query = "WHERE l.Titulo LIKE :busca OR a.Nome LIKE :busca OR e.Nome LIKE :busca";
}

// Consulta SQL para buscar os livros e seus detalhes
$sql = "SELECT
            l.Titulo,
            l.Categoria,
            l.Estante,
            l.Disponivel,
            a.Nome AS Autor,
            e.Nome AS Editora
        FROM Livros l
        JOIN Autores a ON l.ID_Autor = a.ID_Autor
        JOIN Editoras e ON l.ID_Editora = e.ID_Editora
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
                        <li><span>Olá, <?= htmlspecialchars($_SESSION['user_nome']) ?>!</span></li>
                        <li><a href="logout.php">Sair</a></li>
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

            <table class="library-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Editora</th>
                        <th>Categoria</th>
                        <th>Localização (Estante)</th>
                        <th>Status</th>
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
                                        <span class="status-alugado">Alugado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center;">Nenhum livro encontrado no acervo.</td>
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