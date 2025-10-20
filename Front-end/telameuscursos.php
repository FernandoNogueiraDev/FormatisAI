<?php
session_start();
require 'conexao.php';
require_once '../controller/monitoradorLogin.php';


// Inicializa como Visitante
$usuario_nome = "Visitante";
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
    // Pega apenas o primeiro nome e coloca a primeira letra maiúscula
    $primeiroNome = explode(" ", $nomeCompleto)[0];
    $usuario_nome = ucfirst(strtolower($primeiroNome));
    $aluno_id = $idAluno;
  }

  $query->close();
}


// Busca trilhas do aluno do banco de dados
$trilhas_do_usuario = [];


if (isset($aluno_id)) {
  $query = "SELECT idTrilha_estudo, aluno_id, titulo, descricao, status FROM trilha_estudo WHERE aluno_id = " . $aluno_id;

  $result = $conn->query($query);
  $trilhas_do_usuario = $result->fetch_all(MYSQLI_ASSOC);

  $result->close();
}


// Se as tabelas não existem, mostra dados de exemplo para demonstração
$mostrar_dados_exemplo = false;
//$mostrar_dados_exemplo = !$tabela_trilhas_existe || !$tabela_aluno_trilhas_existe;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Minhas Trilhas - Formatis</title>
  <link rel="icon" href="imgs/favicon1.png" type="image/png">
  <style>
    /* ====== VARIÁVEIS CSS ====== */
    :root {
      --primary-color: #3d4ff7;
      --secondary-color: #ffca28;
      --light-color: #eef2ff;
      --dark-color: #333;
      --gray-color: #f5f5f5;
      --white: #fff;
      --success-color: #4CAF50;
      --border-radius: 12px;
      --transition: all 0.3s ease;
    }

    /* ====== BASE ====== */
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f9f9f9;
      color: var(--dark-color);
      line-height: 1.6;
    }

    /* ====== NAVBAR ATUALIZADA ====== */
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

    /* Dropdown Menu Atualizado */
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
      background-color: var(--white);
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

    /* ====== PESQUISA ATUALIZADA ====== */
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

    /* ====== CONTEÚDO PRINCIPAL ====== */
    .main-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }

    /* Hero Section */
    .hero-retomar {
      text-align: center;
      padding: 60px 20px 40px;
      background: linear-gradient(135deg, var(--primary-color), #00bcd4);
      color: white;
      border-radius: 0 0 20px 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      margin-bottom: 30px;
    }

    .hero-retomar h1 {
      font-size: 2.5rem;
      margin-bottom: 10px;
    }

    .hero-retomar p {
      font-size: 1.1rem;
      max-width: 700px;
      margin: 0 auto;
      line-height: 1.5;
    }

    /* Saudação do usuário */
    .user-greeting {
      text-align: center;
      margin-bottom: 20px;
      font-size: 1.2rem;
      color: var(--primary-color);
      font-weight: 600;
    }

    /* ====== TRILHAS ====== */
    .trilha-wrapper {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 25px;
      padding-bottom: 40px;
    }

    .container-trilha {
      background: white;
      padding: 25px;
      border-radius: var(--border-radius);
      width: 100%;
      max-width: 320px;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      color: var(--dark-color);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      border: 1px solid #eee;
    }

    .container-trilha:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .container-trilha img {
      width: 70px;
      height: 70px;
      margin-bottom: 15px;
      border-radius: 8px;
      object-fit: cover;
    }

    .container-trilha h3 {
      margin: 10px 0;
      font-size: 1.3rem;
      color: var(--primary-color);
    }

    .container-trilha .categoria {
      background: var(--light-color);
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.8rem;
      color: var(--primary-color);
      margin-bottom: 10px;
      font-weight: 600;
    }

    .container-trilha p {
      margin: 10px 0;
      flex-grow: 1;
      font-size: 14px;
      color: #666;
    }

    .trilha-info {
      width: 100%;
      margin: 10px 0;
      font-size: 13px;
      color: #888;
      text-align: left;
    }

    .trilha-info div {
      margin: 5px 0;
    }

    /* Progresso da trilha */
    .trilha-progresso {
      width: 100%;
      background-color: #e0e0e0;
      border-radius: 10px;
      margin: 15px 0;
      overflow: hidden;
    }

    .progresso-bar {
      height: 8px;
      background: linear-gradient(90deg, var(--secondary-color), #ffb300);
      border-radius: 10px;
      transition: width 0.5s ease;
    }

    .progresso-texto {
      font-size: 0.9rem;
      margin-top: 5px;
      color: #666;
      text-align: right;
    }

    /* Botões */
    .btn {
      background-color: var(--primary-color);
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: var(--transition);
      display: inline-block;
      text-decoration: none;
      font-size: 14px;
      margin-top: 10px;
      width: 100%;
    }

    .btn:hover {
      background-color: #2a3bd4;
      transform: translateY(-2px);
    }

    .btn-success {
      background-color: var(--success-color);
    }

    .btn-success:hover {
      background-color: #45a049;
    }

    .btn-continuar {
      background: linear-gradient(135deg, var(--primary-color), #00bcd4);
    }

    .btn-continuar:hover {
      background: linear-gradient(135deg, #2a3bd4, #00acc1);
    }

    /* Mensagem quando não há trilhas */
    .mensagem-vazia {
      font-size: 18px;
      color: #666;
      line-height: 1.5;
      text-align: center;
      width: 100%;
      padding: 60px 20px;
      background-color: var(--white);
      border-radius: var(--border-radius);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      margin: 20px 0;
    }

    .mensagem-vazia h3 {
      color: var(--primary-color);
      margin-bottom: 15px;
    }

    /* Botão de explorar trilhas */
    .btn-explorar {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 24px;
      background-color: var(--primary-color);
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    .btn-explorar:hover {
      background-color: #2a3bd4;
    }

    /* Status da trilha */
    .trilha-status {
      font-size: 0.8rem;
      padding: 4px 8px;
      border-radius: 12px;
      margin-bottom: 10px;
      font-weight: 600;
    }

    .status-andamento {
      background-color: #fff3cd;
      color: #856404;
    }

    .status-concluido {
      background-color: #d4edda;
      color: #155724;
    }

    /* Aviso de configuração */
    .aviso-configuracao {
      background-color: #fff3cd;
      border: 1px solid #ffeaa7;
      border-radius: var(--border-radius);
      padding: 20px;
      margin: 20px 0;
      text-align: center;
    }

    .aviso-configuracao h4 {
      color: #856404;
      margin-top: 0;
    }

    /* ====== RESPONSIVIDADE ====== */
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

      .search-box.active .search-input {
        width: 200px;
      }

      .hero-retomar h1 {
        font-size: 2rem;
      }

      .hero-retomar {
        padding: 40px 20px 30px;
      }

      .trilha-wrapper {
        flex-direction: column;
        align-items: center;
      }

      .container-trilha {
        max-width: 100%;
      }
    }

    @media (max-width: 480px) {
      .nav-links {
        flex-direction: column;
        gap: 10px;
      }

      .hero-retomar h1 {
        font-size: 1.8rem;
      }
    }
  </style>
</head>

<body>

  <div class="page">
    <!-- NAVBAR ATUALIZADA -->
    <nav class="navbar" role="navigation" aria-label="Navegação principal">
      <div class="logo"><a href="telainicial.php" aria-label="Página inicial"><img src="imgs/logo.png" alt="Formatis"></a></div>
      <ul class="nav-links">
        <li><a href="telameuscursos.php">Minhas trilhas</a></li>
        <li><a href="telacursos.html">Cursos</a></li>
        <li><a href="quemsomos.html">Quem Somos</a></li>
        <li><a href="ajuda.html">Ajuda</a></li>

        <!-- Menu dropdown corrigido e alinhado -->
        <li class="dropdown">
          <a href="#" class="dropdown-toggle">Área do Aluno</a>
          <ul class="dropdown-menu">
            <li><a href="atualizarsenha.html">Atualizar senha</a></li>
            <li><a href="#AlterarCadastro">Alterar cadastro</a></li>
            <li><a href="areadoaluno.php">Acessar área do aluno</a></li>
<li><a href="logout.php">Sair</a></li>
          </ul>
        </li>
      </ul>
      <div class="search-box">
        <button class="search-toggle" aria-label="Abrir busca">🔍</button>
        <input type="text" class="search-input" placeholder="Procurar trilhas..." aria-label="Campo de busca" id="searchInput">
      </div>
    </nav>

    <!-- 🧠 CONTEÚDO PRINCIPAL -->
    <main class="main-content">
      <section class="hero-retomar">
        <h1>Retome de onde parou</h1>
        <p>Acesse rapidamente as trilhas em que você já está inscrito e continue sua jornada de aprendizado.</p>
        <?php if ($usuario_nome !== "Visitante"): ?>
          <div class="user-greeting">Olá, <?php echo htmlspecialchars($usuario_nome); ?>! 👋</div>
        <?php endif; ?>
      </section>



      <section id="minhasTrilhas" class="trilha-wrapper">
        <?php if (count($trilhas_do_usuario) > 0 || $mostrar_dados_exemplo): ?>
          <?php
          // Se não há trilhas no banco, mostra exemplos
          $trilhas_para_exibir = count($trilhas_do_usuario) > 0 ? $trilhas_do_usuario : [
            [
              'id' => 1,
              'nome' => 'Introdução à Programação',
              'categoria' => 'Programação',
              'descricao' => 'Desenvolver o raciocínio lógico e compreender os fundamentos da programação.',
              'duracao' => '20 horas',
              'nivel' => 'Iniciante',
              'imagem' => 'imgs/programming.png',
              'progresso' => 65,
              'concluido' => false,
              'ultima_aula' => 'Variáveis e Operadores'
            ],
            [
              'id' => 2,
              'nome' => 'Design de Interfaces',
              'categoria' => 'Design',
              'descricao' => 'Aprenda os princípios de UI/UX Design para criar interfaces intuitivas.',
              'duracao' => '15 horas',
              'nivel' => 'Intermediário',
              'imagem' => 'imgs/design.png',
              'progresso' => 30,
              'concluido' => false,
              'ultima_aula' => 'Princípios do Design'
            ],
            [
              'id' => 3,
              'nome' => 'JavaScript Moderno',
              'categoria' => 'Programação',
              'descricao' => 'Domine JavaScript com ES6+ e conceitos modernos de desenvolvimento web.',
              'duracao' => '25 horas',
              'nivel' => 'Intermediário',
              'imagem' => 'imgs/javascript.png',
              'progresso' => 100,
              'concluido' => true,
              'ultima_aula' => 'Async/Await'
            ]
          ];
          ?>

          <?php foreach ($trilhas_do_usuario as $trilha): ?>

            <?php


            $trilhaConteudoResult = $conn->query("SELECT idTrilha_conteudo, trilha_id, conteudo_id, ordem, obrigatorio, estimativa_min FROM trilha_conteudo WHERE trilha_id = " . $trilha['idTrilha_estudo']);
            $trilha_conteudo = $trilhaConteudoResult->fetch_assoc();

            $conteudoResult = $conn->query("SELECT * FROM conteudo WHERE idConteudo = " . $trilha_conteudo['conteudo_id']);
            $conteudo = $conteudoResult->fetch_assoc();


            $progressoTrilhaResult = $conn->query("SELECT * FROM progresso_trilha WHERE trilhaConteudo_id = " . $trilha_conteudo['conteudo_id'] . " AND aluno_id = " . $aluno_id);
            $progressoTrilhaLista = $progressoTrilhaResult->fetch_all(MYSQLI_ASSOC);

            ?>

            <div class="container-trilha">
              <img src="imgs/programming.png"
                alt="<?php echo htmlspecialchars($trilha['titulo']); ?>"
                onerror="this.src='https://via.placeholder.com/70/3d4ff7/ffffff?text=🎯'">

              <div class="categoria"><?php echo htmlspecialchars($conteudo['categoria']); ?></div>


              <?php if (!$progressoTrilhaResult->num_rows > 0): ?>
                  <div class="trilha-status status-andamento">🔄 A iniciar</div>
              <?php else: ?>
                <?php if ($trilha['concluido']): ?>
                  <div class="trilha-status status-concluido">✅ Concluída</div>
                <?php else: ?>
                  <div class="trilha-status status-andamento">🔄 Em Andamento</div>
                <?php endif; ?>
              <?php endif; ?>

              <h3><?php echo htmlspecialchars($trilha['titulo']); ?></h3>
              <p><?php echo htmlspecialchars($trilha['descricao']); ?></p>

              <?php if (isset($trilha['progresso'])): ?>
                <div class="trilha-progresso">
                  <div class="progresso-bar" style="width: <?php echo $trilha['progresso']; ?>%"></div>
                </div>
                <div class="progresso-texto"><?php echo $trilha['progresso']; ?>% concluído</div>
              <?php endif; ?>


                <button class="btn btn-continuar" onclick="acessarTrilha(<?php echo $trilha_conteudo['trilha_id']; ?>)">
                  ▶ Continuar Estudando
                </button>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="mensagem-vazia">
            <?php if ($usuario_nome === "Visitante"): ?>
              <h3>🔒 Acesso Restrito</h3>
              <p>Você precisa estar logado para ver suas trilhas.</p>
              <a href="telalogin.html" class="btn-explorar">Fazer Login</a>
            <?php else: ?>
              <?php if (true): ?>
                <h3>📝 Nenhuma trilha em andamento</h3>
                <p>Você ainda não se inscreveu em nenhuma trilha ou não possui trilhas em progresso.</p>
                <p>Explore nossa plataforma e comece sua jornada de aprendizado!</p>
                <a href="telatrilhas.html" class="btn-explorar">Explorar Trilhas</a>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </section>
    </main>
  </div>

  <script>
    // Função para acessar a trilha
    function acessarTrilha(trilhaId) {
      // Redireciona para a página da trilha
      window.location.href = `minhastrilhas.php?id=${trilhaId}`;
    }

    // Configuração da pesquisa da navbar
    function configurarPesquisa() {
      const searchToggle = document.querySelector('.search-toggle');
      const searchBox = document.querySelector('.search-box');
      const searchInput = document.getElementById('searchInput');

      // Abrir/fechar caixa de pesquisa
      searchToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        searchBox.classList.toggle('active');
        if (searchBox.classList.contains('active')) {
          searchInput.focus();
        }
      });

      // Pesquisa ao pressionar Enter
      searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          realizarPesquisa();
        }
      });

      // Fechar pesquisa ao clicar fora
      document.addEventListener('click', function(e) {
        if (!searchBox.contains(e.target)) {
          searchBox.classList.remove('active');
        }
      });
    }

    function realizarPesquisa() {
      const termo = document.getElementById('searchInput').value.trim();

      if (termo === '') {
        alert('Por favor, digite um termo para pesquisar.');
        return;
      }

      // Fecha a caixa de pesquisa
      document.querySelector('.search-box').classList.remove('active');

      // Redireciona para a página de busca
      window.location.href = `busca_trilhas.php?q=${encodeURIComponent(termo)}`;
    }

    // Inicialização quando o DOM estiver carregado
    document.addEventListener('DOMContentLoaded', function() {
      configurarPesquisa();
    });
  </script>

</body>

</html>