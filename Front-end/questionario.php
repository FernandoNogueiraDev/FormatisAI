<?php
session_start();
require 'conexao.php';
include_once '../controller/monitoradorLogin.php';

$idAluno = $_SESSION['idAluno'];
$idTrilha = intval($_GET['trilha_id'] ?? 0);

if (!$idTrilha) {
    header("Location: telameuscursos.php");
    exit;
}

// confere que a trilha pertence ao aluno logado e busca os conteúdos dela
$stmt = $conn->prepare(
    "SELECT c.titulo, c.categoria
     FROM trilha_conteudo tc
     INNER JOIN conteudo c ON c.idConteudo = tc.conteudo_id
     INNER JOIN trilha_estudo te ON te.idTrilha_estudo = tc.trilha_id
     WHERE tc.trilha_id = ? AND te.aluno_id = ?
     ORDER BY tc.ordem ASC"
);
$stmt->bind_param("ii", $idTrilha, $idAluno);
$stmt->execute();
$resultado = $stmt->get_result();
$conteudos = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (empty($conteudos)) {
    header("Location: telameuscursos.php");
    exit;
}

$conteudosJson = json_encode(array_map(function ($c) {
    return [
        'titulo' => $c['titulo'],
        'descricao' => $c['categoria'] ? ("Conteúdo do tipo " . $c['categoria']) : ""
    ];
}, $conteudos), JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Questionário - FormatisAI</title>
  <link rel="icon" href="imgs/favicon1.png" type="image/png">
  <style>
    :root {
      --primary-color: #3d4ff7;
      --secondary-color: #ffca28;
      --light-color: #eef2ff;
      --dark-color: #333;
      --success-color: #2ecc71;
      --error-color: #e74c3c;
    }
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f9f9f9;
      color: var(--dark-color);
      margin: 0;
      padding: 24px;
    }
    .container {
      max-width: 700px;
      margin: 0 auto;
    }
    h1 {
      color: var(--primary-color);
    }
    #status {
      text-align: center;
      padding: 40px 0;
      color: #777;
    }
    .questao-card {
      background: #fff;
      border: 1px solid #eee;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 18px;
    }
    .questao-enunciado {
      font-weight: 600;
      margin-bottom: 12px;
    }
    .alternativa {
      display: block;
      padding: 10px 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      margin-bottom: 8px;
      cursor: pointer;
    }
    .alternativa:hover {
      background-color: var(--light-color);
    }
    .alternativa input {
      margin-right: 8px;
    }
    textarea.resposta-aberta {
      width: 100%;
      box-sizing: border-box;
      min-height: 80px;
      border-radius: 8px;
      border: 1px solid #ddd;
      padding: 10px;
      font-family: inherit;
      font-size: 14px;
    }
    .btn-responder {
      margin-top: 10px;
      background-color: var(--primary-color);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 8px 16px;
      cursor: pointer;
      font-weight: 600;
    }
    .btn-responder:disabled {
      opacity: 0.5;
      cursor: default;
    }
    .feedback {
      margin-top: 10px;
      padding: 10px 12px;
      border-radius: 8px;
      font-size: 14px;
    }
    .feedback.correta {
      background-color: #eafaf1;
      color: var(--success-color);
      border: 1px solid var(--success-color);
    }
    .feedback.incorreta {
      background-color: #fdecea;
      color: var(--error-color);
      border: 1px solid var(--error-color);
    }
    a.voltar {
      display: inline-block;
      margin-top: 24px;
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Questionário da trilha</h1>
    <p>Um mix de perguntas de múltipla escolha e dissertativas para fixar o que você acabou de estudar.</p>

    <div id="status">Gerando seu questionário com a IA...</div>
    <div id="listaQuestoes"></div>

    <a class="voltar" href="minhastrilhas.php?id=<?php echo $idTrilha; ?>">&larr; Voltar para a trilha</a>
  </div>

  <script>
    const conteudos = <?php echo $conteudosJson; ?>;
    const trilhaId = <?php echo $idTrilha; ?>;

    async function gerarQuestionario() {
      try {
        const resp = await fetch('../controller/QuestionarioController.php?action=gerarQuestionario', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            trilha_id: trilhaId,
            conteudos: conteudos,
            num_multipla: 3,
            num_abertas: 2
          })
        });
        const dados = await resp.json();

        document.getElementById('status').remove();

        if (!dados.success) {
          document.getElementById('listaQuestoes').innerHTML =
            '<p>Não foi possível gerar o questionário agora: ' + (dados.message || '') + '</p>';
          return;
        }

        renderizarQuestoes(dados.questoes);
      } catch (err) {
        document.getElementById('status').textContent = 'Erro de conexão ao gerar o questionário.';
      }
    }

    function renderizarQuestoes(questoes) {
      const lista = document.getElementById('listaQuestoes');

      questoes.forEach((questao, index) => {
        const card = document.createElement('div');
        card.className = 'questao-card';

        let corpoHtml = `<div class="questao-enunciado">${index + 1}. ${escapeHtml(questao.enunciado)}</div>`;

        if (questao.tipo === 'multipla_escolha') {
          questao.alternativas.forEach((alt, i) => {
            corpoHtml += `
              <label class="alternativa">
                <input type="radio" name="questao-${questao.id}" value="${i}">
                ${escapeHtml(alt)}
              </label>`;
          });
        } else {
          corpoHtml += `<textarea class="resposta-aberta" placeholder="Digite sua resposta..."></textarea>`;
        }

        corpoHtml += `<button class="btn-responder">Responder</button><div class="feedback-container"></div>`;

        card.innerHTML = corpoHtml;
        lista.appendChild(card);

        card.querySelector('.btn-responder').addEventListener('click', () => {
          responderQuestao(card, questao);
        });
      });
    }

    function escapeHtml(texto) {
      const div = document.createElement('div');
      div.textContent = texto;
      return div.innerHTML;
    }

    async function responderQuestao(card, questao) {
      let resposta;
      if (questao.tipo === 'multipla_escolha') {
        const selecionada = card.querySelector('input[type="radio"]:checked');
        if (!selecionada) {
          alert('Selecione uma alternativa antes de responder.');
          return;
        }
        resposta = selecionada.value;
      } else {
        resposta = card.querySelector('.resposta-aberta').value.trim();
        if (!resposta) {
          alert('Escreva uma resposta antes de enviar.');
          return;
        }
      }

      const btn = card.querySelector('.btn-responder');
      btn.disabled = true;
      btn.textContent = 'Corrigindo...';

      try {
        const resp = await fetch('../controller/QuestionarioController.php?action=responderQuestao', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            questao_id: questao.id,
            resposta: resposta
          })
        });
        const dados = await resp.json();

        const container = card.querySelector('.feedback-container');
        if (dados.success) {
          container.innerHTML = `<div class="feedback ${dados.correta ? 'correta' : 'incorreta'}">${escapeHtml(dados.feedback)}</div>`;
        } else {
          container.innerHTML = `<div class="feedback incorreta">${escapeHtml(dados.message || 'Erro ao corrigir')}</div>`;
        }
      } catch (err) {
        card.querySelector('.feedback-container').innerHTML =
          '<div class="feedback incorreta">Erro de conexão ao corrigir a resposta.</div>';
      } finally {
        btn.textContent = 'Respondido';
      }
    }

    gerarQuestionario();
  </script>
</body>
</html>
