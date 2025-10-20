<?php
session_start();
require 'conexao.php';

// INÍCIO: CÓDIGO IDÊNTICO AO DA PÁGINA INICIAL
$usuario_nome = "Visitante";
$aluno_nome_completo = "";
$aluno_email = "";
$aluno_id = null;

if(isset($_SESSION['usuario_email'])){
    $email = $_SESSION['usuario_email'];

    // BUSCA COM O NOME CORRETO DA COLUNA ID
    $query = $conn->prepare("SELECT idAluno, nome, email FROM Aluno WHERE email = ? LIMIT 1");
    $query->bind_param("s", $email);
    $query->execute();
    $query->bind_result($idAluno, $nomeCompleto, $email_aluno);
    
    if($query->fetch()){
        $primeiroNome = explode(" ", $nomeCompleto)[0];
        $usuario_nome = ucfirst(strtolower($primeiroNome));
        $aluno_nome_completo = $nomeCompleto;
        $aluno_email = $email_aluno;
        $aluno_id = $idAluno;
    }
    $query->close();
}

// Se não está logado, redireciona
if($usuario_nome === "Visitante" || !$aluno_id) {
    header("Location: telalogin.html");
    exit();
}

// Verifica se as tabelas necessárias existem
$tabela_aluno_cursos_existe = $conn->query("SHOW TABLES LIKE 'aluno_cursos'")->num_rows > 0;
$tabela_cursos_existe = $conn->query("SHOW TABLES LIKE 'cursos'")->num_rows > 0;

// Inicializa variáveis
$total_cursos = 0;
$cursos_concluidos = 0;
$horas_estudo = "0h";
$progresso_geral = "0%";
$cursos_ativos = 0;
$dias_sequencia = 0;
$cursos_andamento = [];
$cursos_concluidos_lista = [];

