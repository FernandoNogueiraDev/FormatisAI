-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 31/10/2025 às 20:14
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
  `tipo_aluno` varchar(100) NOT NULL,
  `data_nascimento` date NOT NULL,
  `criacao_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `aluno`
--

INSERT INTO `aluno` (`idAluno`, `nome`, `email`, `senha`, `tipo_aluno`, `data_nascimento`, `criacao_em`) VALUES
(1, 'Aluno_fulano', 'email@bemmail.com', '$2y$10$sPNNgD61AfjLGgsnp.JzNu0TmheuNYztMLOowj8aq3qJ0IvEbORnO', 'autodidata', '2007-04-04', '2025-10-20 01:45:37'),
(10, 'Teste', 'teste@teste.com', '$2y$10$sPNNgD61AfjLGgsnp.JzNu0TmheuNYztMLOowj8aq3qJ0IvEbORnO', '', '0000-00-00', '2025-10-19 16:38:00'),
(11, 'Fernando', 'fernando@email.com', '$2y$10$Pr8kYU7NdieT.mbk6aXySu66tZb1KA1G5JEmQkdLaCpf.6IQ1eOFO', '', '0000-00-00', '2025-10-20 00:04:39'),
(12, 'teste', 'teste@abc.com', '$2y$10$OfzFB/YJrAIgP1CaiUFhpueT6PlUjl6HO5Ihh995LMTik.omSjW2C', '', '0000-00-00', '2025-10-20 00:45:01'),
(13, 'Aluno da Silva', 'aluno@email.com', '$2y$10$NhT7oNbLeChzct5.rK1eK.bWN2YrSTPbZH6iY7rGo1JWttQzwm2eC', '', '0000-00-00', '2025-10-20 18:38:32'),
(14, 'Nome Aluno', 'email@aluno.com', '$2y$10$Z9kReHPGTkyUCx1jmcy0X.uUccshjxVcS16IPOWmCzev8EpJ.kCfK', '', '0000-00-00', '2025-10-20 18:39:42'),
(15, 'Aluno Exemplo', 'exemplo@email.com', '$2y$10$M8UXhSL8MFvNavdHFp1DYOHpVCOiT38ovlQn89xeXcMEEJimwfZcK', '', '0000-00-00', '2025-10-20 18:40:32'),
(16, 'Usuario Exemplo', 'usuario@exemplo.com', '$2y$10$q.ipl5IxJvrcrtSTa5.PgesakDF2dlGee6UeBDD65pjsHKUnXTpFa', '', '0000-00-00', '2025-10-20 18:44:30');

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

--
-- Despejando dados para a tabela `conteudo`
--

INSERT INTO `conteudo` (`idConteudo`, `fonte_id`, `titulo`, `categoria`, `dificuldade`, `link`, `duracao_min`) VALUES
(1, 1, 'Configurando o Ambiente Java e Sua Primeira Aplicação', 'vídeo', NULL, 'https://www.youtube.com/watch?v=F_fG1y2zG0w', 30),
(2, 2, 'Explorando Variáveis, Tipos de Dados e Operadores em Java', 'vídeo', NULL, 'https://www.youtube.com/watch?v=q6d-EwQh16I', 35),
(3, 3, 'Dominando Estruturas Condicionais e de Repetição', 'vídeo', NULL, 'https://www.youtube.com/watch?v=4y-R6R7rOqs', 50),
(4, 4, 'Desvendando a Programação Orientada a Objetos (POO) em Java', 'artigo', NULL, 'https://www.alura.com.br/artigos/programacao-orientada-a-objetos-poo-na-linguagem-java', 20),
(5, 5, 'Criando Classes, Objetos e Métodos na Prática com Java', 'vídeo', NULL, 'https://www.youtube.com/watch?v=sI9p04X3qA8', 55),
(6, 6, 'Gerenciando Dados com Arrays em Java', 'vídeo', NULL, 'https://www.youtube.com/watch?v=Qh_iB7G3eN8', 40),
(7, 7, 'Trabalhando com ArrayList para Listas Dinâmicas', 'vídeo', NULL, 'https://www.youtube.com/watch?v=uD058L_T41g', 35),
(8, 8, 'Lidando com Erros: Tratamento de Exceções em Java', 'vídeo', NULL, 'https://www.youtube.com/watch?v=qX9Qd2z1yYw', 45),
(9, 9, 'Lógica de Programação e Algoritmos: O Início de Tudo', 'vídeo', NULL, 'https://www.youtube.com/watch?v=8mei6uZT-Ts', 40),
(10, 10, 'Introdução ao Java e Configuração do Ambiente de Desenvolvimento', 'vídeo', NULL, 'https://www.youtube.com/watch?v=sO7tV2HwS38', 30),
(11, 11, 'Fundamentos do Java: Variáveis, Tipos de Dados e Operadores', 'artigo', NULL, 'https://www.devmedia.com.br/java-variaveis-tipos-e-operadores/22026', 25),
(12, 12, 'Estruturas de Controle em Java: Condicionais e Laços de Repetição', 'vídeo', NULL, 'https://www.youtube.com/watch?v=vVjV-7qK5Q8', 45),
(13, 13, 'Introdução à Programação Orientada a Objetos (POO)', 'artigo', NULL, 'https://www.devmedia.com.br/introducao-a-programacao-orientada-a-objetos-poo/26274', 30),
(14, 14, 'POO em Java: Encapsulamento, Herança e Polimorfismo', 'vídeo', NULL, 'https://www.youtube.com/watch?v=gT1m574o7w8', 50),
(15, 15, 'Trabalhando com Arrays e Coleções Básicas (ArrayList) em Java', 'artigo', NULL, 'https://www.devmedia.com.br/java-arraylist-manipulando-dados-em-uma-estrutura-dinamica/28800', 25),
(16, 16, 'Lidando com Erros: Tratamento de Exceções (try-catch) em Java', 'vídeo', NULL, 'https://www.youtube.com/watch?v=420qX9XvQeQ', 35),
(17, 17, 'Projeto Prático: Desenvolvendo uma Calculadora Básica em Java', 'vídeo', NULL, 'https://www.youtube.com/watch?v=x7f83Uv6d7Y', 60),
(18, 18, 'O que é Java e Por Que Usar?', 'artigo', NULL, 'https://www.alura.com.br/artigos/o-que-e-java-introducao-a-linguagem', 15),
(19, 19, 'Configurando o Ambiente de Desenvolvimento Java (JDK e IDE)', 'vídeo', NULL, 'https://www.youtube.com/watch?v=sU36pI9i8OQ', 30),
(20, 20, 'Meu Primeiro Programa Java: \'Hello World\' e Conceitos Básicos', 'vídeo', NULL, 'https://www.youtube.com/watch?v=yYh_B0wK1a0', 30),
(21, 21, 'Variáveis, Tipos de Dados e Operadores em Java', 'artigo', NULL, 'https://www.w3schools.com/java/java_variables.asp', 30),
(22, 22, 'Estruturas de Controle de Fluxo: Condicionais e Laços', 'vídeo', NULL, 'https://www.youtube.com/watch?v=mD0kR4l4_v8', 1),
(23, 23, 'Introdução à Programação Orientada a Objetos (POO) com Java', 'artigo', NULL, 'https://www.alura.com.br/artigos/o-que-e-programacao-orientada-a-objetos-poo', 30),
(24, 24, 'Arrays e Listas (ArrayList) em Java', 'vídeo', NULL, 'https://www.youtube.com/watch?v=M99C-kM99xM', 1),
(25, 25, 'Lógica de Programação: Primeiros Passos Essenciais', 'Vídeo', NULL, 'https://www.youtube.com/watch?v=coU15LqJkC8', 35),
(26, 26, 'Java para Iniciantes: Primeiros Passos e Instalação', 'Vídeo', NULL, 'https://www.youtube.com/watch?v=S-JtXkL3Wc0', 30),
(27, 27, 'Java Básico: Variáveis, Tipos de Dados e Operadores', 'Artigo', NULL, 'https://www.geeksforgeeks.org/java-basic-syntax/', 45),
(28, 28, 'Condicionais e Laços de Repetição em Java', 'Vídeo', NULL, 'https://www.youtube.com/watch?v=zJg5_M-wM4Y', 50),
(29, 29, 'Fundamentos de POO em Java: Classes e Objetos', 'Artigo', NULL, 'https://www.freecodecamp.org/news/object-oriented-programming-oop-in-java-explained-with-examples/', 60),
(30, 30, 'Desenvolvendo com Métodos e Encapsulamento em Java', 'Vídeo', NULL, 'https://www.youtube.com/watch?v=Qh_t1uR4T6Y', 35),
(31, 31, 'Lidando com Listas de Dados: Arrays em Java', 'Vídeo', NULL, 'https://www.youtube.com/watch?v=68E6C8c_3qg', 25),
(32, 32, 'Desafios Práticos de Java para Iniciantes', 'Plataforma de Exercícios', NULL, 'https://www.hackerrank.com/domains/java?filters%5Bskills%5D%5B%5D=Basic', 90),
(33, 33, '1. Fundamentos de Lógica de Programação e Algoritmos', 'vídeo', NULL, 'https://www.youtube.com/playlist?list=PLHz_AreHm4dmSj0Mv9L9_sdRQVEk2x5Dk', 3),
(34, 34, '2. Preparando o Ambiente Java: JDK e IDE', 'vídeo', NULL, 'https://www.youtube.com/watch?v=Fj-yIeK96kY', 45),
(35, 35, '3. Primeiros Passos em Java: Sintaxe Básica, Variáveis e Tipos de Dados', 'artigo', NULL, 'https://www.w3schools.com/java/java_syntax.asp', 1),
(36, 36, '4. Estruturas de Controle de Fluxo: Condicionais (if/else, switch) e Laços (for, while)', 'artigo', NULL, 'https://www.w3schools.com/java/java_conditions.asp', 2),
(37, 37, '5. Introdução aos Métodos (Funções) em Java', 'artigo', NULL, 'https://www.w3schools.com/java/java_methods.asp', 1),
(38, 38, '6. Fundamentos da Programação Orientada a Objetos (POO) com Java', 'vídeo', NULL, 'https://www.youtube.com/watch?v=K3S7f61X85k', 2),
(39, 39, '7. Arrays e ArrayLists: Lidando com Coleções de Dados', 'artigo', NULL, 'https://www.w3schools.com/java/java_arraylist.asp', 2),
(40, 40, '8. Mini-Projeto Prático: Construindo uma Calculadora Simples em Java', 'vídeo', NULL, 'https://www.youtube.com/watch?v=A3_N_y9xVjE', 3),
(41, 41, 'Java para Iniciantes #01 - Instalar JDK e IntelliJ IDEA (Ou Eclipse/VSCode)', 'vídeo', NULL, 'https://www.youtube.com/watch?v=sI-c1t2WJ9E', 30),
(42, 42, 'Java para Iniciantes #02 - Primeiro Programa e Variáveis', 'vídeo', NULL, 'https://www.youtube.com/watch?v=Fj-0D0s4d0Q', 40),
(43, 43, 'Java para Iniciantes #03 - Estruturas Condicionais', 'vídeo', NULL, 'https://www.youtube.com/watch?v=wX1wGfA9cns', 35),
(44, 44, 'Java para Iniciantes #04 - Estruturas de Repetição (Laços)', 'vídeo', NULL, 'https://www.youtube.com/watch?v=9_d8o4rC9Dk', 35),
(45, 45, 'Introdução à Orientação a Objetos em Java | Masterclass #01', 'vídeo', NULL, 'https://www.youtube.com/watch?v=mD_P2Fz1pM8', 1),
(46, 46, 'Arrays em Java - Programação Orientada a Objetos | Loiane Groner', 'vídeo', NULL, 'https://www.youtube.com/watch?v=vX_s6P5y1xM', 45),
(47, 47, 'OOP Concepts In Java With Examples | Encapsulation, Inheritance, Polymorphism', 'artigo', NULL, 'https://www.geeksforgeeks.org/oop-concepts-in-java/', 25),
(48, 48, 'Exceções em Java: o que são e como utilizá-las | Blog da Alura', 'artigo', NULL, 'https://www.alura.com.br/artigos/excecoes-em-java-o-que-sao-e-como-utiliza-las', 20),
(49, 49, 'Configurando o Ambiente Java e Primeiro Programa', 'vídeo', NULL, 'https://www.youtube.com/watch?v=UqQ20z-KjI0', 1),
(50, 50, 'Variáveis, Tipos de Dados e Operadores', 'vídeo', NULL, 'https://www.youtube.com/watch?v=SjB4MvY1_c0', 31),
(51, 51, 'Estruturas Condicionais (if/else)', 'vídeo', NULL, 'https://www.youtube.com/watch?v=nE-L3D5-q_s', 35),
(52, 52, 'Estruturas de Repetição (for, while)', 'vídeo', NULL, 'https://www.youtube.com/watch?v=0k9jL39g4j0', 34),
(53, 53, 'Entendendo e Criando Métodos em Java', 'artigo', NULL, 'https://www.alura.com.br/artigos/o-que-sao-metodos-em-java', 20),
(54, 54, 'Introdução à Programação Orientada a Objetos (POO): Classes e Objetos', 'vídeo', NULL, 'https://www.youtube.com/watch?v=Kk6_f2MvIys', 25),
(55, 55, 'Mini Projeto Prático: Calculadora Simples em Java', 'vídeo', NULL, 'https://www.youtube.com/watch?v=QY-2l67q61s', 1),
(56, 56, '1. Lógica de Programação e Algoritmos para Iniciantes', 'vídeo', NULL, 'https://www.youtube.com/watch?v=iF2MdbrTiBM', 4),
(57, 57, '2. Introdução ao Java: O que é e Configuração do Ambiente', 'artigo', NULL, 'https://www.reddit.com/r/learnprogramming/comments/1430rma/anyone_else_use_code_academy_to_learn_programming/?tl=pt-pt', 30),
(58, 58, '3. Java Básico: Variáveis, Tipos de Dados e Operadores', 'artigo', NULL, 'https://www.reddit.com/r/learnjava/comments/1618623/what_is_the_best_and_realistic_way_of_learning/?tl=pt-br', 45),
(59, 59, '4. Estruturas Condicionais e de Repetição em Java', 'vídeo', NULL, 'https://www.youtube.com/watch?v=2fawKjR8d4c', 1),
(60, 60, '5. Conceitos Fundamentais de Programação Orientada a Objetos (POO)', 'vídeo', NULL, 'https://www.youtube.com/watch?v=dXZRgW-X2ls', 1),
(61, 61, '6. Programação Orientada a Objetos na Prática com Java', 'vídeo', NULL, 'https://www.youtube.com/watch?v=1n4f_FntuEc', 1),
(62, 62, '7. Manipulando Dados: Arrays em Java', 'artigo', NULL, 'https://www.reddit.com/r/learnprogramming/comments/11air3a/i_completed_every_single_certificate_on/?tl=pt-br', 30),
(63, 63, '8. Manipulando Dados: ArrayList em Java (Introdução a Coleções)', 'artigo', NULL, 'https://github.com/arthurspk/guiadevbrasil', 30),
(64, 64, '9. Projeto Prático: Construa sua Primeira Calculadora Simples em Java', 'vídeo', NULL, 'https://www.youtube.com/watch?v=pQWh5jWSMwU', 1),
(65, 65, 'Lógica de Programação Essencial (com Portugol)', 'vídeo', '', 'https://www.youtube.com/watch?v=H7Z_q6kVTRA', 1),
(66, 66, 'Instalando JDK, IDE e Escrevendo seu Primeiro Código Java', 'vídeo', '', 'https://www.youtube.com/watch?v=-uwh4GWJBqc', 35),
(67, 67, 'Variáveis, Tipos de Dados e Operadores em Java', 'artigo', '', 'https://www.devmedia.com.br/iniciando-na-linguagem-java/21136', 45),
(68, 68, 'Estruturas Condicionais (if/else, switch) e Laços de Repetição (for, while) em Java', 'vídeo', '', 'https://www.youtube.com/watch?v=BMmT62Kiml0', 1),
(69, 69, 'O que é Programação Orientada a Objetos (POO)?', 'vídeo', '', 'https://www.youtube.com/watch?v=dXZRgW-X2ls', 40),
(70, 70, 'Conceitos de POO: Classes, Objetos, Métodos e Atributos em Java', 'artigo', '', 'https://www.alura.com.br/artigos/poo-programacao-orientada-a-objetos', 1),
(71, 71, '10 Exercícios de Java para Iniciantes (com resolução)', 'artigo', '', 'https://www.devmedia.com.br/html-basico-codigos-html/16596', 2),
(72, 72, 'Lógica de Programação: O que é Algoritmo?', 'vídeo', '', 'https://www.youtube.com/watch?v=mstMhMT_UeA', 30),
(73, 73, 'Introdução ao Java: Configuração do Ambiente e Primeiro Programa', 'vídeo', '', 'https://www.youtube.com/watch?v=sss0COlyuHs', 25),
(74, 74, 'Fundamentos de Java: Variáveis, Tipos de Dados e Operadores', 'artigo', '', 'https://www.devmedia.com.br/introducao-ao-java-server-pages-jsp/25602', 40),
(75, 75, 'Estruturas de Controle de Fluxo em Java (If/Else, Switch, Laços)', 'vídeo', '', 'https://www.youtube.com/watch?v=BMmT62Kiml0', 45),
(76, 76, 'O que é Programação Orientada a Objetos (POO): Conceitos Básicos', 'artigo', '', 'https://www.alura.com.br/artigos/poo-programacao-orientada-a-objetos', 30),
(77, 77, 'POO na Prática: Classes, Objetos, Atributos e Métodos em Java', 'vídeo', '', 'https://www.youtube.com/watch?v=ohmHbdUhAGc', 50),
(78, 78, 'Arrays em Java: Trabalhando com Vetores', 'vídeo', '', 'https://www.youtube.com/watch?v=KAS94-Lcboc', 45),
(79, 79, 'Introdução às Collections Framework: Conhecendo o ArrayList', 'artigo', '', 'https://www.devmedia.com.br/java-collections-como-utilizar-collections/18450', 35),
(80, 80, 'Lógica de Programação Essencial para Iniciantes (Aula 01)', 'vídeo', '', 'https://www.youtube.com/watch?v=C1QvdJ7gj4E', 30),
(81, 81, 'O que é Java? Uma Introdução para Iniciantes', 'artigo', '', 'https://www.dio.me/articles/5-motivos-pelo-qual-java-e-a-melhor-linguagem-para-iniciar-sua-jornada-como-dev', 15),
(82, 82, 'Como Instalar Java (JDK) e Configurar o Ambiente de Desenvolvimento', 'vídeo', '', 'https://www.youtube.com/watch?v=XloHeYZmqis', 20),
(83, 83, 'Primeiros Passos em Java: Sintaxe Básica, Variáveis e Operadores', 'vídeo', '', 'https://www.youtube.com/watch?v=mo3fpbW_eTA', 40),
(84, 84, 'Estruturas Condicionais em Java: If, Else If, Else e Switch', 'artigo', '', 'https://www.dio.me/articles/como-usar-switch-case-no-java', 20),
(85, 85, 'Estruturas de Repetição em Java: Loops For, While e Do-While', 'vídeo', '', 'https://www.youtube.com/watch?v=dp28i8MLWh4', 35),
(86, 86, 'Criando e Usando Métodos (Funções) em Java', 'artigo', '', 'https://www.reddit.com/r/learnprogramming/comments/1fns0kw/computer_science_in_school_what_is_java_used_for/?tl=pt-br', 25),
(87, 87, 'Entendendo Orientação a Objetos com Java: Classes e Objetos (POO Parte 1)', 'vídeo', '', 'https://www.youtube.com/watch?v=8VcZkAYygoo', 30),
(88, 88, 'Projeto Prático: Como Construir uma Calculadora Simples em Java', 'artigo', '', 'https://www.reddit.com/r/learnjavascript/comments/ihvw8d/is_it_possible_to_learn_javascript_by_myself/?tl=pt-br', 60);

-- --------------------------------------------------------

--
-- Estrutura para tabela `fonte`
--

CREATE TABLE `fonte` (
  `idFonte` int(11) NOT NULL,
  `nome_fonte` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `fonte`
--

INSERT INTO `fonte` (`idFonte`, `nome_fonte`) VALUES
(1, 'Loiane Groner (YouTube)'),
(2, 'Loiane Groner (YouTube)'),
(3, 'Loiane Groner (YouTube)'),
(4, 'Alura (Artigo Gratuito)'),
(5, 'Loiane Groner (YouTube)'),
(6, 'Loiane Groner (YouTube)'),
(7, 'Loiane Groner (YouTube)'),
(8, 'Loiane Groner (YouTube)'),
(9, 'Curso em Vídeo (Gustavo Guanabara)'),
(10, 'Programação Descomplicada (YouTube)'),
(11, 'DevMedia'),
(12, 'Loiane Groner (YouTube)'),
(13, 'DevMedia'),
(14, 'Loiane Groner (YouTube)'),
(15, 'DevMedia'),
(16, 'Loiane Groner (YouTube)'),
(17, 'Mundo da Programação (YouTube)'),
(18, 'Blog Alura'),
(19, 'DevSuperior (YouTube)'),
(20, 'Curso em Vídeo (YouTube)'),
(21, 'W3Schools'),
(22, 'DevSuperior (YouTube)'),
(23, 'Blog Alura'),
(24, 'Curso em Vídeo (YouTube)'),
(25, 'Curso em Vídeo'),
(26, 'Curso em Vídeo'),
(27, 'GeeksforGeeks'),
(28, 'Alura (YouTube)'),
(29, 'freeCodeCamp'),
(30, 'Loiane Groner'),
(31, 'Loiane Groner'),
(32, 'HackerRank'),
(33, 'Curso em Vídeo (Prof. Gustavo Guanabara)'),
(34, 'DevSuperior'),
(35, 'W3Schools'),
(36, 'W3Schools'),
(37, 'W3Schools'),
(38, 'freeCodeCamp.org'),
(39, 'W3Schools'),
(40, 'DevSuperior'),
(41, 'Curso em Vídeo'),
(42, 'Curso em Vídeo'),
(43, 'Curso em Vídeo'),
(44, 'Curso em Vídeo'),
(45, 'Loiane Groner'),
(46, 'Loiane Groner'),
(47, 'GeeksforGeeks'),
(48, 'Alura'),
(49, 'DevSuperior'),
(50, 'Curso em Vídeo'),
(51, 'Curso em Vídeo'),
(52, 'Curso em Vídeo'),
(53, 'Alura'),
(54, 'Loiane Groner'),
(55, 'DevSuperior'),
(56, 'www.youtube.com'),
(57, 'www.reddit.com'),
(58, 'www.reddit.com'),
(59, 'www.youtube.com'),
(60, 'www.youtube.com'),
(61, 'www.youtube.com'),
(62, 'www.reddit.com'),
(63, 'github.com'),
(64, 'www.youtube.com'),
(65, 'www.youtube.com'),
(66, 'www.youtube.com'),
(67, 'www.devmedia.com.br'),
(68, 'www.youtube.com'),
(69, 'www.youtube.com'),
(70, 'www.alura.com.br'),
(71, 'www.devmedia.com.br'),
(72, 'www.youtube.com'),
(73, 'www.youtube.com'),
(74, 'www.devmedia.com.br'),
(75, 'www.youtube.com'),
(76, 'www.alura.com.br'),
(77, 'www.youtube.com'),
(78, 'www.youtube.com'),
(79, 'www.devmedia.com.br'),
(80, 'www.youtube.com'),
(81, 'www.dio.me'),
(82, 'www.youtube.com'),
(83, 'www.youtube.com'),
(84, 'www.dio.me'),
(85, 'www.youtube.com'),
(86, 'www.reddit.com'),
(87, 'www.youtube.com'),
(88, 'www.reddit.com');

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
  `trilha_id` int(11) NOT NULL,
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
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `sugestao_conteudo`
--

INSERT INTO `sugestao_conteudo` (`idSugestao_conteudo`, `aluno_id`, `trilhaConteudo_id`, `titulo_sugerido`, `motivo`, `origem`, `aceito`, `criado_em`) VALUES
(1, 1, 1, 'Desvendando Java: Sua Jornada Autodidata para a Carreira em Tecnologia', 'Para fornecer um caminho claro, objetivo e prático para um autodidata iniciar sua jornada em Java, focando em recursos acessíveis e alinhados com o interesse em tecnologia e o objetivo de carreira, permitindo aprendizado flexível e auto-dirigido.', 'IA Gerado por Aluno', 2, '2025-10-19 16:07:17'),
(2, 1, 9, 'Trilha Java para Autodidatas: Primeiros Passos e Prática', 'Pensada especificamente para um aprendiz independente e flexível, esta trilha oferece uma jornada progressiva e mão na massa em Java. Os conteúdos foram selecionados para serem práticos e de livre acesso, permitindo a aplicação rápida dos conhecimentos, o que se alinha perfeitamente ao seu objetivo de carreira em Java e ao seu perfil autodidata.', 'IA Gerado por Aluno', 1, '2025-10-19 16:17:56'),
(3, 1, 18, 'Desvendando Java: Seus Primeiros Passos como Desenvolvedor Autodidata', 'A trilha atende ao perfil de aprendizado do \'Aluno\' ao oferecer uma progressão lógica de conceitos Java, desde a introdução até temas essenciais de Programação Orientada a Objetos e manipulação de dados. Os recursos são selecionados por serem gratuitos, práticos e flexíveis, permitindo que o aluno avance em seu próprio ritmo, alinhando-se perfeitamente com seu estilo autodidata e foco em resultados rápidos.', 'IA Gerado por Aluno', 1, '2025-10-20 00:28:40'),
(4, 1, 25, 'Jornada Java para Iniciantes: Da Lógica à POO', 'A trilha é progressiva, iniciando com lógica de programação e avançando para a sintaxe e os conceitos essenciais de Java, incluindo POO, que é crucial para a linguagem. Os conteúdos são práticos e gratuitos, alinhados com o perfil de aprendizado independente e flexível do aluno, permitindo que ele construa uma base sólida em Java de forma eficiente.', 'IA Gerado por Aluno', 1, '2025-10-20 00:45:50'),
(5, 1, 33, 'Desvendando Java: Primeiros Passos para o Autodidata', 'Esta trilha foi desenhada especificamente para um aluno autodidata de 23 anos, focado em tecnologia e com o objetivo de iniciar sua carreira em Java, partindo do nível iniciante. A seleção de conteúdos em formato de vídeo e artigo atende à sua preferência e ao contexto de aprendizagem independente e flexível. Priorizamos materiais práticos, gratuitos e de rápida aplicação para construir uma base sólida, desde a lógica de programação até os conceitos essenciais de Java e POO, preparando o terreno para projetos mais complexos.', 'IA Gerado por Aluno', 1, '2025-10-20 00:47:07'),
(6, 1, 41, 'Trilha de Iniciação ao Desenvolvimento Java para Autodidatas', 'Baseado no seu perfil de aluno autodidata com interesse em Tecnologia e objetivo de carreira em Java, esta trilha oferece uma progressão lógica desde os conceitos básicos até os pilares da Programação Orientada a Objetos. Os conteúdos foram selecionados priorizando vídeos e artigos, que se alinham à sua preferência por materiais práticos, gratuitos e de consumo flexível.', 'IA Gerado por Aluno', 1, '2025-10-20 00:47:54'),
(7, 1, 49, 'Primeiros Passos em Java para Desenvolvedores Autodidatas', 'Com base no seu perfil de aluno autodidata, iniciante em Java e buscando conteúdos práticos e gratuitos, esta trilha oferece uma progressão lógica e didática. Os materiais selecionados são de fontes confiáveis e populares entre a comunidade de desenvolvedores, garantindo qualidade e aplicabilidade imediata dos conhecimentos adquiridos. O foco em vídeo e artigo atende à sua preferência de consumo de conteúdo.', 'IA Gerado por Aluno', 1, '2025-10-20 00:49:44'),
(8, 1, 56, 'Jornada Java para Iniciantes: Do Zero ao Primeiro Código Funcional', 'Para o aluno \'Iniciante\' com interesse em \'Tecnologia\' e objetivo de carreira em \'Java\', esta trilha oferece uma progressão lógica, começando pelos fundamentos da programação, passando pela sintaxe básica de Java, conceitos essenciais de Orientação a Objetos e culminando em um projeto prático. Os conteúdos são selecionados para serem gratuitos, práticos e consumíveis de forma flexível, alinhando-se perfeitamente ao perfil de aprendizagem autodidata do aluno.', 'IA Gerado por Aluno', 1, '2025-10-20 18:30:09'),
(9, 1, 65, 'Desvendando Java: Primeiros Passos Essenciais para Autodidatas', 'Com base no perfil de \'Aluno\' – iniciante em Java, autodidata, buscando conteúdos práticos, gratuitos e de rápida aplicação – esta trilha oferece uma base sólida. Começa com lógica para construir o pensamento programático, avança para a configuração do ambiente e os pilares da sintaxe Java, e culmina com a introdução à Programação Orientada a Objetos e exercícios práticos. Os formatos de vídeo e artigo são adequados ao tipo de conteúdo preferido e a duração de cada item permite um aprendizado flexível.', 'IA Gerado por Aluno', 1, '2025-10-20 18:31:02'),
(10, 1, 72, 'Primeiros Passos no Mundo Java: De Iniciante a Fundamentos Essenciais', 'Com base no seu perfil de aluno autodidata, com forte interesse em tecnologia e objetivo de carreira em Java, esta trilha oferece uma progressão lógica e prática. Os conteúdos foram selecionados para serem gratuitos, de rápida absorção e alinhados com seus tipos de conteúdo preferidos (vídeos e artigos), permitindo que você aprenda no seu ritmo e aplique os conhecimentos de forma independente e flexível.', 'IA Gerado por Aluno', 1, '2025-10-20 18:41:53'),
(11, 16, 80, 'Trilha Essencial Java para Iniciantes Autodidatas', 'Sua preferência por conteúdos em vídeo e artigos, juntamente com o desejo de aprendizado prático e gratuito, foi o guia para a seleção desses materiais. A progressão do básico à introdução de POO e um projeto prático foi pensada para consolidar seu conhecimento como iniciante em Java.', 'IA Gerado por Aluno', 1, '2025-10-20 18:45:28');

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

--
-- Despejando dados para a tabela `trilha_conteudo`
--

INSERT INTO `trilha_conteudo` (`idTrilha_conteudo`, `trilha_id`, `conteudo_id`, `ordem`, `obrigatorio`, `estimativa_min`) VALUES
(1, 11, 1, 1, 0, 30),
(2, 11, 2, 2, 0, 35),
(3, 11, 3, 3, 0, 50),
(4, 11, 4, 4, 0, 20),
(5, 11, 5, 5, 0, 55),
(6, 11, 6, 6, 0, 40),
(7, 11, 7, 7, 0, 35),
(8, 11, 8, 8, 0, 45),
(9, 12, 9, 1, 0, 40),
(10, 12, 10, 2, 0, 30),
(11, 12, 11, 3, 0, 25),
(12, 12, 12, 4, 0, 45),
(13, 12, 13, 5, 0, 30),
(14, 12, 14, 6, 0, 50),
(15, 12, 15, 7, 0, 25),
(16, 12, 16, 8, 0, 35),
(17, 12, 17, 9, 0, 60),
(18, 15, 18, 1, 0, 15),
(19, 15, 19, 2, 0, 30),
(20, 15, 20, 3, 0, 30),
(21, 15, 21, 4, 0, 30),
(22, 15, 22, 5, 0, 1),
(23, 15, 23, 6, 0, 30),
(24, 15, 24, 7, 0, 1),
(25, 17, 25, 1, 0, 35),
(26, 17, 26, 2, 0, 30),
(27, 17, 27, 3, 0, 45),
(28, 17, 28, 4, 0, 50),
(29, 17, 29, 5, 0, 60),
(30, 17, 30, 6, 0, 35),
(31, 17, 31, 7, 0, 25),
(32, 17, 32, 8, 0, 90),
(33, 18, 33, 1, 0, 3),
(34, 18, 34, 2, 0, 45),
(35, 18, 35, 3, 0, 1),
(36, 18, 36, 4, 0, 2),
(37, 18, 37, 5, 0, 1),
(38, 18, 38, 6, 0, 2),
(39, 18, 39, 7, 0, 2),
(40, 18, 40, 8, 0, 3),
(41, 19, 41, 1, 0, 30),
(42, 19, 42, 2, 0, 40),
(43, 19, 43, 3, 0, 35),
(44, 19, 44, 4, 0, 35),
(45, 19, 45, 5, 0, 1),
(46, 19, 46, 6, 0, 45),
(47, 19, 47, 7, 0, 25),
(48, 19, 48, 8, 0, 20),
(49, 20, 49, 1, 0, 1),
(50, 20, 50, 2, 0, 31),
(51, 20, 51, 3, 0, 35),
(52, 20, 52, 4, 0, 34),
(53, 20, 53, 5, 0, 20),
(54, 20, 54, 6, 0, 25),
(55, 20, 55, 7, 0, 1),
(56, 21, 56, 1, 0, 4),
(57, 21, 57, 2, 0, 30),
(58, 21, 58, 3, 0, 45),
(59, 21, 59, 4, 0, 1),
(60, 21, 60, 5, 0, 1),
(61, 21, 61, 6, 0, 1),
(62, 21, 62, 7, 0, 30),
(63, 21, 63, 8, 0, 30),
(64, 21, 64, 9, 0, 1),
(65, 22, 65, 1, 0, 1),
(66, 22, 66, 2, 0, 35),
(67, 22, 67, 3, 0, 45),
(68, 22, 68, 4, 0, 1),
(69, 22, 69, 5, 0, 40),
(70, 22, 70, 6, 0, 1),
(71, 22, 71, 7, 0, 2),
(72, 23, 72, 1, 0, 30),
(73, 23, 73, 2, 0, 25),
(74, 23, 74, 3, 0, 40),
(75, 23, 75, 4, 0, 45),
(76, 23, 76, 5, 0, 30),
(77, 23, 77, 6, 0, 50),
(78, 23, 78, 7, 0, 45),
(79, 23, 79, 8, 0, 35),
(80, 24, 80, 1, 0, 30),
(81, 24, 81, 2, 0, 15),
(82, 24, 82, 3, 0, 20),
(83, 24, 83, 4, 0, 40),
(84, 24, 84, 5, 0, 20),
(85, 24, 85, 6, 0, 35),
(86, 24, 86, 7, 0, 25),
(87, 24, 87, 8, 0, 30),
(88, 24, 88, 9, 0, 60);

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

--
-- Despejando dados para a tabela `trilha_estudo`
--

INSERT INTO `trilha_estudo` (`idTrilha_estudo`, `aluno_id`, `titulo`, `descricao`, `status`) VALUES
(11, 1, 'Python', 'Esta trilha foi cuidadosamente elaborada para você, um autodidata iniciante em tecnologia, com o objetivo claro de dominar os fundamentos de Java para sua carreira. Priorizamos conteúdos práticos, gratuitos e de rápida aplicação, utilizando uma combinação estratégica de vídeos e artigos para construir uma base sólida no desenvolvimento Java de forma flexível e independente.', ''),
(12, 1, 'Python', 'Esta trilha foi cuidadosamente elaborada para o aluno autodidata que deseja iniciar sua jornada no mundo da programação Java. Focando em conceitos práticos, gratuitos e de rápida aplicação, a trilha aborda desde a lógica fundamental até os pilares da linguagem, culminando em um projeto prático para solidificar o aprendizado.', ''),
(15, 1, NULL, 'Esta trilha foi cuidadosamente elaborada para o \'Aluno\', um entusiasta da Tecnologia, iniciante em Java e autodidata. O caminho proposto foca em conteúdos práticos, gratuitos e de rápida aplicação, utilizando vídeos e artigos para construir uma base sólida nos fundamentos da programação Java, preparando-o para desafios mais complexos e seu objetivo de carreira.', ''),
(17, 1, 'Python', 'Esta trilha de estudos foi cuidadosamente elaborada para um aluno autodidata, focado em tecnologia e com o objetivo de carreira em Java. Começando do zero, ela aborda os conceitos fundamentais da programação, a linguagem Java e os pilares da Programação Orientada a Objetos (POO), com foco em conteúdos práticos, gratuitos e de rápida aplicação, usando vídeos e artigos.', ''),
(18, 1, 'Python 3', 'Esta trilha de estudos é cuidadosamente elaborada para o aluno iniciante e autodidata que busca dar seus primeiros passos no universo Java. O foco é proporcionar uma base sólida em lógica de programação e nos conceitos fundamentais de Java e Programação Orientada a Objetos (POO), culminando em um projeto prático para consolidar o aprendizado. Os conteúdos são apresentados através de vídeos e artigos gratuitos, priorizando a flexibilidade e a aplicação rápida do conhecimento, alinhado ao interesse em tecnologia e objetivo de carreira em Java.', ''),
(19, 1, 'Calculo 2', 'Esta trilha foi cuidadosamente elaborada para você, um autodidata iniciante, buscando construir uma base sólida em Java. Ela abrange desde a configuração do ambiente até os pilares da Programação Orientada a Objetos, com foco em conteúdos práticos, gratuitos e de rápida aplicação.', ''),
(20, 1, 'Engenharia de Software', 'Esta trilha de estudos foi cuidadosamente elaborada para o \'Aluno\', um autodidata iniciante em tecnologia com foco em Java. Priorizando conteúdos práticos, gratuitos e de rápida aplicação em formatos de vídeo e artigo, ela aborda desde a configuração do ambiente até os fundamentos da Programação Orientada a Objetos, culminando em um projeto prático para consolidar o aprendizado.', ''),
(21, 1, NULL, 'Esta trilha de estudos é projetada para um aluno autodidata e iniciante em tecnologia, com o objetivo de construir uma base sólida em Java. O foco é em conteúdo prático, gratuito e de rápida aplicação, utilizando vídeos e artigos para facilitar o aprendizado independente e flexível.', ''),
(22, 1, 'Python com PyCharm', 'Esta trilha foi cuidadosamente elaborada para o \'Aluno\', um autodidata com interesse em tecnologia e objetivo de carreira em Java. Aborda os fundamentos essenciais do Java e da lógica de programação de forma prática e progressiva, utilizando conteúdos gratuitos em vídeo e artigo, ideais para uma aprendizagem independente e flexível.', ''),
(23, 1, 'Python', 'Esta trilha foi cuidadosamente elaborada para você, autodidata em tecnologia, que busca iniciar sua jornada no desenvolvimento Java. O foco é em aprendizado prático e flexível, utilizando recursos gratuitos de alta qualidade. Percorreremos desde a lógica de programação fundamental até os conceitos essenciais da Programação Orientada a Objetos (POO) e estruturas de dados básicas, tudo com vídeos e artigos para otimizar seu ritmo de aprendizado.', ''),
(24, 16, 'Python', 'Esta trilha foi cuidadosamente elaborada para você, Aluno, um autodidata interessado em tecnologia e com foco em Java. Ela aborda os fundamentos da programação e da linguagem Java de forma progressiva, com conteúdos práticos e gratuitos, ideais para uma aprendizagem flexível e com rápida aplicação.', '');

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
  ADD PRIMARY KEY (`idAluno`);

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
  MODIFY `idAluno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `conteudo`
