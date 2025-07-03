-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Feb 23, 2025 alle 19:59
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `progetto_db`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `libro`
--

CREATE TABLE `libro` (
  `id` int(11) NOT NULL,
  `titolo` varchar(256) NOT NULL,
  `editore` varchar(256) NOT NULL,
  `autore` varchar(256) NOT NULL,
  `genere` varchar(100) NOT NULL,
  `dataLettura` date NOT NULL,
  `numeroPagine` int(11) NOT NULL,
  `dataPubblicazione` date DEFAULT NULL,
  `idUtente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `obiettivi`
--

CREATE TABLE `obiettivi` (
  `id` int(11) NOT NULL,
  `pagineQuotidiane` int(11) DEFAULT NULL,
  `libriAnnuali` int(11) DEFAULT NULL,
  `pagineLibroCorrente` int(11) DEFAULT NULL,
  `idUtente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `obiettivi`
--

INSERT INTO `obiettivi` (`id`, `pagineQuotidiane`, `libriAnnuali`, `pagineLibroCorrente`, `idUtente`) VALUES
(9, 15, 3, 100, 12),
(10, 15, 9, 100, 13);

-- --------------------------------------------------------

--
-- Struttura della tabella `statistiche`
--

CREATE TABLE `statistiche` (
  `id` int(11) NOT NULL,
  `numeroLibri` int(11) DEFAULT NULL,
  `pagineLetteOggi` int(11) DEFAULT NULL,
  `libriLettiAnnualmente` int(11) DEFAULT NULL,
  `letturaLibroCorrente` int(11) DEFAULT NULL,
  `idUtente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `statistiche`
--

INSERT INTO `statistiche` (`id`, `numeroLibri`, `pagineLetteOggi`, `libriLettiAnnualmente`, `letturaLibroCorrente`, `idUtente`) VALUES
(14, NULL, NULL, NULL, NULL, 12),
(15, NULL, NULL, NULL, NULL, 13);

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE `utente` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`id`, `username`, `password`) VALUES
(12, 'Alberto', '$2y$10$Bwnqn3Iu8TpszHxSzLFEeunmKtVZcggEYpvgGDRSv3d6bhGd0LUde'),
(13, 'Carlo', '$2y$10$3FlNSmn9ggooS0vwGLUBAuDJ4iJOXt7SYMbq0DoCA5PT24fxUrLLS');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `libro`
--
ALTER TABLE `libro`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_libro_utente` (`idUtente`);

--
-- Indici per le tabelle `obiettivi`
--
ALTER TABLE `obiettivi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_obiettivi_utente` (`idUtente`);

--
-- Indici per le tabelle `statistiche`
--
ALTER TABLE `statistiche`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_statistiche_utente` (`idUtente`);

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `libro`
--
ALTER TABLE `libro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `obiettivi`
--
ALTER TABLE `obiettivi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la tabella `statistiche`
--
ALTER TABLE `statistiche`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT per la tabella `utente`
--
ALTER TABLE `utente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `libro`
--
ALTER TABLE `libro`
  ADD CONSTRAINT `fk_libro_utente` FOREIGN KEY (`idUtente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `obiettivi`
--
ALTER TABLE `obiettivi`
  ADD CONSTRAINT `fk_obiettivi_utente` FOREIGN KEY (`idUtente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `statistiche`
--
ALTER TABLE `statistiche`
  ADD CONSTRAINT `fk_statistiche_utente` FOREIGN KEY (`idUtente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
