import google.generativeai as genai
from dotenv import load_dotenv
import os, sys, json

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

# ---------------------------------------------------------------
# Entrada esperada (JSON, primeiro argumento):
# {
#   "conteudo_titulo": "Introdução a Java",
#   "conteudo_descricao": "Vídeo sobre variáveis e tipos primitivos",
#   "pergunta": "não entendi a diferença entre int e double",
#   "historico": [
#       {"papel": "aluno", "texto": "..."},
#       {"papel": "ia", "texto": "..."}
#   ]
# }
# ---------------------------------------------------------------
if len(sys.argv) <= 1:
    print(json.dumps({"erro": "nenhum dado de entrada recebido"}))
    sys.exit(1)

try:
    entrada = json.loads(sys.argv[1])
except json.JSONDecodeError:
    print(json.dumps({"erro": "JSON de entrada inválido"}))
    sys.exit(1)

conteudo_titulo = entrada.get("conteudo_titulo", "")
conteudo_descricao = entrada.get("conteudo_descricao", "")
pergunta = entrada.get("pergunta", "").strip()
historico = entrada.get("historico", [])

if not pergunta:
    print(json.dumps({"erro": "pergunta vazia"}))
    sys.exit(1)

# monta o histórico da conversa em texto simples para dar contexto ao modelo
historico_texto = ""
for msg in historico[-10:]:  # limita para não estourar o prompt
    papel = "Aluno" if msg.get("papel") == "aluno" else "Tutor"
    historico_texto += f"{papel}: {msg.get('texto', '')}\n"

prompt_duvida = f"""
Você é um tutor digital paciente e didático, ajudando um aluno a tirar dúvidas
sobre o conteúdo abaixo. Responda de forma clara, com exemplos quando fizer
sentido, e sem se alongar mais do que o necessário para o aluno entender.
Se a pergunta não tiver relação nenhuma com o conteúdo ou com estudos em
geral, responda educadamente que você só pode ajudar com dúvidas de estudo.

Conteúdo em estudo: {conteudo_titulo}
Descrição do conteúdo: {conteudo_descricao}

Histórico da conversa até agora:
{historico_texto if historico_texto else "(início da conversa)"}

Nova pergunta do aluno: {pergunta}

Responda diretamente à pergunta do aluno, em português, em texto simples
(sem markdown pesado, pode usar quebras de linha e exemplos de código quando útil).
"""

model = genai.GenerativeModel("models/gemini-2.5-flash")

try:
    response = model.generate_content(prompt_duvida)
    resposta_texto = response.text.strip()
except Exception as e:
    print(json.dumps({"erro": f"Erro ao consultar a IA: {e}"}))
    sys.exit(1)

print(json.dumps({"resposta": resposta_texto}, ensure_ascii=False))
