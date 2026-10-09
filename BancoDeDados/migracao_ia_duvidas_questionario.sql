-- ================================================================
-- Migração: tabelas para o chat de dúvidas e os questionários da IA
-- Rode este script no banco `plataformaestudos` já existente
-- (depois de importar plataformaestudos.sql).
-- ================================================================

-- --------------------------------------------------------
-- Histórico do chat de dúvidas
-- --------------------------------------------------------
CREATE TABLE `duvida_chat` (
  `idDuvida` int(11) NOT NULL AUTO_INCREMENT,
  `aluno_id` int(11) NOT NULL,
  `conteudo_id` int(11) DEFAULT NULL,
  `pergunta` text NOT NULL,
  `resposta` text NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idDuvida`),
  KEY `fk_duvida_aluno` (`aluno_id`),
  KEY `fk_duvida_conteudo` (`conteudo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `duvida_chat`
  ADD CONSTRAINT `fk_duvida_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_duvida_conteudo` FOREIGN KEY (`conteudo_id`) REFERENCES `conteudo` (`idConteudo`) ON DELETE SET NULL ON UPDATE NO ACTION;

-- --------------------------------------------------------
-- Questionário gerado pela IA (um registro por "rodada")
-- --------------------------------------------------------
CREATE TABLE `questionario` (
  `idQuestionario` int(11) NOT NULL AUTO_INCREMENT,
  `aluno_id` int(11) NOT NULL,
  `trilha_id` int(11) DEFAULT NULL,
  `conteudo_id` int(11) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idQuestionario`),
  KEY `fk_question_aluno` (`aluno_id`),
  KEY `fk_question_trilha` (`trilha_id`),
  KEY `fk_question_conteudo` (`conteudo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `questionario`
  ADD CONSTRAINT `fk_question_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_question_trilha` FOREIGN KEY (`trilha_id`) REFERENCES `trilha_estudo` (`idTrilha_estudo`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_question_conteudo` FOREIGN KEY (`conteudo_id`) REFERENCES `conteudo` (`idConteudo`) ON DELETE SET NULL ON UPDATE NO ACTION;

-- --------------------------------------------------------
-- Questões de um questionário (mistura múltipla escolha + aberta)
-- --------------------------------------------------------
CREATE TABLE `questao` (
  `idQuestao` int(11) NOT NULL AUTO_INCREMENT,
  `questionario_id` int(11) NOT NULL,
  `tipo` enum('multipla_escolha','aberta') NOT NULL,
  `enunciado` text NOT NULL,
  `alternativas` text DEFAULT NULL COMMENT 'JSON, apenas para multipla_escolha',
  `resposta_correta` int(11) DEFAULT NULL COMMENT 'índice da alternativa correta, apenas multipla_escolha',
  `criterio_correcao` text DEFAULT NULL COMMENT 'usado pela IA pra corrigir questões abertas',
  PRIMARY KEY (`idQuestao`),
  KEY `fk_questao_questionario` (`questionario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `questao`
  ADD CONSTRAINT `fk_questao_questionario` FOREIGN KEY (`questionario_id`) REFERENCES `questionario` (`idQuestionario`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- --------------------------------------------------------
-- Respostas do aluno para cada questão
-- --------------------------------------------------------
CREATE TABLE `resposta_aluno` (
  `idResposta` int(11) NOT NULL AUTO_INCREMENT,
  `questao_id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `resposta_texto` text NOT NULL,
  `correta` tinyint(1) DEFAULT NULL,
  `feedback_ia` text DEFAULT NULL COMMENT 'apenas para questões abertas',
  `respondido_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idResposta`),
  KEY `fk_resp_questao` (`questao_id`),
  KEY `fk_resp_aluno` (`aluno_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `resposta_aluno`
  ADD CONSTRAINT `fk_resp_questao` FOREIGN KEY (`questao_id`) REFERENCES `questao` (`idQuestao`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_resp_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE CASCADE ON UPDATE NO ACTION;
