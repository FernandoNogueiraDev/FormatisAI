-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/10/2025 às 23:05
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `plataformaestudos`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `aluno`
--

CREATE TABLE `aluno` (
  `idAluno` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `data_nascimento` date NOT NULL,
  `criacao_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Acionadores `aluno`
--
DELIMITER $$
CREATE TRIGGER `trg_aluno_delete` BEFORE DELETE ON `aluno` FOR EACH ROW BEGIN
    INSERT INTO relatorio (aluno_id, trilha_id, data_ref, desempenho_score, recomendacoes)
    VALUES (OLD.idAluno, NULL, CURDATE(), 0, CONCAT('Aluno ', OLD.nome, ' removido.'));
    DELETE FROM trilha_estudo WHERE aluno_id = OLD.idAluno;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_init_aluno` AFTER INSERT ON `aluno` FOR EACH ROW BEGIN
    DECLARE novaTrilha INT;
    INSERT INTO trilha_estudo (aluno_id, titulo, descricao, status)
    VALUES (NEW.idAluno, CONCAT('Trilha Base de ', NEW.nome), 'Trilha inicial personalizada.', 'ativa');
    SET novaTrilha = LAST_INSERT_ID();
    INSERT INTO relatorio (aluno_id, trilha_id, data_ref, desempenho_score, recomendacoes)
    VALUES (NEW.idAluno, novaTrilha, CURDATE(), 0, 'Nenhuma recomendação inicial.');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `area_interesse`
--

CREATE TABLE `area_interesse` (
  `idArea_interesse` int(11) NOT NULL,
  `nome_area` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `conteudo`
--

CREATE TABLE `conteudo` (
  `idConteudo` int(11) NOT NULL,
  `fonte_id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `dificuldade` enum('basico','intermediario','avancado') DEFAULT NULL,
  `link` varchar(500) NOT NULL,
  `duracao_min` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fonte`
--

CREATE TABLE `fonte` (
  `idFonte` int(11) NOT NULL,
  `nome_fonte` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_escolar`
--

CREATE TABLE `historico_escolar` (
  `idHistorico_escolar` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `disciplina` varchar(120) NOT NULL,
  `periodo` varchar(20) DEFAULT NULL,
  `media` decimal(4,2) DEFAULT NULL,
  `carga_horaria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `preferencia_aluno`
--

CREATE TABLE `preferencia_aluno` (
  `idPreferencia_aluno` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `areaInteresse_id` int(11) NOT NULL,
  `objetivo_carreira` text DEFAULT NULL,
  `nivel_dificuldade_preferido` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `progresso_trilha`
--

CREATE TABLE `progresso_trilha` (
  `idProgresso_conteudo` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `trilhaConteudo_id` int(11) NOT NULL,
  `status` enum('pendente','em andamento','concluido') NOT NULL,
  `inicio` timestamp NULL DEFAULT NULL,
  `fim` timestamp NULL DEFAULT NULL,
  `nota` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Acionadores `progresso_trilha`
--
DELIMITER $$
CREATE TRIGGER `trg_sugestao_conteudo` AFTER INSERT ON `progresso_trilha` FOR EACH ROW BEGIN
    DECLARE media_aluno DECIMAL(5,2);
    SELECT AVG(media) INTO media_aluno
    FROM historico_escolar
    WHERE aluno_id = NEW.aluno_id;

    SET media_aluno = IFNULL(media_aluno, 0);

    IF media_aluno < 7 THEN
        INSERT INTO sugestao_conteudo (
          aluno_id, trilhaConteudo_id, titulo_sugerido, motivo, origem, aceito, criado_em
        )
        VALUES (
          NEW.aluno_id, NEW.trilhaConteudo_id, 'Revisar conteúdos básicos',
          'Baixo desempenho médio detectado pela IA', 'IA', 0, NOW()
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_progresso_trilha` AFTER UPDATE ON `progresso_trilha` FOR EACH ROW BEGIN
    DECLARE media DECIMAL(5,2);
    DECLARE concluidos INT;
    DECLARE total INT;
    SELECT AVG(nota), SUM(CASE WHEN status = 'concluido' THEN 1 ELSE 0 END), COUNT(*) INTO media, concluidos, total
    FROM progresso_trilha WHERE trilhaConteudo_id = NEW.trilhaConteudo_id;
    SET media = IFNULL(media, 0);
    UPDATE relatorio
    SET desempenho_score = media,
        recomendacoes = CONCAT('Conteúdos concluídos: ', concluidos, '/', total)
    WHERE trilha_id = (SELECT trilha_id FROM trilha_conteudo WHERE idTrilha_conteudo = NEW.trilhaConteudo_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `relatorio`
--

CREATE TABLE `relatorio` (
  `idRelatorio` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `trilha_id` int(11) DEFAULT NULL,
  `data_ref` date NOT NULL,
  `desempenho_score` decimal(5,2) DEFAULT NULL,
  `recomendacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sugestao_conteudo`
--

CREATE TABLE `sugestao_conteudo` (
  `idSugestao_conteudo` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `trilhaConteudo_id` int(11) NOT NULL,
  `titulo_sugerido` varchar(200) DEFAULT NULL,
  `motivo` text DEFAULT NULL,
  `origem` varchar(20) NOT NULL,
  `aceito` tinyint(4) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `trilha_conteudo`
--

CREATE TABLE `trilha_conteudo` (
  `idTrilha_conteudo` int(11) NOT NULL,
  `trilha_id` int(11) NOT NULL,
  `conteudo_id` int(11) NOT NULL,
  `ordem` int(11) NOT NULL,
  `obrigatorio` tinyint(4) NOT NULL,
  `estimativa_min` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `trilha_estudo`
--

CREATE TABLE `trilha_estudo` (
  `idTrilha_estudo` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('ativa','pausada','concluida') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_aluno_historico`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_aluno_historico` (
`idAluno` int(11)
,`nome` varchar(100)
,`email` varchar(100)
,`disciplina` varchar(120)
,`periodo` varchar(20)
,`media` decimal(4,2)
,`carga_horaria` int(11)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_conteudos_recomendados`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_conteudos_recomendados` (
`idConteudo` int(11)
,`titulo` varchar(200)
,`categoria` varchar(100)
,`dificuldade` enum('basico','intermediario','avancado')
,`fonte` varchar(120)
,`aluno_id` int(11)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_fonte_conteudo`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_fonte_conteudo` (
`idConteudo` int(11)
,`titulo` varchar(200)
,`fonte` varchar(120)
,`categoria` varchar(100)
,`dificuldade` enum('basico','intermediario','avancado')
,`link` varchar(500)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_relatorios_trilhas`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_relatorios_trilhas` (
`idRelatorio` int(11)
,`aluno` varchar(100)
,`trilha` varchar(200)
,`data_ref` date
,`desempenho_score` decimal(5,2)
,`recomendacoes` text
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_trilhas_personalizadas`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_trilhas_personalizadas` (
`idTrilha_estudo` int(11)
,`aluno` varchar(100)
,`trilha` varchar(200)
,`descricao` text
,`status` enum('ativa','pausada','concluida')
);

-- --------------------------------------------------------

--
-- Estrutura para view `vw_aluno_historico`
--
DROP TABLE IF EXISTS `vw_aluno_historico`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_aluno_historico`  AS SELECT `a`.`idAluno` AS `idAluno`, `a`.`nome` AS `nome`, `a`.`email` AS `email`, `h`.`disciplina` AS `disciplina`, `h`.`periodo` AS `periodo`, `h`.`media` AS `media`, `h`.`carga_horaria` AS `carga_horaria` FROM (`aluno` `a` left join `historico_escolar` `h` on(`a`.`idAluno` = `h`.`aluno_id`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_conteudos_recomendados`
--
DROP TABLE IF EXISTS `vw_conteudos_recomendados`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_conteudos_recomendados`  AS SELECT DISTINCT `c`.`idConteudo` AS `idConteudo`, `c`.`titulo` AS `titulo`, `c`.`categoria` AS `categoria`, `c`.`dificuldade` AS `dificuldade`, `f`.`nome_fonte` AS `fonte`, `ad`.`aluno_id` AS `aluno_id` FROM (((select `h2`.`aluno_id` AS `aluno_id`,case when avg(`h2`.`media`) is null or avg(`h2`.`media`) < 6 then 'basico' when avg(`h2`.`media`) between 6 and 8 then 'intermediario' else 'avancado' end AS `alvo_dificuldade` from `historico_escolar` `h2` group by `h2`.`aluno_id`) `ad` join `conteudo` `c` on(`c`.`dificuldade` = `ad`.`alvo_dificuldade`)) join `fonte` `f` on(`f`.`idFonte` = `c`.`fonte_id`)) ORDER BY `ad`.`aluno_id` ASC ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_fonte_conteudo`
--
DROP TABLE IF EXISTS `vw_fonte_conteudo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_fonte_conteudo`  AS SELECT `c`.`idConteudo` AS `idConteudo`, `c`.`titulo` AS `titulo`, `f`.`nome_fonte` AS `fonte`, `c`.`categoria` AS `categoria`, `c`.`dificuldade` AS `dificuldade`, `c`.`link` AS `link` FROM (`conteudo` `c` join `fonte` `f` on(`f`.`idFonte` = `c`.`fonte_id`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_relatorios_trilhas`
--
DROP TABLE IF EXISTS `vw_relatorios_trilhas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_relatorios_trilhas`  AS SELECT `r`.`idRelatorio` AS `idRelatorio`, `a`.`nome` AS `aluno`, `t`.`titulo` AS `trilha`, `r`.`data_ref` AS `data_ref`, `r`.`desempenho_score` AS `desempenho_score`, `r`.`recomendacoes` AS `recomendacoes` FROM ((`relatorio` `r` join `trilha_estudo` `t` on(`r`.`trilha_id` = `t`.`idTrilha_estudo`)) join `aluno` `a` on(`r`.`aluno_id` = `a`.`idAluno`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_trilhas_personalizadas`
--
DROP TABLE IF EXISTS `vw_trilhas_personalizadas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_trilhas_personalizadas`  AS SELECT `t`.`idTrilha_estudo` AS `idTrilha_estudo`, `a`.`nome` AS `aluno`, `t`.`titulo` AS `trilha`, `t`.`descricao` AS `descricao`, `t`.`status` AS `status` FROM (`trilha_estudo` `t` join `aluno` `a` on(`a`.`idAluno` = `t`.`aluno_id`)) ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `aluno`
--
ALTER TABLE `aluno`
  ADD PRIMARY KEY (`idAluno`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `area_interesse`
--
ALTER TABLE `area_interesse`
  ADD PRIMARY KEY (`idArea_interesse`);

--
-- Índices de tabela `conteudo`
--
ALTER TABLE `conteudo`
  ADD PRIMARY KEY (`idConteudo`),
  ADD KEY `idFonte_idx` (`fonte_id`);

--
-- Índices de tabela `fonte`
--
ALTER TABLE `fonte`
  ADD PRIMARY KEY (`idFonte`);

--
-- Índices de tabela `historico_escolar`
--
ALTER TABLE `historico_escolar`
  ADD PRIMARY KEY (`idHistorico_escolar`),
  ADD KEY `idAluno_idx` (`aluno_id`);

--
-- Índices de tabela `preferencia_aluno`
--
ALTER TABLE `preferencia_aluno`
  ADD PRIMARY KEY (`idPreferencia_aluno`),
  ADD KEY `idaluno_idx` (`aluno_id`),
  ADD KEY `idarea_interesse_idx` (`areaInteresse_id`);

--
-- Índices de tabela `progresso_trilha`
--
ALTER TABLE `progresso_trilha`
  ADD PRIMARY KEY (`idProgresso_conteudo`),
  ADD KEY `idaluno_idx` (`aluno_id`),
  ADD KEY `idtrilha_conteudo_idx` (`trilhaConteudo_id`);

--
-- Índices de tabela `relatorio`
--
ALTER TABLE `relatorio`
  ADD PRIMARY KEY (`idRelatorio`),
  ADD KEY `idaluno_idx` (`aluno_id`),
  ADD KEY `idtrilha_estudo_idx` (`trilha_id`);

--
-- Índices de tabela `sugestao_conteudo`
--
ALTER TABLE `sugestao_conteudo`
  ADD PRIMARY KEY (`idSugestao_conteudo`),
  ADD KEY `idtrilha_conteudo_idx` (`trilhaConteudo_id`),
  ADD KEY `idaluno_idx` (`aluno_id`);

--
-- Índices de tabela `trilha_conteudo`
--
ALTER TABLE `trilha_conteudo`
  ADD PRIMARY KEY (`idTrilha_conteudo`),
  ADD KEY `idtrilha_estudo_idx` (`trilha_id`),
  ADD KEY `idconteudo_idx` (`conteudo_id`);

--
-- Índices de tabela `trilha_estudo`
--
ALTER TABLE `trilha_estudo`
  ADD PRIMARY KEY (`idTrilha_estudo`),
  ADD KEY `idaluno_idx` (`aluno_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `aluno`
--
ALTER TABLE `aluno`
  MODIFY `idAluno` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `area_interesse`
--
ALTER TABLE `area_interesse`
  MODIFY `idArea_interesse` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `conteudo`
--
ALTER TABLE `conteudo`
  MODIFY `idConteudo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fonte`
--
ALTER TABLE `fonte`
  MODIFY `idFonte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico_escolar`
--
ALTER TABLE `historico_escolar`
  MODIFY `idHistorico_escolar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `preferencia_aluno`
--
ALTER TABLE `preferencia_aluno`
  MODIFY `idPreferencia_aluno` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `progresso_trilha`
--
ALTER TABLE `progresso_trilha`
  MODIFY `idProgresso_conteudo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `relatorio`
--
ALTER TABLE `relatorio`
  MODIFY `idRelatorio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `sugestao_conteudo`
--
ALTER TABLE `sugestao_conteudo`
  MODIFY `idSugestao_conteudo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `trilha_conteudo`
--
ALTER TABLE `trilha_conteudo`
  MODIFY `idTrilha_conteudo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `trilha_estudo`
--
ALTER TABLE `trilha_estudo`
  MODIFY `idTrilha_estudo` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `conteudo`
--
ALTER TABLE `conteudo`
  ADD CONSTRAINT `fk_Fonte` FOREIGN KEY (`fonte_id`) REFERENCES `fonte` (`idFonte`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `historico_escolar`
--
ALTER TABLE `historico_escolar`
  ADD CONSTRAINT `fk_historico_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `preferencia_aluno`
--
ALTER TABLE `preferencia_aluno`
  ADD CONSTRAINT `fk_area_interesse` FOREIGN KEY (`areaInteresse_id`) REFERENCES `area_interesse` (`idArea_interesse`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pref_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `progresso_trilha`
--
ALTER TABLE `progresso_trilha`
  ADD CONSTRAINT `fk_progre_trilha_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_progre_trilha_tConteudo` FOREIGN KEY (`trilhaConteudo_id`) REFERENCES `trilha_conteudo` (`idTrilha_conteudo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `relatorio`
--
ALTER TABLE `relatorio`
  ADD CONSTRAINT `fk_relatorio_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_relatorio_trilha_estudo` FOREIGN KEY (`trilha_id`) REFERENCES `trilha_estudo` (`idTrilha_estudo`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `sugestao_conteudo`
--
ALTER TABLE `sugestao_conteudo`
  ADD CONSTRAINT `fk_suge_conteudo_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_suge_conteudo_tConteudo` FOREIGN KEY (`trilhaConteudo_id`) REFERENCES `trilha_conteudo` (`idTrilha_conteudo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `trilha_conteudo`
--
ALTER TABLE `trilha_conteudo`
  ADD CONSTRAINT `fk_conteudo` FOREIGN KEY (`conteudo_id`) REFERENCES `conteudo` (`idConteudo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_trilha_conteudo_tEstudo` FOREIGN KEY (`trilha_id`) REFERENCES `trilha_estudo` (`idTrilha_estudo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `trilha_estudo`
--
ALTER TABLE `trilha_estudo`
  ADD CONSTRAINT `fk_trilha_estudo_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
