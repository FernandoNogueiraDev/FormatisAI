<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=localhost;dbname=plataformaestudos;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Erro na conexão: ' . $e->getMessage());
}

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM cursos WHERE slug = ? AND ativo = 1");
$stmt->execute([$slug]);
$curso = $stmt->fetch();

if (!$curso) {
    http_response_code(404);
    exit('<h1 style="font-family:sans-serif;text-align:center;margin-top:80px;">Curso não encontrado</h1><p style="text-align:center;"><a href="javascript:history.back()">← Voltar</a></p>');
}

$stmt = $pdo->prepare("SELECT * FROM modulos_curso WHERE curso_id = ? ORDER BY ordem");
$stmt->execute([$curso['id']]);
$modulos = $stmt->fetchAll();

$coresCategoria = [
    'Programação'    => ['#3b6fd4', '#00b8e0'],
    'Produtividade'  => ['#16a34a', '#4ade80'],
    'Idiomas'        => ['#f59e0b', '#fbbf24'],
    'Negócios'       => ['#7c3aed', '#c084fc'],
];

$cat       = $curso['categoria'] ?? 'Programação';
$cores     = $coresCategoria[$cat] ?? ['#3b6fd4', '#00b8e0'];
$corHero1  = $cores[0];
$corHero2  = $cores[1];
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
  --texto:           #1f2937;
  --texto-suave:     #6b7280;
  --borda:           #e5e7eb;
  --fundo:           #f9fafb;
  --branco:          #ffffff;
  --raio:            14px;
}

* { box-sizing: border-box; }

body {
  margin: 0;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background: var(--fundo);
  color: var(--texto);
}

/* ============================================================
   FAIXA DO TOPO
   ============================================================ */
.curso-hero {
  color: #fff; padding: 44px 20px;
  position: relative; overflow: hidden;
}
.curso-hero__inner {
  max-width: 1100px; margin: 0 auto;
  display: flex; align-items: center; gap: 18px;
}
.curso-hero__logo-box {
  width: 72px; height: 72px; border-radius: 14px;
  background: rgba(255,255,255,.95); padding: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; box-shadow: 0 6px 18px rgba(0,0,0,.15);
}
.curso-hero__logo-box img {
  max-width: 100%; max-height: 100%;
  object-fit: contain; display: block;
}
.curso-hero__texto { display: flex; flex-direction: column; }
.curso-hero__categoria {
  font-size: .85rem; text-transform: uppercase;
  letter-spacing: 1.5px; opacity: .9; margin-bottom: 4px; font-weight: 600;
}
.curso-hero__titulo { font-size: 1.5rem; font-weight: 700; letter-spacing: .3px; }

/* ============================================================
   CONTAINER
   ============================================================ */
.curso-main { max-width: 1100px; margin: 0 auto; padding: 40px 20px 80px; }
.voltar {
  color: var(--texto-suave); text-decoration: none;
  font-size: .9rem; display: inline-block; margin-bottom: 24px;
}
.voltar:hover { color: var(--cor-categoria); }

.curso-info {
  display: grid; grid-template-columns: 1fr 240px; gap: 48px;
  align-items: start; margin-bottom: 40px;
}
.curso-info__titulo {
  font-size: 2.1rem; font-weight: 700; margin: 0 0 20px;
  color: var(--cor-categoria); line-height: 1.2;
}
.curso-info__desc {
  font-size: 1rem; line-height: 1.7; color: var(--texto); margin: 0 0 16px;
}
.curso-info__desc.curta { color: var(--texto-suave); }
.curso-info__acoes { margin-top: 28px; }

.btn-matricula {
  display: inline-block; background: var(--cor-categoria);
  color: #fff; border: none;
  padding: 16px 44px; border-radius: 999px;
  font-size: 1rem; font-weight: 700; cursor: pointer;
  text-decoration: none;
  transition: filter .2s, transform .1s, box-shadow .2s;
}
.btn-matricula:hover { filter: brightness(.92); box-shadow: 0 8px 20px rgba(0,0,0,.15); }
.btn-matricula:active { transform: scale(.98); }