--
ALTER TABLE `conteudo`
  MODIFY `idConteudo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT de tabela `fonte`
--
ALTER TABLE `fonte`
  MODIFY `idFonte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

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
  MODIFY `idRelatorio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `sugestao_conteudo`
--
ALTER TABLE `sugestao_conteudo`
  MODIFY `idSugestao_conteudo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `trilha_conteudo`
--
ALTER TABLE `trilha_conteudo`
  MODIFY `idTrilha_conteudo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT de tabela `trilha_estudo`
--
ALTER TABLE `trilha_estudo`
  MODIFY `idTrilha_estudo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `conteudo`
--
ALTER TABLE `conteudo`
  ADD CONSTRAINT `fk_Fonte` FOREIGN KEY (`fonte_id`) REFERENCES `fonte` (`idFonte`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `historico_escolar`
--
ALTER TABLE `historico_escolar`
  ADD CONSTRAINT `fk_historico_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `preferencia_aluno`
--
ALTER TABLE `preferencia_aluno`
  ADD CONSTRAINT `fk_area_interesse` FOREIGN KEY (`areaInteresse_id`) REFERENCES `area_interesse` (`idArea_interesse`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pref_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `progresso_trilha`
--
ALTER TABLE `progresso_trilha`
  ADD CONSTRAINT `fk_progre_trilha_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_progre_trilha_tConteudo` FOREIGN KEY (`trilhaConteudo_id`) REFERENCES `trilha_conteudo` (`idTrilha_conteudo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `relatorio`
--
ALTER TABLE `relatorio`
  ADD CONSTRAINT `fk_relatorio_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_relatorio_trilha_estudo` FOREIGN KEY (`trilha_id`) REFERENCES `trilha_estudo` (`idTrilha_estudo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `sugestao_conteudo`
--
ALTER TABLE `sugestao_conteudo`
  ADD CONSTRAINT `fk_suge_conteudo_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_suge_conteudo_tConteudo` FOREIGN KEY (`trilhaConteudo_id`) REFERENCES `trilha_conteudo` (`idTrilha_conteudo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `trilha_conteudo`
--
ALTER TABLE `trilha_conteudo`
  ADD CONSTRAINT `fk_conteudo` FOREIGN KEY (`conteudo_id`) REFERENCES `conteudo` (`idConteudo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_trilha_conteudo_tEstudo` FOREIGN KEY (`trilha_id`) REFERENCES `trilha_estudo` (`idTrilha_estudo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `trilha_estudo`
--
ALTER TABLE `trilha_estudo`
  ADD CONSTRAINT `fk_trilha_estudo_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `aluno` (`idAluno`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
