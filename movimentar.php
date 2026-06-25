<?php
session_start();
include "conexao.php";

// Proteção: só acessa se estiver logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit;
}

// Registrar movimentação
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $produto_id = $_POST["produto_id"];
    $tipo = $_POST["tipo"];
    $quantidade = intval($_POST["quantidade"]);
    $usuario_id = $_SESSION["usuario_id"];

    // Buscar quantidade atual do produto
    $sql = "SELECT quantidade FROM produtos WHERE id = $produto_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $quantidade_atual = $row["quantidade"];

    if ($tipo == "entrada") {
        $nova_quantidade = $quantidade_atual + $quantidade;
    } else {
        // Saída: verificar se não vai ficar negativa
        if ($quantidade > $quantidade_atual) {
            echo "<p class='erro'>Erro: Não é possível retirar mais do que há em estoque!</p>";
            $nova_quantidade = $quantidade_atual;
        } else {
            $nova_quantidade = $quantidade_atual - $quantidade;
        }
    }

    // Atualiza estoque e registra movimentação se permitido
    if ($nova_quantidade != $quantidade_atual) {
        $conn->query("UPDATE produtos SET quantidade = $nova_quantidade WHERE id = $produto_id");
        $conn->query("INSERT INTO movimentacoes (produto_id, usuario_id, tipo, quantidade) VALUES ($produto_id, $usuario_id, '$tipo', $quantidade)");

        // LÓGICA DE INTERCEPTAÇÃO E DISPARO VIA SMTP
        if ($tipo == "saida") {
            $busca = $conn->query("SELECT nome, quantidade_minima FROM produtos WHERE id = $produto_id")->fetch_assoc();
            $limite_definido = $busca['quantidade_minima'];
            $nome_produto = $busca['nome'];

            if ($nova_quantidade <= $limite_definido && $limite_definido > 0) {
                // Puxa o e-mail digitado dinamicamente na página de configurações
                $busca_email = $conn->query("SELECT valor FROM configuracoes WHERE chave = 'email_alerta'")->fetch_assoc();
                $email_banca = $busca_email['valor'];

                include_once "funcao_alerta.php";

                $assunto = "⚠️ Alerta STOCKFY: Limite Crítico - " . $nome_produto;
                $msg = "Olá Administrador,\n\n";
                $msg .= "O produto '" . $nome_produto . "' (ID: " . $produto_id . ") atingiu o limite mínimo definido.\n\n";
                $msg .= "Limite Mínimo Definido: " . $limite_definido . " unidades.\n";
                $msg .= "Limite Atual: " . $nova_quantidade . " unidades.\n\n";
                $msg .= "Mensagem automatizada gerada pelo STOCKFY.";

                enviarEmailAlerta($email_banca, $assunto, $msg);
            }
        }

        echo "<p class='sucesso'>Movimentação registrada com sucesso!</p>";
    }
}

// Buscar produtos
$produtos = $conn->query("SELECT * FROM produtos ORDER BY nome");

// Buscar movimentações
$result = $conn->query("SELECT m.id, p.nome AS produto, u.nome AS usuario, m.tipo, m.quantidade, m.data 
                        FROM movimentacoes m
                        JOIN produtos p ON m.produto_id = p.id
                        JOIN usuarios u ON m.usuario_id = u.id
                        ORDER BY m.data DESC");

// Calcular totais de entrada e saída
$totalEntradas = $conn->query("SELECT SUM(quantidade) AS total FROM movimentacoes WHERE tipo='entrada'")->fetch_assoc()["total"];
$totalSaidas   = $conn->query("SELECT SUM(quantidade) AS total FROM movimentacoes WHERE tipo='saida'")->fetch_assoc()["total"];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimentações - Controle de Estoque</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function buscarMov() {
            let input = document.getElementById("buscaMov");
            let filtro = input.value.toLowerCase();
            let linhas = document.querySelectorAll("#tabelaMov tbody tr");
            linhas.forEach(linha => {
                let texto = linha.innerText.toLowerCase();
                linha.style.display = texto.includes(filtro) ? "" : "none";
            });
        }
    </script>
</head>
<body>

    <h2>📊 Controle de saída </h2>
    <a href="home.php" style="color: #1e3a8a; font-weight: bold; text-decoration: none;">⬅ Voltar ao Painel</a>

    <form method="post" class="form-movimentar">
        <label>Produto:</label>
        <select name="produto_id" required>
            <option value="">Selecione</option>
            <?php while($p = $produtos->fetch_assoc()): ?>
                <option value="<?= $p['id'] ?>">
                    <?= $p['nome'] ?> — <?= $p['quantidade'] ?> em estoque
                </option>
            <?php endwhile; ?>
        </select>

        <label>Tipo de Movimentação:</label>
        <select name="tipo" required>
            <option value="saida">Saída</option>
        </select>

        <label>Quantidade:</label>
        <input type="number" name="quantidade" min="1" required>

        <button type="submit">Registrar Movimentação</button>
    </form>

    <div class="cards-container">
        <div class="card">
            <h3>Total de Entradas</h3>
            <p><?php echo $totalEntradas ?? 0; ?></p>
        </div>
        <div class="card">
            <h3>Total de Saídas</h3>
            <p><?php echo $totalSaidas ?? 0; ?></p>
        </div>
    </div>

    <input type="text" id="buscaMov" placeholder="🔍 Buscar movimentação..." onkeyup="buscarMov()">

    <table id="tabelaMov">
        <thead>
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Usuário</th>
                <th>Tipo</th>
                <th>Quantidade</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row["id"] ?></td>
                        <td><?= $row["produto"] ?></td>
                        <td><?= $row["usuario"] ?></td>
                        <td><?= ucfirst($row["tipo"]) ?></td>
                        <td><?= $row["quantidade"] ?></td>
                        <td><?= date("d/m/Y H:i", strtotime($row["data"])) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">Nenhuma movimentação encontrada</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>