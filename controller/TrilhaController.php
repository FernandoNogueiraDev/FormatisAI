<?php
namespace controller;

use IA\testeIA;
use model\Conteudo;
use model\Fonte;
require_once ($_SERVER['DOCUMENT_ROOT'] . '/IA/testeIA.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/model/Conteudo.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/model/Fonte.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/model/TrilhaConteudo.php');

// Execute the Python script and capture its output
// $output = shell_exec("python ./IA/teste.py");

/*
 * $consulta_ia = "{
 * \"trilha_estudos\": {
 * \"duracao_total_trilha\": \"Duração Total\",
 * \"conteudo\": [
 * {
 * \"titulo\": \"titulo1\",
 * \"descricao\": \"descricao1\",
 * \"link_referencia\": \"link_referencia1\",
 * \"fonte\": \"fonte1\",
 * \"duracao\": \"duracao1\"
 * },
 * {
 * \"titulo\": \"titulo2\",
 * \"descricao\": \"descricao2\",
 * \"link_referencia\": \"link_referencia2\",
 * \"fonte\": \"fonte2\",
 * \"duracao\": \"duracao2\"
 * }
 * ]
 * }
 * }";
 */

$idAluno = $POST["idAluno"];
$TituloTrilha = $POST["TituloTrilha"];

$json_ia = testeIA::montaTrilha("");

echo "<br>";

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

    echo "<br><br>";
    echo "<br><br>";
    echo "<br><br>";

    $trilha_estudos_ia = $json_decodificado['trilha_estudos'];

    echo "<br><br>JSON decodificado: <br>";

    echo "Trilha estudos:<br>";

    echo "<br>duracao_total_trilha: " . $trilha_estudos_ia['duracao_total_trilha'];

    $listaConteudosTrilha[];

    foreach ($trilha_estudos_ia['conteudo'] as $conteudo_trilha) {

        echo "<br><br>";

        echo "<br>titulo: " . $conteudo_trilha['titulo'];

        echo "<br>descricao: " . $conteudo_trilha['descricao'];

        echo "<br>link_referencia: " . $conteudo_trilha['link_referencia'];

        echo "<br>fonte: " . $conteudo_trilha['fonte'];

        echo "<br>duracao: " . $conteudo_trilha['duracao'];

        $conteudo = Conteudo::constroiVazio();

        $conteudo->setTitulo($conteudo_trilha['titulo']);
        $conteudo->setDescricao($conteudo_trilha['descricao']);
        $conteudo->setCategoria($conteudo_trilha['tipo_conteudo']);
        $conteudo->setLink($conteudo_trilha['link_referencia']);
        $conteudo->setDuracao_min($conteudo_trilha['duracao']);

        $fonte = Fonte::selectPorNome($conteudo_trilha['fonte']);

        if (! is_null($fonte)) {
            $conteudo->setfonte_id($fonte[0]->getIdFonte());
        } else {
            $fonteNova = Fonte::constroiVazio();
            $fonteNova->setNome_fonte($conteudo_trilha['fonte']);
            $fonteNova = Fonte::create($fonteNova);
            $conteudo->setfonte_id($fonteNova->getIdFonte());
        }

        $conteudo = Conteudo::create($conteudo);

        array_push($listaConteudosTrilha, $conteudo);
    }

    $trilhaEstudo = new TrilhaEstudo();

    $trilhaEstudo->setAluno_id($idAluno);
    $trilhaEstudo->setTitulo($TituloTrilha);
    $trilhaEstudo->setDescricao("decricaoTrilha");
    $trilhaEstudo->setStatus("pausada");

    $trilhaEstudo = TrilhaEstudo::create();

    $indice = 1;
    $codTrilhaConteudo;
    foreach ($listaConteudo['conteudo'] as $itemConteudo) {
        $trilhaConteudoItem = new TrilhaConteudo();

        $trilhaConteudoItem->setTrilha_id($trilhaEstudo->getIdTrilha_estudo());
        $trilhaConteudoItem->setConteudo_id($itemConteudo->getIdConteudo());
        $trilhaConteudoItem->setOrdem($indice);
        $trilhaConteudoItem->setObrigatorio(0);
        $trilhaConteudoItem->setEstimativa_min($itemConteudo->getDuracao_min());

        $trilhaConteudoItem = TrilhaConteudo::create($trilhaConteudoItem);
        $indice += 1;
    }
    
    $codTrilhaConteudo = $trilhaConteudoItem->getIdTrilha_conteudo();
    
    
    
} catch (Throwable $e) {
    echo "Um Erro Aconteceu!: <br>";
    echo $e->getMessage();
}

?>