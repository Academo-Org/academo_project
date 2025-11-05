-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/11/2025 às 13:30
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
-- Banco de dados: `academo_database`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `assignments`
--

CREATE TABLE `assignments` (
  `id_assignments` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `assignments`
--

INSERT INTO `assignments` (`id_assignments`, `class_id`, `title`, `description`, `due_date`) VALUES
(1, 1, 'Trabalho 1 - Algoritmo de Ordenação', 'Implementar um algoritmo de Bubble Sort em Python.', '2025-09-30 23:59:59'),
(2, 2, 'Estudo de Caso - Requisitos', 'Analisar o estudo de caso da empresa X e levantar 10 requisitos funcionais.', '2025-10-15 23:59:59'),
(3, 6, 'Entrega do Sprint 2', 'AAA', '2025-10-31 12:15:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `attendance`
--

CREATE TABLE `attendance` (
  `id_attendance` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `status` enum('presente','ausente','atrasado','justificado') NOT NULL DEFAULT 'presente',
  `period_number` int(11) NOT NULL COMMENT 'O número da aula no dia (1, 2, 3...)',
  `recorded_by` int(11) DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `attendance`
--

INSERT INTO `attendance` (`id_attendance`, `lesson_id`, `student_id`, `status`, `period_number`, `recorded_by`, `recorded_at`) VALUES
(1, 1, 4, 'presente', 0, 2, '2025-10-07 19:53:01'),
(2, 1, 5, 'atrasado', 0, 2, '2025-10-07 19:53:01'),
(3, 2, 4, 'presente', 0, 2, '2025-10-07 19:53:01'),
(4, 3, 4, 'justificado', 0, 3, '2025-10-07 19:53:01'),
(5, 3, 6, 'ausente', 0, 3, '2025-10-07 19:53:01'),
(6, 6, 7, 'presente', 1, 13, '2025-10-07 20:51:24'),
(7, 6, 7, 'presente', 2, 13, '2025-10-07 20:51:24'),
(8, 6, 7, 'presente', 3, 13, '2025-10-07 20:51:24'),
(9, 6, 7, 'presente', 4, 13, '2025-10-07 20:51:24'),
(10, 7, 12, 'presente', 1, 13, '2025-10-07 21:25:17'),
(11, 7, 12, 'presente', 2, 13, '2025-10-07 21:25:17'),
(12, 7, 12, 'presente', 3, 13, '2025-10-07 21:25:17'),
(13, 7, 12, 'presente', 4, 13, '2025-10-07 21:25:17'),
(14, 7, 4, 'presente', 1, 13, '2025-10-07 21:25:17'),
(15, 7, 4, 'presente', 2, 13, '2025-10-07 21:25:17'),
(16, 7, 4, 'presente', 3, 13, '2025-10-07 21:25:17'),
(17, 7, 4, 'presente', 4, 13, '2025-10-07 21:25:17'),
(18, 7, 7, 'presente', 1, 13, '2025-10-07 21:25:17'),
(19, 7, 7, 'presente', 2, 13, '2025-10-07 21:25:17'),
(20, 7, 7, 'presente', 3, 13, '2025-10-07 21:25:17'),
(21, 7, 7, 'presente', 4, 13, '2025-10-07 21:25:17'),
(22, 7, 6, 'presente', 1, 13, '2025-10-07 21:25:17'),
(23, 7, 6, 'presente', 2, 13, '2025-10-07 21:25:17'),
(24, 7, 6, 'presente', 3, 13, '2025-10-07 21:25:17'),
(25, 7, 6, 'presente', 4, 13, '2025-10-07 21:25:17'),
(26, 7, 8, 'presente', 1, 13, '2025-10-07 21:25:17'),
(27, 7, 8, 'presente', 2, 13, '2025-10-07 21:25:17'),
(28, 7, 8, 'presente', 3, 13, '2025-10-07 21:25:17'),
(29, 7, 8, 'presente', 4, 13, '2025-10-07 21:25:17'),
(30, 7, 5, 'presente', 1, 13, '2025-10-07 21:25:17'),
(31, 7, 5, 'presente', 2, 13, '2025-10-07 21:25:17'),
(32, 7, 5, 'presente', 3, 13, '2025-10-07 21:25:17'),
(33, 7, 5, 'presente', 4, 13, '2025-10-07 21:25:17'),
(34, 8, 15, 'presente', 1, 13, '2025-10-29 14:09:32'),
(35, 8, 15, 'presente', 2, 13, '2025-10-29 14:09:32'),
(36, 8, 15, 'presente', 3, 13, '2025-10-29 14:09:32'),
(37, 8, 15, 'presente', 4, 13, '2025-10-29 14:09:32'),
(38, 8, 15, 'presente', 5, 13, '2025-10-29 14:09:32'),
(39, 8, 15, 'presente', 6, 13, '2025-10-29 14:09:32'),
(40, 8, 4, 'presente', 1, 13, '2025-10-29 14:09:32'),
(41, 8, 4, 'presente', 2, 13, '2025-10-29 14:09:32'),
(42, 8, 4, 'presente', 3, 13, '2025-10-29 14:09:32'),
(43, 8, 4, 'presente', 4, 13, '2025-10-29 14:09:32'),
(44, 8, 4, 'presente', 5, 13, '2025-10-29 14:09:32'),
(45, 8, 4, 'presente', 6, 13, '2025-10-29 14:09:32'),
(46, 8, 7, 'presente', 1, 13, '2025-10-29 14:09:32'),
(47, 8, 7, 'presente', 2, 13, '2025-10-29 14:09:32'),
(48, 8, 7, 'presente', 3, 13, '2025-10-29 14:09:32'),
(49, 8, 7, 'presente', 4, 13, '2025-10-29 14:09:32'),
(50, 8, 7, 'presente', 5, 13, '2025-10-29 14:09:32'),
(51, 8, 7, 'presente', 6, 13, '2025-10-29 14:09:32'),
(52, 8, 6, 'presente', 1, 13, '2025-10-29 14:09:32'),
(53, 8, 6, 'presente', 2, 13, '2025-10-29 14:09:32'),
(54, 8, 6, 'presente', 3, 13, '2025-10-29 14:09:32'),
(55, 8, 6, 'presente', 4, 13, '2025-10-29 14:09:32'),
(56, 8, 6, 'presente', 5, 13, '2025-10-29 14:09:32'),
(57, 8, 6, 'presente', 6, 13, '2025-10-29 14:09:32');

-- --------------------------------------------------------

--
-- Estrutura para tabela `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id_audit_logs` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `row_id` int(11) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `audit_logs`
--

INSERT INTO `audit_logs` (`id_audit_logs`, `user_id`, `action`, `table_name`, `row_id`, `old_value`, `new_value`, `created_at`) VALUES
(1, 1, 'LOGIN_SUCCESS', 'users', 1, NULL, 'User logged in successfully', '2025-10-07 18:50:11'),
(2, 2, 'UPDATE', 'grades', 1, '8.5', '9.5', '2025-10-07 18:50:11');

-- --------------------------------------------------------

--
-- Estrutura para tabela `classes`
--

CREATE TABLE `classes` (
  `id_classes` int(11) NOT NULL,
  `program_id` int(11) DEFAULT NULL,
  `discipline_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `professor_id` int(11) DEFAULT NULL,
  `section_code` varchar(10) DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 60,
  `periods_per_session` int(11) NOT NULL DEFAULT 2 COMMENT 'Número de aulas por encontro',
  `status` enum('aberto','cheio','cancelado') NOT NULL DEFAULT 'aberto',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `classes`
--

INSERT INTO `classes` (`id_classes`, `program_id`, `discipline_id`, `semester_id`, `professor_id`, `section_code`, `capacity`, `periods_per_session`, `status`, `created_at`) VALUES
(1, 1, 1, 2, 2, 'A', 60, 4, 'aberto', '2025-10-07 18:50:11'),
(2, 2, 2, 2, 3, 'B', 50, 4, 'aberto', '2025-10-07 18:50:11'),
(3, 3, 3, 2, 13, 'U', 70, 4, 'aberto', '2025-10-07 18:50:11'),
(4, NULL, 4, 2, 14, 'C', 60, 2, 'aberto', '2025-10-07 21:26:27'),
(5, NULL, 5, 1, 13, NULL, 60, 6, 'aberto', '2025-10-08 14:13:42'),
(6, NULL, 6, 2, 13, NULL, 60, 6, 'aberto', '2025-10-29 14:03:39');

-- --------------------------------------------------------

--
-- Estrutura para tabela `classrooms`
--

CREATE TABLE `classrooms` (
  `id_classrooms` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `classrooms`
--

INSERT INTO `classrooms` (`id_classrooms`, `name`, `location`, `capacity`) VALUES
(1, 'Sala 101', 'Bloco A, 1º Andar', 60),
(2, 'Laboratório de Redes', 'Bloco B, Térreo', 40),
(3, 'Auditório Central', 'Prédio Principal', 150);

-- --------------------------------------------------------

--
-- Estrutura para tabela `disciplines`
--

CREATE TABLE `disciplines` (
  `id_disciplines` int(11) NOT NULL,
  `code` varchar(30) NOT NULL,
  `title` varchar(200) NOT NULL,
  `program_id` int(11) DEFAULT NULL,
  `credits` decimal(4,2) NOT NULL DEFAULT 3.00,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `disciplines`
--

INSERT INTO `disciplines` (`id_disciplines`, `code`, `title`, `program_id`, `credits`, `description`) VALUES
(1, 'CC101', 'Algoritmos e Estruturas de Dados', 1, 4.00, 'Fundamentos de algoritmos e estruturas de dados.'),
(2, 'ES201', 'Engenharia de Requisitos', 2, 3.00, 'Técnicas de levantamento e gerenciamento de requisitos de software.'),
(3, 'ADS301', 'Banco de Dados', 3, 4.00, 'Modelagem, projeto e implementação de bancos de dados relacionais.'),
(4, 'PSI101', 'Psicologia Cognitiva', 1, 3.00, NULL),
(5, 'IA', 'Inteligência Artificial', NULL, 3.00, NULL),
(6, 'EP01', 'Execução de Projetos', NULL, 3.00, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `enrollments`
--

CREATE TABLE `enrollments` (
  `id_enrollments` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('matriculado','cancelado','concluído') NOT NULL DEFAULT 'matriculado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `enrollments`
--

INSERT INTO `enrollments` (`id_enrollments`, `student_id`, `class_id`, `enrolled_at`, `status`) VALUES
(1, 4, 1, '2025-10-07 19:53:01', 'matriculado'),
(2, 4, 2, '2025-10-07 19:53:01', 'matriculado'),
(3, 5, 1, '2025-10-07 19:53:01', 'matriculado'),
(4, 6, 2, '2025-10-07 19:53:01', 'matriculado'),
(5, 7, 3, '2025-10-07 19:53:01', 'matriculado'),
(6, 4, 3, '2025-10-07 21:25:03', 'matriculado'),
(7, 5, 3, '2025-10-07 21:25:03', 'matriculado'),
(8, 6, 3, '2025-10-07 21:25:03', 'matriculado'),
(9, 8, 3, '2025-10-07 21:25:03', 'matriculado'),
(10, 12, 3, '2025-10-07 21:25:03', 'matriculado'),
(11, 4, 4, '2025-10-07 21:26:27', 'matriculado'),
(12, 12, 5, '2025-10-08 14:14:07', 'matriculado'),
(13, 7, 5, '2025-10-08 14:14:08', 'matriculado'),
(14, 4, 5, '2025-10-08 14:14:09', 'matriculado'),
(15, 6, 5, '2025-10-08 14:14:09', 'matriculado'),
(16, 8, 5, '2025-10-08 14:14:09', 'matriculado'),
(17, 5, 5, '2025-10-08 14:14:10', 'matriculado'),
(19, 4, 6, '2025-10-29 14:04:29', 'matriculado'),
(20, 15, 6, '2025-10-29 14:04:32', 'matriculado'),
(21, 7, 6, '2025-10-29 14:04:36', 'matriculado'),
(22, 6, 6, '2025-10-29 14:04:37', 'matriculado');

-- --------------------------------------------------------

--
-- Estrutura para tabela `grades`
--

CREATE TABLE `grades` (
  `id_grades` int(11) NOT NULL,
  `grade_item_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `score` decimal(6,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL COMMENT 'Observações do professor sobre a nota',
  `graded_at` timestamp NULL DEFAULT NULL,
  `graded_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `grades`
--

INSERT INTO `grades` (`id_grades`, `grade_item_id`, `student_id`, `score`, `feedback`, `graded_at`, `graded_by`) VALUES
(1, 1, 4, 8.50, NULL, NULL, 2),
(2, 2, 4, 9.50, NULL, NULL, 2),
(3, 1, 5, 7.00, NULL, NULL, 2),
(4, 4, 7, 10.00, NULL, NULL, 13),
(5, 6, 7, 1.00, 'muito buxa', NULL, 13),
(6, 8, 15, 7.60, 'Bom', NULL, 13),
(7, 8, 4, 8.20, '', NULL, 13),
(8, 8, 7, 4.30, '', NULL, 13),
(9, 8, 6, 10.00, '', NULL, 13);

-- --------------------------------------------------------

--
-- Estrutura para tabela `grade_items`
--

CREATE TABLE `grade_items` (
  `id_grade_items` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `due_date` datetime DEFAULT NULL,
  `max_score` decimal(6,2) NOT NULL DEFAULT 100.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `grade_items`
--

INSERT INTO `grade_items` (`id_grade_items`, `class_id`, `title`, `weight`, `due_date`, `max_score`) VALUES
(1, 1, 'Prova 1', 0.40, NULL, 10.00),
(2, 1, 'Trabalho 1', 0.30, NULL, 10.00),
(3, 1, 'Prova 2', 0.30, NULL, 10.00),
(4, 3, 'Prova Final - Banco de Dados', 0.60, NULL, 10.00),
(5, 3, 'Projeto Prático - Banco de Dados', 0.40, NULL, 10.00),
(6, 3, 'Prova 1', 0.40, NULL, 10.00),
(7, 3, 'Prova 2', 0.20, NULL, 10.00),
(8, 6, 'Prova 1', 0.20, NULL, 10.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `lessons`
--

CREATE TABLE `lessons` (
  `id_lessons` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `classroom_id` int(11) DEFAULT NULL,
  `professor_id` int(11) DEFAULT NULL,
  `lesson_date` datetime NOT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 60,
  `topic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `lessons`
--

INSERT INTO `lessons` (`id_lessons`, `class_id`, `classroom_id`, `professor_id`, `lesson_date`, `duration_minutes`, `topic`, `created_at`) VALUES
(1, 1, 1, 2, '2025-09-02 19:00:00', 90, 'Introdução a Algoritmos', '2025-10-07 18:50:11'),
(2, 1, 1, 2, '2025-09-09 19:00:00', 90, 'Estruturas de Repetição', '2025-10-07 18:50:11'),
(3, 2, 2, 3, '2025-09-03 21:00:00', 90, 'O que é um requisito?', '2025-10-07 18:50:11'),
(4, 3, 1, 2, '2025-09-04 19:00:00', 120, 'Modelagem Entidade-Relacionamento', '2025-10-07 18:50:11'),
(5, 3, 2, 13, '2025-10-08 19:00:00', 90, 'Consultas SQL Avançadas', '2025-10-07 20:45:30'),
(6, 3, NULL, 13, '2025-10-07 17:51:24', 60, 'consulta sql', '2025-10-07 20:51:24'),
(7, 3, NULL, 13, '2025-10-07 18:25:17', 60, 'revisão pra prova', '2025-10-07 21:25:17'),
(8, 6, NULL, 13, '2025-10-29 11:09:32', 60, 'Revisão da Prova', '2025-10-29 14:09:32');

-- --------------------------------------------------------

--
-- Estrutura para tabela `programs`
--

CREATE TABLE `programs` (
  `id_programs` int(11) NOT NULL,
  `code` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `programs`
--

INSERT INTO `programs` (`id_programs`, `code`, `name`, `description`) VALUES
(1, 'CC', 'Ciência da Computação', 'Curso focado em fundamentos da computação, algoritmos e desenvolvimento de software.'),
(2, 'ES', 'Engenharia de Software', 'Curso com foco em processos de desenvolvimento, qualidade e gerenciamento de projetos de software.'),
(3, 'ADS', 'Análise e Desenvolvimento de Sistemas', 'Curso tecnológico voltado para a criação de sistemas para o mercado.');

-- --------------------------------------------------------

--
-- Estrutura para tabela `roles`
--

CREATE TABLE `roles` (
  `id_roles` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `roles`
--

INSERT INTO `roles` (`id_roles`, `name`, `description`) VALUES
(1, 'Administrador', 'Acesso total ao sistema'),
(2, 'Professor', 'Gerencia turmas, notas e presenças'),
(3, 'Aluno', 'Acessa materiais, envia trabalhos e vê notas'),
(4, 'Coordenacao', 'Gerencia o cadastro de professores e alunos');

-- --------------------------------------------------------

--
-- Estrutura para tabela `semesters`
--

CREATE TABLE `semesters` (
  `id_semesters` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `semesters`
--

INSERT INTO `semesters` (`id_semesters`, `name`, `start_date`, `end_date`) VALUES
(1, '2025/1', '2025-02-03', '2025-06-27'),
(2, '2025/2', '2025-08-04', '2025-12-19');

-- --------------------------------------------------------

--
-- Estrutura para tabela `submissions`
--

CREATE TABLE `submissions` (
  `idsubmissions` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `grade` decimal(6,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `submissions`
--

INSERT INTO `submissions` (`idsubmissions`, `assignment_id`, `student_id`, `submitted_at`, `file_path`, `file_name`, `grade`, `feedback`) VALUES
(1, 2, 4, '2025-10-08 14:41:41', 'uploads/academo_database_model_cropped.pdf', 'academo_database_model_cropped.pdf', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id_users` int(11) NOT NULL,
  `login` varchar(80) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id_users`, `login`, `password_hash`, `name`, `email`, `role_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$d/2zTS.AMR15wfR5oTLb5ObYb/5n02xFShS5Pn6R.cdVNejo5wiRW', 'Admin do Sistema', 'admin@academo.com', 1, 'ativo', '2025-10-07 18:50:11', '2025-10-07 19:46:32'),
(2, 'prof.xavier', '$2y$10$d/2zTS.AMR15wfR5oTLb5ObYb/5n02xFShS5Pn6R.cdVNejo5wiRW', 'Charles Xavier', 'charles.x@academo.com', 2, 'ativo', '2025-10-07 18:50:11', '2025-10-07 19:46:32'),
(3, 'prof.logan', '$2y$10$d/2zTS.AMR15wfR5oTLb5ObYb/5n02xFShS5Pn6R.cdVNejo5wiRW', 'James \"Logan\" Howlett', 'logan.h@academo.com', 2, 'ativo', '2025-10-07 18:50:11', '2025-10-07 19:46:32'),
(4, 'jean.grey', '$2y$10$d/2zTS.AMR15wfR5oTLb5ObYb/5n02xFShS5Pn6R.cdVNejo5wiRW', 'Jean Grey', 'jean.g@academo.com', 3, 'ativo', '2025-10-07 18:50:11', '2025-10-07 19:46:32'),
(5, 'scott.summers', '$2y$10$d/2zTS.AMR15wfR5oTLb5ObYb/5n02xFShS5Pn6R.cdVNejo5wiRW', 'Scott Summers', 'scott.s@academo.com', 3, 'ativo', '2025-10-07 18:50:11', '2025-10-07 19:46:32'),
(6, 'ororo.munroe', '$2y$10$d/2zTS.AMR15wfR5oTLb5ObYb/5n02xFShS5Pn6R.cdVNejo5wiRW', 'Ororo Munroe', 'ororo.m@academo.com', 3, 'ativo', '2025-10-07 18:50:11', '2025-10-07 19:46:32'),
(7, 'kurt.wagner', '$2y$10$d/2zTS.AMR15wfR5oTLb5ObYb/5n02xFShS5Pn6R.cdVNejo5wiRW', 'Kurt Wagner', 'kurt.w@academo.com', 3, 'ativo', '2025-10-07 18:50:11', '2025-10-07 19:46:32'),
(8, 'gregory.schafer', '$2y$10$y1HW6KwbOG5fKpUiZsGnyOACGM/FP/ySxYDKXkLFNCQH6yMELJ/i6', 'Oscar Schafer', 'gregory.ishigami@gmail.com', 3, 'ativo', '2025-10-07 20:02:27', '2025-10-07 20:02:27'),
(11, 'gabriel.hausmann', '$2y$10$bo600AvguWZXNHcWqnh6b.I6X8i9eattE56pmcdxssbVKMEKTEcKW', 'Gabriel Hausmann', 'gabriel.hausmann@gmail.com', 4, 'ativo', '2025-10-07 20:24:09', '2025-10-07 20:24:09'),
(12, 'bernardo.21', '$2y$10$lL.TYgS7w/a.Vs3h17r2auvMcxiqHDBiQX1XgdF9VezzmLS3lYFJK', 'Bernardo Amaral', 'bernardo@gmail.com', 3, 'ativo', '2025-10-07 20:24:46', '2025-10-07 20:24:46'),
(13, 'pedro.21', '$2y$10$gMKXsnuepySSUXJJexUSVOySxs3abpEjRh.ayrat5Se/Y39G6zkBy', 'Pedro', 'pedro@gmail.com', 2, 'ativo', '2025-10-07 20:26:08', '2025-10-07 20:26:08'),
(14, 'prof.frost', '$2y$10$d/2zTS.AMR15wfR5oTLb5ObYb/5n02xFShS5Pn6R.cdVNejo5wiRW', 'Emma Frost', 'emma.f@academo.com', 2, 'ativo', '2025-10-07 21:26:27', '2025-10-07 21:26:27'),
(15, 'felipe.2', '$2y$10$Hbt4oHpsSdFskgcjn9K0WOiEeyRK7qXKelQDZ6.7snCFWQpkbsaHq', 'Felipe', 'felipe.2@gmail.com', 3, 'ativo', '2025-10-29 14:01:44', '2025-10-29 14:02:13');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id_assignments`),
  ADD KEY `id_classes10_idx` (`class_id`);

--
-- Índices de tabela `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id_attendance`),
  ADD UNIQUE KEY `unique_attendance` (`lesson_id`,`student_id`,`period_number`),
  ADD KEY `id_lessons_idx` (`lesson_id`),
  ADD KEY `id_users_idx` (`student_id`),
  ADD KEY `id_users2_idx` (`recorded_by`);

--
-- Índices de tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id_audit_logs`),
  ADD KEY `id_users7_idx` (`user_id`);

--
-- Índices de tabela `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id_classes`),
  ADD KEY `programs_id_idx` (`program_id`),
  ADD KEY `id_disciplines_idx` (`discipline_id`),
  ADD KEY `id_semesters_idx` (`semester_id`),
  ADD KEY `id_users_idx` (`professor_id`);

--
-- Índices de tabela `classrooms`
--
ALTER TABLE `classrooms`
  ADD PRIMARY KEY (`id_classrooms`);

--
-- Índices de tabela `disciplines`
--
ALTER TABLE `disciplines`
  ADD PRIMARY KEY (`id_disciplines`),
  ADD UNIQUE KEY `code_UNIQUE` (`code`),
  ADD KEY `id_programs_idx` (`program_id`);

--
-- Índices de tabela `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id_enrollments`),
  ADD KEY `id_users3_idx` (`student_id`),
  ADD KEY `id_classes8_idx` (`class_id`);

--
-- Índices de tabela `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id_grades`),
  ADD UNIQUE KEY `unique_grade` (`grade_item_id`,`student_id`),
  ADD KEY `id_grade_items_idx` (`grade_item_id`),
  ADD KEY `id_users4_idx` (`student_id`),
  ADD KEY `id_users5_idx` (`graded_by`);

--
-- Índices de tabela `grade_items`
--
ALTER TABLE `grade_items`
  ADD PRIMARY KEY (`id_grade_items`),
  ADD KEY `id_classes9_idx` (`class_id`);

--
-- Índices de tabela `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id_lessons`),
  ADD KEY `id_classrooms_idx` (`classroom_id`),
  ADD KEY `id_classes1_idx` (`class_id`),
  ADD KEY `id_users9_idx` (`professor_id`);

--
-- Índices de tabela `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id_programs`),
  ADD UNIQUE KEY `code_UNIQUE` (`code`);

--
-- Índices de tabela `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_roles`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`);

--
-- Índices de tabela `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`id_semesters`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`);

--
-- Índices de tabela `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`idsubmissions`),
  ADD KEY `id_assignments_idx` (`assignment_id`),
  ADD KEY `id_users6_idx` (`student_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_users`),
  ADD UNIQUE KEY `login_UNIQUE` (`login`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`),
  ADD KEY `id_roles_idx` (`role_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id_assignments` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id_attendance` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id_audit_logs` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `classes`
--
ALTER TABLE `classes`
  MODIFY `id_classes` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `classrooms`
--
ALTER TABLE `classrooms`
  MODIFY `id_classrooms` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `disciplines`
--
ALTER TABLE `disciplines`
  MODIFY `id_disciplines` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id_enrollments` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `grades`
--
ALTER TABLE `grades`
  MODIFY `id_grades` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `grade_items`
--
ALTER TABLE `grade_items`
  MODIFY `id_grade_items` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id_lessons` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `programs`
--
ALTER TABLE `programs`
  MODIFY `id_programs` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `roles`
--
ALTER TABLE `roles`
  MODIFY `id_roles` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `semesters`
--
ALTER TABLE `semesters`
  MODIFY `id_semesters` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `submissions`
--
ALTER TABLE `submissions`
  MODIFY `idsubmissions` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id_users` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `id_classes10` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id_classes`) ON DELETE CASCADE;

--
-- Restrições para tabelas `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `id_lessons` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id_lessons`) ON DELETE CASCADE,
  ADD CONSTRAINT `id_users1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id_users`),
  ADD CONSTRAINT `id_users2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id_users`) ON DELETE SET NULL;

--
-- Restrições para tabelas `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `id_users7` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_users`) ON DELETE SET NULL;

--
-- Restrições para tabelas `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `id_disciplines1` FOREIGN KEY (`discipline_id`) REFERENCES `disciplines` (`id_disciplines`),
  ADD CONSTRAINT `id_programs1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id_programs`) ON DELETE SET NULL,
  ADD CONSTRAINT `id_semesters1` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id_semesters`),
  ADD CONSTRAINT `id_users8` FOREIGN KEY (`professor_id`) REFERENCES `users` (`id_users`) ON DELETE SET NULL;

--
-- Restrições para tabelas `disciplines`
--
ALTER TABLE `disciplines`
  ADD CONSTRAINT `id_programs` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id_programs`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `id_classes8` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id_classes`),
  ADD CONSTRAINT `id_users3` FOREIGN KEY (`student_id`) REFERENCES `users` (`id_users`) ON DELETE CASCADE;

