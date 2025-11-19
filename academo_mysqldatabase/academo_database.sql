-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19/11/2025 às 13:10
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

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
(58, 9, 24, 'presente', 1, 23, '2025-11-19 12:05:22'),
(59, 9, 24, 'presente', 2, 23, '2025-11-19 12:05:22'),
(60, 9, 22, 'presente', 1, 23, '2025-11-19 12:05:22'),
(61, 9, 22, 'presente', 2, 23, '2025-11-19 12:05:22'),
(62, 10, 24, 'presente', 1, 23, '2025-11-19 12:05:24'),
(63, 10, 24, 'presente', 2, 23, '2025-11-19 12:05:24'),
(64, 10, 24, 'presente', 3, 23, '2025-11-19 12:05:24'),
(65, 10, 24, 'presente', 4, 23, '2025-11-19 12:05:24'),
(66, 10, 22, 'presente', 1, 23, '2025-11-19 12:05:24'),
(67, 10, 22, 'presente', 2, 23, '2025-11-19 12:05:24'),
(68, 10, 22, 'presente', 3, 23, '2025-11-19 12:05:24'),
(69, 10, 22, 'presente', 4, 23, '2025-11-19 12:05:24');

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

-- --------------------------------------------------------

--
-- Estrutura para tabela `classes`
--

CREATE TABLE `classes` (
  `id_classes` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
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

INSERT INTO `classes` (`id_classes`, `owner_id`, `program_id`, `discipline_id`, `semester_id`, `professor_id`, `section_code`, `capacity`, `periods_per_session`, `status`, `created_at`) VALUES
(8, 21, NULL, 8, 2, 23, NULL, 60, 2, 'aberto', '2025-11-19 12:03:59'),
(9, 21, NULL, 9, 2, 23, NULL, 60, 4, 'aberto', '2025-11-19 12:04:37');

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
(8, 'LPII', 'Linguagens de Programacao II', NULL, 3.00, NULL),
(9, 'ED', 'Estrutura de Dados', NULL, 3.00, NULL);

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
(24, 24, 9, '2025-11-19 12:04:49', 'matriculado'),
(25, 22, 9, '2025-11-19 12:04:50', 'matriculado'),
(26, 24, 8, '2025-11-19 12:04:53', 'matriculado'),
(27, 22, 8, '2025-11-19 12:04:54', 'matriculado');

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
(10, 9, 24, 10.00, 'Muito Bom', NULL, 23),
(11, 9, 22, 10.00, 'Muito Bom', NULL, 23),
(12, 10, 24, 10.00, 'Muito Bom', NULL, 23),
(13, 10, 22, 10.00, 'Muito Bom', NULL, 23);

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
(9, 8, 'Prova 1', 0.50, NULL, 10.00),
(10, 9, 'Prova 1', 0.50, NULL, 10.00);

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
(9, 8, NULL, 23, '2025-11-19 09:05:22', 60, 'Aula 1', '2025-11-19 12:05:22'),
(10, 9, NULL, 23, '2025-11-19 09:05:24', 60, 'Aula 1', '2025-11-19 12:05:24');

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
  `owner_id` int(11) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `status` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id_users`, `login`, `password_hash`, `name`, `email`, `role_id`, `owner_id`, `program_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$d/2zTS.AMR15wfR5oTLb5ObYb/5n02xFShS5Pn6R.cdVNejo5wiRW', 'Admin do Sistema', 'admin@academo.com', 1, NULL, NULL, 'ativo', '2025-10-07 21:50:11', '2025-10-07 22:46:32'),
(21, 'fatec.rc', '$2y$10$wjY6YEwgrkFhF7hvhF2SButYpg68BHLV3c5U06a7dtdEW90d8hvTG', 'Fatec Prof. Álvares Gracioli', 'fatec.rc@gmail.com', 4, NULL, NULL, 'ativo', '2025-11-19 11:59:43', '2025-11-19 11:59:43'),
(22, 'nathan.scremin', '$2y$10$PoO5KKZcUWKQavLJGyvbsOxP6EoTKl8IdE1v/jmQr3V4Zo5l1WekS', 'Nathan Scremin', 'nathan.scremin175@gmail.com', 3, 21, NULL, 'ativo', '2025-11-19 12:00:22', '2025-11-19 12:01:43'),
(23, 'orlando.saraiva', '$2y$10$1In01uNy2YCDmdfnR18FS.eFhZgKHtjZ.T55EpeRO.qkbaX67pbpi', 'Orlando Saraiva Jr', 'orlando.saraiva@gmail.com', 2, 21, NULL, 'ativo', '2025-11-19 12:01:04', '2025-11-19 12:01:04'),
(24, 'gabriel.nanetti', '$2y$10$YPSXMkI4vrKhELorjU0vFeuzGvt6f5CLqcYY5ig9gxRhgayaxugMa', 'Gabriel Vinicios Nanetti', 'gabriel.nanetti@gmail.com', 3, 21, NULL, 'ativo', '2025-11-19 12:01:37', '2025-11-19 12:01:37'),
(25, 'fatec.tt', '$2y$10$Nu4gwRg/B9FKMJLpPndW5eKqtuxQSyBeI1vO4zjfZ//7nj7GLEEEq', 'Fatec Tatuapé \"Victor Civita\"', 'fatec.tt@gmail.com', 4, NULL, NULL, 'ativo', '2025-11-19 12:08:34', '2025-11-19 12:08:34');

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
  ADD KEY `id_roles_idx` (`role_id`),
  ADD KEY `fk_users_program` (`program_id`);

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
  MODIFY `id_attendance` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT de tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id_audit_logs` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `classes`
--
ALTER TABLE `classes`
  MODIFY `id_classes` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `classrooms`
--
ALTER TABLE `classrooms`
  MODIFY `id_classrooms` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `disciplines`
--
ALTER TABLE `disciplines`
  MODIFY `id_disciplines` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id_enrollments` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `grades`
--
ALTER TABLE `grades`
  MODIFY `id_grades` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `grade_items`
--
ALTER TABLE `grade_items`
  MODIFY `id_grade_items` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id_lessons` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `id_users` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
  ADD CONSTRAINT `fk_users_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id_programs`) ON DELETE SET NULL,
  ADD CONSTRAINT `id_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id_roles`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
