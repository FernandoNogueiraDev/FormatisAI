const phrases = [
      "Estamos preparando sua trilha personalizada com IA.",
      "Analisando seu perfil de aprendizado...",
      "Conectando conhecimento aos seus objetivos...",
      "Montando seu caminho ideal de estudos...",
      "Quase lá! Finalizando os ajustes..."
    ];

    let index = 0;
    const phraseElement = document.getElementById("phrase");

    setInterval(() => {
      index = (index + 1) % phrases.length;
      phraseElement.textContent = phrases[index];
    }, 5000);

    // Stickers aleatórios
    const icons = ["🧠", "📘", "🚀", "💡", "🔍"];
    const stickersContainer = document.getElementById("stickers");

    for (let i = 0; i < 25; i++) {
      const icon = icons[Math.floor(Math.random() * icons.length)];
      const sticker = document.createElement("div");
      sticker.className = "sticker";
      sticker.textContent = icon;
      sticker.style.left = `${Math.random() * 100}%`;
      sticker.style.animationDelay = `${Math.random() * 10}s`;
      stickersContainer.appendChild(sticker);
    }

    // Partículas animadas no fundo
    const canvas = document.getElementById("particles");
    const ctx = canvas.getContext("2d");
    let particles = [];

    function resizeCanvas() {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    }

    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    for (let i = 0; i < 100; i++) {
      particles.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        r: Math.random() * 2 + 1,
        dx: (Math.random() - 0.5) * 0.5,
        dy: (Math.random() - 0.5) * 0.5
      });
    }

    function drawParticles() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.fillStyle = "rgba(255,255,255,0.2)";
      particles.forEach(p => {
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fill();
        p.x += p.dx;
        p.y += p.dy;

        if (p.x < 0 || p.x > canvas.width) p.dx *= -1;
        if (p.y < 0 || p.y > canvas.height) p.dy *= -1;
      });
      requestAnimationFrame(drawParticles);
    }

    drawParticles();

   // Recuperar os dados do localStorage
        function carregarDadosCurso() {
            const dadosSalvos = localStorage.getItem('cursoPersonalizado');
            
            if (dadosSalvos) {
                const curso = JSON.parse(dadosSalvos);
                const cursoDetails = document.getElementById('cursoDetails');
                
              
                 console.log("Dados do formulário para enviar:", dadosSalvos);

                
                $.ajax({
    type: "POST",
    url: '../controller/TrilhaController.php', 
    data: { tema: curso["tema"], tipo: curso["tipo"], nivel: curso["nivel"], duracao: curso["duracao"] }, 
    success: function(response) {
        // Handle successful response
        console.log("Success:", response);
     //   window.location.href = "minhastrilhas.html?idTrilha=" +response ["trilha_id"];
        // Update UI, display success message, etc.
    },
    error: function(jqXHR, textStatus, errorThrown) {
        
    }
});
                
            } else {
                
                alert('Nenhum curso encontrado. Voltando para a página inicial.');
                window.location.href = 'telainicial.php';
            }
        }

        // Funções auxiliares para formatar os valores
        function formatarTipo(tipo) {
            const tipos = {
                'video': '🎥 Vídeo',
                'artigo': '📝 Artigo', 
                'ambos': '📚 Vídeo + Artigo'
            };
            return tipos[tipo] || tipo;
        }

        function formatarNivel(nivel) {
            const niveis = {
                'basico': '👶 Básico',
                'medio': '🚶 Intermediário',
                'avancado': '🏃 Avançado'
            };
            return niveis[nivel] || nivel;
        }

        function formatarDuracao(duracao) {
            const duracoes = {
                'curto': '⏱ 10-15 minutos',
                'medio': '🕒 20-25 minutos',
                'longo': '⏳ 30+ minutos'
            };
            return duracoes[duracao] || duracao;
        }

        function voltarParaHome() {
            window.location.href = 'telainicial.php';
        }

        function verProgresso() {
            alert('Em desenvolvimento - acompanhe o progresso da geração do seu curso!');
        }

        // Carregar dados quando a página carregar
        document.addEventListener('DOMContentLoaded', carregarDadosCurso);



