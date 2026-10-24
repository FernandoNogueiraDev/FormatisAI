/* ============================================================
   MODAL DE GERAR CURSO (padronizado)
   ============================================================ */
const modalCurso = document.getElementById('modalCurso');

function abrirModal() {
  modalCurso.classList.add('ativo');
  document.body.style.overflow = 'hidden';
}

function fecharModal() {
  modalCurso.classList.remove('ativo');
  document.body.style.overflow = '';

  const campo = document.getElementById('temaCurso');
  if (campo) {
    campo.value = '';
    campo.style.borderColor = '';
  }
}

// Fecha clicando fora ou ESC
modalCurso.addEventListener('click', (e) => {
  if (e.target.id === 'modalCurso') fecharModal();
});
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && modalCurso.classList.contains('ativo')) fecharModal();
});

/* Estado */
let formatoEscolhido = 'video';
let duracaoEscolhida = 'curto';
let nivelEscolhido   = 'basico';

/* Formato */
document.querySelectorAll('#formatosGrid .formato-card').forEach(card => {
  card.addEventListener('click', () => {
    document.querySelectorAll('#formatosGrid .formato-card').forEach(c => c.classList.remove('ativo'));
    card.classList.add('ativo');
    formatoEscolhido = card.dataset.valor;

    // Só mostra duração se não for artigo
    const mostrar = (formatoEscolhido !== 'artigo');
    document.getElementById('duracaoLabel').style.display = mostrar ? '' : 'none';
    document.getElementById('duracaoGrid').style.display  = mostrar ? '' : 'none';
  });
});

/* Duração */
document.querySelectorAll('#duracaoGrid .nivel-card').forEach(card => {
  card.addEventListener('click', () => {
    document.querySelectorAll('#duracaoGrid .nivel-card').forEach(c => c.classList.remove('ativo'));
    card.classList.add('ativo');
    duracaoEscolhida = card.dataset.valor;
  });
});

/* Nível */
document.querySelectorAll('#nivelGrid .nivel-card').forEach(card => {
  card.addEventListener('click', () => {
    document.querySelectorAll('#nivelGrid .nivel-card').forEach(c => c.classList.remove('ativo'));
    card.classList.add('ativo');
    nivelEscolhido = card.dataset.valor;
  });
});

/* Botão Gerar */
document.getElementById('gerarBtn').addEventListener('click', () => {
  const campo = document.getElementById('temaCurso');
  const tema  = campo.value.trim();

  if (tema.length < 3) {
    campo.focus();
    campo.style.borderColor = '#ef4444';
    setTimeout(() => { campo.style.borderColor = ''; }, 2000);
    return;
  }

  const dados = {
    tema:    tema,
    formato: formatoEscolhido,
    duracao: duracaoEscolhida,
    nivel:   nivelEscolhido
  };

  localStorage.setItem('cursoPersonalizado', JSON.stringify(dados));
  console.log('Curso personalizado:', dados);

  const params = new URLSearchParams(dados);

  // 👇 Se sua página de preparação tem outro nome, troque aqui
  window.location.href = 'preparando.php?' + params.toString();
});

/* ============================================================
   PESQUISA
   ============================================================ */
function configurarPesquisa() {
  const searchToggle = document.querySelector('.search-toggle');
  const searchBox    = document.querySelector('.search-box');
  const searchInput  = document.getElementById('searchInput');
  if (!searchToggle || !searchBox || !searchInput) return;

  searchToggle.addEventListener('click', function (e) {
    e.stopPropagation();
    searchBox.classList.toggle('active');
    if (searchBox.classList.contains('active')) searchInput.focus();
  });

  searchInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') realizarPesquisa();
  });

  document.addEventListener('click', function (e) {
    if (!searchBox.contains(e.target)) searchBox.classList.remove('active');
  });
}

function realizarPesquisa() {
  const termo = document.getElementById('searchInput').value.trim();
  if (termo === '') {
    alert('Por favor, digite um termo para pesquisar.');
    return;
  }
  document.querySelector('.search-box').classList.remove('active');
  window.location.href = `telacursos.html?search=${encodeURIComponent(termo)}`;
}

/* ============================================================
   INICIALIZAÇÃO
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {
  configurarPesquisa();
});