<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ============================================================
   CONEXÃO
   ============================================================ */
try {
    $pdo = new PDO('mysql:host=localhost;dbname=plataformaestudos;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Erro na conexão: ' . $e->getMessage());
}

/* ============================================================
   PARÂMETROS DA URL
   ============================================================ */
$slug    = $_GET['slug']    ?? '';
$formato = $_GET['formato'] ?? 'ambos';   // video | artigo | ambos
$nivel   = $_GET['nivel']   ?? '';

if ($slug === '') {
    die('Curso não informado. <a href="telacursos.html">Voltar</a>');
}

/* ============================================================
   BUSCA O CURSO
   ============================================================ */
$stmt = $pdo->prepare("SELECT * FROM cursos WHERE slug = ? AND ativo = 1");
$stmt->execute([$slug]);
$c = $stmt->fetch();

if (!$c) {
    http_response_code(404);
    die('<div style="font-family:sans-serif;text-align:center;padding:80px 20px;">
        <h1>Curso não encontrado</h1>
        <p><a href="javascript:history.back()">← Voltar</a></p>
    </div>');
}

/* ============================================================
   BUSCA OS MÓDULOS
   ============================================================ */
$stmt = $pdo->prepare("SELECT * FROM modulos_curso WHERE curso_id = ? ORDER BY ordem");
$stmt->execute([$c['id']]);
$modulosBanco = $stmt->fetchAll();

/* Fallback: se o curso ainda não tiver módulos no banco,
   cria 5 módulos genéricos pra não ficar vazio. */
if (!$modulosBanco) {
    $modulosBanco = [
        ['titulo' => 'Introdução',            'duracao' => '10 min', 'conteudo' => '<p>Introdução ao curso.</p>'],
        ['titulo' => 'Conceitos fundamentais','duracao' => '15 min', 'conteudo' => '<p>Conceitos base.</p>'],
        ['titulo' => 'Aplicação prática',     'duracao' => '18 min', 'conteudo' => '<p>Prática guiada.</p>'],
        ['titulo' => 'Exercícios',            'duracao' => '12 min', 'conteudo' => '<p>Exercícios.</p>'],
        ['titulo' => 'Projeto final',         'duracao' => '25 min', 'conteudo' => '<p>Projeto integrador.</p>'],
    ];
}

/* ============================================================
   MONTA O ARRAY NO FORMATO QUE A TELA USA
   ============================================================ */
$curso = [
    'nome'      => $c['nome'],
    'slug'      => $c['slug'],
    'categoria' => $c['categoria'] ?? 'Programação',
    'nivel'     => $nivel ?: ($c['nivel'] ?? 'Básico'),
    'formato'   => $formato,
    'imagem'    => $c['imagem'] ?? '',
    'modulos'   => array_map(function ($m) {
        return [
            'titulo'    => $m['titulo'],
            'duracao'   => $m['duracao'] ?? '15 min',
            'descricao' => $m['descricao'] ?? '',
            'video_url' => '',
            'conteudo'  => $m['conteudo'] ?: '<h3>Sobre este módulo</h3><p>O conteúdo deste módulo será gerado pela IA em breve.</p>',
        ];
    }, $modulosBanco),
];

/* ============================================================
   CORES POR CATEGORIA
   ============================================================ */
$coresCategoria = [
    'Programação'    => ['#3b6fd4', '#00b8e0'],
    'Produtividade'  => ['#16a34a', '#4ade80'],
    'Idiomas'        => ['#f59e0b', '#fbbf24'],
    'Negócios'       => ['#7c3aed', '#c084fc'],
];

$cat    = $curso['categoria'];
$cores  = $coresCategoria[$cat] ?? ['#3b6fd4', '#00b8e0'];
$corA   = $cores[0];
$corB   = $cores[1];
$total  = count($curso['modulos']);

$mostrarVideo  = in_array($formato, ['video', 'ambos']);
$mostrarArtigo = in_array($formato, ['artigo', 'ambos']);
$abaInicial    = $mostrarVideo ? 'video' : 'artigo';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="imgs/favicon1.png" type="image/png">
<title><?= htmlspecialchars($curso['nome']) ?> — Formatis</title>

<style>
:root {
  --cor-a: <?= $corA ?>;
  --cor-b: <?= $corB ?>;
  --texto:       #1f2937;
  --texto-suave: #6b7280;
  --borda:       #e5e7eb;
  --fundo:       #f8fafc;
  --card:        #ffffff;
  --sidebar-w:   320px;
  --header-h:    72px;
}

* { box-sizing: border-box; }

body {
  margin: 0;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background: var(--fundo);
  color: var(--texto);
  overflow-x: hidden;
}

/* ============================================================
   TOPBAR
   ============================================================ */
.topbar {
  position: sticky;
  top: 0;
  z-index: 100;
  height: var(--header-h);
  background: rgba(255,255,255,.9);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--borda);
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 0 24px;
}