// Só busca estatísticas se as tabelas existirem
if ($tabela_aluno_cursos_existe && $tabela_cursos_existe) {
    // Busca estatísticas do aluno
    $totalCursosQuery = $conn->prepare("SELECT COUNT(*) FROM aluno_cursos WHERE aluno_id = ?");
    $totalCursosQuery->bind_param("i", $aluno_id);
    $totalCursosQuery->execute();
    $totalCursosQuery->bind_result($total_cursos);
    $totalCursosQuery->fetch();
    $totalCursosQuery->close();

    $cursosConcluidosQuery = $conn->prepare("SELECT COUNT(*) FROM aluno_cursos WHERE aluno_id = ? AND concluido = 1");
    $cursosConcluidosQuery->bind_param("i", $aluno_id);
    $cursosConcluidosQuery->execute();
    $cursosConcluidosQuery->bind_result($cursos_concluidos);
    $cursosConcluidosQuery->fetch();
    $cursosConcluidosQuery->close();

    // Busca cursos em andamento
    $cursosAndamentoQuery = $conn->prepare("
        SELECT c.id, c.nome, c.categoria, c.imagem, ac.progresso, ac.ultima_aula, ac.data_inicio 
        FROM cursos c 
        INNER JOIN aluno_cursos ac ON c.id = ac.curso_id 
        WHERE ac.aluno_id = ? AND ac.concluido = 0 
        ORDER BY ac.data_inicio DESC
        LIMIT 6
    ");
    $cursosAndamentoQuery->bind_param("i", $aluno_id);
    $cursosAndamentoQuery->execute();
    $cursosAndamentoResult = $cursosAndamentoQuery->get_result();
    $cursos_andamento = $cursosAndamentoResult->fetch_all(MYSQLI_ASSOC);
    $cursosAndamentoQuery->close();

    // Busca cursos concluídos
    $cursosConcluidosQuery = $conn->prepare("
        SELECT c.id, c.nome, c.categoria, c.imagem, ac.progresso, ac.data_conclusao 
        FROM cursos c 
        INNER JOIN aluno_cursos ac ON c.id = ac.curso_id 
        WHERE ac.aluno_id = ? AND ac.concluido = 1 
        ORDER BY ac.data_conclusao DESC
        LIMIT 6
    ");
    $cursosConcluidosQuery->bind_param("i", $aluno_id);
    $cursosConcluidosQuery->execute();
    $cursosConcluidosResult = $cursosConcluidosQuery->get_result();
    $cursos_concluidos_lista = $cursosConcluidosResult->fetch_all(MYSQLI_ASSOC);
    $cursosConcluidosQuery->close();

    // Calcula progresso geral
    $progressoGeralQuery = $conn->prepare("SELECT AVG(progresso) FROM aluno_cursos WHERE aluno_id = ? AND concluido = 0");
    $progressoGeralQuery->bind_param("i", $aluno_id);
    $progressoGeralQuery->execute();
    $progressoGeralQuery->bind_result($progresso_geral);
    $progressoGeralQuery->fetch();
    $progressoGeralQuery->close();

    $progresso_geral = $progresso_geral ? round($progresso_geral) . "%" : "0%";

    // Cursos ativos (em andamento)
    $cursos_ativos = count($cursos_andamento);
}

// Calcula horas de estudo (se a tabela existir)
if($conn->query("SHOW TABLES LIKE 'historico_estudo'")->num_rows > 0) {
    $horasEstudoQuery = $conn->prepare("SELECT SUM(tempo_estudo) FROM historico_estudo WHERE aluno_id = ?");
    $horasEstudoQuery->bind_param("i", $aluno_id);
    $horasEstudoQuery->execute();
    $horasEstudoQuery->bind_result($total_minutos);
    $horasEstudoQuery->fetch();
    $horasEstudoQuery->close();
    
    $horas_estudo = $total_minutos ? floor($total_minutos / 60) . "h" : "0h";
}

// Dias em sequência (se a tabela existir)
if($conn->query("SHOW TABLES LIKE 'historico_acesso'")->num_rows > 0) {
    $diasSequenciaQuery = $conn->prepare("SELECT COUNT(DISTINCT DATE(data_acesso)) FROM historico_acesso WHERE aluno_id = ? AND data_acesso >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $diasSequenciaQuery->bind_param("i", $aluno_id);
    $diasSequenciaQuery->execute();
    $diasSequenciaQuery->bind_result($dias_sequencia);
    $diasSequenciaQuery->fetch();
    $diasSequenciaQuery->close();
}

// Avatar (primeira letra do nome)
$avatar = strtoupper(substr($aluno_nome_completo, 0, 1));
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Área do Aluno - Formatis</title>
  <link rel="icon" href="imgs/favicon1.png" type="image/png">
  <style>
    /* Variáveis CSS para consistência */
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

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      line-height: 1.6;
      color: var(--dark-color);
      background-color: #f9f9f9;
    }

    /* Navbar Atualizada */
    .navbar {
      display: flex; 
      justify-content: space-between; 
      align-items: center;
      margin: auto; 
      padding: 10px 5%;
      background-color: var(--gray-color);
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .logo img { height: 55px; width: auto; }
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
    .nav-links a:hover { color: var(--secondary-color); }
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
    .nav-links a:hover::after { width: 100%; }

    /* Dropdown Menu */
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
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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

    /* Search Box Atualizada */
    .search-box { position: relative; display: flex; align-items: center; }
    .search-toggle { background: none; border: none; font-size: 20px; cursor: pointer; color: var(--primary-color); padding: 8px; border-radius: 50%; transition: var(--transition); }
    .search-toggle:hover { background-color: rgba(61, 79, 247, 0.1); }
    .search-input { width: 0; opacity: 0; padding: 10px 15px; border: 1px solid #ddd; border-radius: 30px; outline: none; transition: var(--transition); margin-left: 10px; font-size: 14px; }
    .search-box.active .search-input { width: 250px; opacity: 1; }

    /* Header do Aluno */
    .student-header {
      background: linear-gradient(135deg, var(--primary-color), #2a3bd4);
      color: white;
      padding: 50px 20px;
      text-align: center;
    }

    .student-info {
      max-width: 800px;
      margin: 0 auto;
    }

    .student-avatar {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background-color: white;
      margin: 0 auto 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 40px;
      color: var(--primary-color);
      font-weight: bold;
    }

    .progress-summary {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin-top: 30px;
      flex-wrap: wrap;
    }

    .progress-item {
      text-align: center;
    }

    .progress-number {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 5px;
    }

    /* Seções */
    section {
      padding: 40px 5%;
    }

    h2 {
      text-align: center;
      color: var(--primary-color);
      margin-bottom: 30px;
      font-size: 2rem;
    }

    /* Cards de curso */
    .curso-wrapper {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 25px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .container-curso {
      background: white;
      padding: 25px;
      border-radius: var(--border-radius);
      width: 100%;
      max-width: 350px;
      text-align: center;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: var(--transition);
      border: 1px solid #eee;
    }

    .container-curso:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .container-curso img {
      width: 80px;
      height: auto;
      margin-bottom: 15px;
    }

    .container-curso h3 {
      margin: 10px 0;
      font-size: 1.3rem;
      color: var(--primary-color);
    }

    .container-curso p {
      font-size: 16px;
      margin-bottom: 15px;
      color: var(--dark-color);
    }

    /* Barras de progresso */
    .progress-bar {
      width: 100%;
      height: 8px;
      background-color: #e0e0e0;
      border-radius: 4px;
      margin: 10px 0;
      overflow: hidden;
    }

    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, var(--secondary-color), #ffd54f);
      border-radius: 4px;
      transition: width 0.5s ease;
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
    }

    .btn:hover {
      background-color: #2a3bd4;
      transform: translateY(-2px);
    }

    .btn-secondary {
      background-color: var(--secondary-color);
      color: var(--dark-color);
    }

    .btn-secondary:hover {
      background-color: #ffb300;
    }

    /* Estatísticas */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      max-width: 1000px;
      margin: 0 auto;
    }

    .stat-card {
      background: white;
      padding: 25px;
      border-radius: var(--border-radius);
      text-align: center;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .stat-number {
      font-size: 2.5rem;
      font-weight: bold;
      color: var(--primary-color);
      margin-bottom: 10px;
    }

    /* Footer */
    footer {
      background-color: #2c3e50;
      color: white;
      text-align: center;
      padding: 30px 20px;
      margin-top: 50px;
    }

    .footer-links {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin: 20px 0;
      flex-wrap: wrap;
    }

    .footer-links a {
      color: white;
      text-decoration: none;
      transition: var(--transition);
    }

    .footer-links a:hover {
      color: var(--secondary-color);
    }

    /* Loading */
    .loading {
      text-align: center;
      padding: 40px;
      color: var(--primary-color);
    }

    /* Mensagem motivacional */
    .motivational-message {
      text-align: center;
      padding: 30px;
      background: linear-gradient(135deg, var(--light-color), #ffffff);
      border-radius: var(--border-radius);
      margin: 30px auto;
      max-width: 800px;
      border-left: 4px solid var(--secondary-color);
    }

    /* Mensagem quando não há cursos */
    .mensagem-vazia {
      text-align: center;
      padding: 60px 20px;
      color: #666;
      width: 100%;
    }

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

    /* Responsividade */
    @media (max-width: 768px) {
      .navbar { flex-direction: column; padding: 15px; }
      .nav-links { margin: 15px 0; }
      .nav-links li { margin: 0 10px; }
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
      .search-box.active .search-input { width: 200px; }
      .progress-summary { gap: 20px; }
      .curso-wrapper { flex-direction: column; align-items: center; }
      .container-curso { max-width: 100%; }
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
        <input type="text" class="search-input" placeholder="Procurar cursos..." aria-label="Campo de busca" id="searchInput">
      </div>
    </nav>

   <!-- Header do Aluno -->
<header class="student-header">
  <div class="student-info">
    <div class="student-avatar"><?php echo $avatar; ?></div>
    <h1>Olá, <?php echo htmlspecialchars($usuario_nome); ?>!</h1>
    <p style="opacity: 0.9;"><?php echo htmlspecialchars($aluno_email); ?></p>
    
    <div class="progress-summary">
      <div class="progress-item">
        <div class="progress-number"><?php echo $total_cursos; ?></div>
        <div>Cursos Inscritos</div>
      </div>
      <div class="progress-item">
        <div class="progress-number"><?php echo $cursos_concluidos; ?></div>
        <div>Cursos Concluídos</div>
      </div>
      <div class="progress-item">
        <div class="progress-number"><?php echo $horas_estudo; ?></div>
        <div>Horas de Estudo</div>
      </div>
    </div>
  </div>
</header>

    <!-- Estatísticas Rápidas -->
    <section style="background-color: var(--light-color);">
      <h2>Meu Progresso</h2>
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-number"><?php echo $progresso_geral; ?></div>
          <div>Progresso Geral</div>
        </div>
        <div class="stat-card">
          <div class="stat-number"><?php echo $cursos_ativos; ?></div>
          <div>Cursos em Andamento</div>
        </div>
        <div class="stat-card">
          <div class="stat-number"><?php echo $dias_sequencia; ?></div>
          <div>Dias de Estudo</div>
        </div>
      </div>
    </section>

    <!-- Mensagem Motivacional -->
    <section style="background-color: white;">
      <div class="motivational-message">
        <h3 style="color: var(--primary-color); margin-top: 0;">🎯 Continue evoluindo!</h3>
        <p style="font-size: 18px; margin-bottom: 0;">
          Cada minuto de estudo é um passo em direção ao seu crescimento profissional. 
          Você está no caminho certo!
        </p>
      </div>
    </section>

    <!-- Meus Cursos em Andamento -->
    <section style="background-color: var(--light-color);">
      <h2>Meus Cursos em Andamento</h2>
      <div class="curso-wrapper">
        <?php if (count($cursos_andamento) > 0): ?>
          <?php foreach ($cursos_andamento as $curso): ?>
            <div class="container-curso">
              <img src="<?php echo htmlspecialchars($curso['imagem'] ?? 'imgs/default-course.png'); ?>" 
                   alt="<?php echo htmlspecialchars($curso['nome']); ?>"
                   onerror="this.src='https://via.placeholder.com/80/3d4ff7/ffffff?text=📚'">
              <h3><?php echo htmlspecialchars($curso['nome']); ?></h3>
              <p><?php echo htmlspecialchars($curso['categoria']); ?></p>
              <div style="margin: 15px 0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                  <span>Progresso:</span>
                  <span><?php echo $curso['progresso']; ?>%</span>
                </div>
                <div class="progress-bar">
                  <div class="progress-fill" style="width: <?php echo $curso['progresso']; ?>%"></div>
                </div>
              </div>
              <p style="font-size: 14px; color: #666;">
                Última aula: <?php echo htmlspecialchars($curso['ultima_aula'] ?? 'Nenhuma aula iniciada'); ?>
              </p>
              <button class="btn" onclick="continuarCurso(<?php echo $curso['id']; ?>)">Continuar Curso</button>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="mensagem-vazia">
            <h3>📝 Nenhum curso em andamento</h3>
            <p>Você ainda não começou nenhum curso.</p>
            <a href="telacursos.html" class="btn-explorar">Explorar Cursos</a>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- Cursos Concluídos -->
    <section style="background-color: white;">
      <h2>Cursos Concluídos</h2>
      <div class="curso-wrapper">
        <?php if (count($cursos_concluidos_lista) > 0): ?>
          <?php foreach ($cursos_concluidos_lista as $curso): ?>
            <div class="container-curso">
              <img src="<?php echo htmlspecialchars($curso['imagem'] ?? 'imgs/default-course.png'); ?>" 
                   alt="<?php echo htmlspecialchars($curso['nome']); ?>"
                   onerror="this.src='https://via.placeholder.com/80/4CAF50/ffffff?text=✅'">
              <h3><?php echo htmlspecialchars($curso['nome']); ?></h3>
              <p><?php echo htmlspecialchars($curso['categoria']); ?></p>
              <div style="margin: 15px 0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                  <span>Status:</span>
                  <span style="color: var(--success-color); font-weight: bold;">Concluído</span>
                </div>
                <div class="progress-bar">
                  <div class="progress-fill" style="width: 100%; background: var(--success-color);"></div>
                </div>
              </div>
              <p style="font-size: 14px; color: #666;">
                Concluído em: <?php echo htmlspecialchars($curso['data_conclusao'] ?? 'Data não disponível'); ?>
              </p>
              <button class="btn btn-secondary" onclick="revisarCurso(<?php echo $curso['id']; ?>)">Revisar Conteúdo</button>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="mensagem-vazia">
            <h3>🎓 Nenhum curso concluído</h3>
            <p>Continue estudando para concluir seus primeiros cursos!</p>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- Footer -->
    <footer>
      <div class="footer-links">
        <a href="#ajuda">Ajuda</a>
        <a href="quemsomos.html">Quem Somos</a>
        <a href="#termos">Termos de Uso</a>
        <a href="#privacidade">Política de Privacidade</a>
        <a href="#contato">Contato</a>
      </div>
      <p>&copy; 2025 Formatis. Todos os direitos reservados.</p>
    </footer>
  </div>

  <script>
    // Funções de ação
    function continuarCurso(cursoId) {
      // Redirecionar para a página do curso
      window.location.href = `curso.php?id=${cursoId}`;
    }

    function revisarCurso(cursoId) {
      // Redirecionar para a página de revisão do curso
      window.location.href = `revisao_curso.php?id=${cursoId}`;
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
        if(searchBox.classList.contains('active')) {
          searchInput.focus();
        }
      });

      // Pesquisa ao pressionar Enter
      searchInput.addEventListener('keypress', function(e) {
        if(e.key === 'Enter') {
          realizarPesquisa();
        }
      });

      // Fechar pesquisa ao clicar fora
      document.addEventListener('click', function(e) {
        if(!searchBox.contains(e.target)) {
          searchBox.classList.remove('active');
        }
      });
    }

    function realizarPesquisa() {
      const termo = document.getElementById('searchInput').value.trim();
      
      if(termo === '') {
        alert('Por favor, digite um termo para pesquisar.');
        return;
      }
      
      // Fecha a caixa de pesquisa
      document.querySelector('.search-box').classList.remove('active');
      
      // Redireciona para a página de busca
      window.location.href = `busca.php?q=${encodeURIComponent(termo)}`;
    }

    // Inicialização quando o DOM estiver carregado
    document.addEventListener('DOMContentLoaded', function() {
      configurarPesquisa();
    });
  </script>
</body>
</html>