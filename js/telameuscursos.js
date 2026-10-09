    // Função para acessar a trilha
    function acessarTrilha(trilhaId) {
      // Redireciona para a página da trilha
      window.location.href = `minhastrilhas.php?id=${trilhaId}`;
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
        if (searchBox.classList.contains('active')) {
          searchInput.focus();
        }
      });

      // Pesquisa ao pressionar Enter
      searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          realizarPesquisa();
        }
      });

      // Fechar pesquisa ao clicar fora
      document.addEventListener('click', function(e) {
        if (!searchBox.contains(e.target)) {
          searchBox.classList.remove('active');
        }
      });
    }

    function realizarPesquisa() {
      const termo = document.getElementById('searchInput').value.trim();

      if (termo === '') {
        alert('Por favor, digite um termo para pesquisar.');
        return;
      }

      // Fecha a caixa de pesquisa
      document.querySelector('.search-box').classList.remove('active');

      // Redireciona para a página de busca
      window.location.href = `busca_trilhas.php?q=${encodeURIComponent(termo)}`;
    }

    // Inicialização quando o DOM estiver carregado
    document.addEventListener('DOMContentLoaded', function() {
      configurarPesquisa();
    });