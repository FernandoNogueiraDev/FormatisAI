-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema PlataformaEstudos
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema PlataformaEstudos
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `PlataformaEstudos` DEFAULT CHARACTER SET utf8 ;
USE `PlataformaEstudos` ;

-- -----------------------------------------------------
-- Table `PlataformaEstudos`.`Aluno`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PlataformaEstudos`.`Aluno` (
  `idAluno` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `data_nascimento` DATE NOT NULL,
  `criacao_em` TIMESTAMP NOT NULL,
  PRIMARY KEY (`idAluno`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `PlataformaEstudos`.`historico_escolar`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PlataformaEstudos`.`historico_escolar` (
  `idHistorico_escolar` INT NOT NULL AUTO_INCREMENT,
  `aluno_id` INT NOT NULL,
  `disciplina` VARCHAR(120) NOT NULL,
  `periodo` VARCHAR(20) NULL,
  `media` DECIMAL(4,2) NULL,
  `carga_horaria` INT NULL,
  PRIMARY KEY (`idHistorico_escolar`),
  INDEX `idAluno_idx` (`aluno_id` ASC),
  CONSTRAINT `fk_historico_aluno`
    FOREIGN KEY (`aluno_id`)
    REFERENCES `PlataformaEstudos`.`Aluno` (`idAluno`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `PlataformaEstudos`.`Fonte`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PlataformaEstudos`.`Fonte` (
  `idFonte` INT NOT NULL AUTO_INCREMENT,
  `nome_fonte` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`idFonte`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `PlataformaEstudos`.`conteudo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PlataformaEstudos`.`conteudo` (
  `idConteudo` INT NOT NULL AUTO_INCREMENT,
  `fonte_id` INT NOT NULL,
  `titulo` VARCHAR(200) NOT NULL,
  `categoria` VARCHAR(100) NOT NULL,
  `dificuldade` ENUM('basico', 'intermediario', 'avancado') NULL,
  `link` VARCHAR(500) NOT NULL,
  `duracao_min` INT NULL,
  PRIMARY KEY (`idConteudo`),
  INDEX `idFonte_idx` (`fonte_id` ASC),
  CONSTRAINT `fk_Fonte`
    FOREIGN KEY (`fonte_id`)
    REFERENCES `PlataformaEstudos`.`Fonte` (`idFonte`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `PlataformaEstudos`.`trilha_estudo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PlataformaEstudos`.`trilha_estudo` (
  `idTrilha_estudo` INT NOT NULL AUTO_INCREMENT,
  `aluno_id` INT NOT NULL,
  `titulo` VARCHAR(200) NULL,
  `descricao` TEXT NULL,
  `status` ENUM('ativa', 'pausada', 'concluida') NULL,
  PRIMARY KEY (`idTrilha_estudo`),
  INDEX `idaluno_idx` (`aluno_id` ASC),
  CONSTRAINT `fk_trilha_estudo_aluno`
    FOREIGN KEY (`aluno_id`)
    REFERENCES `PlataformaEstudos`.`Aluno` (`idAluno`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `PlataformaEstudos`.`trilha_conteudo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PlataformaEstudos`.`trilha_conteudo` (
  `idTrilha_conteudo` INT NOT NULL AUTO_INCREMENT,
  `trilha_id` INT NOT NULL,
  `conteudo_id` INT NOT NULL,
  `ordem` INT NOT NULL,
  `obrigatorio` TINYINT NOT NULL,
  `estimativa_min` INT NULL,
  PRIMARY KEY (`idTrilha_conteudo`),
  INDEX `idtrilha_estudo_idx` (`trilha_id` ASC),
  INDEX `idconteudo_idx` (`conteudo_id` ASC),
  CONSTRAINT `fk_trilha_conteudo_tEstudo`
    FOREIGN KEY (`trilha_id`)
    REFERENCES `PlataformaEstudos`.`trilha_estudo` (`idTrilha_estudo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_conteudo`
    FOREIGN KEY (`conteudo_id`)
    REFERENCES `PlataformaEstudos`.`conteudo` (`idConteudo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `PlataformaEstudos`.`sugestao_conteudo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PlataformaEstudos`.`sugestao_conteudo` (
  `idSugestao_conteudo` INT NOT NULL AUTO_INCREMENT,
  `aluno_id` INT NOT NULL,
  `trilhaConteudo_id` INT NOT NULL,
  `titulo_sugerido` VARCHAR(200) NULL,
  `motivo` TEXT NULL,
  `origem` VARCHAR(20) NOT NULL,
  `aceito` TINYINT NOT NULL,
  `criado_em` TIMESTAMP NOT NULL,
  PRIMARY KEY (`idSugestao_conteudo`),
  INDEX `idtrilha_conteudo_idx` (`trilhaConteudo_id` ASC),
  INDEX `idaluno_idx` (`aluno_id` ASC),
  CONSTRAINT `fk_suge_conteudo_aluno`
    FOREIGN KEY (`aluno_id`)
    REFERENCES `PlataformaEstudos`.`Aluno` (`idAluno`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_suge_conteudo_tConteudo`
    FOREIGN KEY (`trilhaConteudo_id`)
    REFERENCES `PlataformaEstudos`.`trilha_conteudo` (`idTrilha_conteudo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `PlataformaEstudos`.`progresso_trilha`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PlataformaEstudos`.`progresso_trilha` (
  `idProgresso_conteudo` INT NOT NULL AUTO_INCREMENT,
  `aluno_id` INT NOT NULL,
  `trilhaConteudo_id` INT NOT NULL,
  `status` ENUM('pendente', 'em andamento', 'concluido') NOT NULL,
  `inicio` TIMESTAMP NULL,
  `fim` TIMESTAMP NULL,
  `nota` DECIMAL(5,2) NULL,
  PRIMARY KEY (`idProgresso_conteudo`),
  INDEX `idaluno_idx` (`aluno_id` ASC),
  INDEX `idtrilha_conteudo_idx` (`trilhaConteudo_id` ASC),
  CONSTRAINT `fk_progre_trilha_aluno`
    FOREIGN KEY (`aluno_id`)
    REFERENCES `PlataformaEstudos`.`Aluno` (`idAluno`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_progre_trilha_tConteudo`
    FOREIGN KEY (`trilhaConteudo_id`)
    REFERENCES `PlataformaEstudos`.`trilha_conteudo` (`idTrilha_conteudo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `PlataformaEstudos`.`area_interesse`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PlataformaEstudos`.`area_interesse` (
  `idArea_interesse` INT NOT NULL,
  `nome_area` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`idArea_interesse`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `PlataformaEstudos`.`preferencia_aluno`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PlataformaEstudos`.`preferencia_aluno` (
  `idPreferencia_aluno` INT NOT NULL AUTO_INCREMENT,
  `aluno_id` INT NOT NULL,
  `areaInteresse_id` INT NOT NULL,
  `objetivo_carreira` TEXT NULL,
  `nivel_dificuldade_preferido` VARCHAR(20) NULL,
  PRIMARY KEY (`idPreferencia_aluno`),
  INDEX `idaluno_idx` (`aluno_id` ASC),
  INDEX `idarea_interesse_idx` (`areaInteresse_id` ASC),
  CONSTRAINT `fk_pref_aluno`
    FOREIGN KEY (`aluno_id`)
    REFERENCES `PlataformaEstudos`.`Aluno` (`idAluno`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_area_interesse`
    FOREIGN KEY (`areaInteresse_id`)
    REFERENCES `PlataformaEstudos`.`area_interesse` (`idArea_interesse`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `PlataformaEstudos`.`relatorio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PlataformaEstudos`.`relatorio` (
  `idRelatorio` INT NOT NULL AUTO_INCREMENT,
  `aluno_id` INT NOT NULL,
  `trilha_id` INT NOT NULL,
  `data_ref` DATE NOT NULL,
  `desempenho_score` DECIMAL(5,2) NULL,
  `recomendacoes` TEXT NULL,
  PRIMARY KEY (`idRelatorio`),
  INDEX `idaluno_idx` (`aluno_id` ASC),
  INDEX `idtrilha_estudo_idx` (`trilha_id` ASC),
  CONSTRAINT `fk_relatorio_aluno`
    FOREIGN KEY (`aluno_id`)
    REFERENCES `PlataformaEstudos`.`Aluno` (`idAluno`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_relatorio_trilha_estudo`
    FOREIGN KEY (`trilha_id`)
    REFERENCES `PlataformaEstudos`.`trilha_estudo` (`idTrilha_estudo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

---------------------------------------------------------------------------------------
-- VIEWS
--------------------------------------------------------------------------------------

CREATE OR REPLACE VIEW vw_aluno_historico AS
SELECT a.idAluno, a.nome, a.email, h.disciplina, h.periodo, h.media, h.carga_horaria
FROM Aluno a
LEFT JOIN historico_escolar h ON a.idAluno = h.aluno_id;
/* Visualizar dados completos do aluno e seu histórico escolar */

CREATE OR REPLACE VIEW vw_fonte_conteudo AS
SELECT c.idConteudo, c.titulo, f.nome_fonte AS fonte, c.categoria, c.dificuldade, c.link
FROM conteudo c
JOIN Fonte f ON f.idFonte = c.fonte_id;
/* Exibir origem e fonte dos conteúdos cadastrados */

CREATE OR REPLACE VIEW vw_trilhas_personalizadas AS
SELECT t.idTrilha_estudo, a.nome AS aluno, t.titulo AS trilha, t.descricao, t.status
FROM trilha_estudo t
JOIN Aluno a ON a.idAluno = t.aluno_id;
/* Unir dados de trilhas com os nomes dos alunos */

CREATE OR REPLACE VIEW vw_conteudos_recomendados AS
SELECT DISTINCT c.idConteudo, c.titulo, c.categoria, c.dificuldade, f.nome_fonte AS fonte, ad.aluno_id
FROM (
    SELECT h2.aluno_id,
           CASE
               WHEN AVG(h2.media) IS NULL OR AVG(h2.media) < 6 THEN 'basico'
               WHEN AVG(h2.media) BETWEEN 6 AND 8 THEN 'intermediario'
               ELSE 'avancado'
           END AS alvo_dificuldade
    FROM historico_escolar h2
    GROUP BY h2.aluno_id
) AS ad
JOIN conteudo c ON c.dificuldade = ad.alvo_dificuldade
JOIN Fonte f ON f.idFonte = c.fonte_id
ORDER BY ad.aluno_id;
/* Sugerir conteúdos com base no desempenho individual do aluno */

CREATE OR REPLACE VIEW vw_relatorios_trilhas AS
SELECT r.idRelatorio, a.nome AS aluno, t.titulo AS trilha, r.data_ref, r.desempenho_score, r.recomendacoes
FROM relatorio r
JOIN trilha_estudo t ON r.trilha_id = t.idTrilha_estudo
JOIN Aluno a ON r.aluno_id = a.idAluno;
/* Exibir relatórios de desempenho e recomendações dos alunos */

--------------------------------------------------------------------------------------
-- TRIGGERS
-------------------------------------------------------------------------------------
DELIMITER $$

CREATE TRIGGER trg_init_aluno
AFTER INSERT ON Aluno
FOR EACH ROW
BEGIN
    DECLARE novaTrilha INT;
    INSERT INTO trilha_estudo (aluno_id, titulo, descricao, status)
    VALUES (NEW.idAluno, CONCAT('Trilha Base de ', NEW.nome), 'Trilha inicial personalizada.', 'ativa');
    SET novaTrilha = LAST_INSERT_ID();
    INSERT INTO relatorio (aluno_id, trilha_id, data_ref, desempenho_score, recomendacoes)
    VALUES (NEW.idAluno, novaTrilha, CURDATE(), 0, 'Nenhuma recomendação inicial.');
END$$
/* Inicializa trilha e relatório quando um novo aluno é cadastrado */

CREATE TRIGGER trg_update_progresso_trilha
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
END$$
/* Atualiza relatório quando há progresso em um conteúdo da trilha */

CREATE TRIGGER trg_sugestao_conteudo
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
END$$
/* Gera sugestão automática de revisão baseada na média individual do aluno */

CREATE TRIGGER trg_aluno_delete
BEFORE DELETE ON Aluno
FOR EACH ROW
BEGIN
    INSERT INTO relatorio (aluno_id, trilha_id, data_ref, desempenho_score, recomendacoes)
    VALUES (OLD.idAluno, NULL, CURDATE(), 0, CONCAT('Aluno ', OLD.nome, ' removido.'));
    DELETE FROM trilha_estudo WHERE aluno_id = OLD.idAluno;
END$$
/* Insere log de exclusão do aluno e remove suas trilhas */

DELIMITER ;