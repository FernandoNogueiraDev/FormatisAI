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
        window.location.href = 'tela_gerando.php';
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