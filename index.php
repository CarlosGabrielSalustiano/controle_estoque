<?php
session_start();
include "./conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["login"];
    $senha = $_POST["senha"];

    $sql = "SELECT * FROM usuarios WHERE login='$login'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($senha == $user["senha"]) {
            $_SESSION["usuario_id"] = $user["id"];
            $_SESSION["usuario_nome"] = $user["nome"];
            header("Location: home.php");
        } else {
           echo "<div class='erro-msg'>Senha incorreta!</div>";
        }
    } else {
        echo "<div class='erro-msg'>Usuário não encontrado!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Controle de Estoque</title>

    <!-- SOMENTE O INDEX USA ESTE CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- IMAGEM NEON -->
    <img src="logo_neon.png" class="neon-img">
	
    <h1 class="stockfy-neon">STOCKFY</h1>

    <h2>Login</h2>

    <form method="post">
        <div class="form-group">
        <label>Login:</label>
        <input type="text" name="login" required>

        <label>Senha:</label>
        <input type="password" name="senha" required>

        <button type="submit">Entrar</button>
    </form>

</body>
</html>