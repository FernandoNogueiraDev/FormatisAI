import google.generativeai as genai
from dotenv import load_dotenv
import os, sys, json, requests, time

# ---------------------------------------------------------------
# Setup
# ---------------------------------------------------------------
if getattr(sys, "frozen", False):
    base_dir = os.path.dirname(sys.executable)
else:
    base_dir = os.path.dirname(os.path.abspath(__file__))

load_dotenv(os.path.join(base_dir, ".env"))

GEMINI_KEY        = os.getenv("gemini_key")
GOOGLE_SEARCH_KEY = os.getenv("google_search_key")
SEARCH_ENGINE_ID  = os.getenv("search_engine_id")

if not GEMINI_KEY:
    print(json.dumps({"erro": "gemini_key não configurada no .env"}))
    sys.exit(1)

genai.configure(api_key=GEMINI_KEY)


# ---------------------------------------------------------------
# Schema — o Gemini é obrigado a responder nesse formato
# ---------------------------------------------------------------
SCHEMA_TRILHA = {
    "type": "object",
    "properties": {
        "trilha_estudos": {
            "type": "object",
            "properties": {
                "duracao_total_trilha": {"type": "string"},
                "conteudo": {
                    "type": "array",
                    "items": {
                        "type": "object",
                        "properties": {
                            "titulo":          {"type": "string"},
                            "descricao":       {"type": "string"},
                            "tipo_conteudo":   {"type": "string"},
                            "link_referencia": {"type": "string"},
                            "fonte":           {"type": "string"},
                            "duracao":         {"type": "string"}
                        },
                        "required": ["titulo", "descricao", "tipo_conteudo", "duracao"]
                    }
                }
            },
            "required": ["duracao_total_trilha", "conteudo"]
        },
        "perfil_apresentacao": {
            "type": "object",
            "properties": {
                "densidade": {"type": "string", "enum": ["minima", "equilibrada", "detalhada"]},
                "tom":       {"type": "string", "enum": ["informal", "profissional", "motivacional"]},
                "ritmo":     {"type": "string", "enum": ["calmo", "dinamico"]},
                "paleta":    {"type": "string", "enum": ["calmo", "energetico", "tecnico", "minimalista"]},
                "mensagens_carregamento": {
                    "type": "array",
                    "items": {"type": "string"},
                    "minItems": 4,
                    "maxItems": 4
                }
            },
            "required": ["densidade", "tom", "ritmo", "paleta", "mensagens_carregamento"]
        }
    },
    "required": ["trilha_estudos", "perfil_apresentacao"]
}


# Fallback caso a validação falhe
PERFIL_FALLBACK = {
    "densidade": "equilibrada",
    "tom": "profissional",
    "ritmo": "calmo",
    "paleta": "tecnico",
    "mensagens_carregamento": [
        "Preparando seu ambiente",
        "Analisando seu perfil",
        "Organizando sua trilha",
        "Quase pronto"
    ]
}


# ---------------------------------------------------------------
# Perfil de teste (roda sem argumento)
# ---------------------------------------------------------------
aluno_autodidata_teste = {
    "tipo_aluno": "Autodidata",
    "nome": "Joana",
    "idade": 23,
    "interesse": "Tecnologia",
    "objetivo_carreira": "Desenvolvedor Java",
    "nivel": "Iniciante",
    "tipo_conteudo": ["vídeo", "artigo"]
}


# ---------------------------------------------------------------
# Montar perfil
# ---------------------------------------------------------------
def montar_perfil(aluno):
    tipo = aluno.get("tipo_aluno", "").lower()

    if tipo == "autodidata":
        return {
            "nome": aluno["nome"],
            "idade": aluno.get("idade"),
            "interesse": aluno.get("interesse"),
            "objetivo_carreira": aluno.get("objetivo_carreira"),
            "nivel": aluno["nivel"],
            "tipo_conteudo": aluno["tipo_conteudo"],
            "contexto_aprendizagem": (
                "Aluno autodidata que aprende de forma independente e flexível, "
                "buscando conteúdos práticos, gratuitos e de rápida aplicação."
            )
        }
    elif tipo == "escola":
        disciplinas = aluno.get("disciplinas", {})
        disc_dificuldade = [d for d, m in disciplinas.items() if m < 6.0]
        return {
            "nome": aluno["nome"],
            "idade": aluno.get("idade"),
            "disciplinas": disc_dificuldade,
            "nivel": aluno.get("ano_letivo"),
            "tipo_conteudo": aluno["tipo_conteudo"],
            "contexto_aprendizagem": (
                "Aluno de escola com dificuldades em algumas disciplinas. "
                "Reforçar conteúdos de forma didática, com exercícios práticos "
                "e materiais de apoio para revisão antes das provas."
            )
        }
    raise ValueError(f"tipo de perfil inválido: {tipo}")


