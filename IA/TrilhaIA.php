<?php
namespace IA;

class TrilhaIA
{

    public static function montaTrilha($json_string)
    {
        $current_directory = __DIR__;
        $caminhoIA = $current_directory . DIRECTORY_SEPARATOR . "gerar_trilha.exe";

        $resultado = shell_exec(escapeshellarg($caminhoIA) . " " . escapeshellarg($json_string));

        return $resultado;
    }
}

?>
