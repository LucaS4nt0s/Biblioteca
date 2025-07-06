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
$search_query = "";

if (!empty($search_term)) {
    $search_query = "WHERE l.Titulo LIKE :busca OR a.Nome LIKE :busca OR e.Nome LIKE :busca";
}

// Consulta SQL para buscar os livros e seus detalhes
$sql = "SELECT
            l.ID_Livro,
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

            <?php if (in_array($_SESSION['user_tipo'], ['Funcionario', 'Admin'])): ?>
                <div class="admin-actions">
                    <a href="adicionar_livro.php" class="btn btn-add">Adicionar Novo Livro</a>
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
                                        <span class="status-alugado">Alugado</span>
                                    <?php endif; ?>
                                </td>
<td class="actions-cell">
    <?php
    // Lógica para Clientes e Funcionários
    if ($livro['Disponivel']) {
        // Se o livro está disponível, qualquer um pode reservar
        echo '<a href="reservar_livro.php?id=' . $livro['ID_Livro'] . '" class="btn btn-reserve">Reservar</a>';
    } else {
        // Se o livro está alugado, apenas funcionários e admins podem devolver
        if (in_array($_SESSION['user_tipo'], ['Funcionario', 'Admin'])) {
            echo '<a href="devolver_livro.php?id=' . $livro['ID_Livro'] . '" class="btn btn-return">Devolver</a>';
        }
    }

    // Lógica adicional apenas para Admins
    if ($_SESSION['user_tipo'] === 'Admin') {
        echo '<a href="editar_livro.php?id=' . $livro['ID_Livro'] . '" class="btn btn-edit">Editar</a>';
        echo '<a href="excluir_livro.php?id=' . $livro['ID_Livro'] . '" class="btn btn-delete" onclick="return confirm(\'Tem certeza que deseja excluir este livro?\')">Excluir</a>';
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
                    <p>Projeto Interdisciplinar - Banco de Dados II e Programação Web II</p>
                    <p>Nomes: Carlos Barbosa, Celso Junior, Kauan Simão, Luca Samuel, Maria Eduarda.</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>