<?php
namespace IA;

class QuestionarioIA
{
    private static function chamar(array $dados)
    {
        $current_directory = __DIR__;
        $caminhoIA = $current_directory . DIRECTORY_SEPARATOR . "questionario.exe";

        $json_string = json_encode($dados, JSON_UNESCAPED_UNICODE);

        $resultado = shell_exec(escapeshellarg($caminhoIA) . " " . escapeshellarg($json_string));

        return $resultado;
    }

    /**
     * $conteudos = [["titulo" => "...", "descricao" => "..."], ...]
     */
    public static function gerar(array $conteudos, int $numMultipla = 3, int $numAbertas = 2)
    {
        return self::chamar([
            "modo" => "gerar",
            "conteudos" => $conteudos,
            "num_multipla" => $numMultipla,
            "num_abertas" => $numAbertas
        ]);
    }

    public static function corrigirAberta(string $enunciado, string $criterio, string $respostaAluno)
    {
        return self::chamar([
            "modo" => "corrigir",
            "enunciado" => $enunciado,
            "criterio_correcao" => $criterio,
            "resposta_aluno" => $respostaAluno
        ]);
    }
}

?>