.topbar__voltar {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #f1f5f9;
  color: var(--texto);
  text-decoration: none;
  font-size: 18px;
  transition: background .2s, transform .15s;
  flex-shrink: 0;
}
.topbar__voltar:hover { background: #e2e8f0; transform: translateX(-2px); }

.topbar__curso {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
}

.topbar__logo {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: linear-gradient(135deg, var(--cor-a), var(--cor-b));
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  flex-shrink: 0;
}
.topbar__logo img { width: 100%; height: 100%; object-fit: contain; padding: 6px; }

.topbar__texto { display: flex; flex-direction: column; min-width: 0; }
.topbar__cat {
  font-size: .68rem;
  text-transform: uppercase;
  letter-spacing: 1.2px;
  color: var(--texto-suave);
  font-weight: 600;
}
.topbar__nome {
  font-size: .95rem;
  font-weight: 700;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: var(--texto);
}

.topbar__spacer { flex: 1; }

.topbar__progresso {
  display: flex;
  align-items: center;
  gap: 14px;
  flex-shrink: 0;
}
.progresso-barra {
  width: 180px;
  height: 6px;
  background: #e2e8f0;
  border-radius: 999px;
  overflow: hidden;
}
.progresso-fill {
  height: 100%;
  width: 0%;
  background: linear-gradient(90deg, var(--cor-a), var(--cor-b));
  border-radius: 999px;
  transition: width .6s cubic-bezier(.4,0,.2,1);
}
.progresso-txt {
  font-size: .82rem;
  font-weight: 600;
  color: var(--texto-suave);
  white-space: nowrap;
}
.progresso-txt b { color: var(--cor-a); }

/* ============================================================
   LAYOUT
   ============================================================ */
.layout {
  display: grid;
  grid-template-columns: var(--sidebar-w) 1fr;
  min-height: calc(100vh - var(--header-h));
}

/* ============================================================
   SIDEBAR
   ============================================================ */
.sidebar {
  border-right: 1px solid var(--borda);
  background: #ffffff;
  padding: 24px 0;
  position: sticky;
  top: var(--header-h);
  height: calc(100vh - var(--header-h));
  overflow-y: auto;
}

.sidebar__head {
  padding: 0 24px 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid var(--borda);
  margin-bottom: 14px;
}
.sidebar__titulo {
  font-size: .72rem;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color: var(--texto-suave);
  font-weight: 700;
  margin: 0;
}
.sidebar__badge {
  font-size: .72rem;
  font-weight: 700;
  padding: 3px 9px;
  border-radius: 999px;
  background: color-mix(in srgb, var(--cor-a) 12%, white);
  color: var(--cor-a);
}

.modulos { list-style: none; padding: 0; margin: 0; }

.modulo {
  padding: 14px 24px 14px 20px;
  display: flex;
  align-items: flex-start;
  gap: 14px;
  cursor: pointer;
  position: relative;
  transition: background .2s;
  border-left: 3px solid transparent;
}
.modulo:hover { background: #f8fafc; }
.modulo.ativo {
  background: color-mix(in srgb, var(--cor-a) 6%, white);
  border-left-color: var(--cor-a);
}

.modulo__num {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: #f1f5f9;
  color: var(--texto-suave);
  font-size: .78rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all .25s;
}
.modulo.ativo .modulo__num {
  background: linear-gradient(135deg, var(--cor-a), var(--cor-b));
  color: #fff;
  box-shadow: 0 3px 8px color-mix(in srgb, var(--cor-a) 40%, transparent);
}
.modulo.concluido .modulo__num {
  background: #dcfce7;
  color: #16a34a;
}

.modulo__info { flex: 1; min-width: 0; }
.modulo__titulo {
  font-size: .85rem;
  font-weight: 600;
  color: var(--texto);
  line-height: 1.35;
  margin: 0 0 4px;
  display: block;
}
.modulo.ativo .modulo__titulo { color: var(--cor-a); }
.modulo.concluido .modulo__titulo { color: var(--texto-suave); }

.modulo__meta {
  font-size: .72rem;
  color: var(--texto-suave);
  display: flex;
  align-items: center;
  gap: 6px;
}

.modulo__check {
  color: #16a34a;
  font-size: 14px;
  font-weight: 700;
  flex-shrink: 0;
  opacity: 0;
  transition: opacity .2s;
}
.modulo.concluido .modulo__check { opacity: 1; }

/* ============================================================
   CONTEÚDO
   ============================================================ */
.conteudo {
  padding: 40px 56px 120px;
  max-width: 900px;
  width: 100%;
}

.conteudo__head { margin-bottom: 26px; }

.conteudo__badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: .72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1.2px;
  color: var(--cor-a);
  background: color-mix(in srgb, var(--cor-a) 10%, white);
  padding: 5px 12px;
  border-radius: 999px;
  margin-bottom: 14px;
}

.conteudo__titulo {
  font-size: 1.9rem;
  font-weight: 700;
  margin: 0 0 10px;
  color: var(--texto);
  line-height: 1.2;
}

.conteudo__meta {
  font-size: .9rem;
  color: var(--texto-suave);
  display: flex;
  align-items: center;
  gap: 14px;
  flex-wrap: wrap;
}
.conteudo__meta span { display: inline-flex; align-items: center; gap: 6px; }

/* Abas */
.abas {
  display: flex;
  gap: 8px;
  border-bottom: 1px solid var(--borda);
  margin-bottom: 28px;
}
.aba {
  background: none;
  border: none;
  padding: 12px 18px;
  font-family: inherit;
  font-size: .9rem;
  font-weight: 600;
  color: var(--texto-suave);
  cursor: pointer;
  border-bottom: 3px solid transparent;
  margin-bottom: -1px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: color .2s, border-color .2s;
}
.aba:hover { color: var(--cor-a); }
.aba.ativa {
  color: var(--cor-a);
  border-bottom-color: var(--cor-a);
}
.aba .ico { font-size: 1.05rem; }

.aba-conteudo { display: none; }
.aba-conteudo.ativa { display: block; animation: fadeUp .3s ease; }

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Vídeo */
.video-player {
  aspect-ratio: 16 / 9;
  background: #0f172a;
  border-radius: 16px;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 20px 50px rgba(0,0,0,.2);
  cursor: pointer;
  margin-bottom: 20px;
}
.video-player::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 50% 50%, color-mix(in srgb, var(--cor-a) 30%, transparent), transparent 60%),
    linear-gradient(135deg, #0f172a, #1e293b);
}
.video-player__play {
  position: relative;
  z-index: 1;
  width: 78px;
  height: 78px;
  border-radius: 50%;
  background: rgba(255,255,255,.95);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 30px;
  color: var(--cor-a);
  box-shadow: 0 12px 40px rgba(0,0,0,.4);
  transition: transform .25s;
  padding-left: 6px;
}
.video-player:hover .video-player__play { transform: scale(1.1); }

