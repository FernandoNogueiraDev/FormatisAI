<?php
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Processamento do cadastro
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    
    // Verifica se o email já existe
    $checkQuery = $conn->prepare("SELECT idAluno FROM Aluno WHERE email = ?");
    $checkQuery->bind_param("s", $email);
    $checkQuery->execute();
    $checkQuery->store_result();
    
    if ($checkQuery->num_rows > 0) {
        echo "email_existente";
        $checkQuery->close();
        exit;
    }
    $checkQuery->close();
    
    // Insere no banco - APENAS colunas que existem
    $insertQuery = $conn->prepare("INSERT INTO Aluno (nome, email, senha) VALUES (?, ?, ?)");
    $insertQuery->bind_param("sss", $nome, $email, $senha);
    
    if ($insertQuery->execute()) {
        echo "success";
    } else {
        echo "Erro ao cadastrar: " . $insertQuery->error;
    }
    
    $insertQuery->close();
    $conn->close();
}
?>