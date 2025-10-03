from openai import OpenAI
from dotenv import load_dotenv
import os
import json
import requests

#carregando a chave de acesso
load_dotenv()
client = OpenAI(api_key= os.getenv("openai_key"))

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
   duração total da trilha (tempo para consumir todo o conteúdo).
- Retorne apenas links gratuitos, acessíveis publicamente (ex: YouTube, blogs, artigos gratuitos).
- NÃO inclua links de plataformas pagas ou que exijam login.
- Retorne em formato JSON
"""

#gerando trilha com o GPT
response_trilha = client.chat.completions.create(
    model="gpt-4o-mini",
    messages=[
        {"role": "system", "content": "Você é um especialista em educação personalizada."},
        {"role": "user", "content": prompt_trilha}
    ],
    temperature=0.5,
    response_format={"type": "json_object"}
)


try:
    #trilha no formato JSON
    trilha_json = response_trilha.choices[0].message.content
    #carregando a trilha em dicionário
    trilha = json.loads(trilha_json)
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
    link = conteudo["link"]
    if link_ativo(link):
        print(f"Link válido: {link}")
    else:
        print(f"Link inválido: {link}")

"""
#exibir trilha
print("----------------------------------------------")
print(f"Olá {perfil_aluno['nome']}, Bem vinda a Formatis AI :)\nEstá é sua Trilha Personalizada. Bom Estudo!!")
print("----------------------------------------------\n")
print(f"Trilha para {perfil_aluno['objetivo_carreira']} de nível {perfil_aluno['nivel']}:\n")


for conteudo in trilha["trilha_estudos"]:
    print(f"Título: {conteudo['titulo']}")
    print(f"Descrição: {conteudo['descricao']}\n")
    print(f"    Link: {conteudo['link']}")
    print(f"    Fonte: {conteudo['fonte']}")
    print(f"Duração: {conteudo['duracao']}\n")
print(f"Duração Total da Trilha: {trilha['duracao_total']}")
"""