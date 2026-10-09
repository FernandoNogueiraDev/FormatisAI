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
          realizarPesquisa();
        }
      });

      // Fechar pesquisa ao clicar fora
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
      
      // Fecha a caixa de pesquisa
      document.querySelector('.search-box').classList.remove('active');
      
      // Aqui você implementaria a lógica de pesquisa
      console.log('Pesquisando por:', termo);
      alert(`Pesquisando por: ${termo}\n\nEm um sistema real, você seria redirecionado para a página de resultados.`);
    }

    // Animação de entrada para as seções
    document.addEventListener('DOMContentLoaded', function() {
      // Configurar pesquisa
      configurarPesquisa();
      
      const sections = document.querySelectorAll('.content-section, .hero-section');
      
      const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      };
      
      const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
          }
        });
      }, observerOptions);
      
      sections.forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(section);
      });
    });