.curso-info__lateral { padding-top: 8px; }
.curso-icones {
  display: flex; gap: 16px; justify-content: flex-end;
  font-size: 22px; color: var(--cor-categoria); margin-bottom: 12px;
}
.curso-icones span { cursor: pointer; transition: transform .15s; }
.curso-icones span:hover { transform: scale(1.15); }
.meta-divisor { height: 1px; background: var(--borda); margin: 14px 0; }
.meta-linha {
  display: flex; justify-content: space-between; align-items: center; font-size: .95rem;
}
.meta-linha__label { color: var(--texto-suave); }
.meta-linha__valor { font-weight: 700; color: var(--cor-categoria); }

/* ============================================================
   ABAS
   ============================================================ */
.tabs { border-bottom: 1px solid var(--borda); display: flex; gap: 34px; margin-top: 20px; }
.tab {
  padding: 14px 2px; font-size: 1rem; font-weight: 600;
  color: var(--texto-suave); cursor: pointer; background: none;
  border: none; border-bottom: 3px solid transparent; margin-bottom: -1px;
  transition: color .2s, border-color .2s;
}
.tab:hover { color: var(--cor-categoria); }
.tab.ativo { color: var(--cor-categoria); border-bottom-color: var(--cor-categoria); }
.tab-conteudo { padding: 30px 0; display: none; }
.tab-conteudo.ativo { display: block; }

.lista-modulos { list-style: none; padding: 0; margin: 0; }
.lista-modulos li {
  padding: 15px 0; border-bottom: 1px dashed var(--borda);
  display: flex; align-items: flex-start; gap: 12px;
  font-size: .95rem; color: var(--texto);
}
.lista-modulos li::before {
  content: ''; width: 8px; height: 8px; border-radius: 50%;
  background: var(--cor-categoria); flex-shrink: 0; margin-top: 8px;
}
.lista-modulos li:last-child { border-bottom: none; }
.tab-conteudo h3 { font-size: 1.05rem; margin: 0 0 12px; color: var(--texto); }
.tab-conteudo h3 + p { margin-bottom: 24px; }
.tab-conteudo p {
  font-size: .95rem; color: var(--texto-suave); line-height: 1.7; margin: 0 0 12px;
}

@media (max-width: 780px) {
  .curso-info { grid-template-columns: 1fr; gap: 28px; }
  .curso-info__titulo { font-size: 1.6rem; }
  .curso-info__lateral { border-top: 1px solid var(--borda); padding-top: 24px; }
  .curso-icones { justify-content: flex-start; }
  .curso-hero__titulo { font-size: 1.15rem; }
  .curso-hero__logo-box { width: 58px; height: 58px; padding: 8px; }
  .curso-hero { padding: 32px 20px; }
}
@media (max-width: 480px) {
  .curso-main { padding: 28px 16px 60px; }
  .btn-matricula { width: 100%; text-align: center; padding: 16px; }
  .tabs { gap: 22px; }
}

/* ============================================================
   MODAL DE MATRÍCULA — com cores forçadas
   ============================================================ */
.modal-overlay {
  position: fixed; inset: 0;
  background: rgba(15, 23, 42, .7);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  display: flex; align-items: center; justify-content: center;
  padding: 20px; z-index: 9999;
  opacity: 0; visibility: hidden;
  transition: opacity .3s, visibility .3s;
}
.modal-overlay.ativo { opacity: 1; visibility: visible; }

.modal-box {
  background: #ffffff !important;
  color: #1f2937 !important;
  border-radius: 24px;
  width: 100%; max-width: 540px; max-height: 92vh;
  overflow-y: auto;
  box-shadow: 0 30px 70px rgba(0,0,0,.4);
  transform: translateY(30px) scale(.95);
  transition: transform .4s cubic-bezier(.2,.9,.3,1.3);
  position: relative;
}
.modal-overlay.ativo .modal-box { transform: translateY(0) scale(1); }

