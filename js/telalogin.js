let isLoading = false;

function setLoading(state) {
  isLoading = state;
  const loginBtn = document.getElementById('loginBtn');
  const errorMsg = document.getElementById('error-message');
  
  if (state) {
    loginBtn.innerHTML = '<span class="loading-dots">Entrando</span>';
    loginBtn.classList.add('btn-loading');
    errorMsg.style.display = 'none';
  } else {
    loginBtn.innerHTML = '<span>🚀 Entrar na Plataforma</span>';
    loginBtn.classList.remove('btn-loading');
  }
}

function showMessage(message, type = 'error') {
  const errorMsg = document.getElementById('error-message');
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

function login() {
  if (isLoading) return;
  
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
      if (r === 'success') {
        showMessage('Login realizado com sucesso! Redirecionando...', 'success');
        setTimeout(() => {
          window.location.href = 'telainicial.php';
        }, 1000);
      } else {
        showMessage(r || 'Credenciais inválidas. Tente novamente.');
      }
    })
    .catch((error) => { 
      showMessage('Erro de conexão com o servidor. Tente novamente.');
      console.error('Erro:', error);
    })
    .finally(() => {
      setLoading(false);
    });
}

function cadastro() {
  if (isLoading) return;
  window.location.href = 'index.html';
}

function esqueciSenha() {
  showMessage('Em breve: Você poderá redefinir sua senha por e-mail!', 'success');
}

function continuarComoVisitante() {
  if (isLoading) return;
  showMessage('Entrando como visitante...', 'success');
  setTimeout(() => {
    window.location.href = 'telainicial.php';
  }, 1000);
}

// Melhorias de UX
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('loginForm');
  const inputs = form.querySelectorAll('input');
  
  // Enter para submeter
  inputs.forEach(input => {
    input.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        login();
      }
    });
  });
  
  // Foco automático no primeiro campo
  document.getElementById('username').focus();
  
  // Limpar mensagens ao digitar
  inputs.forEach(input => {
    input.addEventListener('input', function() {
      const errorMsg = document.getElementById('error-message');
      const successMsg = document.getElementById('success-message');
      if (errorMsg.style.display !== 'none' || successMsg.style.display !== 'none') {
        errorMsg.style.display = 'none';
        successMsg.style.display = 'none';
      }
    });
  });
});
