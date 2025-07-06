<?php
// Inclui o arquivo de conexão
require_once 'conexao.php';

// Lógica de busca
$search_query = "";
$search_term = '';
if (isset($_GET['busca']) && !empty($_GET['busca'])) {
    $search_term = $_GET['busca'];
    // Usa a cláusula OR para buscar em vários campos
    $search_query = "WHERE l.Titulo LIKE :busca OR a.Nome LIKE :busca OR e.Nome LIKE :busca";
}

// Consulta principal para buscar livros com detalhes do autor e editora
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

if (!empty($search_query)) {
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
                        <li><a href="index.php">Início</a></li>
                        <li><a href="#">Minha Conta</a></li>
                        <li><a href="#">Contato</a></li>
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
                            <td colspan="6" style="text-align:center;">Nenhum livro encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; 2025 Biblioteca Central. Todos os direitos reservados.</p>
                <div class="footer-info">
                    <p>Projeto Interdisciplinar - Banco de Dados I e Programação Web II</p>
                    [cite_start]<p>Nomes: Celso Junior, Kauan Simão, Luca Samuel, Maria Eduarda. [cite: 1]</p>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>