<?php
namespace controller;
session_start();

use Exception;
use IA\QuestionarioIA;
use model\Banco;

require_once ($_SERVER['DOCUMENT_ROOT'] . '/IA/QuestionarioIA.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/model/Banco.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$banco = new Banco();

try {
    switch ($action) {
        case 'gerarQuestionario':
            gerarQuestionario($banco);
            break;

        case 'responderQuestao':
            responderQuestao($banco);
            break;

        case 'getQuestionario':
            getQuestionario($banco);
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

function gerarQuestionario($banco)
{
    if (!isset($_SESSION["idAluno"])) {
        echo json_encode(['success' => false, 'message' => 'Aluno não autenticado']);
        return;
    }

    $idAluno = $_SESSION["idAluno"];

    $dadosEntrada = json_decode(file_get_contents('php://input'), true);
    if ($dadosEntrada === null) {
        $dadosEntrada = $_POST;
    }

    $trilhaId = $dadosEntrada['trilha_id'] ?? null;
    $conteudoId = $dadosEntrada['conteudo_id'] ?? null;
    $conteudos = $dadosEntrada['conteudos'] ?? []; // [{titulo, descricao}, ...]
    $numMultipla = intval($dadosEntrada['num_multipla'] ?? 3);
    $numAbertas = intval($dadosEntrada['num_abertas'] ?? 2);

    if (empty($conteudos)) {
        echo json_encode(['success' => false, 'message' => 'Nenhum conteúdo informado']);
        return;
    }

    $respostaIA = QuestionarioIA::gerar($conteudos, $numMultipla, $numAbertas);
    $respostaDecodificada = json_decode($respostaIA, true);

    if (!$respostaDecodificada || isset($respostaDecodificada['erro']) || !isset($respostaDecodificada['questoes'])) {
        echo json_encode([
            'success' => false,
            'message' => $respostaDecodificada['erro'] ?? 'Não foi possível gerar o questionário'
        ]);
        return;
    }

    $conexao = $banco->conexao();

    try {
        $conexao->beginTransaction();

        $stmtQuestionario = $conexao->prepare(
            "INSERT INTO questionario (aluno_id, trilha_id, conteudo_id) VALUES (:aluno_id, :trilha_id, :conteudo_id)"
        );
        $stmtQuestionario->execute([
            ':aluno_id' => $idAluno,
            ':trilha_id' => $trilhaId,
            ':conteudo_id' => $conteudoId
        ]);
        $idQuestionario = $conexao->lastInsertId();

        $stmtQuestao = $conexao->prepare(
            "INSERT INTO questao (questionario_id, tipo, enunciado, alternativas, resposta_correta, criterio_correcao)
             VALUES (:questionario_id, :tipo, :enunciado, :alternativas, :resposta_correta, :criterio_correcao)"
        );

        $questoesParaFrontend = [];

        foreach ($respostaDecodificada['questoes'] as $questao) {
            $tipo = $questao['tipo'] === 'multipla_escolha' ? 'multipla_escolha' : 'aberta';

            $stmtQuestao->execute([
                ':questionario_id' => $idQuestionario,
                ':tipo' => $tipo,
                ':enunciado' => $questao['enunciado'],
                ':alternativas' => $tipo === 'multipla_escolha' ? json_encode($questao['alternativas'], JSON_UNESCAPED_UNICODE) : null,
                ':resposta_correta' => $tipo === 'multipla_escolha' ? $questao['resposta_correta'] : null,
                ':criterio_correcao' => $tipo === 'aberta' ? ($questao['criterio_correcao'] ?? '') : null
            ]);
            $idQuestao = $conexao->lastInsertId();

            // não devolve resposta_correta/criterio_correcao pro front-end
            $questaoFrontend = [
                'id' => $idQuestao,
                'tipo' => $tipo,
                'enunciado' => $questao['enunciado']
            ];
            if ($tipo === 'multipla_escolha') {
                $questaoFrontend['alternativas'] = $questao['alternativas'];
            }
            $questoesParaFrontend[] = $questaoFrontend;
        }

        $conexao->commit();

        echo json_encode([
            'success' => true,
            'questionario_id' => $idQuestionario,
            'questoes' => $questoesParaFrontend
        ]);
    } catch (Exception $e) {
        $conexao->rollBack();
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar questionário: ' . $e->getMessage()]);
    }
}

function responderQuestao($banco)
{
    if (!isset($_SESSION["idAluno"])) {
        echo json_encode(['success' => false, 'message' => 'Aluno não autenticado']);
        return;
    }

    $idAluno = $_SESSION["idAluno"];

    $dadosEntrada = json_decode(file_get_contents('php://input'), true);
    if ($dadosEntrada === null) {
        $dadosEntrada = $_POST;
    }

    $questaoId = $dadosEntrada['questao_id'] ?? null;
    $respostaAluno = trim($dadosEntrada['resposta'] ?? '');

    if (!$questaoId || $respostaAluno === '') {
        echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
        return;
    }

    $conexao = $banco->conexao();

    $stmtQuestao = $conexao->prepare("SELECT * FROM questao WHERE idQuestao = :id");
    $stmtQuestao->execute([':id' => $questaoId]);
    $questao = $stmtQuestao->fetch(\PDO::FETCH_ASSOC);

    if (!$questao) {
        echo json_encode(['success' => false, 'message' => 'Questão não encontrada']);
        return;
    }

    $correta = null;
    $feedback = null;

    if ($questao['tipo'] === 'multipla_escolha') {
        $correta = intval($respostaAluno) === intval($questao['resposta_correta']);
        $feedback = $correta ? 'Resposta correta!' : 'Resposta incorreta.';
    } else {
        // questão aberta: pede pra IA corrigir
        $respostaIA = QuestionarioIA::corrigirAberta(
            $questao['enunciado'],
            $questao['criterio_correcao'] ?? '',
            $respostaAluno
        );
        $respostaDecodificada = json_decode($respostaIA, true);

        if (!$respostaDecodificada || isset($respostaDecodificada['erro'])) {
            echo json_encode([
                'success' => false,
                'message' => $respostaDecodificada['erro'] ?? 'Não foi possível corrigir a resposta'
            ]);
            return;
        }

        $correta = (bool) $respostaDecodificada['correta'];
        $feedback = $respostaDecodificada['feedback'];
    }

    $stmtResposta = $conexao->prepare(
        "INSERT INTO resposta_aluno (questao_id, aluno_id, resposta_texto, correta, feedback_ia)
         VALUES (:questao_id, :aluno_id, :resposta_texto, :correta, :feedback_ia)"
    );
    $stmtResposta->execute([
        ':questao_id' => $questaoId,
        ':aluno_id' => $idAluno,
        ':resposta_texto' => $respostaAluno,
        ':correta' => $correta ? 1 : 0,
        ':feedback_ia' => $feedback
    ]);

    echo json_encode([
        'success' => true,
        'correta' => $correta,
        'feedback' => $feedback
    ]);
}

function getQuestionario($banco)
{
    $idQuestionario = $_GET['id'] ?? null;

    if (!$idQuestionario) {
        echo json_encode(['success' => false, 'message' => 'ID do questionário não informado']);
        return;
    }

    $conexao = $banco->conexao();
    $stmt = $conexao->prepare(
        "SELECT idQuestao, tipo, enunciado, alternativas FROM questao WHERE questionario_id = :id"
    );
    $stmt->execute([':id' => $idQuestionario]);
    $questoes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($questoes as &$q) {
        if ($q['alternativas']) {
            $q['alternativas'] = json_decode($q['alternativas'], true);
        }
    }

    echo json_encode(['success' => true, 'questoes' => $questoes]);
}

?>
