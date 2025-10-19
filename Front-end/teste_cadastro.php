<?php
require 'conexao.php';

$nome = "Gustavo";
$email = "gustavo@email.com";
$senha = "123456";
$senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO Aluno (nome, email, senha) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nome, $email, $senhaCriptografada);

if ($stmt->execute()) {
    echo "✅ Aluno cadastrado com sucesso!";
} else {
    echo "❌ Erro: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>