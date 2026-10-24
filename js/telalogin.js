/* ============================================================
   LOGIN — Formatis (visual novo + lógica antiga)
   ============================================================ */

let isLoading = false;

/* ============================================================
   ANO NO RODAPÉ
   ============================================================ */
const anoEl = document.getElementById('anoAtual');
if (anoEl) anoEl.textContent = new Date().getFullYear();

/* ============================================================
   MOSTRAR / OCULTAR SENHA
   ============================================================ */
function toggleSenha() {
  const input = document.getElementById('password');
  const btn = document.getElementById('togglePass');
  if (!input || !btn) return;

  if (input.type === 'password') {
    input.type = 'text';
    btn.textContent = '🙈';
    btn.setAttribute('aria-label', 'Ocultar senha');
  } else {
    input.type = 'password';
    btn.textContent = '👁';
    btn.setAttribute('aria-label', 'Mostrar senha');
  }
}

/* ============================================================
   ESTADO DE CARREGAMENTO (novo botão com spans)
   ============================================================ */
function setLoading(state) {
  isLoading = state;
  const loginBtn = document.getElementById('loginBtn');
  if (!loginBtn) return;

  const txt  = loginBtn.querySelector('.btn-primario__txt');
  const load = loginBtn.querySelector('.btn-primario__load');

  if (state) {
    txt.style.display  = 'none';
    load.style.display = 'inline-flex';
    loginBtn.disabled = true;

    // Esconde mensagens ao começar
    document.getElementById('error-message').style.display = 'none';
    document.getElementById('success-message').style.display = 'none';
  } else {
    txt.style.display  = 'inline';
    load.style.display = 'none';
    loginBtn.disabled = false;
  }
}

/* ============================================================
   MENSAGENS
   ============================================================ */
function showMessage(message, type = 'error') {
  const errorMsg   = document.getElementById('error-message');
  const successMsg = document.getElementById('success-message');

  if (type === 'error') {
    errorMsg.textContent = message;
    errorMsg.style.display = 'block';
    successMsg.style.display = 'none';
  } else {
    successMsg.textContent = message;
    successMsg.style.display = 'block';
    errorMsg.style.display = 'none';
  }
}

function limparMensagens() {
  document.getElementById('error-message').style.display = 'none';
  document.getElementById('success-message').style.display = 'none';
}

/* ============================================================
  LOGIN 
   ============================================================ */
function login() {
  if (isLoading) return;

  limparMensagens();

  const email = document.getElementById('username').value.trim();
  const senha = document.getElementById('password').value.trim();

  // Validação básica
  if (!email || !senha) {
    showMessage('Por favor, preencha todos os campos.');
    return;
  }

  if (!email.includes('@')) {
    showMessage('Por favor, insira um e-mail válido.');
    return;
  }

  setLoading(true);

  const formData = new FormData();
  formData.append('email', email);
  formData.append('senha', senha);

  fetch('login.php', {
    method: 'POST',
    body: formData
  })
    .then(res => {
      if (!res.ok) throw new Error('Erro na rede');
      return res.text();
    })
    .then(r => {
      if (r.trim() === 'success') {
        // Lembra o e-mail se marcou a caixinha
        const lembrar = document.getElementById('lembrar');
        if (lembrar && lembrar.checked) {
          localStorage.setItem('formatis_usuario_lembrado', email);
        } else {
          localStorage.removeItem('formatis_usuario_lembrado');
        }

        showMessage('Login realizado com sucesso! Redirecionando...', 'success');
        setTimeout(() => {
          window.location.href = 'telainicial.php';
        }, 1000);
      } else {
        showMessage(r.trim() || 'Credenciais inválidas. Tente novamente.');
        setLoading(false);
      }
    })
    .catch((error) => {
      showMessage('Erro de conexão com o servidor. Tente novamente.');
      console.error('Erro:', error);
      setLoading(false);
    });
}

/* ============================================================
   CADASTRO
   ============================================================ */
function cadastro() {
  if (isLoading) return;
  window.location.href = 'cadastro.html';
}

/* ============================================================
   ESQUECI A SENHA
   ============================================================ */
function esqueciSenha() {
  showMessage('Em breve: Você poderá redefinir sua senha por e-mail!', 'success');
}

/* ============================================================
   VISITANTE
   ============================================================ */
function continuarComoVisitante() {
  if (isLoading) return;
  showMessage('Entrando como visitante...', 'success');
  setTimeout(() => {
    window.location.href = 'telainicial.php';
  }, 1000);
}

/* ============================================================
   UX — Enter, foco, limpar mensagens
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('loginForm');
  if (form) {
    const inputs = form.querySelectorAll('input');

    // Enter pra submeter
    inputs.forEach(input => {
      input.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') login();
      });
    });

    // Limpar mensagens ao digitar
    inputs.forEach(input => {
      input.addEventListener('input', function () {
        limparMensagens();
      });
    });
  }

  // Foco no primeiro campo
  const userField = document.getElementById('username');
  if (userField) userField.focus();

  // Preenche se lembrou antes
  const lembrado = localStorage.getItem('formatis_usuario_lembrado');
  if (lembrado && userField) {
    userField.value = lembrado;
    const cb = document.getElementById('lembrar');
    if (cb) cb.checked = true;
    const pass = document.getElementById('password');
    if (pass) pass.focus();
  }
});