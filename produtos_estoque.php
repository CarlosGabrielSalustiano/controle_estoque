<?php
session_start();
include "conexao.php";

if(!isset($_SESSION["usuario_id"])){
    header("Location: index.php");
    exit;
}

$result = $conn->query("SELECT * FROM produtos");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="pt-br">
    <title>Listar Produtos - STOCKFY</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>STOCKFY - Produtos em Estoque</h1>
    <p><a href="home.php" style="color: #1e3a8a; font-weight: bold; text-decoration: none;">⬅ Voltar ao Painel</a></p>

    <table id="tabelaProdutos">
        <thead>
            <tr>
                <th>ID</th><th>Produto</th><th>Categoria</th><th>Preço (R$)</th><th>Quantidade</th><th>Mínimo Definido</th><th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): 
                $critico = ($row["quantidade"] <= $row["quantidade_minima"] && $row["quantidade_minima"] > 0);
            ?>
                <tr class="<?= $critico ? 'linha-critica' : '' ?>">
                    <td><?php echo $row["id"]; ?></td>
                    <td><?php echo $row["nome"]; ?></td>
                    <td><?php echo $row["categoria"]; ?></td>
                    <td><?php echo number_format($row["preco"], 2, ',', '.'); ?></td>
                    <td class="<?= $critico ? 'texto-critico' : '' ?>"><?php echo $row["quantidade"]; ?> <?= $critico ? '⚠️' : '' ?></td>
                    <td><?php echo $row["quantidade_minima"]; ?> unidades</td>
                    
                    <td><a href="editar_produto.php?id=<?= $row['id'] ?>" class="btn-config">⚙️ Limite</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>