/* Header */
.modal-header {
  background: linear-gradient(135deg,
    var(--cor-categoria, #3b6fd4),
    var(--cor-cat-2, #00b8e0));
  padding: 32px 30px 24px;
  border-radius: 24px 24px 0 0;
  text-align: center; position: relative; overflow: hidden;
  color: #ffffff !important;
}
.modal-header::before,
.modal-header::after {
  content: ''; position: absolute; border-radius: 50%;
  background: rgba(255,255,255,.12); pointer-events: none;
}
.modal-header::before { width: 150px; height: 150px; top: -70px; right: -50px; }
.modal-header::after { width: 100px; height: 100px; bottom: -50px; left: -40px; }

.modal-header__emoji {
  font-size: 46px; line-height: 1; display: inline-block; margin-bottom: 12px;
  animation: float 3s ease-in-out infinite;
  filter: drop-shadow(0 6px 14px rgba(0,0,0,.25));
  position: relative; z-index: 1;
}
@keyframes float {
  0%, 100% { transform: translateY(0) rotate(-3deg); }
  50%      { transform: translateY(-8px) rotate(3deg); }
}
.modal-header__titulo {
  font-size: 1.3rem; font-weight: 700; margin: 0 0 4px;
  position: relative; z-index: 1;
  color: #ffffff !important;
  text-shadow: 0 2px 8px rgba(0,0,0,.15);
}
.modal-header__sub {
  font-size: .85rem; opacity: .95; margin: 0;
  position: relative; z-index: 1; color: #ffffff !important;
}
.modal-fechar {
  position: absolute; top: 14px; right: 14px;
  width: 34px; height: 34px; border: none;
  background: rgba(255,255,255,.25); color: #fff;
  border-radius: 50%; font-size: 18px; line-height: 1;
  cursor: pointer; z-index: 2;
  display: flex; align-items: center; justify-content: center;
  transition: background .2s, transform .2s;
}
.modal-fechar:hover { background: rgba(255,255,255,.4); transform: rotate(90deg); }

.modal-progresso {
  display: flex; align-items: center; justify-content: center;
  gap: 8px; margin-top: 14px; position: relative; z-index: 1;
}
.modal-progresso__dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: rgba(255,255,255,.4); transition: all .3s;
}
.modal-progresso__dot.ativo { background: #fff; width: 24px; border-radius: 999px; }

/* Corpo */
.modal-body {
  padding: 30px 28px 28px;
  background: #ffffff !important;
  color: #1f2937 !important;
}

.modal-label {
  display: flex; align-items: center; gap: 8px;
  font-size: .78rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: .8px;
  color: #374151 !important;
  margin: 0 0 14px;
}
.modal-label__num {
  display: inline-flex; align-items: center; justify-content: center;
  width: 22px; height: 22px; border-radius: 50%;
  background: linear-gradient(135deg,
    var(--cor-categoria, #3b6fd4),
    var(--cor-cat-2, #00b8e0));
  color: #fff; font-size: .72rem; font-weight: 800;
}

/* Grids */
.formatos-grid,
.niveis-grid {
  display: grid; grid-template-columns: repeat(3, 1fr);
  gap: 12px; margin-bottom: 28px;
}

.formato-card,
.nivel-card {
  padding: 22px 10px 18px;
  text-align: center;
  cursor: pointer;
  position: relative;
  user-select: none;
  border-radius: 18px;
  border: 2px solid #e5e7eb;
  background: #ffffff;
  color: #1f2937;
  transition: transform .25s cubic-bezier(.2,.9,.3,1.3),
              border-color .2s, box-shadow .3s, background .25s;
  opacity: 0;
  transform: translateY(10px);
  overflow: hidden;
}

.modal-overlay.ativo .formato-card,
.modal-overlay.ativo .nivel-card {
  animation: cardIn .4s cubic-bezier(.2,.9,.3,1.2) forwards;
}
.modal-overlay.ativo .formato-card:nth-child(1),
.modal-overlay.ativo .nivel-card:nth-child(1) { animation-delay: .12s; }
.modal-overlay.ativo .formato-card:nth-child(2),
.modal-overlay.ativo .nivel-card:nth-child(2) { animation-delay: .2s; }
.modal-overlay.ativo .formato-card:nth-child(3),
.modal-overlay.ativo .nivel-card:nth-child(3) { animation-delay: .28s; }

@keyframes cardIn {
  to { opacity: 1; transform: translateY(0); }
}

.formato-card:hover,
.nivel-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0,0,0,.1);
  border-color: color-mix(in srgb, var(--cor-categoria, #3b6fd4) 40%, #e5e7eb);
}

/* ============================================================
   ⭐ ATIVO — AGORA FICA MUITO ÓBVIO
   Fundo sólido na cor da categoria + texto branco
   ============================================================ */
.formato-card.ativo,
.nivel-card.ativo {
  background: linear-gradient(135deg,
    var(--cor-categoria, #3b6fd4),
    var(--cor-cat-2, #00b8e0)) !important;
  border-color: transparent !important;
  box-shadow:
    0 16px 34px color-mix(in srgb, var(--cor-categoria, #3b6fd4) 50%, transparent),
    inset 0 1px 0 rgba(255,255,255,.25);
}

.formato-card.ativo .formato-card__nome,
.nivel-card.ativo .nivel-card__nome {
  color: #ffffff !important;
}
.formato-card.ativo .formato-card__desc,
.nivel-card.ativo .nivel-card__desc {
  color: rgba(255,255,255,.85) !important;
}
.formato-card.ativo .formato-card__ico,
.nivel-card.ativo .nivel-card__ico {
  background: rgba(255,255,255,.25) !important;
  border-color: rgba(255,255,255,.4) !important;
  transform: scale(1.15) rotate(-8deg);
  animation: bounceIcon .5s cubic-bezier(.2,.9,.3,1.6);
}
@keyframes bounceIcon {
  0%   { transform: scale(1) rotate(0); }
  50%  { transform: scale(1.28) rotate(-12deg); }
  100% { transform: scale(1.15) rotate(-8deg); }
}

/* Badge check */
.formato-card .check-badge,
.nivel-card .check-badge {
  position: absolute;
  top: 10px; right: 10px;
  width: 26px; height: 26px;
  border-radius: 50%;
  background: #ffffff;
  color: var(--cor-categoria, #3b6fd4);
  font-size: 14px;
  line-height: 26px;
  text-align: center;
  font-weight: 900;
  opacity: 0;
  transform: scale(0) rotate(-90deg);
  transition: opacity .3s, transform .35s cubic-bezier(.2,.9,.3,1.6);
  box-shadow: 0 4px 10px rgba(0,0,0,.2);
  z-index: 3;
}
.formato-card.ativo .check-badge,
.nivel-card.ativo .check-badge {
  opacity: 1;
  transform: scale(1) rotate(0);
}

/* Ícone padrão */
.formato-card__ico,
.nivel-card__ico {
  display: flex; align-items: center; justify-content: center;
  width: 56px; height: 56px; margin: 0 auto 12px;
  border-radius: 50%;
  font-size: 26px;
  background: #f8fafc;
  border: 1px solid #e5e7eb;
  transition: all .35s cubic-bezier(.2,.9,.3,1.4);
}
.formato-card:hover .formato-card__ico,
.nivel-card:hover .nivel-card__ico {
  transform: scale(1.08) rotate(-3deg);
}

.formato-card__nome,
.nivel-card__nome {
  font-size: .9rem; font-weight: 700;
  color: #1f2937 !important;
  display: block; margin-bottom: 3px;
}
.formato-card__desc,
.nivel-card__desc {
  font-size: .72rem;
  color: #6b7280 !important;
  line-height: 1.35; display: block; font-weight: 500;
}

/* Botão */
.modal-btn {
  width: 100%; padding: 18px; border: none; border-radius: 14px;
  background: linear-gradient(135deg,
    var(--cor-categoria, #3b6fd4),
    var(--cor-cat-2, #00b8e0)) !important;
  color: #ffffff !important;
  font-family: inherit; font-size: 1.02rem;
  font-weight: 700; letter-spacing: .2px;
  cursor: pointer; position: relative; overflow: hidden;
  box-shadow:
    0 10px 24px color-mix(in srgb, var(--cor-categoria, #3b6fd4) 40%, transparent),
    inset 0 1px 0 rgba(255,255,255,.25);
  transition: transform .15s, box-shadow .25s, filter .2s;
}
.modal-btn::before {
  content: ''; position: absolute; top: 0; left: -100%;
  width: 100%; height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,.4), transparent);
  transition: left .7s;
}
.modal-btn:hover::before { left: 100%; }
.modal-btn:hover {
  transform: translateY(-2px);
  filter: brightness(1.06);
}
.modal-btn:active { transform: translateY(0) scale(.98); }

@media (max-width: 520px) {
  .modal-box { border-radius: 20px; }
  .modal-header { padding: 24px 22px 18px; border-radius: 20px 20px 0 0; }
  .modal-header__emoji { font-size: 38px; }
  .modal-header__titulo { font-size: 1.1rem; }
  .modal-body { padding: 24px 20px 24px; }
  .formatos-grid, .niveis-grid { gap: 8px; }
  .formato-card, .nivel-card { padding: 16px 6px 12px; border-radius: 14px; }
  .formato-card__ico, .nivel-card__ico { width: 44px; height: 44px; font-size: 20px; margin-bottom: 8px; }
  .formato-card__nome, .nivel-card__nome { font-size: .8rem; }
  .formato-card__desc, .nivel-card__desc { display: none; }
}
</style>
</head>
<body style="--cor-categoria: <?= $corHero1 ?>; --cor-cat-2: <?= $corHero2 ?>;">

<section class="curso-hero" style="background: linear-gradient(90deg, <?= $corHero1 ?>, <?= $corHero2 ?>);">
  <div class="curso-hero__inner">
    <div class="curso-hero__logo-box">
      <?php if (!empty($curso['imagem'])): ?>
        <img src="<?= htmlspecialchars($curso['imagem']) ?>" alt="<?= htmlspecialchars($curso['nome']) ?>">
      <?php else: ?>
        📘
      <?php endif; ?>
    </div>
    <div class="curso-hero__texto">
      <span class="curso-hero__categoria"><?= htmlspecialchars($cat) ?></span>
      <span class="curso-hero__titulo"><?= htmlspecialchars($curso['nome']) ?></span>
    </div>
  </div>
</section>

<main class="curso-main">

  <a href="javascript:history.back()" class="voltar">← Voltar para todos os cursos</a>

  <div class="curso-info">
    <div>
      <h1 class="curso-info__titulo"><?= htmlspecialchars($curso['nome']) ?></h1>
      <p class="curso-info__desc"><?= nl2br(htmlspecialchars($curso['descricao'])) ?></p>
      <p class="curso-info__desc curta">
        Além disso, você conhecerá os conceitos que fazem parte do dia a dia
        da área de <?= htmlspecialchars($cat) ?>, com exemplos práticos e
        exercícios para fixar o conteúdo.
      </p>
      <div class="curso-info__acoes">
        <a href="#" class="btn-matricula">Quero me matricular</a>
      </div>
    </div>

    <aside class="curso-info__lateral">
      <div class="curso-icones">
        <span title="Compartilhar">🔗</span>
        <span title="Favoritar">♡</span>
      </div>
      <div class="meta-divisor"></div>
      <div class="meta-linha">
        <span class="meta-linha__label">Duração</span>
        <span class="meta-linha__valor"><?= (int)$curso['carga_horaria'] ?>h</span>
      </div>
      <div class="meta-divisor"></div>
      <div class="meta-linha">
        <span class="meta-linha__label">Nível</span>
        <span class="meta-linha__valor"><?= htmlspecialchars($curso['nivel'] ?? 'Iniciante') ?></span>
      </div>
      <div class="meta-divisor"></div>
      <div class="meta-linha">
        <span class="meta-linha__label">Módulos</span>
        <span class="meta-linha__valor"><?= count($modulos) ?: (int)$curso['modulos'] ?></span>
      </div>
    </aside>
  </div>

  <div class="tabs">
    <button class="tab ativo" data-tab="conteudo">Conteúdo</button>
    <button class="tab" data-tab="detalhes">Detalhes</button>
  </div>

  <div class="tab-conteudo ativo" id="tab-conteudo">
    <?php if ($modulos): ?>
      <ul class="lista-modulos">
        <?php foreach ($modulos as $i => $m): ?>
          <li>Módulo <?= $i + 1 ?> — <?= htmlspecialchars($m['titulo']) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>Os módulos deste curso ainda não foram cadastrados.</p>
    <?php endif; ?>
  </div>

  <div class="tab-conteudo" id="tab-detalhes">
    <h3>Sobre este curso</h3>
    <p><?= nl2br(htmlspecialchars($curso['descricao'])) ?></p>
    <h3>O que você vai aprender</h3>
    <p>
      Ao concluir este curso, você terá domínio dos principais conceitos
      e estará apto a aplicar o conhecimento em projetos reais, com carga
      horária total de <?= (int)$curso['carga_horaria'] ?> horas e nível
      <?= htmlspecialchars($curso['nivel'] ?? 'Iniciante') ?>.
    </p>
  </div>

</main>

<!-- ============ MODAL DE MATRÍCULA ============ -->
<div class="modal-overlay" id="modalMatricula">
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalTitulo">

    <div class="modal-header">
      <button class="modal-fechar" onclick="fecharModalMatricula()" aria-label="Fechar">×</button>
      <span class="modal-header__emoji">🎓</span>
      <h2 class="modal-header__titulo" id="modalTitulo">Vamos personalizar seu curso</h2>
      <p class="modal-header__sub">
        A IA vai montar <strong><?= htmlspecialchars($curso['nome']) ?></strong> do seu jeito
      </p>
      <div class="modal-progresso">
        <span class="modal-progresso__dot ativo"></span>
        <span class="modal-progresso__dot"></span>
      </div>
    </div>

    <div class="modal-body">

      <span class="modal-label">
        <span class="modal-label__num">1</span>
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

      <span class="modal-label">
        <span class="modal-label__num">2</span>
        Qual seu nível no tema?
      </span>

      <div class="niveis-grid" id="niveisGrid">
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

      <button class="modal-btn" onclick="confirmarMatricula()">
        ✨ Começar curso
      </button>

    </div>
  </div>
</div>

<script>
/* ============================================================
   ABAS
   ============================================================ */
document.querySelectorAll('.tab').forEach(tab => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('ativo'));
    document.querySelectorAll('.tab-conteudo').forEach(c => c.classList.remove('ativo'));
    tab.classList.add('ativo');
    document.getElementById('tab-' + tab.dataset.tab).classList.add('ativo');
  });
});

/* ============================================================
   FAVORITAR
   ============================================================ */
const heart = document.querySelector('.curso-icones span:nth-child(2)');
if (heart) {
  heart.addEventListener('click', () => {
    heart.textContent = heart.textContent === '♡' ? '♥' : '♡';
  });
}

/* ============================================================
   MODAL
   ============================================================ */
const modalMatricula = document.getElementById('modalMatricula');
const btnMatricula = document.querySelector('.btn-matricula');

if (btnMatricula && modalMatricula) {
  btnMatricula.addEventListener('click', (e) => {
    e.preventDefault();
    abrirModalMatricula();
  });
}

function abrirModalMatricula() {
  modalMatricula.classList.add('ativo');
  document.body.style.overflow = 'hidden';
}

function fecharModalMatricula() {
  modalMatricula.classList.remove('ativo');
  document.body.style.overflow = '';
}

modalMatricula.addEventListener('click', (e) => {
  if (e.target.id === 'modalMatricula') fecharModalMatricula();
});
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && modalMatricula.classList.contains('ativo')) {
    fecharModalMatricula();
  }
});

let formatoEscolhido = 'video';
document.querySelectorAll('.formato-card').forEach(card => {
  card.addEventListener('click', () => {
    document.querySelectorAll('.formato-card').forEach(c => c.classList.remove('ativo'));
    card.classList.add('ativo');
    formatoEscolhido = card.dataset.valor;
  });
});

let nivelEscolhido = 'basico';
document.querySelectorAll('.nivel-card').forEach(card => {
  card.addEventListener('click', () => {
    document.querySelectorAll('.nivel-card').forEach(c => c.classList.remove('ativo'));
    card.classList.add('ativo');
    nivelEscolhido = card.dataset.valor;
    document.querySelectorAll('.modal-progresso__dot').forEach(d => d.classList.add('ativo'));
  });
});

/* ============================================================
   CONFIRMAR → REDIRECIONA PARA SUA TELA DE GERANDO
   ============================================================ */

const URL_GERANDO = 'preparando.php';


/* ============================================================
   CONFIRMAR → PREPARANDO (com slug, formato e nível na URL)
   ============================================================ */
function confirmarMatricula() {
  const prefs = {
    formato: formatoEscolhido,
    nivel:   nivelEscolhido
  };

  // Slug do curso (vem do PHP)
  const slug = '<?= urlencode($curso['slug']) ?>';

  // Monta a URL com todos os parâmetros
  const url =
    'preparando.php' +
    '?slug='    + encodeURIComponent(slug) +
    '&formato=' + encodeURIComponent(prefs.formato) +
    '&nivel='   + encodeURIComponent(prefs.nivel);

  console.log('Redirecionando para:', url);

  fecharModalMatricula();
  window.location.href = url;
}
</script>

</body>
</html>