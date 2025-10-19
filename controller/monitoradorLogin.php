<?php
if (!isset($_SESSION['idAluno']) || !isset($_SESSION['nome']) || !isset($_SESSION['usuario_email'])){
    session_destroy();
    header("Location: index.html");
} else {
    echo $_SESSION['idAluno'];
    echo $_SESSION['nome'];
    echo $_SESSION['usuario_email'];
}
?>