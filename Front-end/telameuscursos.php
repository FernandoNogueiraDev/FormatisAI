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
  <link rel="stylesheet" href="../css/telameuscursos.css">
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

  <script src="../js/telameuscursos.js"></script>

</body>

</html>