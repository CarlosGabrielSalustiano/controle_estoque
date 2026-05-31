<?php
session_start();
include "conexao.php";

// Proteção: só entra se estiver logado
if(!isset($_SESSION["usuario_id"])){
    header("Location: index.php");
    exit;
}

// Buscar todos os produtos
$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);

// Calcular resumos
$totalProdutos = $conn->query("SELECT COUNT(*) AS total FROM produtos")->fetch_assoc()["total"];
$totalCategorias = $conn->query("SELECT COUNT(DISTINCT categoria) AS total FROM produtos")->fetch_assoc()["total"];
$totalValor = $conn->query("SELECT SUM(preco * quantidade) AS total FROM produtos")->fetch_assoc()["total"];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Listar Produtos - Controle de Estoque</title>
    <link rel="stylesheet" href="/controle_estoque/style.css">
    <script>
        // Função para busca dinâmica
        function buscarProduto() {
            let input = document.getElementById("busca");
            let filtro = input.value.toLowerCase();
            let linhas = document.querySelectorAll("#tabelaProdutos tbody tr");

            linhas.forEach(linha => {
                let texto = linha.innerText.toLowerCase();
                linha.style.display = texto.includes(filtro) ? "" : "none";
            });
        }
    </script>
</head>
<body>
    <!-- Bem-vindo no canto superior esquerdo -->
    <div class="welcome">
        Bem-vindo, <?php echo $_SESSION["usuario_nome"]; ?>!
    </div>

    <h2>📦 Produtos em Estoque</h2>

    <!-- Cards de resumo -->
    <div class="cards-container">
        <div class="card">
            <h3>Total de Produtos</h3>
            <p><?php echo $totalProdutos; ?></p>
        </div>
        <div class="card">
            <h3>Categorias</h3>
            <p><?php echo $totalCategorias; ?></p>
        </div>
        <div class="card">
            <h3>Valor em Estoque</h3>
            <p>R$ <?php echo number_format($totalValor, 2, ',', '.'); ?></p>
        </div>
    </div>

    <!-- Campo de busca -->
    <input type="text" id="busca" placeholder="🔍 Buscar produto..." onkeyup="buscarProduto()">

    <!-- Tabela de produtos -->
    <table id="tabelaProdutos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Categoria</th>
                <th>Preço (R$)</th>
                <th>Quantidade</th>
                <th>Total (R$)</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row["id"]; ?></td>
                    <td><?php echo $row["nome"]; ?></td>
                    <td><?php echo $row["categoria"]; ?></td>
                    <td><?php echo number_format($row["preco"], 2, ',', '.'); ?></td>
                    <td><?php echo $row["quantidade"]; ?></td>
                    <td><?php echo number_format($row["preco"] * $row["quantidade"], 2, ',', '.'); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <br>
    <a href="home.php">⬅ Voltar ao Painel</a>
</body>
</html>