function handleSelection(groupId) {
      const buttons = document.querySelectorAll(`#${groupId} .btn`);
      buttons.forEach(btn => {
        btn.addEventListener('click', () => {
          buttons.forEach(b => b.classList.remove('selected'));
          btn.classList.add('selected');
        });
      });
    }

    handleSelection('tipoConteudo');
    handleSelection('duracaoBtns');
    handleSelection('nivelBtns');

    const tipoBtns = document.querySelectorAll('#tipoConteudo .btn');
    const duracaoSection = document.getElementById('duracaoSection');

    tipoBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const tipo = btn.dataset.value;
        const duracaoBtns = document.querySelectorAll('#duracaoBtns .btn');

        if (tipo === 'video' || tipo === 'ambos') {
          duracaoSection.classList.remove('hidden');
        } else {
          duracaoSection.classList.add('hidden');
          duracaoBtns.forEach(b => b.classList.remove('selected'));
        }
      });
    });

    const enviarBtn = document.getElementById("enviarBtn");
    const mensagemErro = document.getElementById("mensagemErro");
    const campoTexto = document.getElementById("campoTexto");

    enviarBtn.addEventListener("click", () => {
      mensagemErro.classList.add("hidden");
      mensagemErro.textContent = "";

      const texto = campoTexto.value.trim();
      const tipoSelecionado = document.querySelector("#tipoConteudo .btn.selected");
      const nivelSelecionado = document.querySelector("#nivelBtns .btn.selected");
      const duracaoVisivel = !duracaoSection.classList.contains("hidden");
      const duracaoSelecionada = document.querySelector("#duracaoBtns .btn.selected");

      let erros = [];

      if (!texto) erros.push("Digite o que deseja aprender.");
      if (!tipoSelecionado) erros.push("Selecione o tipo de conteúdo.");
      if (!nivelSelecionado) erros.push("Selecione o nível de conhecimento.");
      if (duracaoVisivel && !duracaoSelecionada) erros.push("Selecione a duração desejada.");

      if (erros.length > 0) {
        mensagemErro.textContent = erros.join(" ");
        mensagemErro.classList.remove("hidden");
        return;
      }

      // Se tudo estiver preenchido:
      document.body.classList.add("fade-out");
      setTimeout(() => {
        window.location.href = "file:///C:/Users/gusta/OneDrive/%C3%81rea%20de%20Trabalho/tela%20gerando.html";
      }, 1500);
    });