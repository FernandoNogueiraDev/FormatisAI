<?php
namespace controller;

use IA\TrilhaIA;
use model\Banco;
use model\Aluno;
use model\Conteudo;
use model\Fonte;
use model\TrilhaConteudo;
use model\TrilhaEstudo;
use model\SugestaoConteudo;

require_once ($_SERVER['DOCUMENT_ROOT'] . '/IA/TrilhaIA.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/model/Banco.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/model/Aluno.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/model/Conteudo.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/model/Fonte.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/model/TrilhaConteudo.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/model/TrilhaEstudo.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/model/SugestaoConteudo.php');

$banco = new Banco();

// Execute the Python script and capture its output
// $output = shell_exec("python ./IA/teste.py");

/*
 * aluno_autodidata_teste = {
 * "tipo_aluno": "Autodidata",
 * "nome": "Joana",
 * "idade": 23,
 * "interesse": "Tecnologia",
 * "objetivo_carreira": "Desenvolvedor Java",
 * "nivel": "Iniciante",
 * "tipo_conteudo": ["vídeo", "artigo"]
 * }
 *
 * aluno_escola_teste = {
 * "tipo_aluno": "Escola",
 * "nome": "Paulo",
 * "idade": 14,
 * "disciplinas": {
 * "matemática": 6.0,
 * "biologia": 5.2,
 * "física": 4.5
 * },
 * "ano_letivo": "8º ano",
 * "tipo_conteudo": ["vídeo"]
 * }
 */

// Autentica (Verifica se o usuário está logado pegando coisas da sessão)
// Autentica

$POST["idAluno"] = 1; // REMOVER
$POST["tipo_aluno"] = "Autodidata";
$POST["TituloTrilha"] = "Java";
$POST["Nivel"] = "Iniciante";
$idAluno = $POST["idAluno"];
$tipo_aluno = $POST["tipo_aluno"];
$TituloTrilha = $POST["TituloTrilha"];
$Nivel = $POST["Nivel"];

$aluno = Aluno::selectPorId($idAluno);

$json_php = [];

if ($tipo_aluno == "Autodidata") {
    $json_php = [
        "tipo_aluno" => "Autodidata",
        "nome" => $aluno->getNome(),
        "idade" => $aluno->calculaIdade(),
        "interesse" => "Tecnologia",
        "objetivo_carreira" => $TituloTrilha,
        "nivel" => $Nivel,
        "tipo_conteudo" => [
            "vídeo",
            "artigo"
        ]
    ];
} else {
    $json_php = [
        "tipo_aluno" => "Escola",
        "nome" => "Paulo",
        "idade" => 14,
        "disciplinas" => [
            "matemática" => 6.0,
            "biologia" => 5.2,
            "física" => 4.5
        ],
        "ano_letivo" => "8º ano",
        "tipo_conteudo" => [
            "vídeo"
        ]
    ];
}


echo var_dump(implode($json_php));


$jsonString = json_encode($json_php);

echo "<br><br><br> Json Encodificada:" . $jsonString;

$start = microtime(true);


echo "<br><br><br> Executando IA:";
$json_ia = TrilhaIA::montaTrilha($jsonString);

$time_elapsed_secs = microtime(true) - $start;

echo "<br><br><br>";

echo "<br> Tempo de execução IA: " . $time_elapsed_secs . " segundos";

echo "<br><br><br>";

echo "JSON retorno da API: <br>" . $json_ia;

echo "<br><br>";
echo "<br><br>";
echo "<br><br>";
echo "<br><br>";

echo "json_ia é string: " . is_string($json_ia);
echo "<br><br>";
echo "json_ia é array: " . is_array($json_ia);
echo "<br><br>";

$json_decodificado = json_decode($json_ia, true, 512, null);

try {

    echo "json_decodificado é string: " . is_string($json_decodificado);
    echo "<br><br>";
    echo "json_decodificado é array: " . is_array($json_decodificado);
    echo "<br><br>";
    echo "json_decodificado é nulo: " . is_null($json_decodificado);
    echo "<br><br>";

    if (is_array($json_decodificado)) {
        echo (implode("ÇÇÇ", $json_decodificado));
    }
} catch (TypeError $e) {
    echo "Erro de tipo";
}

