<?php
session_start();
require 'conexao.php';
include_once '../controller/monitoradorLogin.php';

// Inicializa como Visitante
$aluno_id = null;

// Verifica se o usuário está logado
if (isset($_SESSION['usuario_email'])) {
  $email = $_SESSION['usuario_email'];


  // Busca o nome do aluno pelo email
  $query = $conn->prepare("SELECT idAluno, nome FROM Aluno WHERE email = ? LIMIT 1");
  $query->bind_param("s", $email);
  $query->execute();
  $query->bind_result($idAluno, $nomeCompleto);


  if ($query->fetch()) {
    $aluno_id = $idAluno;
  }

  $query->close();

  $idTrilha = $_GET['id'];

  //echo "SELECT * FROM trilha_conteudo, trilha_estudo WHERE trilha_id = idTrilha_estudo AND aluno_id =" . $aluno_id;

  $queryTrilhasPertenceUsu = $conn->query("SELECT * FROM trilha_conteudo, trilha_estudo WHERE trilha_id = idTrilha_estudo AND trilha_id = " . $idTrilha . " AND aluno_id =" . $aluno_id);
  $trilhasPertenceUsu = $queryTrilhasPertenceUsu->fetch_all(MYSQLI_ASSOC);
  if (!$queryTrilhasPertenceUsu->num_rows > 0) {
    header("Location: telameuscursos.php");
  }

  $trilhasConteudo = $conn->query("SELECT * FROM trilha_conteudo, trilha_estudo WHERE trilha_id = idTrilha_estudo AND trilha_id = " . $idTrilha . " AND aluno_id =" . $aluno_id);

  foreach ($trilhasPertenceUsu as $trilha) {
    $titulo = $trilha['titulo'];
    $descricao = $trilha['descricao'];
  }
}


?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Minhas trilhas - Fomatis</title>
  <link rel="icon" href="imgs/favicon1.png" type="image/png">
  <link rel= "stylesheet" href= "../css/minhastrilhas.css">
  <link rel="stylesheet" href="duvidas-chat.css">
</head>
<?php

// echo "SELECT * FROM conteudo, trilha_conteudo WHERE idConteudo = trilha_id AND trilha_id = " . $idTrilha;
// $resultadoConteudoURL = $conn->query("SELECT * FROM conteudo, trilha_conteudo WHERE idConteudo = conteudo_id AND trilha_id = " . $idTrilha);
// $resultadoConteudoURLLista = $resultadoConteudoURL->fetch_all(MYSQLI_ASSOC);
// echo "let etapas[]";
// echo "etapas[";
// foreach ($resultadoConteudoURL as $res) {
//   echo "{
//           \"video\": " . "\"" . $res['link'] . "\"}\"];";
//   echo "<br><br>";
// }
// ?>

<script>
  <?php

  $resultadoConteudoURL = $conn->query("SELECT * FROM conteudo, trilha_conteudo WHERE idConteudo = conteudo_id AND trilha_id = " . $idTrilha);
  $resultadoConteudoURLLista = $resultadoConteudoURL->fetch_all(MYSQLI_ASSOC);
  echo "let etapas = []; \n";
  echo "etapas = [";

  foreach ($resultadoConteudoURL as $res) {
    echo "{
          \"video\": " . "\"" . $res['link'] . "\"}];\n ";
    $tituloConteudo = $res['titulo'];
    $linkConteudo = $res['link'];
    $conteudoIdAtual = $res['idConteudo'];
    break;
  }


  ?>

  console.log("Terminou");
</script>

<script>
  // Contexto usado pelo widget de dúvidas (duvidas-chat.js)
  window.duvidaContexto = {
    conteudo_id: <?php echo json_encode($conteudoIdAtual ?? null); ?>,
    conteudo_titulo: <?php echo json_encode($tituloConteudo ?? $titulo); ?>,
    conteudo_descricao: <?php echo json_encode($descricao ?? ''); ?>
  };
</script>

<body>

  <nav class="navbar" role="navigation" aria-label="Navegação principal">
    <div class="logo"><a href="telainicial.php" aria-label="Página inicial"><img src="imgs/logo.png" alt="logo"></a></div>
    <ul class="nav-links">
      <li><a href="telameuscursos.php">Minhas trilhas</a></li>
      <li><a href="telacursos.html">Cursos</a></li>
      <li><a href="quemsomos.html">Quem Somos</a></li>
      <li><a href="ajuda.html">Ajuda</a></li>

      <!-- Menu dropdown corrigido e alinhado -->
      <li class="dropdown">
        <a href="#" class="dropdown-toggle">Área do Aluno</a>
        <ul class="dropdown-menu">
          <li><a href="#AtualizarSenha">Atualizar senha</a></li>
          <li><a href="#AlterarCadastro">Alterar cadastro</a></li>
          <li><a href="areadoaluno.php">Acessar área do aluno</a></li>
