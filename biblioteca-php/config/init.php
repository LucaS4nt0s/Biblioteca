<?php
require_once 'database.php';

// Inicializar banco de dados
echo "Criando banco de dados...\n";
criarBanco();

echo "Criando tabelas...\n";
criarTabelas();

echo "Inserindo dados iniciais...\n";
inserirDadosIniciais();

echo "Banco de dados inicializado com sucesso!\n";
echo "Admin padrão: CPF: 12345678901, Senha: admin123\n";
?>

