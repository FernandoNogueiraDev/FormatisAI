<?php
session_start();
require 'conexao.php';

$usuario_nome = "Visitante";

// Apenas busca o nome se estiver logado, sem redirecionamentos
if(isset($_SESSION['usuario_email'])){
    $email = $_SESSION['usuario_email'];

    // Busca o nome do aluno pelo email
    $query = $conn->prepare("SELECT nome FROM Aluno WHERE email = ? LIMIT 1");
    $query->bind_param("s", $email);
    $query->execute();
    $query->bind_result($nomeCompleto);
    
    if($query->fetch()){
        // Pega apenas o primeiro nome e coloca a primeira letra maiúscula
        $primeiroNome = explode(" ", $nomeCompleto)[0];
        $usuario_nome = ucfirst(strtolower($primeiroNome));
    }

    $query->close();
}
?>

  <!DOCTYPE html>
  <html lang="pt-br">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - Formatis</title>
    <link rel="icon" href="imgs/favicon1.png" type="image/png">
    <link rel="stylesheet" href="../css/telainicial.css">

  </head>
  <body>
    <div class="page">
      <!-- NAVBAR -->
      <nav class="navbar" role="navigation" aria-label="Navegação principal">
        <div class="logo">
          <a href="telainicial.php" aria-label="Página inicial">
            <img src="imgs/logo.png" alt="Formatis">
          </a>
        </div>
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

      <!-- HERO SECTION -->
      <section class="hero-section">
        <div class="hero-content">
          <div class="user-greeting">
            👋 Olá, <?php echo htmlspecialchars($usuario_nome); ?>!
          </div>
          <h1>Transforme seu futuro com aprendizado personalizado</h1>
          <p>Descubra cursos incríveis, trilhas personalizadas e conteúdos gerados por IA especialmente para você</p>
          <div class="cta-buttons">
            <a href="telameuscursos.php" class="btn-primary">📚 Ver Minhas Trilhas</a>
            <a href="telacursos.html" class="btn-secondary">🎓 Explorar Cursos</a>
          </div>
        </div>
      </section>


      <section class="section categories-section">
        <h2 class="section-title">Categorias Populares</h2>
        <p class="section-subtitle">Comece sua jornada de aprendizado com nossas categorias mais procuradas</p>
        
        <div class="cursos-grid">
          <div class="card-curso">
            <div class="card-imagem">
              <img src="imgs/programming.png" alt="Programação" onerror="this.src='https://via.placeholder.com/100/3d4ff7/ffffff?text=💻'">
            </div>
            <div class="card-conteudo">
              <h3>Programação</h3>
              <p>Aprenda Python, JavaScript, Java e outras linguagens para desenvolver suas habilidades em tecnologia.</p>
              <div class="card-info">
                <span>📚 25+ cursos</span>
                <span>⭐ 4.8</span>
              </div>
              <a href="telacursos.html?categoria=programacao" class="btn-acessar">Explorar</a>
            </div>
          </div>

          <div class="card-curso">
            <div class="card-imagem">
              <img src="imgs/design.png" alt="Design" onerror="this.src='https://via.placeholder.com/100/3d4ff7/ffffff?text=🎨'">
            </div>
            <div class="card-conteudo">
              <h3>Design</h3>
              <p>UI/UX, ferramentas criativas, prototipagem e design thinking para criar experiências incríveis.</p>
              <div class="card-info">
                <span>📚 15+ cursos</span>
                <span>⭐ 4.7</span>
              </div>
              <a href="telacursos.html?categoria=design" class="btn-acessar">Explorar</a>
            </div>
          </div>

          <div class="card-curso">
            <div class="card-imagem">
              <img src="imgs/business.png" alt="Negócios" onerror="this.src='https://via.placeholder.com/100/3d4ff7/ffffff?text=💼'">
            </div>
            <div class="card-conteudo">
              <h3>Negócios</h3>
              <p>Marketing digital, gestão, empreendedorismo, vendas e estratégias para alavancar sua carreira.</p>
              <div class="card-info">
                <span>📚 20+ cursos</span>
                <span>⭐ 4.6</span>
              </div>
              <a href="telacursos.html?categoria=negocios" class="btn-acessar">Explorar</a>
            </div>
          </div>

          <div class="card-curso">
            <div class="card-imagem">
              <img src="imgs/languages.png" alt="Idiomas" onerror="this.src='https://via.placeholder.com/100/3d4ff7/ffffff?text=🌎'">
            </div>
            <div class="card-conteudo">
              <h3>Idiomas</h3>
              <p>Inglês, espanhol, francês e outros idiomas para expandir suas oportunidades globais.</p>
              <div class="card-info">
                <span>📚 12+ cursos</span>
                <span>⭐ 4.9</span>
              </div>
              <a href="telacursos.html?categoria=idiomas" class="btn-acessar">Explorar</a>
            </div>
          </div>
        </div>
      </section>

      <!-- RECOMENDAÇÕES DA IA -->
      <section class="section recommendations-section">
        <h2 class="section-title">Recomendados para Você</h2>
        <p class="section-subtitle">Conteúdos selecionados com base no seu perfil e interesses</p>
        
        <div class="cursos-grid">
          <div class="card-curso">
            <div class="card-imagem">
              <img src="imgs/ai-human.png" alt="IA e Humanos" onerror="this.src='https://via.placeholder.com/100/3d4ff7/ffffff?text=🤖'">
            </div>
            <div class="card-conteudo">
              <h3>Como a IA está mudando o mundo</h3>
              <p>Artigo interativo sobre os impactos da inteligência artificial na sociedade e no mercado de trabalho.</p>
              <div class="card-info">
                <span>🕒 15 min</span>
                <span>📖 Artigo</span>
              </div>
              <a href="#" class="btn-acessar">Ler Agora</a>
            </div>
          </div>

          <div class="card-curso">
            <div class="card-imagem">
              <img src="imgs/ai.png" alt="IA" onerror="this.src='https://via.placeholder.com/100/3d4ff7/ffffff?text=🧠'">
            </div>
            <div class="card-conteudo">
              <h3>Curso Rápido: IA em 10 Minutos</h3>
              <p>Vídeo introdutório perfeito para iniciantes entenderem os conceitos básicos de inteligência artificial.</p>
              <div class="card-info">
                <span>🕒 10 min</span>
                <span>🎥 Vídeo</span>
              </div>
              <a href="#" class="btn-acessar">Assistir</a>
            </div>
          </div>
            </div>
          </div>
        </div>
      </section>

      <!-- SEÇÃO GERAR CURSO PERSONALIZADO -->
      <section class="section generate-section">
        <h2 class="section-title">Não encontrou o que procura?</h2>
        <p class="section-subtitle" style="color: white;">Crie um curso personalizado sobre qualquer assunto que desejar com nossa IA</p>
        <button class="btn-gerar" onclick="abrirModal()">
          ✨ Gerar Curso Personalizado
        </button>
      </section>

     <!-- MODAL GERAR CURSO (padronizado) -->
