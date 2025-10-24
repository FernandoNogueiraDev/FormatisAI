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
  <style>
    :root {
      --primary-color: #3d4ff7;
      --secondary-color: #ffca28;
      --light-color: #eef2ff;
      --dark-color: #333;
      --gray-color: #f5f5f5;
      --success-color: #4CAF50;
      --border-radius: 12px;
      --transition: all 0.3s ease;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f9f9f9;
      color: #333;
      line-height: 1.6;
    }

    /* Navbar */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: auto;
      padding: 10px 5%;
      background-color: var(--gray-color);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .logo img {
      height: 55px;
      width: auto;
    }

    .nav-links {
      display: flex;
      list-style: none;
      margin: 0;
      padding: 0;
      align-items: center;
    }

    .nav-links li {
      margin: 0 15px;
      position: relative;
    }

    .nav-links a {
      text-decoration: none;
      color: var(--primary-color);
      font-weight: 600;
      transition: var(--transition);
      padding: 8px 0;
      position: relative;
      display: inline-block;
    }

    .nav-links a:hover {
      color: var(--secondary-color);
    }

    .nav-links a::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: 0;
      left: 0;
      background-color: var(--secondary-color);
      transition: var(--transition);
    }

    .nav-links a:hover::after {
      width: 100%;
    }

    /* Dropdown Menu Corrigido */
    .dropdown {
      position: relative;
    }

    .dropdown-toggle {
      display: flex;
      align-items: center;
      gap: 4px;
      cursor: pointer;
    }

    .dropdown-toggle::after {
      content: "▼";
      font-size: 10px;
      transition: transform 0.3s ease;
      margin-left: 2px;
    }

    .dropdown:hover .dropdown-toggle::after {
      transform: rotate(180deg);
    }

    .dropdown-menu {
      list-style: none;
      padding: 8px 0;
      margin: 0;
      position: absolute;
      top: 100%;
      left: 0;
      background-color: #fff;
      min-width: 200px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      z-index: 1000;
      border: 1px solid #eee;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.3s ease;
    }

    .dropdown:hover .dropdown-menu {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .dropdown-menu li {
      border-bottom: 1px solid #f0f0f0;
    }

    .dropdown-menu li:last-child {
      border-bottom: none;
    }

    .dropdown-menu li a {
      display: block;
      padding: 10px 16px;
      color: var(--primary-color);
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s ease;
      position: relative;
    }

    .dropdown-menu li a::after {
      content: '';
      position: absolute;
      width: 0;
      height: 100%;
      top: 0;
      left: 0;
      background-color: var(--light-color);
      transition: width 0.2s ease;
      z-index: -1;
    }

    .dropdown-menu li a:hover {
      color: var(--secondary-color);
    }

    .dropdown-menu li a:hover::after {
      width: 100%;
    }

    .search-box {
      position: relative;
      display: flex;
      align-items: center;
    }

    .search-toggle {
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: var(--primary-color);
      padding: 8px;
      border-radius: 50%;
      transition: var(--transition);
    }

    .search-toggle:hover {
      background-color: rgba(61, 79, 247, 0.1);
    }

    .search-input {
      width: 0;
      opacity: 0;
      padding: 10px 15px;
      border: 1px solid #ddd;
      border-radius: 30px;
      outline: none;
      transition: var(--transition);
      margin-left: 10px;
      font-size: 14px;
    }

    .search-box.active .search-input {
      width: 250px;
      opacity: 1;
    }

    /* Trilha */
    .trilha-section {
      padding: 40px 5%;
      text-align: center;
      background-color: var(--light-color);
      min-height: calc(100vh - 160px);
    }

    .trilha-section h1 {
      color: var(--primary-color);
      margin-bottom: 10px;
      font-size: 2.2rem;
    }

    .trilha-section p {
      font-size: 18px;
      color: var(--dark-color);
      max-width: 800px;
      margin: 0 auto 30px;
    }

    /* Número de etapa */
    #progressoTexto {
      margin: 0 0 10px;
      color: var(--dark-color);
      font-weight: 600;
    }

    /* Video e tabela lado a lado */
    .video-tabela {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      margin: 30px auto 0;
      gap: 30px;
      max-width: 1200px;
    }

    .video-container {
      flex: 1;
      min-width: 300px;
      position: relative;
      height: 360px;
    }

    #youtubePlayer {
      width: 100%;
      height: 100%;
      border-radius: var(--border-radius);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      background-color: #000;
      border: none;
    }

    .video-loading {
      display: flex;
      justify-content: center;
      align-items: center;
      color: white;
      font-size: 18px;
      height: 100%;
      background: #000;
      border-radius: var(--border-radius);
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 2;
    }

    .video-loading.hidden {
      display: none;
    }

    /* Tabela */
    .tabela-wrapper {
      background-color: #fff;
      padding: 20px;
      border-radius: var(--border-radius);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      min-width: 300px;
      max-width: 500px;
      flex: 1;
      height: 360px;
      overflow: hidden;
    }

    .tabela-wrapper h2 {
      color: var(--primary-color);
      margin-top: 0;
      padding-bottom: 15px;
      border-bottom: 1px solid #eee;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      table-layout: fixed;
    }

    table th,
    table td {
      border: 1px solid var(--primary-color);
      padding: 10px 12px;
      text-align: left;
      word-wrap: break-word;
      overflow-wrap: break-word;
      white-space: normal;
    }

    table th {
      background-color: var(--primary-color);
      color: white;
    }

    table tr.completed td:first-child::after {
      content: '✓';
      position: absolute;
      right: 10px;
      color: var(--success-color);
      font-weight: bold;
    }

    /* Define largura fixa para as colunas e evita overflow */
    #tabelaAulas th:first-child,
    #tabelaAulas td:first-child {
      width: 35%;
      max-width: 35%;
    }

    #tabelaAulas th:last-child,
    #tabelaAulas td:last-child {
      width: 65%;
      max-width: 65%;
    }

    /* Botões */
    .btn-group-navegacao {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 30px;
    }

    .btn {
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      background-color: var(--primary-color);
      color: white;
      font-weight: bold;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: var(--transition);
    }

    .btn:hover {
      background-color: #2a3bd4;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn:active {
      transform: translateY(0);
    }

    .btn.hidden {
      display: none;
    }

    .btn-secondary {
      background-color: #6c757d;
    }

    .btn-secondary:hover {
      background-color: #5a6268;
    }

    /* Estilos para os resultados da pesquisa */
    .curso-card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      width: 300px;
      text-align: left;
      margin: 10px;
    }

    .categoria-tag {
      background: var(--light-color);
      padding: 5px 10px;
      border-radius: 20px;
      display: inline-block;
      font-size: 0.8rem;
      color: var(--primary-color);
      margin-bottom: 10px;
    }

    /* Tela de Conclusão */
    .tela-conclusao {
      display: none;
      padding: 40px 5%;
      text-align: center;
      background: linear-gradient(135deg, var(--light-color) 0%, #ffffff 100%);
      min-height: calc(100vh - 160px);
    }

    .conclusao-container {
      max-width: 900px;
      margin: 0 auto;
      background: white;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      position: relative;
      overflow: hidden;
    }

    .conclusao-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 8px;
      background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    }

    .conclusao-titulo {
      color: var(--primary-color);
      font-size: 2.5rem;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 15px;
    }

    .conclusao-subtitulo {
      color: var(--dark-color);
      font-size: 1.2rem;
      margin-bottom: 30px;
    }

    .conclusao-info {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
      margin: 30px 0;
    }

    .info-card {
      background: var(--light-color);
      padding: 20px;
      border-radius: 12px;
      min-width: 200px;
      flex: 1;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .info-card h3 {
      color: var(--primary-color);
      margin-top: 0;
      font-size: 1.1rem;
    }

    .info-card p {
      font-size: 1.8rem;
      font-weight: bold;
      margin: 10px 0;
      color: var(--dark-color);
    }

    /* Conquistas */
    .conquistas-container {
      margin: 40px 0;
      padding: 30px;
      background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
      border-radius: 15px;
      border: 2px dashed var(--primary-color);
      position: relative;
    }

    .conquistas-titulo {
      color: var(--primary-color);
      font-size: 1.8rem;
      margin-bottom: 25px;
    }

    .conquistas-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }

    .conquista-card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
      text-align: center;
      transition: var(--transition);
      border: 2px solid transparent;
    }

    .conquista-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
      border-color: var(--secondary-color);
    }

    .conquista-icone {
      font-size: 2.5rem;
      margin-bottom: 15px;
      display: block;
    }

    .conquista-card h4 {
      color: var(--primary-color);
      margin: 0 0 10px 0;
      font-size: 1.2rem;
    }

    .conquista-card p {
      color: #666;
      margin: 0;
      font-size: 0.95rem;
    }

    .proximos-passos {
      margin-top: 40px;
      text-align: left;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }

    .proximos-passos h3 {
      color: var(--primary-color);
      text-align: center;
      margin-bottom: 20px;
    }

    .passo-card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 15px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
      border-left: 4px solid var(--secondary-color);
      display: flex;
      align-items: center;
      gap: 15px;
      transition: var(--transition);
    }

    .passo-card:hover {
      transform: translateX(5px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .passo-card i {
      font-size: 1.5rem;
      color: var(--primary-color);
      min-width: 30px;
    }

    .passo-card h4 {
      margin: 0 0 5px 0;
      color: var(--dark-color);
    }

    .passo-card p {
      margin: 0;
      color: #666;
      font-size: 0.95rem;
    }

    .conclusao-botoes {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 30px;
      flex-wrap: wrap;
    }

    .btn-compartilhar {
      background: linear-gradient(135deg, #25D366, #128C7E);
      color: white;
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: var(--transition);
    }

    .btn-compartilhar:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
    }

    .btn-nova-trilha {
      background: linear-gradient(135deg, var(--secondary-color), #ffb300);
      color: white;
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: var(--transition);
    }

    .btn-nova-trilha:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 202, 40, 0.3);
    }

    .btn-voltar-inicio {
      background: linear-gradient(135deg, #6c757d, #5a6268);
      color: white;
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: var(--transition);
    }

    .btn-voltar-inicio:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
    }

    /* Confetti */
    .confetti {
      position: fixed;
      width: 10px;
      height: 10px;
      background-color: var(--primary-color);
      top: -10px;
      z-index: 1000;
    }

    footer {
      background-color: var(--gray-color);
      color: var(--primary-color);
      text-align: center;
      padding: 25px;
    }

    footer a {
      color: var(--primary-color);
      text-decoration: none;
      margin: 0 15px;
      font-weight: 600;
      transition: var(--transition);
    }

    footer a:hover {
      color: var(--secondary-color);
    }

    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        padding: 15px;
      }

      .nav-links {
        margin: 15px 0;
      }

      .nav-links li {
        margin: 0 10px;
      }

      .dropdown-menu {
        position: static;
        box-shadow: none;
        border: none;
        opacity: 1;
        visibility: visible;
        transform: none;
        display: none;
      }

      .dropdown:hover .dropdown-menu {
        display: block;
      }

      .video-tabela {
        flex-direction: column;
      }

      .tabela-wrapper,
      .video-container {
        max-width: 100%;
        height: auto;
      }

      .video-container {
        height: 300px;
      }

      .search-box.active .search-input {
        width: 200px;
      }

      .curso-card {
        width: 100%;
        margin: 10px 0;
      }

      .conclusao-titulo {
        font-size: 2rem;
        flex-direction: column;
        gap: 10px;
      }

      .conclusao-info {
        flex-direction: column;
      }

      .conquistas-grid {
        grid-template-columns: 1fr;
      }

      .conclusao-botoes {
        flex-direction: column;
        align-items: center;
      }

      .conclusao-botoes .btn {
        width: 100%;
        max-width: 300px;
        justify-content: center;
      }
    }
  </style>
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
    break;
  }


  ?>

  console.log("Terminou");
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


  <script>
    carregarDadosTrilha();
    console.log("teste");

    // Função para pegar parâmetros da URL
    function getUrlParams() {
      const params = new URLSearchParams(window.location.search);
      const paramsObj = {};
      for (const [key, value] of params) {
        paramsObj[key] = value;
      }
      return paramsObj;
    }

    // Função para obter o ID da trilha da URL
    function getTrilhaId() {
      const params = getUrlParams();
      return params.id || params.trilha_id || null;
    }

    //let etapas = [];
    let etapaAtual = 0;
    let trilhaId = null;
    let dadosTrilha = null;

    // Função principal para carregar dados da trilha
    async function carregarDadosTrilha() {
      trilhaId = getTrilhaId();
      inicializarTrilha();
      /*
      if (!trilhaId) {
          alert('ID da trilha não encontrado na URL!');
          window.location.href = 'telameuscursos.php';
          return;
      }

      try {
        
        
          

          // Mostrar loading
          document.getElementById('mainContent').innerHTML = `
              <div style="text-align: center; padding: 50px;">
                  <h2>Carregando trilha...</h2>
                  <p>Por favor, aguarde enquanto carregamos sua trilha de estudos.</p>
              </div>
          `;

          // Buscar dados da trilha
           console.log("Fetch");
          const response = await fetch(`../controller/TrilhaController.php?action=getTrilha&id=${trilhaId}`);
          console.log(`../controller/TrilhaController.php?action=getTrilha&id=${trilhaId}`);
          if (!response.ok) {
              throw new Error('Erro ao carregar trilha');
          }

          const data = await response.json();
          
          if (data.success) {
              console.log("Sucesso");
              dadosTrilha = data.trilha;
              etapas = data.etapas || [];
              inicializarTrilha();
          } else {
              throw new Error(data.message || 'Erro ao carregar trilha');
          }
              

      } catch (error) {
          console.error('Erro:', error);
          document.getElementById('mainContent').innerHTML = `
              <div style="text-align: center; padding: 50px;">
                  <h2 style="color: #ff4444;">Erro ao carregar trilha</h2>
                  <p>${error.message}</p>
                  <button class="btn" onclick="window.location.href='telameuscursos.php'">Voltar para Minhas Trilhas</button>
              </div>
          `;
      }
          */
    }

    // Função para inicializar a trilha com os dados do BD
    function inicializarTrilha() {
      const mainContent = document.getElementById('mainContent');

      mainContent.innerHTML = `
        <h1>Trilha: <?php echo $titulo ?></h1>
        <p><?php echo $descricao ?></p>

        <div id="progressoTexto">Etapa <span id="etapaAtualNumero">1</span> de <span id="totalEtapas">1</span></div>

        <div class="video-tabela">
    <div class="tabela-wrapper">
      <h2 id="nomeEtapa">Etapa 1 - <?php echo $tituloConteudo ?></h2>
      <p><?php echo $descricao ?></p>
    </div>

    <div class="video-container">
        <div id="youtubePlayer"><iframe width="100%" height="360" src="https://www.youtube.com/embed/<?php
         
         
$video_id = explode("?v=", $linkConteudo);
$video_id = $video_id[1];
         echo $video_id;
         ?>?rel=0&modestbranding=1&" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen title="Vídeo da aula"></iframe></div>
    </div>
  </div>

  <div class="btn-group-navegacao">
    <button id="voltarBtn" class="btn btn-secondary hidden">← Voltar</button>
    <button id="proximaEtapaBtn" class="btn">Próxima etapa →</button>
  </div>
</main>
    `;

      // Reconfigurar event listeners
      document.getElementById('proximaEtapaBtn').addEventListener('click', function() {
        if (etapaAtual === etapas.length - 1) {
          mostrarTelaConclusao();
        } else {
          etapaAtual++;
          carregarEtapa();
        }
      });

      document.getElementById('voltarBtn').addEventListener('click', function() {
        if (etapaAtual > 0) {
          etapaAtual--;
          carregarEtapa();
        }
      });

      // Carregar primeira etapa
      if (etapas.length > 0) {
        carregarEtapa();
      } else {
        mainContent.innerHTML = `
            <div style="text-align: center; padding: 50px;">
                <h2>Trilha vazia</h2>
                <p>Esta trilha ainda não possui conteúdo.</p>
                <button class="btn" onclick="window.location.href='telameuscursos.php'">Voltar para Minhas Trilhas</button>
            </div>
        `;
      }
    }

    // Função para carregar etapa atual
    function carregarEtapa() {
      if (etapas.length === 0) return;



      // Carregar aulas da etapa
      const tbody = document.getElementById('tabelaAulas').querySelector('tbody');
      tbody.innerHTML = '';

      etapas[etapaAtual].aulas.forEach((aula, i) => {
        const tr = document.createElement('tr');
        const storageKey = `trilha_${trilhaId}_etapa_${etapaAtual}_aula_${i}`;

        if (localStorage.getItem(storageKey) === 'true') tr.classList.add('completed');

        const tdAula = document.createElement('td');
        tdAula.textContent = (i + 1) + '. ' + aula.titulo;
        const tdObjetivo = document.createElement('td');
        tdObjetivo.textContent = aula.objetivo;

        tr.appendChild(tdAula);
        tr.appendChild(tdObjetivo);

        tr.addEventListener('click', () => {
          tr.classList.toggle('completed');
          localStorage.setItem(storageKey, tr.classList.contains('completed').toString());
          salvarProgresso();
        });

        tbody.appendChild(tr);
      });

      // Carregar vídeo
      const videoId = etapas[etapaAtual].video;
      const container = document.getElementById('youtubePlayer');
      const loading = document.getElementById('loadingMessage');

      loading.classList.remove('hidden');
      loading.textContent = 'Carregando vídeo...';

      const tempoSalvo = localStorage.getItem(`trilha_${trilhaId}_video_tempo_${videoId}`) || 0;

      if (videoId) {
        //container.innerHTML = `<iframe width="100%" height="360" src="https://www.youtube.com/embed/${videoId}?rel=0&modestbranding=1&start=${tempoSalvo}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen title="Vídeo da aula"></iframe>`;
        const iframe = container.querySelector('iframe');
        iframe.onload = () => loading.classList.add('hidden');
      } else {
        container.innerHTML = `
            <div style="display: flex; justify-content: center; align-items: center; height: 100%; background: #f0f0f0; border-radius: 12px;">
                <div style="text-align: center; padding: 20px;">
                    <h3>Conteúdo de Leitura</h3>
                    <p>${etapas[etapaAtual].conteudo_texto || 'Estude o material fornecido para esta etapa.'}</p>
                </div>
            </div>
        `;
        loading.classList.add('hidden');
      }

      setTimeout(() => loading.classList.add('hidden'), 5000);
    }

    // Função para salvar progresso no backend
    async function salvarProgresso() {
      try {
        const progresso = {
          trilha_id: trilhaId,
          etapa_atual: etapaAtual,
          concluido: etapaAtual === etapas.length - 1
        };

        await fetch('../controller/TrilhaController.php?action=salvarProgresso', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(progresso)
        });
      } catch (error) {
        console.error('Erro ao salvar progresso:', error);
      }
    }

    // Modifique o DOMContentLoaded para carregar a trilha do BD
    document.addEventListener('DOMContentLoaded', function() {
      // Configuração da pesquisa (mantenha este código)
      const searchToggle = document.querySelector('.search-toggle');
      const searchBox = document.querySelector('.search-box');
      const searchInput = document.getElementById('searchInput');

      searchToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        searchBox.classList.toggle('active');
        if (searchBox.classList.contains('active')) {
          searchInput.focus();
        }
      });

      searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          realizarPesquisa();
        }
      });

      document.addEventListener('click', function(e) {
        if (!searchBox.contains(e.target)) {
          searchBox.classList.remove('active');
        }
      });

      // Botões da tela de conclusão
      //document.getElementById('btnCompartilhar').addEventListener('click', compartilharConquista);
      //document.getElementById('btnNovaTrilha').addEventListener('click', iniciarNovaTrilha);
      //document.getElementById('btnVoltarInicio').addEventListener('click', voltarAoInicio);

      // Carregar trilha do banco de dados
      carregarDadosTrilha();
    });

    // Atualize a função mostrarTelaConclusao
    function mostrarTelaConclusao() {
      // Esconde a tela principal
      document.getElementById('mainContent').style.display = 'none';

      // Mostra a tela de conclusão
      document.getElementById('telaConclusao').style.display = 'block';

      // Atualiza o título com o nome da trilha
      document.querySelector('.conclusao-titulo span').textContent = `🎉 Parabéns!`;
      document.querySelector('.conclusao-subtitulo').innerHTML =
        `Você concluiu com sucesso a trilha <strong>${dadosTrilha.titulo || 'Introdução à Programação'}</strong>`;

      // Atualiza estatísticas
      atualizarEstatisticas();

      // Dispara confetti
      dispararConfetti();

      // Marca trilha como concluída no backend
      salvarProgresso();
    }

    // Mantenha as outras funções (atualizarEstatisticas, dispararConfetti, etc.) como estão
  </script>



</body>

</html>