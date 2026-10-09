
    carregarDadosTrilha();
    console.log("teste");

    // Função para pegar parâmetros da URL
    function getUrlParams() {
      const params = new URLSearchParams(window.location.search);
      const paramsObj = {};
      for (const [key, value] of params) {
        paramsObj[key] = value;
      }
      return paramsObj;
    }

    // Função para obter o ID da trilha da URL
    function getTrilhaId() {
      const params = getUrlParams();
      return params.id || params.trilha_id || null;
    }

    //let etapas = [];
    let etapaAtual = 0;
    let trilhaId = null;
    let dadosTrilha = null;

    // Função principal para carregar dados da trilha
    async function carregarDadosTrilha() {
      trilhaId = getTrilhaId();
      inicializarTrilha();
      /*
      if (!trilhaId) {
          alert('ID da trilha não encontrado na URL!');
          window.location.href = 'telameuscursos.php';
          return;
      }

      try {
        
        
          

          // Mostrar loading
          document.getElementById('mainContent').innerHTML = `
              <div style="text-align: center; padding: 50px;">
                  <h2>Carregando trilha...</h2>
                  <p>Por favor, aguarde enquanto carregamos sua trilha de estudos.</p>
              </div>
          `;

          // Buscar dados da trilha
           console.log("Fetch");
          const response = await fetch(`../controller/TrilhaController.php?action=getTrilha&id=${trilhaId}`);
          console.log(`../controller/TrilhaController.php?action=getTrilha&id=${trilhaId}`);
          if (!response.ok) {
              throw new Error('Erro ao carregar trilha');
          }

          const data = await response.json();
          
          if (data.success) {
              console.log("Sucesso");
              dadosTrilha = data.trilha;
              etapas = data.etapas || [];
              inicializarTrilha();
          } else {
              throw new Error(data.message || 'Erro ao carregar trilha');
          }
              

      } catch (error) {
          console.error('Erro:', error);
          document.getElementById('mainContent').innerHTML = `
              <div style="text-align: center; padding: 50px;">
                  <h2 style="color: #ff4444;">Erro ao carregar trilha</h2>
                  <p>${error.message}</p>
                  <button class="btn" onclick="window.location.href='telameuscursos.php'">Voltar para Minhas Trilhas</button>
              </div>
          `;
      }
          */
    }

    // Função para inicializar a trilha com os dados do BD
    function inicializarTrilha() {
      const mainContent = document.getElementById('mainContent');

      mainContent.innerHTML = `
        <h1>Trilha: <?php echo $titulo ?></h1>
        <p><?php echo $descricao ?></p>

        <div id="progressoTexto">Etapa <span id="etapaAtualNumero">1</span> de <span id="totalEtapas">1</span></div>

        <div class="video-tabela">
    <div class="tabela-wrapper">
      <h2 id="nomeEtapa">Etapa 1 - <?php echo $tituloConteudo ?></h2>
      <p><?php echo $descricao ?></p>
    </div>

    <div class="video-container">
        <div id="youtubePlayer"><iframe width="100%" height="360" src="https://www.youtube.com/embed/<?php
         
         
$video_id = explode("?v=", $linkConteudo);
$video_id = $video_id[1];
         echo $video_id;
         ?>?rel=0&modestbranding=1&" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen title="Vídeo da aula"></iframe></div>
    </div>
  </div>

  <div class="btn-group-navegacao">
    <button id="voltarBtn" class="btn btn-secondary hidden">← Voltar</button>
    <button id="proximaEtapaBtn" class="btn">Próxima etapa →</button>
  </div>
</main>
    `;

      // Reconfigurar event listeners
      document.getElementById('proximaEtapaBtn').addEventListener('click', function() {
        if (etapaAtual === etapas.length - 1) {
          mostrarTelaConclusao();
        } else {
          etapaAtual++;
          carregarEtapa();
        }
      });

      document.getElementById('voltarBtn').addEventListener('click', function() {
        if (etapaAtual > 0) {
          etapaAtual--;
          carregarEtapa();
        }
      });

      // Carregar primeira etapa
      if (etapas.length > 0) {
        carregarEtapa();
      } else {
        mainContent.innerHTML = `
            <div style="text-align: center; padding: 50px;">
                <h2>Trilha vazia</h2>
                <p>Esta trilha ainda não possui conteúdo.</p>
                <button class="btn" onclick="window.location.href='telameuscursos.php'">Voltar para Minhas Trilhas</button>
            </div>
        `;
      }
    }

    // Função para carregar etapa atual
    function carregarEtapa() {
      if (etapas.length === 0) return;



      // Carregar aulas da etapa
      const tbody = document.getElementById('tabelaAulas').querySelector('tbody');
      tbody.innerHTML = '';

      etapas[etapaAtual].aulas.forEach((aula, i) => {
        const tr = document.createElement('tr');
        const storageKey = `trilha_${trilhaId}_etapa_${etapaAtual}_aula_${i}`;

        if (localStorage.getItem(storageKey) === 'true') tr.classList.add('completed');

        const tdAula = document.createElement('td');
        tdAula.textContent = (i + 1) + '. ' + aula.titulo;
        const tdObjetivo = document.createElement('td');
        tdObjetivo.textContent = aula.objetivo;

        tr.appendChild(tdAula);
        tr.appendChild(tdObjetivo);

        tr.addEventListener('click', () => {
          tr.classList.toggle('completed');
          localStorage.setItem(storageKey, tr.classList.contains('completed').toString());
          salvarProgresso();
        });

        tbody.appendChild(tr);
      });

      // Carregar vídeo
      const videoId = etapas[etapaAtual].video;
      const container = document.getElementById('youtubePlayer');
      const loading = document.getElementById('loadingMessage');

      loading.classList.remove('hidden');
      loading.textContent = 'Carregando vídeo...';

      const tempoSalvo = localStorage.getItem(`trilha_${trilhaId}_video_tempo_${videoId}`) || 0;

      if (videoId) {
        //container.innerHTML = `<iframe width="100%" height="360" src="https://www.youtube.com/embed/${videoId}?rel=0&modestbranding=1&start=${tempoSalvo}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen title="Vídeo da aula"></iframe>`;
        const iframe = container.querySelector('iframe');
        iframe.onload = () => loading.classList.add('hidden');
      } else {
        container.innerHTML = `
            <div style="display: flex; justify-content: center; align-items: center; height: 100%; background: #f0f0f0; border-radius: 12px;">
                <div style="text-align: center; padding: 20px;">
                    <h3>Conteúdo de Leitura</h3>
                    <p>${etapas[etapaAtual].conteudo_texto || 'Estude o material fornecido para esta etapa.'}</p>
                </div>
            </div>
        `;
        loading.classList.add('hidden');
      }

      setTimeout(() => loading.classList.add('hidden'), 5000);
    }

    // Função para salvar progresso no backend
    async function salvarProgresso() {
      try {
        const progresso = {
          trilha_id: trilhaId,
          etapa_atual: etapaAtual,
          concluido: etapaAtual === etapas.length - 1
        };

        await fetch('../controller/TrilhaController.php?action=salvarProgresso', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(progresso)
        });
      } catch (error) {
        console.error('Erro ao salvar progresso:', error);
      }
    }

    // Modifique o DOMContentLoaded para carregar a trilha do BD
    document.addEventListener('DOMContentLoaded', function() {
      // Configuração da pesquisa (mantenha este código)
      const searchToggle = document.querySelector('.search-toggle');
      const searchBox = document.querySelector('.search-box');
      const searchInput = document.getElementById('searchInput');

      searchToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        searchBox.classList.toggle('active');
        if (searchBox.classList.contains('active')) {
          searchInput.focus();
        }
      });

      searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          realizarPesquisa();
        }
      });

      document.addEventListener('click', function(e) {
        if (!searchBox.contains(e.target)) {
          searchBox.classList.remove('active');
        }
      });

      // Botões da tela de conclusão
      //document.getElementById('btnCompartilhar').addEventListener('click', compartilharConquista);
      //document.getElementById('btnNovaTrilha').addEventListener('click', iniciarNovaTrilha);
      //document.getElementById('btnVoltarInicio').addEventListener('click', voltarAoInicio);

      // Carregar trilha do banco de dados
      carregarDadosTrilha();
    });

    // Atualize a função mostrarTelaConclusao
    function mostrarTelaConclusao() {
      // Esconde a tela principal
      document.getElementById('mainContent').style.display = 'none';

      // Mostra a tela de conclusão
      document.getElementById('telaConclusao').style.display = 'block';

      // Atualiza o título com o nome da trilha
      document.querySelector('.conclusao-titulo span').textContent = `🎉 Parabéns!`;
      document.querySelector('.conclusao-subtitulo').innerHTML =
        `Você concluiu com sucesso a trilha <strong>${dadosTrilha.titulo || 'Introdução à Programação'}</strong>`;

      // Atualiza estatísticas
      atualizarEstatisticas();

      // Dispara confetti
      dispararConfetti();

      // Marca trilha como concluída no backend
      salvarProgresso();
    }

    // Mantenha as outras funções (atualizarEstatisticas, dispararConfetti, etc.) como estão
