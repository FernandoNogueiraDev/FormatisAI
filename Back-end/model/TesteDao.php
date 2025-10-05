<?php

require_once './Aluno.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste DAOs</title>
</head>

<body>
    <h1>Lista todos os alunos</h1>

    <ol>
    <?php
    foreach (Aluno::selectTodos() as $teste) {
        echo "<li>" . $teste->getidAluno() . "; " . $teste->getNome() . "; " . $teste->getEmail() . "; " . $teste->getDataNascimento() . "; " . "</li>";
    }
    ?>
    </ol>
</body>

</html>