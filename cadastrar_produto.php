<?php
session_start();
include "conexao.php"; // Conexão com o banco

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit;
}

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $categoria = $_POST["categoria"];
    $preco = $_POST["preco"];
    $quantidade = $_POST["quantidade"];

    // Inserir produto no banco
    $sql = "INSERT INTO produtos (nome, categoria, preco, quantidade) VALUES ('$nome', '$categoria', $preco, $quantidade)";
    if ($conn->query($sql) === TRUE) {
        echo "Produto cadastrado com sucesso!";
    } else {
        echo "Erro: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Produto</title>
    <link rel="stylesheet" href="/controle_estoque/style.css">
</head>
<body>
    <h2>Cadastrar Produto</h2>
    <form method="post">
        <label>Nome:</label>
        <input type="text" name="nome" required><br>
        <label>Categoria:</label>
        <input type="text" name="categoria"><br>
        <label>Preço:</label>
        <input type="number" step="0.01" name="preco" required><br>
        <label>Quantidade:</label>
        <input type="number" name="quantidade" value="0" required><br>
        <button type="submit">Cadastrar</button>
    </form>
    <br>
    <a href="home.php">Voltar</a>
</body>
</html>