# FormatisAI

Plataforma Educacional com criação de trilha de aprendizada personalizada com sugestões e feedback de IA.

# Seções

## Back-End
- API de Controle entre as outras seções

## Front-End
- Camada de interação visual do Sistema

## IA
- Scripts da IA
- `gerar_trilha.py`: gera a trilha de estudos personalizada (perfil real do aluno)
- `duvida.py`: chat livre para o aluno tirar dúvidas sobre o conteúdo que está estudando
- `questionario.py`: gera questionários mistos (múltipla escolha + dissertativas) e corrige as respostas abertas

## Banco de Dados
- Modelo e Documentação do Banco de Dados
- Rodar `BancoDeDados/migracao_ia_duvidas_questionario.sql` depois do dump principal, para criar as tabelas do chat de dúvidas e dos questionários

## Configuração
- Copie `IA/.env.example` para `IA/.env` e preencha com suas próprias chaves de API (nunca commitar `.env`)
- Compile os três scripts Python em `.exe` seguindo `IA/pyinstaller.txt` sempre que alterá-los
