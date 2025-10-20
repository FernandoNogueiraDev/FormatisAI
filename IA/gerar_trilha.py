import google.generativeai as genai
from dotenv import load_dotenv
import os, json, re, requests, sys

load_dotenv()

genai.configure(api_key="AIzaSyCZqBRiwVfZKZU6tZJVfeukcSQJ0--TgEk")
google_search_key = "AIzaSyDYrEeCgxktVjYP4SuMFoIBTaD5DADsU4E"
search_engine_id = "82412ce23bed240c3"

def montar_perfil(aluno: dict) -> dict:
    tipo_aluno = aluno.get("tipo_aluno", "").lower()
    if tipo_aluno == "autodidata":
        perfil = {
            "nome": aluno['nome'],
            "idade": aluno['idade'],
            "interesse": aluno['interesse'],
            "objetivo_carreira": aluno['objetivo_carreira'],
            "nivel": aluno['nivel'],
            "tipo_conteudo": aluno['tipo_conteudo'],
            "contexto_aprendizagem": (
                "Aluno autodidata que aprende de forma independente e flexível, "
                "buscando conteúdos práticos, gratuitos e de rápida aplicação. "
            )
        }
    elif tipo_aluno == "escola":
        disc_dificuldade = [disc for disc, media in aluno["disciplinas"].items() if media < 6.0]
        perfil = {
            "nome": aluno['nome'],
            "idade": aluno['idade'],
            "disciplinas": disc_dificuldade,
            "nivel": aluno['ano_letivo'],
            "contexto_aprendizagem": (
                "Aluno de escola com dificuldades em algumas disciplinas. "
                "O objetivo é reforçar os conteúdos de forma didática, com explicações passo a passo, "
                "exercícios práticos e materiais de apoio para revisão antes das provas."
            )
        }
    else:
        raise ValueError("tipo de perfil inválido.")
    return perfil


def construir_prompt_trilha(perfil_aluno: dict) -> str:
    prompt_trilha = f"""
Você é um tutor digital. Crie uma trilha_estudos personalizada para o aluno abaixo.
Perfil do aluno: {perfil_aluno}

- Sugira trilha progressiva a partir do nível ({perfil_aluno['nivel']}).
- De 5 a 10 conteúdos.
- Para cada item, forneça: titulo, breve descrição, tipo_conteúdo (vídeo, texto, artigo e etc.),
 link_referência (forneça links reais, de fontes confiaveis e gratuitas), fonte (origem do link),
 duração (tempo para consumir o conteúdo).
- duração total da trilha (tempo para consumir todo o conteúdo).
- Retorne apenas o JSON puro no formato a seguir:
""" + """
  {
    "trilha_estudos": {
      "titulo_trilha": "",
      "descricao_trilha": "",
      "motivo": "",
      "duracao_total_trilha": "",
      "conteudo": [
        {
          "titulo": "",
          "descricao": "",
          "tipo_conteudo": "",
          "link_referencia": "",
          "fonte": "",
          "duracao": ""
        }
      ]
    }
  }
"""
    return prompt_trilha


def gerar_trilha_com_gemini(prompt: str):
    model = genai.GenerativeModel("models/gemini-2.5-flash")
    return model.generate_content(prompt)


def limpar_json_markdown(texto: str) -> str:
    """
    Remove cercas de código ``` ou ```json do início/fim, se existirem.
    """
    if texto is None:
        return ""
    t = texto.strip()
    # remove blocos de ```json ... ``` ou ``` ... ```
    t = re.sub(r"^```(?:json)?\s*", "", t)
    t = re.sub(r"\s*```$", "", t)
    return t.strip()


def buscar_link(busca: str, tipo_conteudo: str, fonte: str) -> dict:
    try:
        url = "https://www.googleapis.com/customsearch/v1"
        params = {
            "key": google_search_key,
            "cx": search_engine_id,
            "q": f"{busca} site:youtube.com/watch" if tipo_conteudo == "vídeo"
                 else f"{busca} Java tutorial {fonte}",
            "num": 5,
            "safe": "active",  # segurança
            "lr": "lang_pt",   # prioriza português
        }
        response_busca = requests.get(url=url, params=params, timeout=20)
        data = response_busca.json()
        if "items" in data and len(data["items"]) > 0:
            item = data["items"][0]
            return {"link": item.get("link", "Não encontrado"),
                    "fonte": item.get("displayLink", "Desconhecida")}
        return {"link": "Não encontrado", "fonte": "N/A"}
    except Exception as e:
        print("Erro ao buscar link:", e)
        return {"link": "Erro", "fonte": "Erro"}


def substituir_links_reais(trilha: dict) -> None:
    """
    Altera a trilha in-place substituindo 'link_referencia' e 'fonte'
    pelos resultados reais da busca.
    """
    trilha_estudos = trilha.get("trilha_estudos", {})
    conteudos = trilha_estudos.get("conteudo", [])
    for conteudo in conteudos:
        titulo = conteudo.get("titulo", "")
        tipo = conteudo.get("tipo_conteudo", "")
        fonte = conteudo.get("fonte", "")
        resultado = buscar_link(titulo, tipo, fonte)
        conteudo["link_referencia"] = resultado["link"]
        conteudo["fonte"] = resultado["fonte"]

    trilha["trilha_estudos"]["conteudo"] = conteudos
    return trilha

def imprimir_trilha(trilha: dict) -> None:
    print(json.dumps(trilha, ensure_ascii=False, indent=2))


# ======================================================
# Fluxo principal
# ======================================================
def main(json_data:dict):
    # 1) Monta perfil (mantendo o exemplo original)
    perfil_aluno = montar_perfil(json_data)

    # 2) Constrói prompt
    prompt = construir_prompt_trilha(perfil_aluno)

    # 3) Gera trilha com Gemini
    response_trilha = gerar_trilha_com_gemini(prompt)

    # 4) Limpa e carrega JSON
    try:
        trilha_texto = response_trilha.text
        json_limpo = limpar_json_markdown(trilha_texto)
        trilha = json.loads(json_limpo)
    except json.JSONDecodeError as erro_json:
        print("Erro ao converter JSON:", erro_json)
        return

    # 5) Substitui links por resultados reais de busca
    substituir_links_reais(trilha)

    imprimir_trilha(trilha)

if __name__ == "__main__":
    aluno_autodidata_teste = {"tipo_aluno":"Autodidata","nome":"Aluno","idade":23,"interesse":"Tecnologia","objetivo_carreira":"Java","nivel":"Iniciante","tipo_conteudo":["v\u00eddeo","artigo"]}

    aluno_escola_teste = {
        "tipo_aluno": "Escola",
        "nome": "Paulo",
        "idade": 14,
        "disciplinas": {
        "matemática": 6.0,
        "biologia": 5.2,
        "física": 4.5
        },
        "ano_letivo": "8º ano",
        "tipo_conteudo": ["vídeo"]
    }

    main(aluno_autodidata_teste)

def main(args):
    if not sys.argv[1]:
        return "Sem argumentos"
    elif len(sys.argv) == 1:
        main(json.loads(sys.argv[1]))
    return None