# ---------------------------------------------------------------
# Prompt
# ---------------------------------------------------------------
def montar_prompt(perfil):
    return f"""Você é um tutor digital especializado em personalização de aprendizagem.

Perfil do aluno: {perfil}

PARTE 1 — TRILHA DE ESTUDOS
- Trilha progressiva a partir do nível: {perfil['nivel']}
- Entre 5 e 10 conteúdos, do mais básico ao mais avançado
- Deixe link_referencia e fonte como string vazia "" (preenchidos depois)
- Duração no formato "30min", "1h20", "45min"

PARTE 2 — PERFIL DE APRESENTAÇÃO
Baseado em QUEM É esse aluno, escolha como a interface deve se comunicar:
- densidade: minima (tela limpa) | equilibrada (log resumido) | detalhada (log completo)
- tom: informal | profissional | motivacional
- ritmo: calmo | dinamico
- paleta: calmo | energetico | tecnico | minimalista
- mensagens_carregamento: EXATAMENTE 4 frases curtas (até 40 chars), no tom escolhido, personalizadas pro aluno

Exemplos de escolha correta:
- Autodidata jovem, tech, iniciante → detalhada, informal, dinâmico, energético
- Aluno de escola com dificuldades → equilibrada, profissional, calmo, técnico
- Aluno avançado que só quer começar → minima, profissional, calmo, minimalista
"""


# ---------------------------------------------------------------
# Chamar Gemini com retry (3 tentativas, backoff exponencial)
# ---------------------------------------------------------------
def gerar_com_retry(model, prompt, tentativas=3):
    ultimo_erro = None
    for i in range(tentativas):
        try:
            resp = model.generate_content(prompt)
            if not resp or not resp.text:
                raise ValueError("resposta vazia do modelo")
            return resp
        except Exception as e:
            ultimo_erro = e
            if i < tentativas - 1:
                time.sleep(2 ** i)   # 1s, 2s
    raise RuntimeError(f"falha após {tentativas} tentativas: {ultimo_erro}")


# ---------------------------------------------------------------
# Validar saída da IA antes de entregar
# ---------------------------------------------------------------
def validar_trilha(dados):
    erros = []
    trilha   = dados.get("trilha_estudos", {})
    conteudo = trilha.get("conteudo", [])

    if not (5 <= len(conteudo) <= 10):
        erros.append(f"quantidade de itens fora do range: {len(conteudo)}")

    for i, item in enumerate(conteudo):
        for campo in ["titulo", "descricao", "duracao", "tipo_conteudo"]:
            if not str(item.get(campo, "")).strip():
                erros.append(f"item {i} sem '{campo}'")

    titulos = [c.get("titulo", "").lower().strip() for c in conteudo]
    if len(titulos) != len(set(titulos)):
        erros.append("títulos duplicados")

    perfil = dados.get("perfil_apresentacao", {})
    permitidos = {
        "densidade": ["minima", "equilibrada", "detalhada"],
        "tom":       ["informal", "profissional", "motivacional"],
        "ritmo":     ["calmo", "dinamico"],
        "paleta":    ["calmo", "energetico", "tecnico", "minimalista"],
    }
    for campo, validos in permitidos.items():
        if perfil.get(campo) not in validos:
            erros.append(f"perfil.{campo} inválido")

    if len(perfil.get("mensagens_carregamento", [])) != 4:
        erros.append("mensagens_carregamento não tem 4 itens")

    return erros


