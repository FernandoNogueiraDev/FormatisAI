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
    <style>
      /* ====== VARIÁVEIS CSS ====== */
      :root {
        --primary-color: #3d4ff7;
        --secondary-color: #ffca28;
        --light-color: #eef2ff;
        --dark-color: #333;
        --gray-color: #f5f5f5;
        --white: #fff;
        --error-color: #e74c3c;
        --success-color: #2ecc71;
        --border-radius: 12px;
        --transition: all 0.3s ease;
        --shadow: 0 4px 15px rgba(0,0,0,0.1);
        --shadow-hover: 0 8px 25px rgba(0,0,0,0.15);
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

      /* ====== NAVBAR ====== */
      .navbar {
        display: flex; 
        justify-content: space-between; 
        align-items: center;
        margin: auto; 
        padding: 10px 5%;
        background-color: var(--white);
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
        box-shadow: var(--shadow);
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
      }
      .dropdown-menu li a:hover {
        background-color: var(--light-color);
        color: var(--secondary-color);
      }

      /* ====== PESQUISA ====== */
      .search-box { position: relative; display: flex; align-items: center; }
      .search-toggle { background: none; border: none; font-size: 20px; cursor: pointer; color: var(--primary-color); padding: 8px; border-radius: 50%; transition: var(--transition); }
      .search-toggle:hover { background-color: rgba(61, 79, 247, 0.1); }
      .search-input { width: 0; opacity: 0; padding: 10px 15px; border: 1px solid #ddd; border-radius: 30px; outline: none; transition: var(--transition); margin-left: 10px; font-size: 14px; }
      .search-box.active .search-input { width: 250px; opacity: 1; }

      /* ====== HERO SECTION ====== */
      .hero-section {
        text-align: center;
        padding: 100px 20px 80px;
        background: linear-gradient(135deg, var(--primary-color), #00bcd4);
        color: white;
        position: relative;
        overflow: hidden;
      }

      .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.05)" points="0,1000 1000,0 1000,1000"/></svg>');
        background-size: cover;
      }

      .hero-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
        margin: 0 auto;
      }

      .hero-section h1 {
        font-size: 3.2rem;
        margin-bottom: 20px;
        font-weight: 700;
        line-height: 1.2;
      }

      .hero-section p {
        font-size: 1.3rem;
        margin-bottom: 30px;
        opacity: 0.9;
      }

      .user-greeting {
        background: rgba(255, 255, 255, 0.2);
        padding: 10px 25px;
        border-radius: 25px;
        display: inline-block;
        margin-bottom: 20px;
        backdrop-filter: blur(10px);
        font-weight: 600;
      }

      .cta-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 30px;
      }

      .btn-primary, .btn-secondary {
        padding: 14px 28px;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        transition: var(--transition);
        display: inline-block;
      }

      .btn-primary {
        background: var(--white);
        color: var(--primary-color);
      }

      .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(255, 255, 255, 0.2);
      }

      .btn-secondary {
        background: transparent;
        color: var(--white);
        border: 2px solid var(--white);
      }

      .btn-secondary:hover {
        background: var(--white);
        color: var(--primary-color);
        transform: translateY(-3px);
      }

      /* ====== SEÇÕES DE CONTEÚDO ====== */
      .section {
        padding: 80px 20px;
      }

      .section-title {
        text-align: center;
        color: var(--primary-color);
        margin-bottom: 50px;
        font-size: 2.4rem;
      }

      .section-subtitle {
        text-align: center;
        color: #666;
        margin-bottom: 40px;
        font-size: 1.2rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
      }

      .categories-section {
        background-color: var(--white);
      }

      .recommendations-section {
        background-color: var(--light-color);
      }

      .generate-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: var(--white);
        text-align: center;
      }

      .generate-section .section-title {
        color: var(--white);
      }

      /* ====== GRID DE CURSOS ====== */
      .cursos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
      }

      .card-curso {
        background: var(--white);
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        height: 100%;
      }

      .card-curso:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-hover);
      }

      .card-imagem {
        height: 160px;
        background: var(--light-color);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
      }

      .card-imagem img {
        max-height: 100px;
        max-width: 100%;
        object-fit: contain;
      }

      .card-conteudo {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
      }

      .card-curso h3 {
        margin: 0 0 10px;
        color: var(--primary-color);
        font-size: 1.3rem;
      }

      .card-curso p {
        color: #666;
        margin-bottom: 20px;
        flex-grow: 1;
      }

      .card-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        font-size: 0.9rem;
        color: #888;
      }

      .card-info span {
        display: flex;
        align-items: center;
        gap: 5px;
      }

      .btn-acessar {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: var(--transition);
        text-align: center;
        text-decoration: none;
        display: block;
      }

      .btn-acessar:hover {
        background: #2a3bd4;
        transform: translateY(-2px);
      }

      /* ====== BOTÃO GERAR CURSO ====== */
      .btn-gerar {
        background: var(--white);
        color: var(--primary-color);
        border: none;
        padding: 16px 32px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: var(--transition);
        font-size: 1.1rem;
        display: inline-flex;
        align-items: center;
        gap: 10px;
      }

      .btn-gerar:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(255, 255, 255, 0.2);
      }

      /* ====== MODAL ====== */
      .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.6);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        padding: 20px;
        box-sizing: border-box;
      }

      .modal-container {
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 40px;
        max-width: 600px;
        width: 100%;
        box-shadow: var(--shadow-hover);
        position: relative;
        animation: fadeIn 0.4s ease;
      }

      @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
      }

      .modal-close {
        position: absolute;
        top: 20px;
        right: 20px;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #999;
        transition: var(--transition);
      }

      .modal-close:hover {
        color: var(--error-color);
      }

      .modal h2 {
        color: var(--primary-color);
        text-align: center;
        margin-bottom: 30px;
        font-size: 1.8rem;
      }

      .input-box {
        display: block;
        margin: 20px auto;
        width: 95%;
        max-width: 700px;
        padding: 14px 18px;
        font-size: 18px;
        border-radius: 8px;
        border: 2px solid #ddd;
        outline: none;
        transition: var(--transition);
        box-sizing: border-box;
      }

      .input-box:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(61, 79, 247, 0.1);
      }

      .section-modal {
        margin: 25px 0;
      }

      .section-modal h3 {
        color: var(--primary-color);
        margin-bottom: 15px;
        text-align: center;
        font-size: 1.2rem;
      }

      .btn-group { 
        display: flex; 
        justify-content: center; 
        gap: 10px; 
        flex-wrap: wrap; 
        margin-top: 10px; 
      }
      
      .btn {
        padding: 12px 20px; 
        border: 2px solid transparent; 
        border-radius: 8px;
        background: #f0f0f0; 
        color: #222; 
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.3s ease; 
        font-weight: bold;
        text-align: center;
        min-width: 120px;
      }
      
      .btn:hover { 
        transform: scale(1.05); 
        box-shadow: 0 0 10px rgba(255,255,255,0.3); 
      }
      
      .btn.selected { 
        color: #000; 
        background: #fff; 
        animation: rainbow-glow 2.5s linear infinite; 
      }
      
      @keyframes rainbow-glow {
        0% { box-shadow: 0 0 16px rgba(255, 0, 0, 0.8); }
        25% { box-shadow: 0 0 16px rgba(255, 165, 0, 0.8); }
        50% { box-shadow: 0 0 16px rgba(0, 255, 0, 0.8); }
        75% { box-shadow: 0 0 16px rgba(0, 0, 255, 0.8); }
        100% { box-shadow: 0 0 16px rgba(238, 130, 238, 0.8); }
      }
      
      .hidden { display: none; }
      .submit-btn { margin-top: 30px; display: flex; justify-content: center; }

      #gerarBtn {
        background-color: var(--primary-color);
        color: white;
        font-size: 18px;
        padding: 14px 28px;
        border: none;
        border-radius: 10px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
      }

      #gerarBtn:hover {
        background-color: #2a3bd4;
        transform: translateY(-2px);
      }

     
      #mensagemStatus {
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        margin: 20px auto;
        font-weight: bold;
        font-size: 16px;
        padding: 12px 20px;
        border-radius: 8px;
        max-width: 500px;
        min-height: 40px;
        transition: opacity 1s ease-out;
      }

      #mensagemStatus.error {
        color: var(--error-color);
        background-color: rgba(231, 76, 60, 0.1);
      }

      #mensagemStatus.success {
        color: var(--success-color);
        background-color: rgba(46, 204, 113, 0.1);
      }

      #mensagemStatus.fade-out {
        opacity: 0;
      }

      /* ====== RESPONSIVIDADE ====== */
      @media (max-width: 768px) {
        .navbar { 
          flex-direction: column; 
          padding: 15px; 
        }
        .nav-links { 
          margin: 15px 0; 
          flex-wrap: wrap;
          justify-content: center;
        }
        .nav-links li { 
          margin: 5px 10px; 
        }
        .dropdown-menu { 
          position: static; 
          box-shadow: none; 
          border: none;
          display: none;
          opacity: 1;
          visibility: visible;
          transform: none;
        }
        .dropdown:hover .dropdown-menu {
          display: block;
        }
        .search-box.active .search-input { 
          width: 200px; 
        }
        
        .hero-section h1 {
          font-size: 2.4rem;
        }
        
        .hero-section {
          padding: 80px 20px 60px;
        }
        
        .section {
          padding: 60px 20px;
        }
        
        .section-title {
          font-size: 2rem;
        }
        
        .cursos-grid {
          grid-template-columns: 1fr;
        }
        
        .btn-group {
          flex-direction: column;
          align-items: center;
        }
        
        .btn {
          width: 100%;
          max-width: 200px;
        }

        .cta-buttons {
          flex-direction: column;
          align-items: center;
        }

        .btn-primary, .btn-secondary {
          width: 100%;
          max-width: 250px;
          text-align: center;
        }

        .modal-container {
          padding: 30px 20px;
        }
      }

      @media (max-width: 480px) {
        .hero-section h1 {
          font-size: 2rem;
        }
        
        .section-title {
          font-size: 1.8rem;
        }
      }
    </style>
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
              <li><a href="alterarcadastro.html">Alterar cadastro</a></li>
              <li><a href="areadoaluno.php">Acessar área do aluno</a></li>
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
        <p class="section-subtitle">Crie um curso personalizado sobre qualquer assunto que desejar com nossa IA</p>
        <button class="btn-gerar" onclick="abrirModal()">
          ✨ Gerar Curso Personalizado
        </button>
      </section>

      <!-- MODAL GERAR CURSO -->
      <div id="modalCurso" class="modal">
        <div class="modal-container">
          <button class="modal-close" onclick="fecharModal()">×</button>
          <h2>Criar Curso Personalizado com IA</h2>
          
          <input type="text" class="input-box" placeholder="Digite o tema do curso...">

          <div class="section-modal">
            <h3>Como deseja ver o conteúdo?</h3>
            <div class="btn-group" id="tipoConteudo">
              <div class="btn" data-value="video">🎥 Vídeo</div>
              <div class="btn" data-value="artigo">📝 Artigo</div>
              <div class="btn" data-value="ambos">📚 Ambos</div>
            </div>
          </div>

          <div class="section-modal hidden" id="duracaoSection">
            <h3>Tempo de Duração</h3>
            <div class="btn-group" id="duracaoBtns">
              <div class="btn" data-value="curto">⏱️ Curto<div>10-15 min</div></div>
              <div class="btn" data-value="medio">🕒 Médio<div>20-25 min</div></div>
              <div class="btn" data-value="longo">⏳ Longo<div>30+ min</div></div>
            </div>
          </div>

          <div class="section-modal">
            <h3>Nível de Conhecimento</h3>
            <div class="btn-group" id="nivelBtns">
              <div class="btn" data-value="basico">👶 Básico</div>
              <div class="btn" data-value="medio">🚶 Intermediário</div>
              <div class="btn" data-value="avancado">🏃 Avançado</div>
            </div>
          </div>

          <div class="submit-btn">
            <button id="gerarBtn" class="btn"><span>🚀 Gerar Curso</span></button>
          </div>
          
          <div id="mensagemStatus"></div>
        </div>
      </div>
    </div>

    <script>
      // Controle do Modal
      function abrirModal() {
        document.getElementById('modalCurso').style.display = 'flex';
      }

      function fecharModal() {
        const modal = document.getElementById('modalCurso');
        modal.style.display = 'none';

        // Limpa seleções
        document.querySelectorAll('#modalCurso .btn.selected').forEach(btn => {
          btn.classList.remove('selected');
        });

        // Oculta duração
        document.getElementById('duracaoSection').classList.add('hidden');

        // Limpa campo de texto
        document.querySelector('#modalCurso .input-box').value = '';

        // Limpa mensagem
        const mensagemStatus = document.getElementById('mensagemStatus');
        mensagemStatus.textContent = '';
        mensagemStatus.className = '';
        mensagemStatus.classList.remove('fade-out');
        mensagemStatus.style.opacity = '1';
      }

      // Sistema de Seleção de Botões
      function handleSelection(groupId) {
        const buttons = document.querySelectorAll(`#${groupId} .btn`);
        buttons.forEach(btn => {
          btn.addEventListener('click', () => {
            buttons.forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            
            // Se for o grupo de tipo de conteúdo, mostrar/ocultar duração
            if (groupId === 'tipoConteudo') {
              const tipo = btn.dataset.value;
              const duracaoSection = document.getElementById('duracaoSection');
              
              if (tipo === 'video' || tipo === 'ambos') {
                duracaoSection.classList.remove('hidden');
              } else {
                duracaoSection.classList.add('hidden');
                // Limpa seleção de duração se houver
                document.querySelectorAll('#duracaoBtns .btn.selected').forEach(b => {
                  b.classList.remove('selected');
                });
              }
            }
          });
        });
      }

      // Validação do Formulário
      function validarFormulario() {
        const tema = document.querySelector(".input-box").value.trim();
        const tipoSelecionado = document.querySelector("#tipoConteudo .btn.selected");
        const nivelSelecionado = document.querySelector("#nivelBtns .btn.selected");
        const duracaoSelecionada = document.querySelector("#duracaoBtns .btn.selected");
        const mensagemStatus = document.getElementById("mensagemStatus");

        const tipoValor = tipoSelecionado ? tipoSelecionado.getAttribute("data-value") : "";
        const duracaoObrigatoria = (tipoValor === "video" || tipoValor === "ambos");

        let erro = "";

        if (!tema) {
          erro = "Digite o tema do curso.";
        } else if (!tipoSelecionado) {
          erro = "Selecione o tipo de conteúdo.";
        } else if (!nivelSelecionado) {
          erro = "Selecione o nível de conhecimento.";
        } else if (duracaoObrigatoria && !duracaoSelecionada) {
          erro = "Selecione a duração do vídeo.";
        }

        if (erro) {
          mostrarMensagem(erro, 'error');
          return false;
        }

        return true;
      }

      // Função para Exibir Mensagens
      function mostrarMensagem(mensagem, tipo) {
        const mensagemStatus = document.getElementById("mensagemStatus");
        mensagemStatus.textContent = mensagem;
        mensagemStatus.className = tipo;
        mensagemStatus.classList.remove("fade-out");
        mensagemStatus.style.opacity = '1';

        setTimeout(() => {
          mensagemStatus.classList.add("fade-out");
          setTimeout(() => {
            mensagemStatus.textContent = '';
            mensagemStatus.classList.remove("fade-out");
            mensagemStatus.style.opacity = '1';
          }, 500);
        }, 3000);
      }