.video-player__legenda {
  position: absolute;
  bottom: 20px;
  left: 24px;
  right: 24px;
  z-index: 1;
  color: #fff;
  font-size: .85rem;
  opacity: .85;
}
.video-player__legenda strong { display: block; font-size: 1rem; margin-bottom: 2px; }

/* Artigo */
.artigo {
  font-size: 1rem;
  line-height: 1.75;
  color: #374151;
}
.artigo h3 {
  font-size: 1.2rem;
  margin: 30px 0 12px;
  color: var(--texto);
  font-weight: 700;
}
.artigo h3:first-child { margin-top: 0; }
.artigo p { margin: 0 0 16px; }
.artigo ul, .artigo ol {
  padding-left: 22px;
  margin: 0 0 18px;
}
.artigo li { margin-bottom: 6px; }
.artigo strong { color: var(--texto); font-weight: 700; }
.artigo code {
  font-family: 'JetBrains Mono', 'Courier New', monospace;
  font-size: .88em;
  background: #f1f5f9;
  color: var(--cor-a);
  padding: 2px 6px;
  border-radius: 4px;
  font-weight: 600;
}
.artigo pre {
  background: #0f172a;
  color: #e2e8f0;
  padding: 18px 20px;
  border-radius: 12px;
  overflow-x: auto;
  margin: 0 0 20px;
  font-size: .88rem;
  line-height: 1.6;
  box-shadow: 0 4px 14px rgba(0,0,0,.1);
}
.artigo pre code {
  background: transparent;
  color: inherit;
  padding: 0;
  font-weight: 400;
}
.artigo blockquote {
  border-left: 4px solid var(--cor-a);
  background: color-mix(in srgb, var(--cor-a) 6%, white);
  padding: 14px 18px;
  margin: 0 0 20px;
  border-radius: 0 10px 10px 0;
  font-size: .95rem;
  color: #4b5563;
}

