<?php
$host = "localhost";   
$user = "root";        
$pass = "";            
$db = "estoqueria";  

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// 1. Cria a coluna de limite de estoque se ela não existir
$checa_coluna = $conn->query("SHOW COLUMNS FROM `produtos` LIKE 'quantidade_minima'");
if ($checa_coluna->num_rows == 0) {
    $conn->query("ALTER TABLE `produtos` ADD COLUMN `quantidade_minima` INT(11) NOT NULL DEFAULT 0 AFTER `quantidade`");
}

// 2. Cria a tabela de configurações de e-mail se ela não existir
$checa_tabela = $conn->query("SHOW TABLES LIKE 'configuacoes'");
if ($checa_tabela->num_rows == 0) {
    $conn->query("CREATE TABLE `configuacoes` (
        `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `chave` varchar(50) NOT NULL UNIQUE,
        `valor` text NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1");
    
    // Insere o e-mail administrativo padrão
    $conn->query("INSERT INTO `configuacoes` (`chave`, `valor`) VALUES ('email_alerta', 'gerente_padrao@empresa.com')");
}
?>