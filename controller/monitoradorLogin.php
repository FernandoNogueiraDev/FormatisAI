<?php
if (!isset($_SESSION['idAluno']) || !isset($_SESSION['nome']) || !isset($_SESSION['usuario_email'])){
    session_destroy();
    header("Location: telalogin.html");
} else {

}
?>