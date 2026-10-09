<?php
namespace controller;
session_start();

use Exception;
use IA\DuvidaIA;
use model\Banco;

require_once ($_SERVER['DOCUMENT_ROOT'] . '/IA/DuvidaIA.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/model/Banco.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$banco = new Banco();

try {
    switch ($action) {
        case 'perguntar':
            perguntar($banco);
            break;

        case 'historico':
            historico($banco);
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

function perguntar($banco)
{
    if (!isset($_SESSION["idAluno"])) {
        echo json_encode(['success' => false, 'message' => 'Aluno não autenticado']);
        return;
    }

    $idAluno = $_SESSION["idAluno"];

    $dadosEntrada = json_decode(file_get_contents('php://input'), true);
    if ($dadosEntrada === null) {
        // fallback para requisições x-www-form-urlencoded / multipart
        $dadosEntrada = $_POST;
    }

    $conteudoId = $dadosEntrada['conteudo_id'] ?? null;
    $conteudoTitulo = $dadosEntrada['conteudo_titulo'] ?? '';
    $conteudoDescricao = $dadosEntrada['conteudo_descricao'] ?? '';
    $pergunta = trim($dadosEntrada['pergunta'] ?? '');
    $historico = $dadosEntrada['historico'] ?? [];

    if ($pergunta === '') {
        echo json_encode(['success' => false, 'message' => 'Pergunta vazia']);
        return;
    }

    $payload = [
        'conteudo_titulo' => $conteudoTitulo,
        'conteudo_descricao' => $conteudoDescricao,
        'pergunta' => $pergunta,
        'historico' => $historico
    ];

    $respostaIA = DuvidaIA::perguntar($payload);
    $respostaDecodificada = json_decode($respostaIA, true);

    if (!$respostaDecodificada || isset($respostaDecodificada['erro'])) {
        echo json_encode([
            'success' => false,
            'message' => $respostaDecodificada['erro'] ?? 'Não foi possível obter resposta da IA'
        ]);
        return;
    }

    $resposta = $respostaDecodificada['resposta'];

    // salva o histórico da conversa no banco
    $stmt = $banco->conexao()->prepare(
        "INSERT INTO duvida_chat (aluno_id, conteudo_id, pergunta, resposta) VALUES (:aluno_id, :conteudo_id, :pergunta, :resposta)"
    );
    $stmt->execute([
        ':aluno_id' => $idAluno,
        ':conteudo_id' => $conteudoId,
        ':pergunta' => $pergunta,
        ':resposta' => $resposta
    ]);

    echo json_encode([
        'success' => true,
        'resposta' => $resposta
    ]);
}

function historico($banco)
{
    if (!isset($_SESSION["idAluno"])) {
        echo json_encode(['success' => false, 'message' => 'Aluno não autenticado']);
        return;
    }

    $idAluno = $_SESSION["idAluno"];
    $conteudoId = $_GET['conteudo_id'] ?? null;

    $stmt = $banco->conexao()->prepare(
        "SELECT pergunta, resposta, criado_em FROM duvida_chat
         WHERE aluno_id = :aluno_id AND conteudo_id = :conteudo_id
         ORDER BY criado_em ASC"
    );
    $stmt->execute([
        ':aluno_id' => $idAluno,
        ':conteudo_id' => $conteudoId
    ]);

    echo json_encode([
        'success' => true,
        'historico' => $stmt->fetchAll(\PDO::FETCH_ASSOC)
    ]);
}

?>
