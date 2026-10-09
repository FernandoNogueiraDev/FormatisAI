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


$avatar = strtoupper(substr($aluno_nome_completo, 0, 1));
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Área do Aluno - Formatis</title>
  <link rel="icon" href="imgs/favicon1.png" type="image/png">
  <link rel= "stylesheet" href= "../css/areadoaluno.css">
 
</head>
<body>
  <div class="page">

    <nav class="navbar" role="navigation" aria-label="Navegação principal">
      <div class="logo"><a href="telainicial.php" aria-label="Página inicial"><img src="imgs/logo.png" alt="Formatis"></a></div>
      <ul class="nav-links">
        <li><a href="telameuscursos.php">Minhas trilhas</a></li>
        <li><a href="telacursos.html">Cursos</a></li>
        <li><a href="quemsomos.html">Quem Somos</a></li>
        <li><a href="ajuda.html">Ajuda</a></li>
        
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

<script src="../js/areadoaluno.js"></script>

</body>
</html>