function enviarDadosFormulario() {
    const tema = document.querySelector(".input-box").value.trim();
    const tipoSelecionado = document.querySelector("#tipoConteudo .btn.selected");
    const nivelSelecionado = document.querySelector("#nivelBtns .btn.selected");
    const duracaoSelecionada = document.querySelector("#duracaoBtns .btn.selected");
    
    const dadosCurso = {
        tema: tema,
        tipo: tipoSelecionado ? tipoSelecionado.getAttribute("data-value") : "",
        nivel: nivelSelecionado ? nivelSelecionado.getAttribute("data-value") : "",
        duracao: duracaoSelecionada ? duracaoSelecionada.getAttribute("data-value") : "",
        dataCriacao: new Date().toISOString(),
        status: "gerando"
    };
    
    console.log("Dados do formulário para enviar:", dadosCurso);
    

    localStorage.setItem('cursoPersonalizado', JSON.stringify(dadosCurso));
    

    mostrarMensagem("Curso personalizado criado! Redirecionando...", 'success');
    

    setTimeout(() => {
        window.location.href = 'tela_gerando.html';
    }, 1500);
}

      // Pesquisa
      function configurarPesquisa() {
        const searchToggle = document.querySelector('.search-toggle');
        const searchBox = document.querySelector('.search-box');
        const searchInput = document.getElementById('searchInput');
        
        searchToggle.addEventListener('click', function(e) {
          e.stopPropagation();
          searchBox.classList.toggle('active');
          if(searchBox.classList.contains('active')) {
            searchInput.focus();
          }
        });

        searchInput.addEventListener('keypress', function(e) {
          if(e.key === 'Enter') {
            realizarPesquisa();
          }
        });

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
        
        document.querySelector('.search-box').classList.remove('active');
        
        // Redireciona para a página de cursos com o termo de pesquisa
        window.location.href = `telacursos.html?search=${encodeURIComponent(termo)}`;
      }

      // Inicialização
      document.addEventListener('DOMContentLoaded', function() {
        handleSelection('tipoConteudo');
        handleSelection('duracaoBtns');
        handleSelection('nivelBtns');
        configurarPesquisa();
        
        document.getElementById("gerarBtn").addEventListener("click", function() {
          if (validarFormulario()) {
            enviarDadosFormulario();
          }
        });
        
        document.getElementById('modalCurso').addEventListener('click', function(e) {
          if (e.target === this) fecharModal();
        });
        
        document.addEventListener('keydown', function(e) {
          if (e.key === 'Escape') fecharModal();
        });
      });
    </script>
  </body>
  </html>