<div id="modalCurso" class="modal-overlay" role="dialog" aria-modal="true">
  <div class="modal-box">

    <!-- Header -->
    <div class="modal-header">
      <button class="modal-fechar" onclick="fecharModal()" aria-label="Fechar">×</button>
      <span class="modal-header__emoji">✨</span>
      <h2 class="modal-header__titulo">Criar curso personalizado</h2>
      <p class="modal-header__sub">A IA vai montar o curso do seu jeito</p>
      <div class="modal-progresso">
        <span class="modal-progresso__dot ativo"></span>
        <span class="modal-progresso__dot"></span>
        <span class="modal-progresso__dot"></span>
      </div>
    </div>

    <!-- Corpo -->
    <div class="modal-body">

      <!-- 1. Tema -->
      <span class="modal-label">
        <span class="modal-label__num">1</span>
        Sobre o que quer aprender?
      </span>
      <input type="text" id="temaCurso" class="modal-input"
             placeholder="Ex: Python para iniciantes, Marketing digital...">

      <!-- 2. Formato -->
      <span class="modal-label">
        <span class="modal-label__num">2</span>
        Como prefere o conteúdo?
      </span>
      <div class="formatos-grid" id="formatosGrid">
        <div class="formato-card ativo" data-valor="video">
          <span class="check-badge">✓</span>
          <span class="formato-card__ico">🎬</span>
          <span class="formato-card__nome">Vídeo</span>
          <span class="formato-card__desc">Assistir aulas</span>
        </div>
        <div class="formato-card" data-valor="artigo">
          <span class="check-badge">✓</span>
          <span class="formato-card__ico">📖</span>
          <span class="formato-card__nome">Artigo</span>
          <span class="formato-card__desc">Ler no ritmo</span>
        </div>
        <div class="formato-card" data-valor="ambos">
          <span class="check-badge">✓</span>
          <span class="formato-card__ico">🎯</span>
          <span class="formato-card__nome">Ambos</span>
          <span class="formato-card__desc">Combinar os dois</span>
        </div>
      </div>

      <!-- 3. Duração -->
      <span class="modal-label" id="duracaoLabel">
        <span class="modal-label__num">3</span>
        Tempo de duração?
      </span>
      <div class="niveis-grid" id="duracaoGrid">
        <div class="nivel-card ativo" data-valor="curto">
          <span class="check-badge">✓</span>
          <span class="nivel-card__ico">⚡</span>
          <span class="nivel-card__nome">Curto</span>
          <span class="nivel-card__desc">10 a 15 min</span>
        </div>
        <div class="nivel-card" data-valor="medio">
          <span class="check-badge">✓</span>
          <span class="nivel-card__ico">🕒</span>
          <span class="nivel-card__nome">Médio</span>
          <span class="nivel-card__desc">20 a 25 min</span>
        </div>
        <div class="nivel-card" data-valor="longo">
          <span class="check-badge">✓</span>
          <span class="nivel-card__ico">⏳</span>
          <span class="nivel-card__nome">Longo</span>
          <span class="nivel-card__desc">30+ min</span>
        </div>
      </div>

      <!-- 4. Nível -->
      <span class="modal-label">
        <span class="modal-label__num">4</span>
        Qual seu nível?
      </span>
      <div class="niveis-grid" id="nivelGrid">
        <div class="nivel-card ativo" data-valor="basico">
          <span class="check-badge">✓</span>
          <span class="nivel-card__ico">🌱</span>
          <span class="nivel-card__nome">Básico</span>
          <span class="nivel-card__desc">Nunca vi nada</span>
        </div>
        <div class="nivel-card" data-valor="medio">
          <span class="check-badge">✓</span>
          <span class="nivel-card__ico">🌿</span>
          <span class="nivel-card__nome">Médio</span>
          <span class="nivel-card__desc">Já conheço um pouco</span>
        </div>
        <div class="nivel-card" data-valor="avancado">
          <span class="check-badge">✓</span>
          <span class="nivel-card__ico">🌳</span>
          <span class="nivel-card__nome">Avançado</span>
          <span class="nivel-card__desc">Quero me aprofundar</span>
        </div>
      </div>

      <button class="modal-btn" id="gerarBtn">✨ Gerar curso</button>

    </div>
  </div>
</div>

    <script src="../js/telainicial.js"></script>
  </body>
  </html>