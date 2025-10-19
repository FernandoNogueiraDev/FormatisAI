<?php
require 'conexao.php';

$result = $conn->query("DESCRIBE Aluno");
echo "<h3>Estrutura da tabela Aluno:</h3>";
echo "<table border='1'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['Field']}</td>
            <td>{$row['Type']}</td>
            <td>{$row['Null']}</td>
            <td>{$row['Key']}</td>
          </tr>";
}
echo "</table>";

// Mostra alguns dados de exemplo
echo "<h3>Dados de exemplo:</h3>";
$dados = $conn->query("SELECT * FROM Aluno LIMIT 1");
if ($dados && $row = $dados->fetch_assoc()) {
    echo "<pre>";
    print_r($row);
    echo "</pre>";
}
?>