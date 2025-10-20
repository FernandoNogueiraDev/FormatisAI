<?php
namespace controller;
session_start();

use Exception;

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

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');


// Verificar a ação solicitada
$action = $_GET['action'] ?? $_POST['action'] ?? '';

$banco = new Banco();
try {
    switch ($action) {
        case 'getTrilha':
            getTrilha($banco);
            break;

        case 'salvarProgresso':
            salvarProgresso($banco);
            break;

        case 'gerarTrilha':
            // Seu código original de gerar trilha
            gerarTrilha($banco);
            break;

        default:
            echo json_encode([
                'success' => false,
                'message' => 'Ação não especificada'
            ]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro: ' . $e->getMessage()
    ]);
}

// Função para buscar trilha específica
function getTrilha($banco)
{
    $trilhaId = $_GET['id'] ?? null;

    if (! $trilhaId) {
        echo json_encode([
            'success' => false,
            'message' => 'ID da trilha não fornecido'
        ]);
        return;
    }

    try {
        // Buscar dados da trilha
        $trilha = TrilhaEstudo::selectPorId($trilhaId);

        if (! $trilha) {
            echo json_encode([
                'success' => false,
                'message' => 'Trilha não encontrada'
            ]);
            return;
        }

        // Buscar conteúdos da trilha
        $conteudosTrilha = TrilhaConteudo::selectPorId($trilhaId);

        // Organizar em etapas (você pode adaptar esta lógica conforme sua estrutura)
        $etapas = organizarEmEtapas($conteudosTrilha);

        echo json_encode([
            'success' => true,
            'trilha' => [
                'id' => $trilha->getIdTrilha_estudo(),
                'titulo' => $trilha->getTitulo(),
                'descricao' => $trilha->getDescricao(),
                'status' => $trilha->getStatus()
            ],
            'etapas' => $etapas
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao buscar trilha: ' . $e->getMessage()
        ]);
    }
}

// Função para organizar conteúdos em etapas
function organizarEmEtapas($conteudosTrilha)
{
    $etapas = [];

    // Agrupar por ordem (cada ordem pode ser uma etapa)
    $grupos = [];
    foreach ($conteudosTrilha as $conteudoTrilha) {
        $ordem = $conteudoTrilha->getOrdem();
        if (! isset($grupos[$ordem])) {
            $grupos[$ordem] = [];
        }
        $grupos[$ordem][] = $conteudoTrilha;
    }

    // Criar estrutura de etapas
    foreach ($grupos as $ordem => $conteudos) {
        $etapa = [
            'nome' => "Etapa $ordem",
            'video' => extrairVideoId($conteudos), // Extrair ID do YouTube se existir
            'aulas' => []
        ];

        foreach ($conteudos as $conteudoTrilha) {
            $conteudo = Conteudo::selectPorId($conteudoTrilha->getConteudo_id());
            if ($conteudo) {
                $etapa['aulas'][] = [
                    'titulo' => $conteudo->getTitulo(),
                    'objetivo' => $conteudo->getCategoria() . ' - ' . $conteudoTrilha->getEstimativa_min() . 'min',
                    'tipo' => $conteudo->getCategoria(),
                    'duracao' => $conteudoTrilha->getEstimativa_min()
                ];
            }
        }

        $etapas[] = $etapa;
    }

    return $etapas;
}

// Função para extrair ID do YouTube do link
function extrairVideoId($conteudos)
{
    foreach ($conteudos as $conteudoTrilha) {
        $conteudo = Conteudo::selectPorId($conteudoTrilha->getConteudo_id());
        if ($conteudo && strpos($conteudo->getLink(), 'youtube.com') !== false) {
            // Extrair ID do vídeo do YouTube
            $url = $conteudo->getLink();
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
            return $matches[1] ?? null;
        }
    }
    return null;
}

