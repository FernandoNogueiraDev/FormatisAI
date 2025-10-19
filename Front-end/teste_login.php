<?php
require 'conexao.php';

$stmt = $conn->prepare("SELECT idAluno, nome, senha FROM Aluno LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "✅ Login script pode acessar o banco!";
} else {
    echo "⚠️ Nenhum aluno encontrado, mas a conexão funciona.";
}
?>