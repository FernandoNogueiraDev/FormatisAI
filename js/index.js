
  let isLoading = false;
  let tipoAluno = 'autodidata';
  let arquivoSelecionado = null;

  // Elementos DOM
  const toggleSwitch = document.getElementById('tipoAlunoToggle');
  const uploadContainer = document.getElementById('uploadContainer');
  const uploadArea = document.getElementById('uploadArea');
  const fileInput = document.getElementById('historicoUpload');
  const fileName = document.getElementById('fileName');
  const registerBtn = document.getElementById('registerBtn');
  const messageDiv = document.getElementById('message');
  const form = document.getElementById('registerForm');

  // Configuração do toggle switch
  toggleSwitch.addEventListener('click', function() {
    this.classList.toggle('active');
    tipoAluno = this.classList.contains('active') ? 'escolar' : 'autodidata';
    
    if (tipoAluno === 'escolar') {
      uploadContainer.classList.add('visible');
    } else {
      uploadContainer.classList.remove('visible');
      fileInput.value = '';
      fileName.textContent = 'Nenhum arquivo selecionado';
      arquivoSelecionado = null;
    }
  });

  // Upload de arquivo
  uploadArea.addEventListener('click', () => fileInput.click());
  
  fileInput.addEventListener('change', e => {
    if (fileInput.files.length > 0) {
      arquivoSelecionado = fileInput.files[0];
      fileName.textContent = arquivoSelecionado.name;
    } else {
      arquivoSelecionado = null;
      fileName.textContent = 'Nenhum arquivo selecionado';
    }
  });

  // Drag & Drop
  uploadArea.addEventListener('dragover', e => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
  });
  
  uploadArea.addEventListener('dragleave', e => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
  });
  
  uploadArea.addEventListener('drop', e => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    
    if (e.dataTransfer.files.length > 0) {
      fileInput.files = e.dataTransfer.files;
      arquivoSelecionado = e.dataTransfer.files[0];
      fileName.textContent = arquivoSelecionado.name;
    }
  });

  // Função para mostrar mensagens
  function showMessage(message, type = 'error') {
    messageDiv.textContent = message;
    messageDiv.className = `message ${type}`;
    messageDiv.style.display = 'block';
    
    if (type === 'success') {
      setTimeout(() => {
        messageDiv.style.display = 'none';
      }, 5000);
    }
  }

  // Função para definir estado de carregamento
  function setLoading(state) {
    isLoading = state;
    
    if (state) {
      registerBtn.innerHTML = '<span class="loading-dots">Criando conta</span>';
      registerBtn.classList.add('btn-loading');
      registerBtn.disabled = true;
      messageDiv.style.display = 'none';
    } else {
      registerBtn.innerHTML = '<span>🚀 Criar Minha Conta</span>';
      registerBtn.classList.remove('btn-loading');
      registerBtn.disabled = false;
    }
  }

  // Validação do formulário
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

  // Envio do formulário
  form.addEventListener('submit', async function(event) {
    event.preventDefault();
    
    if (isLoading) return;
    
    if (!validarFormulario()) return;
    
    setLoading(true);
    
    const nome = document.getElementById('nome').value.trim();
    const email = document.getElementById('email').value.trim();
    const senha = document.getElementById('senha').value.trim();
    
    const formData = new FormData();
    formData.append('nome', nome);
    formData.append('email', email);
    formData.append('senha', senha);
    
    if (arquivoSelecionado) {
      formData.append('historico', arquivoSelecionado);
    }
    
    try {
      const resposta = await fetch('cadastrar.php', {
        method: 'POST',
        body: formData
      });
      
      const resultado = await resposta.text();
      console.log('Resposta do servidor:', resultado);
      
      if (resultado === 'success') {
        showMessage('Cadastro realizado com sucesso! Redirecionando...', 'success');
        setTimeout(() => {
          window.location.href = 'telalogin.html';
        }, 2000);
      } else if (resultado === 'email_existente') {
        showMessage('Este e-mail já está cadastrado. Tente fazer login ou use outro e-mail.');
      } else {
        showMessage('Erro: ' + resultado);
      }
    } catch (error) {
      console.error('Erro:', error);
      showMessage('Erro de conexão. Tente novamente.');
    } finally {
      setLoading(false);
    }
  });

  // Função para voltar ao login
  function voltarParaLogin() {
    if (isLoading) return;
    window.location.href = 'telalogin.html';
  }

  // Inicialização
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('nome').focus();
  });
