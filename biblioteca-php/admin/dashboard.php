<?php
session_start();
require_once '../config/database.php';

// Verificar se é admin
if (!isset($_SESSION['user_cpf']) || $_SESSION['user_tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$pdo = conectarBanco();

// Buscar estatísticas
$stats = [];

// Total de livros
$stmt = $pdo->query("SELECT COUNT(*) FROM livro");
$stats['total_livros'] = $stmt->fetchColumn();

// Livros disponíveis
$stmt = $pdo->query("SELECT COUNT(*) FROM livro WHERE disponivel = 1");
$stats['livros_disponiveis'] = $stmt->fetchColumn();

// Total de clientes
$stmt = $pdo->query("SELECT COUNT(*) FROM cliente");
$stats['total_clientes'] = $stmt->fetchColumn();

// Total de funcionários
$stmt = $pdo->query("SELECT COUNT(*) FROM funcionario");
$stats['total_funcionarios'] = $stmt->fetchColumn();

// Aluguéis ativos
$stmt = $pdo->query("SELECT COUNT(*) FROM aluguel WHERE devolvido = 0");
$stats['alugueis_ativos'] = $stmt->fetchColumn();

// Aluguéis hoje
$stmt = $pdo->query("SELECT COUNT(*) FROM aluguel WHERE DATE(data_saida) = CURDATE()");
$stats['alugueis_hoje'] = $stmt->fetchColumn();

// Funcionários recentes
$stmt = $pdo->query("
    SELECT p.nome, p.cpf, f.funcao, p.created_at 
    FROM pessoa p 
    JOIN funcionario f ON p.cpf = f.cpf 
    ORDER BY p.created_at DESC 
    LIMIT 5
");
$funcionarios_recentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Aluguéis recentes
$stmt = $pdo->query("
    SELECT a.*, p.nome as cliente_nome, l.nome as livro_nome 
    FROM aluguel a 
    JOIN pessoa p ON a.cpf = p.cpf 
    JOIN livro l ON a.id_livro = l.id_livro 
    ORDER BY a.data_saida DESC 
    LIMIT 10
");
$alugueis_recentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administrador</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <main class="main-content">
        <div class="container">
            <div class="card fade-in">
                <div class="card-header">
                    <h1 class="card-title">Dashboard - Administrador</h1>
                    <p class="card-subtitle">Visão geral do sistema de biblioteca</p>
                </div>
                
                <!-- Estatísticas -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['total_livros']; ?></div>
                        <div class="stat-label">Total de Livros</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['livros_disponiveis']; ?></div>
                        <div class="stat-label">Livros Disponíveis</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['total_clientes']; ?></div>
                        <div class="stat-label">Total de Clientes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['total_funcionarios']; ?></div>
                        <div class="stat-label">Total de Funcionários</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['alugueis_ativos']; ?></div>
                        <div class="stat-label">Aluguéis Ativos</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['alugueis_hoje']; ?></div>
                        <div class="stat-label">Aluguéis Hoje</div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-2">
                <!-- Funcionários Recentes -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Funcionários Recentes</h2>
                        <a href="funcionarios.php" class="btn btn-primary">Ver Todos</a>
                    </div>
                    
                    <?php if (empty($funcionarios_recentes)): ?>
                        <p>Nenhum funcionário cadastrado.</p>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Função</th>
                                        <th>Cadastrado em</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($funcionarios_recentes as $funcionario): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($funcionario['nome']); ?></td>
                                            <td><?php echo htmlspecialchars($funcionario['funcao']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($funcionario['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Aluguéis Recentes -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Aluguéis Recentes</h2>
                        <a href="../funcionario/alugueis.php" class="btn btn-primary">Ver Todos</a>
                    </div>
                    
                    <?php if (empty($alugueis_recentes)): ?>
                        <p>Nenhum aluguel registrado.</p>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Livro</th>
                                        <th>Data</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($alugueis_recentes as $aluguel): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($aluguel['cliente_nome']); ?></td>
                                            <td><?php echo htmlspecialchars($aluguel['livro_nome']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($aluguel['data_saida'])); ?></td>
                                            <td>
                                                <?php if ($aluguel['devolvido']): ?>
                                                    <span style="color: green;">Devolvido</span>
                                                <?php else: ?>
                                                    <span style="color: orange;">Ativo</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Ações Rápidas -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Ações Rápidas</h2>
                </div>
                
                <div class="grid grid-3">
                    <a href="funcionarios.php?action=add" class="btn btn-success">Adicionar Funcionário</a>
                    <a href="../funcionario/livros.php?action=add" class="btn btn-primary">Adicionar Livro</a>
                    <a href="relatorios.php" class="btn btn-warning">Gerar Relatórios</a>
                </div>
            </div>
        </div>
    </main>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>

