 // Função para alternar entre categorias de ajuda
    function configurarCategoriasAjuda() {
      const categorias = document.querySelectorAll('.categoria-item');
      const conteudos = document.querySelectorAll('.categoria-conteudo');
      
      categorias.forEach(categoria => {
        categoria.addEventListener('click', function() {
          // Remove a classe ativa de todas as categorias
          categorias.forEach(cat => cat.classList.remove('ativo'));
          
          // Adiciona a classe ativa à categoria clicada
          this.classList.add('ativo');
          
          // Oculta todos os conteúdos
          conteudos.forEach(conteudo => conteudo.style.display = 'none');
          
          // Mostra o conteúdo correspondente
          const categoriaId = this.getAttribute('data-categoria');
          document.getElementById(`${categoriaId}-conteudo`).style.display = 'block';
        });
      });
    }

    // Função para o FAQ
    function configurarFAQ() {
      const faqItems = document.querySelectorAll('.faq-item');
      
      faqItems.forEach(item => {
        const pergunta = item.querySelector('.faq-pergunta');
        
        pergunta.addEventListener('click', function() {
          // Fecha todas as outras respostas
          faqItems.forEach(otherItem => {
            if (otherItem !== item) {
              otherItem.classList.remove('ativo');
            }
          });
          
          // Alterna o estado do item clicado
          item.classList.toggle('ativo');
        });
      });
    }

    // Função para o formulário de contato
    function configurarFormularioContato() {
      const form = document.getElementById('formContato');
      
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Simula o envio do formulário
        alert('Mensagem enviada com sucesso! Entraremos em contato em breve.');
        form.reset();
      });
    }

    // Configuração da pesquisa da navbar
    function configurarPesquisa() {
      const searchToggle = document.querySelector('.search-toggle');
      const searchBox = document.querySelector('.search-box');
      const searchInput = document.getElementById('searchInput');
      
      // Abrir/fechar caixa de pesquisa
      searchToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        searchBox.classList.toggle('active');
        if(searchBox.classList.contains('active')) {
          searchInput.focus();
        }
      });

      // Pesquisa ao pressionar Enter
      searchInput.addEventListener('keypress', function(e) {
        if(e.key === 'Enter') {
          realizarPesquisaAjuda();
        }
      });

      // Fechar pesquisa ao clicar fora
      document.addEventListener('click', function(e) {
        if(!searchBox.contains(e.target)) {
          searchBox.classList.remove('active');
        }
      });
    }

    function realizarPesquisaAjuda() {
      const termo = document.getElementById('searchInput').value.trim();
      
      if(termo === '') {
        alert('Por favor, digite um termo para pesquisar.');
        return;
      }
      
      // Fecha a caixa de pesquisa
      document.querySelector('.search-box').classList.remove('active');
      
      // Busca local nas perguntas frequentes
      const faqPerguntas = document.querySelectorAll('.faq-pergunta');
      const topicos = document.querySelectorAll('.topico-ajuda h3');
      
      let encontrouResultado = false;
      
      // Procura nas perguntas do FAQ
      faqPerguntas.forEach(pergunta => {
        const texto = pergunta.textContent.toLowerCase();
        if(texto.includes(termo.toLowerCase())) {
          pergunta.scrollIntoView({ behavior: 'smooth', block: 'center' });
          pergunta.parentElement.classList.add('ativo');
          encontrouResultado = true;
        }
      });
      
      // Procura nos tópicos
      topicos.forEach(topico => {
        const texto = topico.textContent.toLowerCase();
        if(texto.includes(termo.toLowerCase())) {
          topico.scrollIntoView({ behavior: 'smooth', block: 'center' });
          encontrouResultado = true;
        }
      });
      
      if(!encontrouResultado) {
        alert(`Nenhum resultado encontrado para: "${termo}". Tente outros termos.`);
      }
    }

    // Inicialização quando o DOM estiver carregado
    document.addEventListener('DOMContentLoaded', function() {
      configurarCategoriasAjuda();
      configurarFAQ();
      configurarFormularioContato();
      configurarPesquisa();
    });