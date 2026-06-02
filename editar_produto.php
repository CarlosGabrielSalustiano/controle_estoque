<?php
session_start();
include "conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novo_minimo = intval($_POST["quantidade_minima"]);
    $nome = $_POST["nome"];
    $categoria = $_POST["categoria"];
    $preco = $_POST["preco"];
    
    $conn->query("UPDATE produtos SET nome='$nome', categoria='$categoria', preco=$preco, quantidade_minima=$novo_minimo WHERE id=$id");
    header("Location: listar_produtos.php");
    exit;
}

$prod = $conn->query("SELECT * FROM produtos WHERE id = $id")->fetch_assoc();
if (!$prod) { die("Produto não encontrado."); }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="pt-br">
    <title>Configurar Produto - STOCKFY</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <center><h1>STOCKFY</h1></center>
    <form method="post">
        <h2>⚙️ Configurar Limites</h2>
        <div class="form-group"><label>Nome do Produto:</label><input type="text" name="nome" value="<?= $prod['nome'] ?>" required></div>
        <div class="form-group"><label>Categoria:</label><input type="text" name="categoria" value="<?= $prod['categoria'] ?>"></div>
        <div class="form-group"><label>Preço (R$):</label><input type="number" step="0.01" name="preco" value="<?= $prod['preco'] ?>" required></div>
        
        <div class="form-group">
            <label style="color: #f7c307#b45309;">Estoque Mínimo para Alerta:</label>
            <input type="number" name="quantidade_minima" value="<?= $prod['quantidade_minima'] ?>" min="0" required style="border-color: #e71502;">
        </div>
        <button type="submit">Atualizar Limites</button>
        <br><br>
        <center><a href="listar_produtos.php" style="color: #1e3a8a; font-weight: bold; text-decoration: none;">Cancelar</a></center>
    </form>
</body>
</html>