<li><a href="logout.php">Sair</a></li>
        </ul>
      </li>
    </ul>
    <div class="search-box">
      <button class="search-toggle" aria-label="Abrir busca">🔍</button>
      <input type="text" class="search-input" placeholder="Procurar cursos..." aria-label="Campo de busca" id="searchInput">
    </div>
  </nav>

  <main class="trilha-section" id="mainContent">
    <h1><?php echo "Trilha: " . $titulo ?></h1>

    <div id="progressoTexto">Etapa <span id="etapaAtualNumero">1</span> de <span id="totalEtapas">2</span></div>

    <div class="video-tabela">
      <div class="tabela-wrapper">
        <h2 id="nomeEtapa">Etapa 1 - <?php echo $tituloConteudo ?></h2>
        <p><?php echo $descricao ?></p>
      </div>

      <div class="video-container">
        <div id="youtubePlayer"><iframe width="100%" height="360" src="https://www.youtube.com/embed/<?php echo $linkConteudo;?>?rel=0&modestbranding=1&" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen title="Vídeo da aula"></iframe></div>
      </div>
    </div>

    <div class="btn-group-navegacao">
      <button id="voltarBtn" class="btn btn-secondary hidden">← Voltar</button>
      <button id="proximaEtapaBtn" class="btn">Próxima etapa →</button>
    </div>
  </main>

  <!-- Nova tela de conclusão -->
  <section class="tela-conclusao" id="telaConclusao">
    <div class="conclusao-container">
      <h1 class="conclusao-titulo">
        <span>🎉 Parabéns!</span>
      </h1>
      <p class="conclusao-subtitulo">Você concluiu com sucesso a trilha <strong>Introdução à Programação</strong></p>

      <div class="conclusao-info">
        <div class="info-card">
          <h3>Dias de Estudo</h3>
          <p id="diasEstudo">7</p>
          <span>Dedicados ao aprendizado</span>
        </div>
        <div class="info-card">
          <h3>Aulas Concluídas</h3>
          <p id="aulasConcluidas">10</p>
          <span>Conhecimento adquirido</span>
        </div>
        <div class="info-card">
          <h3>Nível Alcançado</h3>
          <p>Iniciante</p>
          <span>Em Programação</span>
        </div>
      </div>

      <div class="conquistas-container">
        <h2 class="conquistas-titulo">Conquistas Destacadas</h2>
        <div class="conquistas-grid">
          <div class="conquista-card">
            <div class="conquista-icone">🧠</div>
            <h4>Pensamento Lógico</h4>
            <p>Você desenvolveu habilidades fundamentais de raciocínio lógico e resolução de problemas.</p>
          </div>

          <div class="conquista-card">
            <div class="conquista-icone">⚡</div>
            <h4>Fundamentos Sólidos</h4>
            <p>Domina os conceitos essenciais que formam a base de qualquer linguagem de programação.</p>
          </div>

          <div class="conquista-card">
            <div class="conquista-icone">🚀</div>
            <h4>Pronto para Avançar</h4>
            <p>Adquiriu conhecimentos suficientes para seguir para tópicos mais avançados em programação.</p>
          </div>
        </div>
      </div>

      <div class="proximos-passos">
        <h3>Próximos Passos na Sua Jornada</h3>

        <div class="passo-card">
          <div>📚</div>
          <div>
            <h4>Explore Nossas Outras Trilhas</h4>
            <p>Continue aprendendo com nossos cursos de JavaScript, Python e desenvolvimento web.</p>
          </div>
        </div>

        <div class="passo-card">
          <div>👥</div>
          <div>
            <h4>Participe da Comunidade</h4>
            <p>Conecte-se com outros alunos, tire dúvidas e compartilhe conhecimento.</p>
          </div>
        </div>

        <div class="passo-card">
          <div>💼</div>
          <div>
            <h4>Aplique Seu Conhecimento</h4>
            <p>Crie seus primeiros projetos práticos para consolidar o aprendizado.</p>
          </div>
        </div>
      </div>

      <div class="conclusao-botoes">
        <a class="btn-nova-trilha" href="questionario.php?trilha_id=<?php echo intval($idTrilha); ?>">
          📝 Fazer questionário sobre a trilha
        </a>
        <button class="btn-compartilhar" id="btnCompartilhar">
          📢 Compartilhar Conquista
        </button>
        <button class="btn-nova-trilha" id="btnNovaTrilha">
          🚀 Iniciar Nova Trilha
        </button>
        <button class="btn-voltar-inicio" id="btnVoltarInicio">
          🏠 Voltar ao Início
        </button>
      </div>
    </div>
  </section>

  <footer role="contentinfo">
    <p>&copy; 2025 Fomatis. Todos os direitos reservados.</p>
    <p><a href="#ajuda">Ajuda</a> | <a href="quemsomos.html">Quem Somos</a> | <a href="#termos">Termos de Uso</a> | <a href="#privacidade">Política de Privacidade</a></p>
  </footer>
<script src="../js/minhastrilhas.js"></script>
</body>

</html>