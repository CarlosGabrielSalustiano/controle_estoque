<?php
// Conexão com o MySQL usando mysqli
$host = "localhost";    // Servidor local
$user = "root";         // Usuário padrão do MySQL no XAMPP
$pass = "";             // Senha padrão é vazia (se você não mudou)
$db   = "estoqueria";      // Nome do banco que criamos

// Cria a conexão
$conn = new mysqli($host, $user, $pass, $db);

// Verifica se houve erro
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>