// Função para salvar progresso
function salvarProgresso($banco)
{
    $input = json_decode(file_get_contents('php://input'), true);

    $trilhaId = $input['trilha_id'] ?? null;
    $etapaAtual = $input['etapa_atual'] ?? null;
    $concluido = $input['concluido'] ?? false;

    if (! $trilhaId) {
        echo json_encode([
            'success' => false,
            'message' => 'ID da trilha não fornecido'
        ]);
        return;
    }

    try {
        // Aqui você pode implementar a lógica para salvar o progresso no banco
        // Por exemplo, atualizar uma tabela de progresso_trilha

        // Por enquanto, vamos apenas retornar sucesso
        echo json_encode([
            'success' => true,
            'message' => 'Progresso salvo com sucesso',
            'data' => [
                'trilha_id' => $trilhaId,
                'etapa_atual' => $etapaAtual,
                'concluido' => $concluido
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao salvar progresso: ' . $e->getMessage()
        ]);
    }
}

function gerarTrilha($banco)
{
    $idAluno = $_SESSION["idAluno"];
    $duracao = $_POST['duracao'];
    $tipo_conteudo = $_POST['tipo'];
    $TituloTrilha = $_POST['tema'];
    $Nivel = $_POST['nivel'];
    $tipo_aluno = "autodidata";

    $duracaoTrilha = "";

    switch ($duracao) {
        case "curto":
            $duracaoTrilha = "10-15 minutos";
        case "medio":
            $duracaoTrilha = "20-25 minutos";
        case "longo":
            $duracaoTrilha = "30+ minutos";
    }

    $aluno = Aluno::selectPorId($idAluno);

    $json_php = [];

    if ($aluno->getTipo_aluno() == "autodidata") {
        $json_php = [
            "tipo_aluno" => "Autodidata",
            "nome" => $aluno->getNome(),
            "idade" => $aluno->calculaIdade(),
            "interesse" => "Estudar",
            "duracao_trilha" => $duracaoTrilha,
            "objetivo_carreira" => $TituloTrilha,
            "nivel" => $Nivel,
            "tipo_conteudo" => [
                "vídeo"
            ]
        ];
    } else {
        $json_php = [
            "tipo_aluno" => "Escolar",
            "nome" => $aluno->getNome(),
            "idade" => $aluno->calculaIdade(),
            "duracao_trilha" => $duracaoTrilha,
            // Pegar do histórico escolar, implementações futuras
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

    $jsonString = json_encode($json_php);

    $start = microtime(true);
    $json_ia = TrilhaIA::montaTrilha($jsonString);
    $time_elapsed_secs = microtime(true) - $start;

    $json_decodificado = json_decode($json_ia, true, 512, null);

    try {
        $banco->conexao()->beginTransaction();

        $trilha_estudos_ia = $json_decodificado['trilha_estudos'];
        $listaConteudosTrilha = [];

        foreach ($trilha_estudos_ia['conteudo'] as $conteudo_trilha) {
            $conteudo = Conteudo::constroiVazio();
            $conteudo->setTitulo($conteudo_trilha['titulo']);
            $conteudo->setCategoria($conteudo_trilha['tipo_conteudo']);
            $conteudo->setDificuldade($Nivel);
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
            $indice += 1;
        }

        $idTrilha = reset($codTrilhaConteudo);

        $sugestao = new SugestaoConteudo();
        $sugestao->setAluno_id($idAluno);
        $sugestao->setTrilhaConteudo_id($idTrilha);
        $sugestao->setTitulo_sugerido($trilha_estudos_ia['titulo_trilha']);
        $sugestao->setMotivo($trilha_estudos_ia['motivo']);
        $sugestao->setOrigem("IA Gerado por Aluno");
        $sugestao->setAceito(1);

        $sugestao = SugestaoConteudo::create($sugestao, $banco);

        $banco->conexao()->commit();

        http_response_code(200);
        // Retornar o ID da trilha criada
        echo json_encode([
            'success' => true,
            'trilha_id' => $trilhaEstudo->getIdTrilha_estudo(),
            'message' => 'Trilha gerada com sucesso'
        ]);
    } catch (Throwable $e) {
        $banco->conexao()->rollBack();
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao gerar trilha: ' . $e->getMessage()
        ]);
    }
}
?>