# ---------------------------------------------------------------
# Busca — pontua resultados em vez de pegar o primeiro
# ---------------------------------------------------------------
DOMINIOS_CONFIAVEIS = [
    "youtube.com", "youtu.be", "coursera.org", "edx.org",
    "khanacademy.org", "developer.mozilla.org", "medium.com",
    "dev.to", "github.com", "w3schools.com", "alura.com.br",
    "udemy.com", "rocketseat.com.br", "dio.me", "freecodecamp.org"
]


def buscar_link(titulo, tipo_conteudo):
    if not GOOGLE_SEARCH_KEY or not SEARCH_ENGINE_ID:
        return {"link": "Não encontrado", "fonte": "N/A"}

    query = (f"{titulo} site:youtube.com/watch"
             if tipo_conteudo.lower() == "vídeo"
             else titulo)

    params = {
        "key": GOOGLE_SEARCH_KEY,
        "cx": SEARCH_ENGINE_ID,
        "q": query,
        "num": 5,
        "safe": "active",
        "lr": "lang_pt",
    }

    try:
        r = requests.get(
            "https://www.googleapis.com/customsearch/v1",
            params=params, timeout=8
        )
        data = r.json()
    except Exception as e:
        print(f"Erro na busca: {e}", file=sys.stderr)
        return {"link": "Erro", "fonte": "Erro"}

    items = data.get("items", [])
    if not items:
        return {"link": "Não encontrado", "fonte": "N/A"}

    def pontuar(item):
        link = item.get("link", "")
        host = item.get("displayLink", "").lower()
        score = 0

        # Domínio confiável = bônus grande
        for d in DOMINIOS_CONFIAVEIS:
            if d in host:
                score += 10
                break

        # Palavras do título aparecendo no resultado
        palavras = [p.lower() for p in titulo.split() if len(p) > 3]
        tl = item.get("title", "").lower()
        score += sum(1 for p in palavras if p in tl)

        # HTTPS ganha ponto
        if link.startswith("https://"):
            score += 1

        return score

    melhor = max(items, key=pontuar)
    return {
        "link":  melhor["link"],
        "fonte": melhor.get("displayLink", "Desconhecida")
    }


# ---------------------------------------------------------------
# Fluxo principal
# ---------------------------------------------------------------
def main():
    # Lê argumento vindo do PHP
    if len(sys.argv) > 1:
        try:
            aluno_input = json.loads(sys.argv[1])
        except json.JSONDecodeError:
            print(json.dumps({"erro": "JSON de entrada inválido"}))
            sys.exit(1)
    else:
        aluno_input = aluno_autodidata_teste

    try:
        perfil_aluno = montar_perfil(aluno_input)
    except ValueError as e:
        print(json.dumps({"erro": str(e)}))
        sys.exit(1)

    # Modelo com schema — a resposta já vem no formato certo
    model = genai.GenerativeModel(
        "models/gemini-2.5-flash",
        generation_config=genai.GenerationConfig(
            response_mime_type="application/json",
            response_schema=SCHEMA_TRILHA,
            temperature=0.4,
            top_p=0.9,
        )
    )

    # Gera com retry
    try:
        response = gerar_com_retry(model, montar_prompt(perfil_aluno))
        dados = json.loads(response.text)
    except Exception as e:
        print(json.dumps({"erro": f"Falha ao gerar trilha: {e}"}))
        sys.exit(1)

    # Valida — se falhar em algo do perfil de apresentação, cai no fallback
    erros = validar_trilha(dados)
    if erros:
        print(f"Aviso de validação: {erros}", file=sys.stderr)
        dados["perfil_apresentacao"] = PERFIL_FALLBACK

    # Enriquece os links com busca real
    for item in dados["trilha_estudos"]["conteudo"]:
        resultado = buscar_link(
            item.get("titulo", ""),
            item.get("tipo_conteudo", "artigo")
        )
        item["link_referencia"] = resultado["link"]
        item["fonte"]           = resultado["fonte"]

    # Saída final
    print(json.dumps({
        "trilha_estudos":      dados["trilha_estudos"],
        "perfil_apresentacao": dados["perfil_apresentacao"]
    }, ensure_ascii=False))


if __name__ == "__main__":
    main()