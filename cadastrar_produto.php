<?php
session_start();
include "conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit;
}

$mensagem = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $categoria = $_POST["categoria"];
    $preco = $_POST["preco"];
    $quantidade = $_POST["quantidade"];
    $quantidade_minima = intval($_POST["quantidade_minima"]);

    $sql = "INSERT INTO produtos (nome, categoria, preco, quantidade, quantidade_minima) VALUES ('$nome', '$categoria', $preco, $quantidade, $quantidade_minima)";
    if ($conn->query($sql) === TRUE) {
        $mensagem = "<div class='msg-sucesso'>Produto cadastrado com sucesso!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="pt-br">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto - STOCKFY</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <center><h1>STOCKFY</h1></center>
    <?= $mensagem ?>
    <form method="post">
        <h2>Cadastrar Produto</h2>
        <div class="form-group"><label>Nome do Produto:</label><input type="text" name="nome" required></div>
        <div class="form-group"><label>Categoria:</label><input type="text" name="categoria"></div>
        <div class="form-group"><label>Preço:</label><input type="number" step="0.01" name="preco" required></div>
        <div class="form-group"><label>Quantidade Inicial:</label><input type="number" name="quantidade" value="0" required></div>
        
        <div class="form-group">
            <label style="color: #f7c307;">Estoque Mínimo de Alerta:</label>
            <input type="number" name="quantidade_minima" value="0" min="0" required style="border-color: #e71502;">
        </div>
        <button type="submit">Cadastrar</button>
        <br><br>
        <center><a href="home.php" style="color: #1e3a8a; font-weight: bold; text-decoration: none;">⬅ Voltar ao Painel</a></center>
    </form>
</body>
</html>