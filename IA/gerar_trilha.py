import google.generativeai as genai
from dotenv import load_dotenv
import os
import json, re
import requests

#carregando a chave de acesso
load_dotenv()
genai.configure(api_key=os.getenv("gemini_key"))


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
Você é um tutor digital. Crie uma trilha de estudos personalizada para o aluno abaixo.
Perfil do aluno: {perfil_aluno}

- Sugira trilha progressiva a partir do nível ({perfil_aluno['nivel']}).
- De 5 a 10 conteúdos.
- Para cada item, forneça: titulo, breve descrição, tipo de conteúdo (vídeo, texto, artigo e etc.),
 link de referência (forneça links reais, de fontes confiaveis e gratuitas), fonte (origem do link),
   duração (tempo para consumir o conteúdo).
- duração total da trilha (tempo para consumir todo o conteúdo).
- verificar se o conteúdo do link ainda exista, se não estiver disponível pode excluir o link.
- NÃO inclua links de plataformas pagas ou que exijam login.
- Retorne apenas o JSON, sem explicações, sem formatação Markdown e sem texto adicional.
"""


#gerando trilha com gemini API
model = genai.GenerativeModel("models/gemini-2.5-flash")
response_trilha = model.generate_content(prompt_trilha)


try:
    #trilha no formato JSON
    trilha_json = response_trilha.text
    #Remove blocos de markdown
    json_limpo = re.sub(r"^```json|```$", "", trilha_json.strip(), flags=re.MULTILINE).strip()
    #carregando a trilha em dicionário
    trilha = json.loads(json_limpo)
except json.JSONDecodeError as erro_json:
    print("Erro ao converter JSON:", erro_json)


#função para verificar se o link está ativo
def link_ativo(url: str) -> bool:
    try:
        resposta = requests.head(url, allow_redirects=True, timeout=5)
        return resposta.status_code == 200
    except:
        return False

#testando link
for conteudo in trilha["trilha_estudos"]:
    link = conteudo["link_referencia"]
    try:
        link_ativo(link)
    except:
        print(f"Link inválido: {link}")


#exibir trilha
print("----------------------------------------------")
print(f"Olá {perfil_aluno['nome']}, Bem vinda a Formatis AI :)\nEstá é sua Trilha Personalizada. Bom Estudo!!")
print("----------------------------------------------\n")
print(f"Trilha para {perfil_aluno['objetivo_carreira']} de nível {perfil_aluno['nivel']}:\n")


for conteudo in trilha["trilha_estudos"]:
    print(f"Título: {conteudo['titulo']}")
    print(f"Descrição: {conteudo['breve_descricao']}\n")
    print(f"    Link: {conteudo['link_referencia']}")
    print(f"    Fonte: {conteudo['fonte']}")
    print(f"Duração: {conteudo['duracao']}\n")
print(f"Duração Total da Trilha: {trilha['duracao_total_trilha']}")