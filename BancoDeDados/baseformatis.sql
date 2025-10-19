CREATE DATABASE  IF NOT EXISTS `plataformaestudos` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci */;
USE `plataformaestudos`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: plataformaestudos
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `aluno`
--

DROP TABLE IF EXISTS `aluno`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aluno` (
  `idAluno` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `data_nascimento` date NOT NULL,
  `criacao_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idAluno`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_init_aluno
AFTER INSERT ON Aluno
FOR EACH ROW
BEGIN
    DECLARE novaTrilha INT;
    INSERT INTO trilha_estudo (aluno_id, titulo, descricao, status)
    VALUES (NEW.idAluno, CONCAT('Trilha Base de ', NEW.nome), 'Trilha inicial personalizada.', 'ativa');
    SET novaTrilha = LAST_INSERT_ID();
    INSERT INTO relatorio (aluno_id, trilha_id, data_ref, desempenho_score, recomendacoes)
    VALUES (NEW.idAluno, novaTrilha, CURDATE(), 0, 'Nenhuma recomendação inicial.');
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_aluno_delete
BEFORE DELETE ON Aluno
FOR EACH ROW
BEGIN
    INSERT INTO relatorio (aluno_id, trilha_id, data_ref, desempenho_score, recomendacoes)
    VALUES (OLD.idAluno, NULL, CURDATE(), 0, CONCAT('Aluno ', OLD.nome, ' removido.'));
    DELETE FROM trilha_estudo WHERE aluno_id = OLD.idAluno;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `area_interesse`
--

DROP TABLE IF EXISTS `area_interesse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `area_interesse` (
  `idArea_interesse` int(11) NOT NULL,
  `nome_area` varchar(100) NOT NULL,
  PRIMARY KEY (`idArea_interesse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conteudo`
--

DROP TABLE IF EXISTS `conteudo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conteudo` (
  `idConteudo` int(11) NOT NULL AUTO_INCREMENT,
  `fonte_id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `dificuldade` enum('basico','intermediario','avancado') DEFAULT NULL,
  `link` varchar(500) NOT NULL,
  `duracao_min` int(11) DEFAULT NULL,
  PRIMARY KEY (`idConteudo`),
  KEY `idFonte_idx` (`fonte_id`),
  CONSTRAINT `fk_Fonte` FOREIGN KEY (`fonte_id`) REFERENCES `fonte` (`idFonte`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fonte`
--

DROP TABLE IF EXISTS `fonte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fonte` (
  `idFonte` int(11) NOT NULL AUTO_INCREMENT,
  `nome_fonte` varchar(120) NOT NULL,
  PRIMARY KEY (`idFonte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `historico_escolar`
--

DROP TABLE IF EXISTS `historico_escolar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historico_escolar` (
  `idHistorico_escolar` int(11) NOT NULL AUTO_INCREMENT,
  `aluno_id` int(11) NOT NULL,
  `disciplina` varchar(120) NOT NULL,
  `periodo` varchar(20) DEFAULT NULL,
  `media` decimal(4,2) DEFAULT NULL,
  `carga_horaria` int(11) DEFAULT NULL,
  PRIMARY KEY (`idHistorico_escolar`),
  KEY `idAluno_idx` (`aluno_id`),
  CONSTRAINT `fk_historico_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `preferencia_aluno`
--

DROP TABLE IF EXISTS `preferencia_aluno`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `preferencia_aluno` (
  `idPreferencia_aluno` int(11) NOT NULL AUTO_INCREMENT,
  `aluno_id` int(11) NOT NULL,
  `areaInteresse_id` int(11) NOT NULL,
  `objetivo_carreira` text DEFAULT NULL,
  `nivel_dificuldade_preferido` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`idPreferencia_aluno`),
  KEY `idaluno_idx` (`aluno_id`),
  KEY `idarea_interesse_idx` (`areaInteresse_id`),
  CONSTRAINT `fk_area_interesse` FOREIGN KEY (`areaInteresse_id`) REFERENCES `area_interesse` (`idArea_interesse`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pref_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `progresso_trilha`
--

DROP TABLE IF EXISTS `progresso_trilha`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `progresso_trilha` (
  `idProgresso_conteudo` int(11) NOT NULL AUTO_INCREMENT,
  `aluno_id` int(11) NOT NULL,
  `trilhaConteudo_id` int(11) NOT NULL,
  `status` enum('pendente','em andamento','concluido') NOT NULL,
  `inicio` timestamp NULL DEFAULT NULL,
  `fim` timestamp NULL DEFAULT NULL,
  `nota` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`idProgresso_conteudo`),
  KEY `idaluno_idx` (`aluno_id`),
  KEY `idtrilha_conteudo_idx` (`trilhaConteudo_id`),
  CONSTRAINT `fk_progre_trilha_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_progre_trilha_tConteudo` FOREIGN KEY (`trilhaConteudo_id`) REFERENCES `trilha_conteudo` (`idTrilha_conteudo`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_sugestao_conteudo
AFTER INSERT ON progresso_trilha
FOR EACH ROW
BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_update_progresso_trilha
AFTER UPDATE ON progresso_trilha
FOR EACH ROW
BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `relatorio`
--

DROP TABLE IF EXISTS `relatorio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `relatorio` (
  `idRelatorio` int(11) NOT NULL AUTO_INCREMENT,
  `aluno_id` int(11) NOT NULL,
  `trilha_id` int(11) NOT NULL,
  `data_ref` date NOT NULL,
  `desempenho_score` decimal(5,2) DEFAULT NULL,
  `recomendacoes` text DEFAULT NULL,
  PRIMARY KEY (`idRelatorio`),
  KEY `idaluno_idx` (`aluno_id`),
  KEY `idtrilha_estudo_idx` (`trilha_id`),
  CONSTRAINT `fk_relatorio_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_relatorio_trilha_estudo` FOREIGN KEY (`trilha_id`) REFERENCES `trilha_estudo` (`idTrilha_estudo`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sugestao_conteudo`
--

DROP TABLE IF EXISTS `sugestao_conteudo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sugestao_conteudo` (
  `idSugestao_conteudo` int(11) NOT NULL AUTO_INCREMENT,
  `aluno_id` int(11) NOT NULL,
  `trilhaConteudo_id` int(11) NOT NULL,
  `titulo_sugerido` varchar(200) DEFAULT NULL,
  `motivo` text DEFAULT NULL,
  `origem` varchar(20) NOT NULL,
  `aceito` tinyint(4) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idSugestao_conteudo`),
  KEY `idtrilha_conteudo_idx` (`trilhaConteudo_id`),
  KEY `idaluno_idx` (`aluno_id`),
  CONSTRAINT `fk_suge_conteudo_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_suge_conteudo_tConteudo` FOREIGN KEY (`trilhaConteudo_id`) REFERENCES `trilha_conteudo` (`idTrilha_conteudo`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trilha_conteudo`
--

DROP TABLE IF EXISTS `trilha_conteudo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trilha_conteudo` (
  `idTrilha_conteudo` int(11) NOT NULL AUTO_INCREMENT,
  `trilha_id` int(11) NOT NULL,
  `conteudo_id` int(11) NOT NULL,
  `ordem` int(11) NOT NULL,
  `obrigatorio` tinyint(4) NOT NULL,
  `estimativa_min` int(11) DEFAULT NULL,
  PRIMARY KEY (`idTrilha_conteudo`),
  KEY `idtrilha_estudo_idx` (`trilha_id`),
  KEY `idconteudo_idx` (`conteudo_id`),
  CONSTRAINT `fk_conteudo` FOREIGN KEY (`conteudo_id`) REFERENCES `conteudo` (`idConteudo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_trilha_conteudo_tEstudo` FOREIGN KEY (`trilha_id`) REFERENCES `trilha_estudo` (`idTrilha_estudo`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trilha_estudo`
--

DROP TABLE IF EXISTS `trilha_estudo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trilha_estudo` (
  `idTrilha_estudo` int(11) NOT NULL AUTO_INCREMENT,
  `aluno_id` int(11) NOT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('ativa','pausada','concluida') DEFAULT NULL,
  PRIMARY KEY (`idTrilha_estudo`),
  KEY `idaluno_idx` (`aluno_id`),
  CONSTRAINT `fk_trilha_estudo_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `vw_aluno_historico`
--

DROP TABLE IF EXISTS `vw_aluno_historico`;
/*!50001 DROP VIEW IF EXISTS `vw_aluno_historico`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_aluno_historico` AS SELECT 
 1 AS `idAluno`,
 1 AS `nome`,
 1 AS `email`,
 1 AS `disciplina`,
 1 AS `periodo`,
 1 AS `media`,
 1 AS `carga_horaria`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_conteudos_recomendados`
--

DROP TABLE IF EXISTS `vw_conteudos_recomendados`;
/*!50001 DROP VIEW IF EXISTS `vw_conteudos_recomendados`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_conteudos_recomendados` AS SELECT 
 1 AS `idConteudo`,
 1 AS `titulo`,
 1 AS `categoria`,
 1 AS `dificuldade`,
 1 AS `fonte`,
 1 AS `aluno_id`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_fonte_conteudo`
--

DROP TABLE IF EXISTS `vw_fonte_conteudo`;
/*!50001 DROP VIEW IF EXISTS `vw_fonte_conteudo`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_fonte_conteudo` AS SELECT 
 1 AS `idConteudo`,
 1 AS `titulo`,
 1 AS `fonte`,
 1 AS `categoria`,
 1 AS `dificuldade`,
 1 AS `link`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_relatorios_trilhas`
--

DROP TABLE IF EXISTS `vw_relatorios_trilhas`;
/*!50001 DROP VIEW IF EXISTS `vw_relatorios_trilhas`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_relatorios_trilhas` AS SELECT 
 1 AS `idRelatorio`,
 1 AS `aluno`,
 1 AS `trilha`,
 1 AS `data_ref`,
 1 AS `desempenho_score`,
 1 AS `recomendacoes`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_trilhas_personalizadas`
--

DROP TABLE IF EXISTS `vw_trilhas_personalizadas`;
/*!50001 DROP VIEW IF EXISTS `vw_trilhas_personalizadas`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_trilhas_personalizadas` AS SELECT 
 1 AS `idTrilha_estudo`,
 1 AS `aluno`,
 1 AS `trilha`,
 1 AS `descricao`,
 1 AS `status`*/;
SET character_set_client = @saved_cs_client;

--
-- Dumping events for database 'plataformaestudos'
--

--
-- Dumping routines for database 'plataformaestudos'
--

--
-- Final view structure for view `vw_aluno_historico`
--

/*!50001 DROP VIEW IF EXISTS `vw_aluno_historico`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_aluno_historico` AS select `a`.`idAluno` AS `idAluno`,`a`.`nome` AS `nome`,`a`.`email` AS `email`,`h`.`disciplina` AS `disciplina`,`h`.`periodo` AS `periodo`,`h`.`media` AS `media`,`h`.`carga_horaria` AS `carga_horaria` from (`aluno` `a` left join `historico_escolar` `h` on(`a`.`idAluno` = `h`.`aluno_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_conteudos_recomendados`
--

/*!50001 DROP VIEW IF EXISTS `vw_conteudos_recomendados`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_conteudos_recomendados` AS select distinct `c`.`idConteudo` AS `idConteudo`,`c`.`titulo` AS `titulo`,`c`.`categoria` AS `categoria`,`c`.`dificuldade` AS `dificuldade`,`f`.`nome_fonte` AS `fonte`,`ad`.`aluno_id` AS `aluno_id` from (((select `h2`.`aluno_id` AS `aluno_id`,case when avg(`h2`.`media`) is null or avg(`h2`.`media`) < 6 then 'basico' when avg(`h2`.`media`) between 6 and 8 then 'intermediario' else 'avancado' end AS `alvo_dificuldade` from `historico_escolar` `h2` group by `h2`.`aluno_id`) `ad` join `conteudo` `c` on(`c`.`dificuldade` = `ad`.`alvo_dificuldade`)) join `fonte` `f` on(`f`.`idFonte` = `c`.`fonte_id`)) order by `ad`.`aluno_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_fonte_conteudo`
--

/*!50001 DROP VIEW IF EXISTS `vw_fonte_conteudo`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_fonte_conteudo` AS select `c`.`idConteudo` AS `idConteudo`,`c`.`titulo` AS `titulo`,`f`.`nome_fonte` AS `fonte`,`c`.`categoria` AS `categoria`,`c`.`dificuldade` AS `dificuldade`,`c`.`link` AS `link` from (`conteudo` `c` join `fonte` `f` on(`f`.`idFonte` = `c`.`fonte_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_relatorios_trilhas`
--

/*!50001 DROP VIEW IF EXISTS `vw_relatorios_trilhas`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_relatorios_trilhas` AS select `r`.`idRelatorio` AS `idRelatorio`,`a`.`nome` AS `aluno`,`t`.`titulo` AS `trilha`,`r`.`data_ref` AS `data_ref`,`r`.`desempenho_score` AS `desempenho_score`,`r`.`recomendacoes` AS `recomendacoes` from ((`relatorio` `r` join `trilha_estudo` `t` on(`r`.`trilha_id` = `t`.`idTrilha_estudo`)) join `aluno` `a` on(`r`.`aluno_id` = `a`.`idAluno`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_trilhas_personalizadas`
--

/*!50001 DROP VIEW IF EXISTS `vw_trilhas_personalizadas`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_trilhas_personalizadas` AS select `t`.`idTrilha_estudo` AS `idTrilha_estudo`,`a`.`nome` AS `aluno`,`t`.`titulo` AS `trilha`,`t`.`descricao` AS `descricao`,`t`.`status` AS `status` from (`trilha_estudo` `t` join `aluno` `a` on(`a`.`idAluno` = `t`.`aluno_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-19 12:37:02
