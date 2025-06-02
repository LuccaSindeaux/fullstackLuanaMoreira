--
-- Versão do script unificada para o projeto Luana Moreira Fisioterapia
--

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "-03:00";

--
-- Banco de dados: `luana_moreira_fisioterapia`
--
DROP DATABASE IF EXISTS `luana_moreira_fisioterapia`;
CREATE DATABASE IF NOT EXISTS `luana_moreira_fisioterapia` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `luana_moreira_fisioterapia`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pacientes` (anteriormente `usuarios`)
--
CREATE TABLE `pacientes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  `telefone` VARCHAR(20) NULL,
  `admin` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Inserindo dados de exemplo em `pacientes` com senhas seguras
--
INSERT INTO `pacientes` (`id`, `nome`, `email`, `senha`, `telefone`, `admin`) VALUES
(1, 'Luana Moreira', 'luana@fisioweb.com', '$2y$10$yI.i9.UqX.qV9O1aVd9Vp.8X4B.uC2s4.lT.lI.i9.UqX.qV9O1aV', '51999998888', 1), -- Senha: admin123
(2, 'João da Silva', 'joao@email.com', '$2y$10$sP.o3.nL.eR.t5s6u7v8w9xY.z1a2b3c4d5e6f7g8h9i', '51988887777', 0);     -- Senha: senha123

-- --------------------------------------------------------

--
-- Estrutura da tabela `disponibilidade` (anteriormente `disponibilidades`)
-- Com as colunas `data_hora` e `status` corretas
--
CREATE TABLE `disponibilidade` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `data_hora` DATETIME NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'disponivel', -- Ex: 'disponivel', 'indisponivel'
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_hora_unica` (`data_hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Inserindo dados de exemplo em `disponibilidade`
--
INSERT INTO `disponibilidade` (`data_hora`, `status`) VALUES
('2025-06-09 09:00:00', 'disponivel'),
('2025-06-09 10:00:00', 'disponivel'),
('2025-06-10 14:00:00', 'disponivel'),
('2025-06-10 15:00:00', 'disponivel');

-- --------------------------------------------------------

--
-- Estrutura da tabela `fichas`
-- Com a coluna `id_paciente` para relacionar com o paciente
--
CREATE TABLE `fichas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` INT(11) NOT NULL,
  `nome` VARCHAR(255) NULL,
  `idade` INT(11) NULL,
  `estado_civil` VARCHAR(50) NULL,
  `email` VARCHAR(255) NULL,
  `nascimento` DATE NULL,
  `telefone` VARCHAR(20) NULL,
  `praticou_yoga` VARCHAR(10) NULL,
  `coluna` TEXT NULL,
  `cirurgias` TEXT NULL,
  `atividade_fisica` VARCHAR(10) NULL,
  `qual_atividade` TEXT NULL,
  `plano` VARCHAR(100) NULL,
  `data_preenchimento` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_paciente` (`id_paciente`),
  CONSTRAINT `fichas_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `agendamentos`
-- Com as colunas `id_paciente`, `id_disponibilidade`, `data_agendamento` e `plano` corretas
--
CREATE TABLE `agendamentos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` INT(11) NOT NULL,
  `id_disponibilidade` INT(11) NOT NULL,
  `data_agendamento` DATETIME NOT NULL,
  `plano` VARCHAR(100) NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'Confirmado',
  `pago` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_paciente` (`id_paciente`),
  KEY `id_disponibilidade` (`id_disponibilidade`),
  CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agendamentos_ibfk_2` FOREIGN KEY (`id_disponibilidade`) REFERENCES `disponibilidade` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Tabela para guaradr tokens de recriar senhas
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    KEY email_idx (email),
    KEY token_idx (token)
);


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;