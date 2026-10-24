/* ============================================================
   CADASTRO — Formatis
   ============================================================ */

let isLoading = false;
let tipoAluno = 'autodidata';
let arquivoSelecionado = null;

/* DOM */
const toggleSwitch     = document.getElementById('tipoAlunoToggle');
const uploadContainer  = document.getElementById('uploadContainer');
const uploadArea       = document.getElementById('uploadArea');
const fileInput        = document.getElementById('historicoUpload');
const fileName         = document.getElementById('fileName');
const registerBtn      = document.getElementById('registerBtn');
const messageDiv       = document.getElementById('message');
const form             = document.getElementById('registerForm');

/* ============================================================
   MOSTRAR / OCULTAR SENHA
   ============================================================ */
function toggleSenha() {
  const input = document.getElementById('senha');
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
   TOGGLE TIPO DE ALUNO
   ============================================================ */
function setTipoAluno(valor) {
  tipoAluno = valor;

  // Alterna a classe active no container (move o slider)
  if (valor === 'escolar') {
    toggleSwitch.classList.add('active');
    uploadContainer.classList.add('visible');
  } else {
    toggleSwitch.classList.remove('active');
    uploadContainer.classList.remove('visible');
    fileInput.value = '';
    fileName.textContent = 'Nenhum arquivo selecionado';
    arquivoSelecionado = null;
  }

  // Atualiza a cor do texto das opções
  document.querySelectorAll('.tipo-toggle__opt').forEach(opt => {
    opt.classList.toggle('active', opt.dataset.value === valor);
  });
}

// Clique em cada opção do toggle
document.querySelectorAll('.tipo-toggle__opt').forEach(opt => {
  opt.addEventListener('click', () => setTipoAluno(opt.dataset.value));
});

/* ============================================================
   UPLOAD
   ============================================================ */
uploadArea.addEventListener('click', () => fileInput.click());

fileInput.addEventListener('change', () => {
  if (fileInput.files.length > 0) {
    arquivoSelecionado = fileInput.files[0];
    fileName.textContent = arquivoSelecionado.name;
  } else {
    arquivoSelecionado = null;
    fileName.textContent = 'Nenhum arquivo selecionado';
  }
});

// Drag & drop
uploadArea.addEventListener('dragover', (e) => {
  e.preventDefault();
  uploadArea.classList.add('dragover');
});
uploadArea.addEventListener('dragleave', (e) => {
  e.preventDefault();
  uploadArea.classList.remove('dragover');
});
uploadArea.addEventListener('drop', (e) => {
  e.preventDefault();
  uploadArea.classList.remove('dragover');

  if (e.dataTransfer.files.length > 0) {
    fileInput.files = e.dataTransfer.files;
    arquivoSelecionado = e.dataTransfer.files[0];
    fileName.textContent = arquivoSelecionado.name;
  }
});

/* ============================================================
   MENSAGENS
   ============================================================ */
function showMessage(msg, type = 'error') {
  messageDiv.textContent = msg;
  messageDiv.className = 'message ' + type;
  messageDiv.style.display = 'block';

  if (type === 'success') {
    setTimeout(() => {
      messageDiv.style.display = 'none';
    }, 5000);
  }
}

/* ============================================================
   LOADING
   ============================================================ */
function setLoading(state) {
  isLoading = state;

  const txt = registerBtn.querySelector('.btn-primario__txt');
  const load = registerBtn.querySelector('.btn-primario__load');

  if (state) {
    txt.style.display = 'none';
    load.style.display = 'inline-flex';
    registerBtn.disabled = true;
    messageDiv.style.display = 'none';
  } else {
    txt.style.display = 'inline';
    load.style.display = 'none';
    registerBtn.disabled = false;
  }
}

/* ============================================================
   VALIDAÇÃO
   ============================================================ */
function validarFormulario() {
  const nome = document.getElementById('nome').value.trim();
  const email = document.getElementById('email').value.trim();
  const senha = document.getElementById('senha').value.trim();

  if (!nome || !email || !senha) {
    showMessage('Por favor, preencha todos os campos obrigatórios.');
    return false;
  }

  if (!email.includes('@') || !email.includes('.')) {
    showMessage('Por favor, insira um e-mail válido.');
    return false;
  }

  if (senha.length < 6) {
    showMessage('A senha deve ter pelo menos 6 caracteres.');
    return false;
  }

  return true;
}

/* ============================================================
   ENVIO DO FORMULÁRIO
   ============================================================ */
form.addEventListener('submit', async function (event) {
  event.preventDefault();

  if (isLoading) return;
  if (!validarFormulario()) return;

  setLoading(true);

  const nome = document.getElementById('nome').value.trim();
  const email = document.getElementById('email').value.trim();
  const senha = document.getElementById('senha').value.trim();
  const dataNascimento = document.getElementById('dataNascimento').value;

  const formData = new FormData();
  formData.append('nome', nome);
  formData.append('email', email);
  formData.append('senha', senha);
  formData.append('dataNascimento', dataNascimento);
  formData.append('tipoAluno', tipoAluno);

  if (arquivoSelecionado) {
    formData.append('historico', arquivoSelecionado);
  }

  try {
    const resposta = await fetch('cadastrar.php', {
      method: 'POST',
      body: formData
    });

    const resultado = (await resposta.text()).trim();
    console.log('Resposta do servidor:', resultado);

    if (resultado === 'success') {
      showMessage('Cadastro realizado! Redirecionando...', 'success');
      setTimeout(() => {
        window.location.href = 'telalogin.html';
      }, 1800);
    } else if (resultado === 'email_existente') {
      showMessage('Este e-mail já está cadastrado. Tente fazer login ou use outro e-mail.');
      setLoading(false);
    } else {
      showMessage('Erro: ' + resultado);
      setLoading(false);
    }
  } catch (error) {
    console.error('Erro:', error);
    showMessage('Erro de conexão. Tente novamente.');
    setLoading(false);
  }
});

/* ============================================================
   VOLTAR PARA O LOGIN
   ============================================================ */
function voltarParaLogin() {
  if (isLoading) return;
  window.location.href = 'telalogin.html';
}

/* ============================================================
   INICIALIZAÇÃO
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
  const nome = document.getElementById('nome');
  if (nome) nome.focus();
});