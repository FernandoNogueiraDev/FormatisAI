<?php
namespace IA;

class DuvidaIA
{
    /**
     * Envia a pergunta do aluno (+ contexto do conteúdo + histórico da
     * conversa) para o script de IA e retorna o JSON bruto de resposta.
     *
     * $dados deve ser um array associativo com:
     *   conteudo_titulo, conteudo_descricao, pergunta, historico (opcional)
     */
    public static function perguntar(array $dados)
    {
        $current_directory = __DIR__;
        $caminhoIA = $current_directory . DIRECTORY_SEPARATOR . "duvida.exe";

        $json_string = json_encode($dados, JSON_UNESCAPED_UNICODE);

        $resultado = shell_exec(escapeshellarg($caminhoIA) . " " . escapeshellarg($json_string));

        return $resultado;
    }
}

?>
