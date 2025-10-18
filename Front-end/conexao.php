<?php
$servername = "localhost";
$username = "root"; // altere se tiver outro usuário
$password = "";     // coloque sua senha MySQL, se houver
$database = "PlataformaEstudos";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>
