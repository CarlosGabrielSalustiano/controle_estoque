<?php
include "conexao.php";

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST["nome"]);
    $login = trim($_POST["login"]);
    $senha = trim($_POST["senha"]);

    if (!empty($nome) && !empty($login) && !empty($senha)) {

        $verifica = $conn->prepare(
            "SELECT id FROM usuarios WHERE login = ?"
        );

        $verifica->bind_param("s", $login);
        $verifica->execute();

        $resultado = $verifica->get_result();

        if ($resultado->num_rows > 0) {

            $mensagem = "Login já cadastrado.";

        } else {

            $senhaHash = password_hash(
                $senha,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO usuarios(nome, login, senha)
                 VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "sss",
                $nome,
                $login,
                $senhaHash
            );

            if ($stmt->execute()) {

                header("Location: index.php");
                exit;

            } else {

                $mensagem = "Erro ao cadastrar usuário.";

            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Criar Conta - STOCKFY</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<h1>STOCKFY</h1>

<div class="container">

    <h2>Criar Conta</h2>

    <?php if($mensagem): ?>
        <p style="color:red;">
            <?= $mensagem ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <input
            type="text"
            name="nome"
            placeholder="Nome"
            required
        >

        <input
            type="text"
            name="login"
            placeholder="Login"
            required
        >

        <input
            type="password"
            name="senha"
            placeholder="Senha"
            required
        >

        <button type="submit">
            Cadastrar
        </button>

    </form>

    <br>

    <a href="home.php" style="color: #1e3a8a; font-weight: bold; text-decoration: none;">⬅ Voltar para tela de login</a>

</div>

</body>
</html>