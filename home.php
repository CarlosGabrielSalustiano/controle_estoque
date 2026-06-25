<?php
session_start();
include "conexao.php";

if(!isset($_SESSION["usuario_id"])){
    header("Location: index.php");
    exit;
}

$total_produtos = $conn->query("SELECT COUNT(*) as total FROM produtos")->fetch_assoc()['total'];
$total_entradas = $conn->query("SELECT SUM(quantidade) as total FROM movimentacoes WHERE tipo='entrada'")->fetch_assoc()['total'];
$total_saidas   = $conn->query("SELECT SUM(quantidade) as total FROM movimentacoes WHERE tipo='saida'")->fetch_assoc()['total'];

// Conta dinamicamente itens críticos
$total_criticos = $conn->query("SELECT COUNT(*) as total FROM produtos WHERE quantidade <= quantidade_minima AND quantidade_minima > 0")->fetch_assoc()['total'];

$ultimas_mov = $conn->query("
    SELECT m.*, p.nome as produto_nome, u.nome as usuario_nome 
    FROM movimentacoes m
    JOIN produtos p ON m.produto_id = p.id
    JOIN usuarios u ON m.usuario_id = u.id
    ORDER BY m.data DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - STOCKFY</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="welcome">Bem-vindo, <strong><?php echo $_SESSION["usuario_nome"]; ?></strong>!</div>
<h1>STOCKFY</h1>

<nav>
    <ul>
        <li><a href="home.php" style="font-weight: bold;">Dashboard</a></li>
        <li><a href="cadastrar_produto.php">Cadastrar Produto</a></li>
        <li><a href="produtos_estoque.php">Listar Produtos</a></li>
        <li><a href="movimentar.php">Movimentações</a></li>
        <li><a href="configuracoes.php">Configurações</a></li>
        <li><a href="logout.php" style="color: #ef4444;">Sair</a></li>
    </ul>
</nav>

<div class="cards-container">
    <div class="card"><h3>Total de Produtos</h3><p><?php echo $total_produtos; ?></p></div>
    <div class="card"><h3>Total de Entradas</h3><p><?php echo $total_entradas ?: 0; ?></p></div>
    <div class="card"><h3>Total de Saídas</h3><p><?php echo $total_saidas ?: 0; ?></p></div>
    
    <div class="card <?= $total_criticos > 0 ? 'card-alerta-aceso' : '' ?>">
        <h3>Estoque Crítico</h3>
        <p style="color: <?= $total_criticos > 0 ? '#b45309' : '#111827' ?>;"><?php echo $total_criticos; ?></p>
    </div>
</div>

<h2>Últimas Movimentações</h2>
<table>
    <thead>
        <tr>
            <th>Produto</th><th>Tipo</th><th>Quantidade</th><th>Usuário</th><th>Data</th>
        </tr>
    </thead>
    <tbody>
        <?php while($mov = $ultimas_mov->fetch_assoc()): ?>
        <tr>
            <td><?php echo $mov['produto_nome']; ?></td>
            <td><?php echo ucfirst($mov['tipo']); ?></td>
            <td><?php echo $mov['quantidade']; ?></td>
            <td><?php echo $mov['usuario_nome']; ?></td>
            <td><?php echo date('d/m/Y H:i', strtotime($mov['data'])); ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</body>
</html>