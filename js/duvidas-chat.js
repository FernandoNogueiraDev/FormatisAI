/**
 * Widget de chat de dúvidas.
 *
 * Para usar em qualquer página do Front-end, basta:
 *   1) incluir duvidas-chat.css e duvidas-chat.js
 *   2) definir, ANTES do <script src="duvidas-chat.js">, o objeto:
 *
 *      window.duvidaContexto = {
 *        conteudo_id: 12,               // opcional
 *        conteudo_titulo: "Título do conteúdo",
 *        conteudo_descricao: "Descrição/resumo do conteúdo"
 *      };
 *
 *   Se window.duvidaContexto não existir, o widget assume um contexto
 *   genérico (dúvidas gerais sobre estudos).
 */
(function () {
  const contexto = window.duvidaContexto || {
    conteudo_id: null,
    conteudo_titulo: "Dúvidas gerais",
    conteudo_descricao: "Dúvidas gerais sobre a plataforma e sobre estudos."
  };

  let historico = [];
  let carregando = false;

  function montarWidget() {
    const btn = document.createElement("button");
    btn.id = "duvidasChatBtn";
    btn.title = "Tirar dúvidas com a IA";
    btn.innerHTML = "💬";

    const painel = document.createElement("div");
    painel.id = "duvidasChatPanel";
    painel.innerHTML = `
      <div id="duvidasChatHeader">
        <div>
          Tire suas dúvidas
          <small>${escapeHtml(contexto.conteudo_titulo || "")}</small>
        </div>
        <button id="duvidasChatFechar" aria-label="Fechar">✕</button>
      </div>
      <div id="duvidasChatMensagens"></div>
      <form id="duvidasChatForm">
        <textarea id="duvidasChatInput" rows="1" placeholder="Digite sua dúvida..."></textarea>
        <button type="submit" id="duvidasChatEnviar">Enviar</button>
      </form>
    `;

    document.body.appendChild(btn);
    document.body.appendChild(painel);

    btn.addEventListener("click", () => {
      painel.classList.toggle("aberto");
      if (painel.classList.contains("aberto") && historico.length === 0) {
        adicionarMensagem("ia", "Olá! Pode perguntar o que quiser sobre este conteúdo. 🙂");
      }
    });

    document.getElementById("duvidasChatFechar").addEventListener("click", () => {
      painel.classList.remove("aberto");
    });

    document.getElementById("duvidasChatForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      if (carregando) return;

      const input = document.getElementById("duvidasChatInput");
      const pergunta = input.value.trim();
      if (!pergunta) return;

      adicionarMensagem("aluno", pergunta);
      historico.push({ papel: "aluno", texto: pergunta });
      input.value = "";

      await enviarPergunta(pergunta);
    });
  }

  function escapeHtml(texto) {
    const div = document.createElement("div");
    div.textContent = texto;
    return div.innerHTML;
  }

  function adicionarMensagem(papel, texto) {
    const container = document.getElementById("duvidasChatMensagens");
    const msg = document.createElement("div");
    msg.className = `duvida-msg ${papel}`;
    msg.textContent = texto;
    container.appendChild(msg);
    container.scrollTop = container.scrollHeight;
    return msg;
  }

  async function enviarPergunta(pergunta) {
    carregando = true;
    document.getElementById("duvidasChatEnviar").disabled = true;
    const msgCarregando = adicionarMensagem("ia carregando", "Pensando...");

    try {
      const resp = await fetch("../controller/DuvidaController.php?action=perguntar", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          conteudo_id: contexto.conteudo_id,
          conteudo_titulo: contexto.conteudo_titulo,
          conteudo_descricao: contexto.conteudo_descricao,
          pergunta: pergunta,
          historico: historico
        })
      });

      const dados = await resp.json();
      msgCarregando.remove();

      if (dados.success) {
        adicionarMensagem("ia", dados.resposta);
        historico.push({ papel: "ia", texto: dados.resposta });
      } else {
        adicionarMensagem("ia", "Desculpa, não consegui responder agora (" + (dados.message || "erro desconhecido") + ").");
      }
    } catch (err) {
      msgCarregando.remove();
      adicionarMensagem("ia", "Erro de conexão ao tentar falar com a IA.");
    } finally {
      carregando = false;
      document.getElementById("duvidasChatEnviar").disabled = false;
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", montarWidget);
  } else {
    montarWidget();
  }
})();
