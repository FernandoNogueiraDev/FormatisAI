<?php
session_start();
$_SESSION["idAluno"] = null;
$_SESSION["nome"] = null;
$_SESSION["usuario_email"] = null;
session_destroy();

header("Location: index.html");

?>