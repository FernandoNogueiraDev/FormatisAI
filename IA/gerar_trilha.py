import google.generativeai as genai
from dotenv import load_dotenv
import os, json, re, requests

#carregando as chaves de acesso
load_dotenv()

genai.configure(api_key=os.getenv("gemini_key"))
google_search_key = os.getenv("google_search_key")
search_engine_id = os.getenv("search_engine_id")


#perfil ficticio de aluno
perfil_aluno = {
    "nome": "Joana",
    "idade": 23,
    "interesse": "Tecnologia",
    "objetivo_carreira": "Desenvolvedor Java",
    "nivel": "Iniciante",
    "tipo_conteudo": ["vídeo", "artigo"]
}

#criação do prompt
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
    "duracao_total_trilha": "",
    "conteudo": [
      {
        "titulo": "",
        "descricao": "",
        "link_referencia": "",
        "fonte": "",
        "duracao": ""
      }
    ]
  }
}

"""


#gerando trilha com gemini API
model = genai.GenerativeModel("models/gemini-2.5-flash")
response_trilha = model.generate_content(prompt_trilha)


try:
    #trilha no formato JSON
    trilha_json = response_trilha.text
    #Remove blocos de markdown
    json_limpo = re.sub(r"^```json|```$", "", trilha_json.strip(), flags=re.MULTILINE).strip()
    print(json_limpo)
    #carregando a trilha em dicionário
    trilha = json.loads(json_limpo)
except json.JSONDecodeError as erro_json:
    print("Erro ao converter JSON:", erro_json)


# Função de busca na WEB
def buscar_link(busca: str, tipo_conteudo: str, fonte: str):
    try:
        url = f"https://www.googleapis.com/customsearch/v1?"
        params = {
            "key": google_search_key,
            "cx": search_engine_id,
            "q": f"{busca} site:youtube.com" if tipo_conteudo == "vídeo"
              else f"{busca} Java tutorial {fonte}",
            "num": 5,
            "safe": "active",  # segurança na busca
            "lr": "lang_pt",  # prioriza português
        }

        #retorna a busca e guarda em JSON
        response_busca = requests.get(url=url, params=params)
        data = response_busca.json()

        #pega o primeiro item da busca e retorna o link
        if "items" in data and len(data["items"]) > 0:
            item = data["items"][0]
            return {
                "link": item["link"],
                "fonte": item.get("displayLink", "Desconhecida")
            }
        return {"link": "Não encontrado", "fonte": "N/A"}
    except Exception as e:
        print("Erro ao buscar link:", e)
        return {"link": "Erro", "fonte": "Erro"}
    

# Substituir links fictícios pelos reais


trilha_estudos = trilha.get("trilha_estudos")

for conteudo in trilha_estudos["conteudo"]:
    titulo = conteudo["titulo"]
    tipo = conteudo["tipo_conteudo"]
    fonte = conteudo["fonte"]
    resultado = buscar_link(titulo, tipo, fonte)
    conteudo["link_referencia"] = resultado["link"]
    conteudo["fonte"] = resultado["fonte"]


#exibir trilha
print("----------------------------------------------")
print(f"Olá {perfil_aluno['nome']}, Bem vinda a Formatis AI :)\nEstá é sua Trilha Personalizada. Bom Estudo!!")
print("----------------------------------------------\n")
print(f"Trilha para {perfil_aluno['objetivo_carreira']} de nível {perfil_aluno['nivel']}:\n")


for conteudo in trilha_estudos["conteudo"]:
    print(f"Título: {conteudo['titulo']}")
    print(f"Descrição: {conteudo['descricao']}\n")
    print(f"    Link: {conteudo['link_referencia']}")
    print(f"    Fonte: {conteudo['fonte']}")
    print(f"Duração: {conteudo['duracao']}\n")
print(f"Duração Total da Trilha: {trilha['duracao_total_trilha']}")

