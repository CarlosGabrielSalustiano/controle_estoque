<?php
session_start();
$produtos = [];

include "conexao.php";

if(!isset($_SESSION["usuario_id"])){
    header("Location: index.php");
    exit;
}

// Consultas para cards
$total_produtos = $conn->query("SELECT COUNT(*) as total FROM produtos")->fetch_assoc()['total'];
$total_entradas = $conn->query("SELECT SUM(quantidade) as total FROM movimentacoes WHERE tipo='entrada'")->fetch_assoc()['total'];
$total_saidas   = $conn->query("SELECT SUM(quantidade) as total FROM movimentacoes WHERE tipo='saida'")->fetch_assoc()['total'];

// Últimas 5 movimentações
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
    <title>Dashboard - Controle de Estoque</title>
    <link rel="stylesheet" href="/controle_estoque/style.css">
</head>
<body>

<!-- Bem-vindo -->
<div class="welcome">
    Bem-vindo, <?php echo $_SESSION["usuario_nome"]; ?>!
    <!-- Alertas de estoque -->
<?php
foreach($produtos as $p){
    if(isset($p['alerta']) && $p['quantidade'] <= $p['alerta']){
        echo "<p class='alerta'>⚠ O produto <b>".htmlspecialchars($p['nome'])."</b> atingiu o estoque mínimo (".intval($p['quantidade'])." unidades)</p>";
    }
}
?>
</div>

<!-- Menu lateral -->
<nav>
    <ul>
        <li><a href="home.php">Dashboard</a></li>
        <li><a href="cadastrar_produto.php">Cadastrar Produto</a></li>
        <li><a href="listar_produtos.php">Listar Produtos</a></li>
        <li><a href="movimentar.php">Movimentações</a></li>
        <li><a href="index.php">Sair</a></li>
    </ul>
</nav>

<!-- Cards de resumo -->
<div class="cards-container">
    <div class="card">
        <h3>Total de Produtos</h3>
        <p><?php echo $total_produtos; ?></p>
    </div>
    <div class="card">
        <h3>Total de Entradas</h3>
        <p><?php echo $total_entradas ?: 0; ?></p>
    </div>
    <div class="card">
        <h3>Total de Saídas</h3>
        <p><?php echo $total_saidas ?: 0; ?></p>
    </div>
</div>

<!-- Últimas movimentações -->
<h2>Últimas Movimentações</h2>
<table>
    <thead>
        <tr>
            <th>Produto</th>
            <th>Tipo</th>
            <th>Quantidade</th>
            <th>Usuário</th>
            <th>Data</th>
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