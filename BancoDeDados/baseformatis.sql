-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema PlataformaEstudos
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `PlataformaEstudos` DEFAULT CHARACTER SET utf8mb4;
USE `PlataformaEstudos`;

-- -----------------------------------------------------
-- Table: Aluno
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Aluno` (
  `idAluno` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `senha` VARCHAR(100) NOT NULL,
  `data_nascimento` DATE NOT NULL,
  `criacao_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idAluno`)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: historico_escolar
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `historico_escolar` (
  `idHistorico_escolar` INT NOT NULL AUTO_INCREMENT,
  `aluno_id` INT NOT NULL,
  `disciplina` VARCHAR(120) NOT NULL,
  `periodo` VARCHAR(20),
  `media` DECIMAL(4,2),
  `carga_horaria` INT,
  PRIMARY KEY (`idHistorico_escolar`),
  INDEX (`aluno_id`),
  CONSTRAINT `fk_historico_aluno`
    FOREIGN KEY (`aluno_id`)
    REFERENCES `Aluno` (`idAluno`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: Fonte
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Fonte` (
  `idFonte` INT NOT NULL AUTO_INCREMENT,
  `nome_fonte` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`idFonte`)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: conteudo
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `conteudo` (
  `idConteudo` INT NOT NULL AUTO_INCREMENT,
  `fonte_id` INT NOT NULL,
  `titulo` VARCHAR(200) NOT NULL,
  `categoria` VARCHAR(100) NOT NULL,
  `dificuldade` ENUM('basico', 'intermediario', 'avancado'),
  `link` VARCHAR(500) NOT NULL,
  `duracao_min` INT,
  PRIMARY KEY (`idConteudo`),
  INDEX (`fonte_id`),
  CONSTRAINT `fk_Fonte`
    FOREIGN KEY (`fonte_id`)
    REFERENCES `Fonte` (`idFonte`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: trilha_estudo
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `trilha_estudo` (
  `idTrilha_estudo` INT NOT NULL AUTO_INCREMENT,
  `aluno_id` INT NOT NULL,
  `titulo` VARCHAR(200),
  `descricao` TEXT,
  `status` ENUM('ativa', 'pausada', 'concluida') DEFAULT 'ativa',
  PRIMARY KEY (`idTrilha_estudo`),
  INDEX (`aluno_id`),
  CONSTRAINT `fk_trilha_estudo_aluno`
    FOREIGN KEY (`aluno_id`)
    REFERENCES `Aluno` (`idAluno`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: trilha_conteudo
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `trilha_conteudo` (
  `idTrilha_conteudo` INT NOT NULL AUTO_INCREMENT,
  `trilha_id` INT NOT NULL,
  `conteudo_id` INT NOT NULL,
  `ordem` INT NOT NULL,
  `obrigatorio` TINYINT NOT NULL,
  `estimativa_min` INT,
  PRIMARY KEY (`idTrilha_conteudo`),
  INDEX (`trilha_id`),
  INDEX (`conteudo_id`),
  CONSTRAINT `fk_trilha_conteudo_tEstudo`
    FOREIGN KEY (`trilha_id`)
    REFERENCES `trilha_estudo` (`idTrilha_estudo`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_conteudo`
    FOREIGN KEY (`conteudo_id`)
    REFERENCES `conteudo` (`idConteudo`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: sugestao_conteudo
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sugestao_conteudo` (
  `idSugestao_conteudo` INT NOT NULL AUTO_INCREMENT,
  `aluno_id` INT NOT NULL,
  `trilhaConteudo_id` INT NOT NULL,
  `titulo_sugerido` VARCHAR(200),
  `motivo` TEXT,
  `origem` VARCHAR(20) NOT NULL,
  `aceito` TINYINT NOT NULL DEFAULT 0,
  `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idSugestao_conteudo`),
  INDEX (`aluno_id`),
  INDEX (`trilhaConteudo_id`),
  CONSTRAINT `fk_sug_aluno`
    FOREIGN KEY (`aluno_id`)
    REFERENCES `Aluno` (`idAluno`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_sug_trilhaConteudo`
    FOREIGN KEY (`trilhaConteudo_id`)
    REFERENCES `trilha_conteudo` (`idTrilha_conteudo`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: progresso_trilha
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `progresso_trilha` (
  `idProgresso_conteudo` INT NOT NULL AUTO_INCREMENT,
  `aluno_id` INT NOT NULL,
  `trilhaConteudo_id` INT NOT NULL,
  `status` ENUM('pendente', 'em andamento', 'concluido') NOT NULL DEFAULT 'pendente',
  `inicio` TIMESTAMP NULL,
  `fim` TIMESTAMP NULL,
  `nota` DECIMAL(5,2),
  PRIMARY KEY (`idProgresso_conteudo`),
  INDEX (`aluno_id`),
  INDEX (`trilhaConteudo_id`),
  CONSTRAINT `fk_prog_aluno`
    FOREIGN KEY (`aluno_id`)
    REFERENCES `Aluno` (`idAluno`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_prog_trilhaConteudo`
    FOREIGN KEY (`trilhaConteudo_id`)
    REFERENCES `trilha_conteudo` (`idTrilha_conteudo`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: area_interesse
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `area_interesse` (
  `idArea_interesse` INT NOT NULL AUTO_INCREMENT,
  `nome_area` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`idArea_interesse`)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: preferencia_aluno
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `preferencia_aluno` (
  `idPreferencia_aluno` INT NOT NULL AUTO_INCREMENT,
  `aluno_id` INT NOT NULL,
  `areaInteresse_id` INT NOT NULL,
  `objetivo_carreira` TEXT,
  `nivel_dificuldade_preferido` VARCHAR(20),
  PRIMARY KEY (`idPreferencia_aluno`),
  INDEX (`aluno_id`),
  INDEX (`areaInteresse_id`),
  CONSTRAINT `fk_pref_aluno`
    FOREIGN KEY (`aluno_id`)
    REFERENCES `Aluno` (`idAluno`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_area_interesse`
    FOREIGN KEY (`areaInteresse_id`)
    REFERENCES `area_interesse` (`idArea_interesse`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: relatorio
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `relatorio` (
  `idRelatorio` INT NOT NULL AUTO_INCREMENT,
  `aluno_id` INT NOT NULL,
  `trilha_id` INT,
  `data_ref` DATE NOT NULL DEFAULT CURDATE(),
  `desempenho_score` DECIMAL(5,2) DEFAULT 0,
  `recomendacoes` TEXT,
  PRIMARY KEY (`idRelatorio`),
  INDEX (`aluno_id`),
  INDEX (`trilha_id`),
  CONSTRAINT `fk_rel_aluno`
    FOREIGN KEY (`aluno_id`)
    REFERENCES `Aluno` (`idAluno`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_rel_trilha`
    FOREIGN KEY (`trilha_id`)
    REFERENCES `trilha_estudo` (`idTrilha_estudo`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Views
-- -----------------------------------------------------
CREATE OR REPLACE VIEW vw_aluno_historico AS
SELECT a.idAluno, a.nome, a.email, h.disciplina, h.periodo, h.media, h.carga_horaria
FROM Aluno a
LEFT JOIN historico_escolar h ON a.idAluno = h.aluno_id;

CREATE OR REPLACE VIEW vw_fonte_conteudo AS
SELECT c.idConteudo, c.titulo, f.nome_fonte AS fonte, c.categoria, c.dificuldade, c.link
FROM conteudo c
JOIN Fonte f ON f.idFonte = c.fonte_id;

CREATE OR REPLACE VIEW vw_trilhas_personalizadas AS
SELECT t.idTrilha_estudo, a.nome AS aluno, t.titulo AS trilha, t.descricao, t.status
FROM trilha_estudo t
JOIN Aluno a ON a.idAluno = t.aluno_id;

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

CREATE OR REPLACE VIEW vw_relatorios_trilhas AS
SELECT r.idRelatorio, a.nome AS aluno, t.titulo AS trilha, r.data_ref, r.desempenho_score, r.recomendacoes
FROM relatorio r
JOIN trilha_estudo t ON r.trilha_id = t.idTrilha_estudo
JOIN Aluno a ON r.aluno_id = a.idAluno;

-- -----------------------------------------------------
-- Triggers
-- -----------------------------------------------------
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

DELIMITER ;
