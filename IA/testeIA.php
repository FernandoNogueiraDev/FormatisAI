<?php
namespace IA;

class testeIA
{

    public static function montaTrilha($algo)
    {
        $current_directory = $_SERVER['DOCUMENT_ROOT'] . '/IA';

        // Display the current directory
        echo "The current working directory is: " . $current_directory;

        echo "<br><br>";

        echo "Caminho IA: " . $current_directory . "/gerar_trilha.exe";

        $caminhoIA = $current_directory . "./gerar_trilha.exe";

        echo "<br><br>";

        echo "Escape dir: " . escapeshellarg($current_directory);

        echo "<br><br>";

        echo "Escape IA: " . escapeshellarg($caminhoIA);

        echo "<br><br>";

        echo "Teste rodando IA:";

        echo "<br><br>";

        $resultado = shell_exec(escapeshellarg($caminhoIA));

        echo "<br><br>";
        echo "Terminou de rodar IA: " . $resultado;

        return $resultado;
    }
}

?>