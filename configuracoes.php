<?php
session_start();
include "conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit;
}

$mensagem = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novo_email = $conn->real_escape_string($_POST["email_alerta"]);
    $conn->query("UPDATE configuracoes SET valor = '$novo_email' WHERE chave = 'email_alerta'");
    $mensagem = "<div class='msg-sucesso'>E-mail de destino atualizado!</div>";
}

$busca_config = $conn->query("SELECT valor FROM configuracoes WHERE chave = 'email_alerta'")->fetch_assoc();
$email_atual = $busca_config['valor'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - STOCKFY</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <center><h1>STOCKFY</h1></center>
    
    <?= $mensagem ?>

    <form method="post" style="max-width: 400px;">
        <h2>⚙️ Configurações Gerais</h2>
        <div class="form-group">
            <label>E-mail do responsável:</label>
            <input type="email" name="email_alerta" value="<?= htmlspecialchars($email_atual) ?>" required style="width: 90%;">
            <span style="font-size: 12px; color: #6b7280; margin-top: 5px;">Este E-mail receberá as notificações de alerta do estoque.</span>
        </div>
        <button type="submit">Salvar Configuração</button>
        <br><br>
        <center><a href="home.php" style="color: #1e3a8a; font-weight: bold; text-decoration: none;">⬅ Voltar ao Painel</a></center>
    </form>
</body>
</html>