--
-- Restrições para tabelas `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `id_grade_items` FOREIGN KEY (`grade_item_id`) REFERENCES `grade_items` (`id_grade_items`) ON DELETE CASCADE,
  ADD CONSTRAINT `id_users4` FOREIGN KEY (`student_id`) REFERENCES `users` (`id_users`) ON DELETE CASCADE,
  ADD CONSTRAINT `id_users5` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id_users`) ON DELETE SET NULL;

--
-- Restrições para tabelas `grade_items`
--
ALTER TABLE `grade_items`
  ADD CONSTRAINT `id_classes9` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id_classes`) ON DELETE CASCADE;

--
-- Restrições para tabelas `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `id_classes1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id_classes`),
  ADD CONSTRAINT `id_classrooms` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id_classrooms`) ON DELETE SET NULL,
  ADD CONSTRAINT `id_users9` FOREIGN KEY (`professor_id`) REFERENCES `users` (`id_users`) ON DELETE SET NULL;

--
-- Restrições para tabelas `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `id_assignments` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id_assignments`) ON DELETE CASCADE,
  ADD CONSTRAINT `id_users6` FOREIGN KEY (`student_id`) REFERENCES `users` (`id_users`) ON DELETE CASCADE;

--
-- Restrições para tabelas `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `id_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id_roles`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