try {
    echo $json_decodificado;
} catch (Throwable $e) {

    echo "<br><br>";
    echo "<br><br>";

    echo $e->getMessage();
}

try {

    $banco->conexao()->beginTransaction();

    echo "<br><br>";
    echo "<br><br>";
    echo "<br><br>";

    $trilha_estudos_ia = $json_decodificado['trilha_estudos'];

    echo "<br><br>JSON decodificado: <br>";

    echo "Trilha estudos:<br>";

    echo "<br>duracao_total_trilha: " . $trilha_estudos_ia['duracao_total_trilha'];

    $listaConteudosTrilha = [];

    foreach ($trilha_estudos_ia['conteudo'] as $conteudo_trilha) {

        echo "<br><br>";

        echo "<br>titulo: " . $conteudo_trilha['titulo'];

        echo "<br>tipo_conteudo: " . $conteudo_trilha['tipo_conteudo'];

        echo "<br>link_referencia: " . $conteudo_trilha['link_referencia'];

        echo "<br>fonte: " . $conteudo_trilha['fonte'];

        echo "<br>duracao: " . $conteudo_trilha['duracao'];

        $conteudo = Conteudo::constroiVazio();

        $conteudo->setTitulo($conteudo_trilha['titulo']);
        $conteudo->setCategoria($conteudo_trilha['tipo_conteudo']);
        $conteudo->setLink($conteudo_trilha['link_referencia']);
        $conteudo->setDuracao_min($conteudo_trilha['duracao']);

        $fonte = Fonte::selectPorNome($conteudo_trilha['fonte']);

        if (! is_null($fonte)) {
            $conteudo->setfonte_id($fonte[0]->getIdFonte());
        } else {
            $fonteNova = Fonte::constroiVazio();
            $fonteNova->setNome_fonte($conteudo_trilha['fonte']);
            $fonteNova = Fonte::create($fonteNova, $banco);
            $conteudo->setfonte_id($fonteNova->getIdFonte());
        }

        $conteudo = Conteudo::create($conteudo, $banco);

        array_push($listaConteudosTrilha, $conteudo);
    }

    $trilhaEstudo = new TrilhaEstudo();

    $trilhaEstudo->setAluno_id($idAluno);
    $trilhaEstudo->setTitulo($TituloTrilha);
    $trilhaEstudo->setDescricao($trilha_estudos_ia['descricao_trilha']);
    $trilhaEstudo->setStatus("A aceitar");
    $trilhaEstudo = TrilhaEstudo::create($trilhaEstudo, $banco);

    // Trukha

    $indice = 1;
    $codTrilhaConteudo = [];
    foreach ($listaConteudosTrilha as $itemConteudo) {
        $trilhaConteudoItem = new TrilhaConteudo();

        $trilhaConteudoItem->setTrilha_id($trilhaEstudo->getIdTrilha_estudo());
        $trilhaConteudoItem->setConteudo_id($itemConteudo->getIdConteudo());
        $trilhaConteudoItem->setOrdem($indice);
        $trilhaConteudoItem->setObrigatorio(0);
        $trilhaConteudoItem->setEstimativa_min($itemConteudo->getDuracao_min());

        $trilhaConteudoItem = TrilhaConteudo::create($trilhaConteudoItem, $banco);
        $codTrilhaConteudo[] = $trilhaConteudoItem->getIdTrilha_conteudo();
        echo "<br><br><br>IdTrilha Conteudo :" . $trilhaConteudoItem->getIdTrilha_conteudo();
        $indice += 1;
    }

    // Criar sugestão

    $idTrilha = reset($codTrilhaConteudo);

    echo "<br><br><br>IdTrilha Sla:" . $idTrilha;

    $sugestao = new SugestaoConteudo();
    $sugestao->setAluno_id($idAluno);
    $sugestao->setTrilhaConteudo_id($idTrilha);
    $sugestao->setTitulo_sugerido($trilha_estudos_ia['titulo_trilha']);
    $sugestao->setMotivo($trilha_estudos_ia['motivo']);
    $sugestao->setOrigem("IA Gerado por Aluno");
    $sugestao->setAceito(2); // A decidir

    $sugestao = SugestaoConteudo::create($sugestao, $banco);

    $banco->conexao()->commit();
    echo "Sucesso";
} catch (Throwable $e) {
    echo "Erro";
    $banco->conexao()->rollBack();
}

?>