/* ============================================================
   AÇÕES INFERIORES
   ============================================================ */
.acoes {
  position: fixed;
  bottom: 0;
  left: var(--sidebar-w);
  right: 0;
  padding: 16px 56px;
  background: rgba(255,255,255,.92);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-top: 1px solid var(--borda);
  display: flex;
  align-items: center;
  gap: 12px;
  z-index: 50;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 22px;
  border: none;
  border-radius: 10px;
  font-family: inherit;
  font-size: .92rem;
  font-weight: 600;
  cursor: pointer;
  transition: transform .15s, box-shadow .2s, background .2s, color .2s;
  text-decoration: none;
}
.btn:active { transform: scale(.97); }

.btn--ghost {
  background: #f1f5f9;
  color: var(--texto);
}
.btn--ghost:hover { background: #e2e8f0; }

.btn--concluir {
  background: #ffffff;
  color: #16a34a;
  border: 2px solid #bbf7d0;
}
.btn--concluir:hover { background: #f0fdf4; }
.btn--concluir.feito {
  background: #16a34a;
  color: #fff;
  border-color: #16a34a;
}

.btn--proximo {
  margin-left: auto;
  background: linear-gradient(135deg, var(--cor-a), var(--cor-b));
  color: #fff;
  box-shadow: 0 8px 20px color-mix(in srgb, var(--cor-a) 35%, transparent);
}
.btn--proximo:hover {
  transform: translateY(-1px);
  box-shadow: 0 12px 26px color-mix(in srgb, var(--cor-a) 45%, transparent);
}

/* ============================================================
   MOBILE
   ============================================================ */
.menu-toggle {
  display: none;
  width: 40px;
  height: 40px;
  border-radius: 10px;
  border: none;
  background: #f1f5f9;
  font-size: 18px;
  cursor: pointer;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
}

.sidebar-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(15,23,42,.5);
  z-index: 90;
  opacity: 0;
  transition: opacity .3s;
}
.sidebar-overlay.ativo { opacity: 1; }

@media (max-width: 900px) {
  :root { --sidebar-w: 0px; }
  .layout { grid-template-columns: 1fr; }

  .sidebar {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    width: 300px;
    z-index: 95;
    transform: translateX(-100%);
    transition: transform .3s cubic-bezier(.4,0,.2,1);
    height: 100vh;
    padding-top: 80px;
  }
  .sidebar.aberta { transform: translateX(0); }
  .sidebar-overlay { display: block; }
  .menu-toggle { display: inline-flex; }

  .conteudo { padding: 28px 20px 140px; }
  .conteudo__titulo { font-size: 1.5rem; }

  .acoes {
    left: 0;
    padding: 12px 20px;
    flex-wrap: wrap;
  }
  .btn { padding: 11px 16px; font-size: .85rem; }
  .btn--proximo { padding: 11px 20px; }

  .topbar { padding: 0 16px; gap: 12px; }
  .topbar__nome { font-size: .85rem; }
  .progresso-barra { width: 100px; }
  .progresso-txt { font-size: .75rem; }
}

@media (max-width: 520px) {
  .topbar__texto { display: none; }
  .progresso-barra { width: 80px; }
}
</style>
</head>
<body>

<!-- ================= TOPBAR ================= -->
<header class="topbar">
  <button class="menu-toggle" id="menuToggle" aria-label="Módulos">☰</button>

  <a href="detalhecurso.php?slug=<?= urlencode($curso['slug']) ?>" class="topbar__voltar" aria-label="Voltar">←</a>

  <div class="topbar__curso">
    <div class="topbar__logo">
      <?php if (!empty($curso['imagem'])): ?>
        <img src="<?= htmlspecialchars($curso['imagem']) ?>" alt="">
      <?php endif; ?>
    </div>
    <div class="topbar__texto">
      <span class="topbar__cat"><?= htmlspecialchars($cat) ?></span>
      <span class="topbar__nome"><?= htmlspecialchars($curso['nome']) ?></span>
    </div>
  </div>

  <div class="topbar__spacer"></div>

  <div class="topbar__progresso">
    <div class="progresso-barra">
      <div class="progresso-fill" id="progressoFill"></div>
    </div>
    <span class="progresso-txt">
      <b id="progNum">0</b> de <?= $total ?> módulos
    </span>
  </div>
</header>

<!-- ================= LAYOUT ================= -->
<div class="layout">

  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar__head">
      <h3 class="sidebar__titulo">Módulos do curso</h3>
      <span class="sidebar__badge"><?= $total ?></span>
    </div>

    <ul class="modulos" id="listaModulos">
      <?php foreach ($curso['modulos'] as $i => $m): ?>
        <li class="modulo <?= $i === 0 ? 'ativo' : '' ?>"
            data-index="<?= $i ?>"
            data-titulo="<?= htmlspecialchars($m['titulo']) ?>"
            data-duracao="<?= htmlspecialchars($m['duracao']) ?>">
          <span class="modulo__num"><?= $i + 1 ?></span>
          <div class="modulo__info">
            <span class="modulo__titulo"><?= htmlspecialchars($m['titulo']) ?></span>
            <span class="modulo__meta">⏱ <?= htmlspecialchars($m['duracao']) ?></span>
          </div>
          <span class="modulo__check">✓</span>
        </li>
      <?php endforeach; ?>
    </ul>
  </aside>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- CONTEÚDO -->
  <main class="conteudo">

    <div class="conteudo__head">
      <span class="conteudo__badge" id="modBadge">Módulo 1 de <?= $total ?></span>
      <h1 class="conteudo__titulo" id="modTitulo"><?= htmlspecialchars($curso['modulos'][0]['titulo']) ?></h1>
      <div class="conteudo__meta">
        <span>⏱ <span id="modDuracao"><?= htmlspecialchars($curso['modulos'][0]['duracao']) ?></span></span>
        <span>🎯 <?= htmlspecialchars($curso['nivel']) ?></span>
        <span>📚 <?= $total ?> módulos no total</span>
      </div>
    </div>

    <!-- Abas -->
    <?php if ($mostrarVideo && $mostrarArtigo): ?>
      <div class="abas">
        <button class="aba <?= $abaInicial === 'video' ? 'ativa' : '' ?>" data-aba="video">
          <span class="ico">🎬</span> Vídeo
        </button>
        <button class="aba <?= $abaInicial === 'artigo' ? 'ativa' : '' ?>" data-aba="artigo">
          <span class="ico">📖</span> Artigo
        </button>
      </div>
    <?php endif; ?>

    <!-- Aba vídeo -->
    <?php if ($mostrarVideo): ?>
      <div class="aba-conteudo <?= $abaInicial === 'video' ? 'ativa' : '' ?>" id="aba-video">
        <div class="video-player" id="videoPlayer">
          <div class="video-player__play">▶</div>
          <div class="video-player__legenda">
            <strong id="videoTitulo"><?= htmlspecialchars($curso['modulos'][0]['titulo']) ?></strong>
            <span>Aula em vídeo · <?= htmlspecialchars($curso['modulos'][0]['duracao']) ?></span>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Aba artigo -->
    <?php if ($mostrarArtigo): ?>
      <div class="aba-conteudo <?= $abaInicial === 'artigo' ? 'ativa' : '' ?>" id="aba-artigo">
        <div class="artigo" id="artigoCorpo">
          <?= $curso['modulos'][0]['conteudo'] ?>
        </div>
      </div>
    <?php endif; ?>

  </main>
</div>

<!-- ================= AÇÕES INFERIORES ================= -->
<div class="acoes">
  <button class="btn btn--ghost" id="btnAnterior">← Anterior</button>

  <button class="btn btn--concluir" id="btnConcluir">Marcar como concluído</button>

  <button class="btn btn--proximo" id="btnProximo">
    Próximo módulo →
  </button>
</div>

<script>
/* ============================================================
   DADOS DOS MÓDULOS (vêm do PHP)
   ============================================================ */
const MODULOS = <?= json_encode($curso['modulos'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
const TOTAL   = MODULOS.length;
const CURSO_SLUG = <?= json_encode($curso['slug']) ?>;

/* ============================================================
   ESTADO — localStorage por curso
   ============================================================ */
let atual = 0;
const chave = 'progresso_' + CURSO_SLUG;
let concluidos = new Set(JSON.parse(localStorage.getItem(chave) || '[]'));

/* ============================================================
   RENDER
   ============================================================ */
function render() {
  const m = MODULOS[atual];
  if (!m) return;

  document.getElementById('modBadge').textContent = `Módulo ${atual + 1} de ${TOTAL}`;
  document.getElementById('modTitulo').textContent = m.titulo;
  document.getElementById('modDuracao').textContent = m.duracao;

  const vt = document.getElementById('videoTitulo');
  if (vt) vt.textContent = m.titulo;

  const art = document.getElementById('artigoCorpo');
  if (art) art.innerHTML = m.conteudo;

  document.querySelectorAll('.modulo').forEach((el, i) => {
    el.classList.toggle('ativo', i === atual);
    el.classList.toggle('concluido', concluidos.has(i));
  });

  const btnAnt = document.getElementById('btnAnterior');
  btnAnt.disabled = atual === 0;
  btnAnt.style.opacity = atual === 0 ? .5 : 1;

  const ultimo = atual === TOTAL - 1;
  document.getElementById('btnProximo').textContent = ultimo ? 'Concluir curso ✓' : 'Próximo módulo →';

  atualizarBtnConcluir();
  atualizarProgresso();

  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function atualizarBtnConcluir() {
  const btn = document.getElementById('btnConcluir');
  if (concluidos.has(atual)) {
    btn.textContent = '✓ Concluído';
    btn.classList.add('feito');
  } else {
    btn.textContent = 'Marcar como concluído';
    btn.classList.remove('feito');
  }
}

function atualizarProgresso() {
  const n = concluidos.size;
  document.getElementById('progNum').textContent = n;
  document.getElementById('progressoFill').style.width = (n / TOTAL * 100) + '%';
}

/* ============================================================
   NAVEGAÇÃO
   ============================================================ */
function irPara(i) {
  if (i < 0 || i >= TOTAL) return;
  atual = i;
  render();
  fecharSidebar();
}

function proximo() {
  if (atual === TOTAL - 1) {
    const pct = Math.round(concluidos.size / TOTAL * 100);
    alert(`🎉 Parabéns! Você concluiu ${concluidos.size} de ${TOTAL} módulos (${pct}%).`);
    return;
  }
  irPara(atual + 1);
}

function anterior() { irPara(atual - 1); }

document.getElementById('btnProximo').addEventListener('click', proximo);
document.getElementById('btnAnterior').addEventListener('click', anterior);

document.getElementById('btnConcluir').addEventListener('click', () => {
  if (concluidos.has(atual)) {
    concluidos.delete(atual);
  } else {
    concluidos.add(atual);
    setTimeout(proximo, 400);
  }
  localStorage.setItem(chave, JSON.stringify([...concluidos]));
  atualizarBtnConcluir();
  atualizarProgresso();
  document.querySelectorAll('.modulo')[atual].classList.toggle('concluido', concluidos.has(atual));
});

/* ============================================================
   LISTA LATERAL
   ============================================================ */
document.querySelectorAll('.modulo').forEach(el => {
  el.addEventListener('click', () => irPara(parseInt(el.dataset.index, 10)));
});

/* ============================================================
   ABAS
   ============================================================ */
document.querySelectorAll('.aba').forEach(aba => {
  aba.addEventListener('click', () => {
    document.querySelectorAll('.aba').forEach(a => a.classList.remove('ativa'));
    document.querySelectorAll('.aba-conteudo').forEach(c => c.classList.remove('ativa'));
    aba.classList.add('ativa');
    const alvo = document.getElementById('aba-' + aba.dataset.aba);
    if (alvo) alvo.classList.add('ativa');
  });
});

/* ============================================================
   TECLADO
   ============================================================ */
document.addEventListener('keydown', (e) => {
  if (['INPUT', 'TEXTAREA'].includes(e.target.tagName)) return;
  if (e.key === 'ArrowRight') proximo();
  if (e.key === 'ArrowLeft') anterior();
  if (e.key === 'Enter') {
    e.preventDefault();
    document.getElementById('btnConcluir').click();
  }
});

/* ============================================================
   MOBILE
   ============================================================ */
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
const menuBtn = document.getElementById('menuToggle');

function abrirSidebar() {
  sidebar.classList.add('aberta');
  overlay.classList.add('ativo');
}
function fecharSidebar() {
  sidebar.classList.remove('aberta');
  overlay.classList.remove('ativo');
}

menuBtn.addEventListener('click', () => {
  if (sidebar.classList.contains('aberta')) fecharSidebar();
  else abrirSidebar();
});
overlay.addEventListener('click', fecharSidebar);

/* ============================================================
   BOOT
   ============================================================ */
render();
</script>

</body>
</html>