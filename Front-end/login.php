<?php
session_start();
include 'conexao.php';

// Pega dados do formulário
$email = $_POST['email'] ?? null;
$senha = $_POST['senha'] ?? null;

// Validação básica
if (!$email || !$senha) {
    echo "Preencha todos os campos.";
    exit;
}

// Busca usuário pelo email na tabela Aluno
$stmt = $conn->prepare("SELECT idAluno, nome, email, senha FROM Aluno WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "E-mail não cadastrado.";
    exit;
}

$user = $result->fetch_assoc();

// Verifica senha
if (password_verify($senha, $user['senha'])) {
    // Cria variáveis de sessão
    $_SESSION['idAluno'] = $user['idAluno'];
    $_SESSION['nome'] = $user['nome'];
    $_SESSION['usuario_email'] = $user['email']; // ESSENCIAL para pegar o primeiro nome na tela principal

    // Retorna sucesso (pode ser usado em AJAX)
    echo "success";

    // Opcional: redirecionar para a página inicial
    // header("Location: index.php");
    // exit;
} else {
    echo "Senha incorreta.";
}

$stmt->close();
$conn->close();
?>
