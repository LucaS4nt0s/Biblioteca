<?php
if (!isset($_SESSION['user_cpf'])) {
    header('Location: ../index.php');
    exit;
}

$user_nome = $_SESSION['user_nome'];
$user_tipo = $_SESSION['user_tipo'];

// Definir menu baseado no tipo de usuário
$menu_items = [];
switch ($user_tipo) {
    case 'admin':
        $menu_items = [
            ['url' => 'dashboard.php', 'text' => 'Dashboard'],
            ['url' => 'funcionarios.php', 'text' => 'Funcionários'],
            ['url' => 'relatorios.php', 'text' => 'Relatórios']
        ];
        break;
    case 'funcionario':
        $menu_items = [
            ['url' => 'dashboard.php', 'text' => 'Dashboard'],
            ['url' => 'livros.php', 'text' => 'Livros'],
            ['url' => 'autores.php', 'text' => 'Autores'],
            ['url' => 'editoras.php', 'text' => 'Editoras'],
            ['url' => 'alugueis.php', 'text' => 'Aluguéis'],
            ['url' => 'clientes.php', 'text' => 'Clientes']
        ];
        break;
    case 'cliente':
        $menu_items = [
            ['url' => 'dashboard.php', 'text' => 'Início'],
            ['url' => 'catalogo.php', 'text' => 'Catálogo'],
            ['url' => 'meus-alugueis.php', 'text' => 'Meus Aluguéis'],
            ['url' => 'perfil.php', 'text' => 'Perfil']
        ];
        break;
}
?>

<header class="header">
    <div class="container">
        <div class="header-content">
            <a href="dashboard.php" class="logo">📚 Biblioteca</a>
            
            <nav>
                <ul class="nav-menu">
                    <?php foreach ($menu_items as $item): ?>
                        <li><a href="<?php echo $item['url']; ?>"><?php echo $item['text']; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            
            <div class="user-info">
                <span>Olá, <?php echo htmlspecialchars($user_nome); ?> (<?php echo ucfirst($user_tipo); ?>)</span>
                <a href="logout.php" class="btn-logout">Sair</a>
            </div>
        </div>
    </div>
</header>

