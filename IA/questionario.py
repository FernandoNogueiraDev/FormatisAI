import google.generativeai as genai
from dotenv import load_dotenv
import os, sys, json, re

# ---------------------------------------------------------------
# Carrega o .env ao lado do script/executável.
# ---------------------------------------------------------------
if getattr(sys, "frozen", False):
    base_dir = os.path.dirname(sys.executable)
else:
    base_dir = os.path.dirname(os.path.abspath(__file__))

load_dotenv(os.path.join(base_dir, ".env"))

GEMINI_KEY = os.getenv("gemini_key")

if not GEMINI_KEY:
    print(json.dumps({"erro": "gemini_key não configurada no .env"}))
    sys.exit(1)

genai.configure(api_key=GEMINI_KEY)
model = genai.GenerativeModel("models/gemini-2.5-flash")


def extrair_json(texto):
    texto = re.sub(r"^```json|```$", "", texto.strip(), flags=re.MULTILINE).strip()
    return json.loads(texto)


# ---------------------------------------------------------------
# Modo "gerar": cria um questionário misto (múltipla escolha + aberta)
# Entrada:
# {
#   "modo": "gerar",
#   "conteudos": [{"titulo": "...", "descricao": "..."}, ...],
#   "num_multipla": 3,
#   "num_abertas": 2
# }
# ---------------------------------------------------------------
def gerar_questionario(dados):
    conteudos = dados.get("conteudos", [])
    num_multipla = int(dados.get("num_multipla", 3))
    num_abertas = int(dados.get("num_abertas", 2))

    if not conteudos:
        return {"erro": "nenhum conteúdo informado para gerar o questionário"}

    temas = "\n".join(
        f"- {c.get('titulo', '')}: {c.get('descricao', '')}" for c in conteudos
    )

    prompt = f"""
Você é um professor criando um questionário de fixação sobre os conteúdos abaixo,
que um aluno acabou de estudar.

Conteúdos estudados:
{temas}

Crie exatamente {num_multipla} questões de múltipla escolha (4 alternativas cada,
apenas uma correta) e exatamente {num_abertas} questões abertas (dissertativas,
que exigem uma resposta em texto do aluno).

Retorne APENAS o JSON puro, no formato exato abaixo, sem nenhum texto antes ou depois:
{{
  "questoes": [
    {{
      "tipo": "multipla_escolha",
      "enunciado": "",
      "alternativas": ["", "", "", ""],
      "resposta_correta": 0
    }},
    {{
      "tipo": "aberta",
      "enunciado": "",
      "criterio_correcao": "breve descrição do que a resposta ideal deveria conter, usada depois para corrigir a resposta do aluno"
    }}
  ]
}}
"resposta_correta" é o índice (começando em 0) da alternativa correta na lista "alternativas".
"""

    response = model.generate_content(prompt)
    try:
        questionario = extrair_json(response.text)
    except json.JSONDecodeError as e:
        return {"erro": f"Erro ao converter JSON da IA: {e}"}

    return questionario


# ---------------------------------------------------------------
# Modo "corrigir": avalia a resposta de uma questão aberta
# Entrada:
# {
#   "modo": "corrigir",
#   "enunciado": "...",
#   "criterio_correcao": "...",
#   "resposta_aluno": "..."
# }
# ---------------------------------------------------------------
def corrigir_resposta_aberta(dados):
    enunciado = dados.get("enunciado", "")
    criterio = dados.get("criterio_correcao", "")
    resposta_aluno = dados.get("resposta_aluno", "").strip()

    if not resposta_aluno:
        return {"erro": "resposta do aluno vazia"}

    prompt = f"""
Você é um professor corrigindo a resposta de um aluno para uma questão dissertativa.

Questão: {enunciado}
O que a resposta ideal deveria abordar: {criterio}
Resposta do aluno: {resposta_aluno}

Avalie se a resposta do aluno está correta, parcialmente correta ou incorreta,
e escreva um feedback curto, construtivo e específico (2 a 4 frases), em português,
explicando o que está certo e o que poderia melhorar.

Retorne APENAS o JSON puro no formato:
{{
  "correta": true,
  "feedback": ""
}}
"correta" deve ser true se a resposta estiver correta ou parcialmente correta o
suficiente para ser aceita, e false se estiver claramente errada ou vazia de conteúdo.
"""

    response = model.generate_content(prompt)
    try:
        resultado = extrair_json(response.text)
    except json.JSONDecodeError as e:
        return {"erro": f"Erro ao converter JSON da IA: {e}"}

    return resultado


# ---------------------------------------------------------------
if len(sys.argv) <= 1:
    print(json.dumps({"erro": "nenhum dado de entrada recebido"}))
    sys.exit(1)

try:
    dados_entrada = json.loads(sys.argv[1])
except json.JSONDecodeError:
    print(json.dumps({"erro": "JSON de entrada inválido"}))
    sys.exit(1)

modo = dados_entrada.get("modo", "gerar")

try:
    if modo == "gerar":
        resultado_final = gerar_questionario(dados_entrada)
    elif modo == "corrigir":
        resultado_final = corrigir_resposta_aberta(dados_entrada)
    else:
        resultado_final = {"erro": f"modo desconhecido: {modo}"}
except Exception as e:
    resultado_final = {"erro": f"Erro ao consultar a IA: {e}"}

print(json.dumps(resultado_final, ensure_ascii=False))
