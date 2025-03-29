USE ab; 
-- Remove the above line if your database name is not 'ab'
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `arbeitszeiten` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `schicht_tag` varchar(256) NOT NULL,
  `schicht_anfang` varchar(256) NOT NULL,
  `schicht_ende` varchar(256) NOT NULL,
  `username` varchar(255) NOT NULL,
  `ort` text DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `review` tinyint(1) DEFAULT NULL,
  `type` varchar(11) DEFAULT NULL,
  `pause_start` varchar(255) DEFAULT NULL,
  `pause_end` varchar(255) DEFAULT NULL,
  `attachements` text DEFAULT NULL,
  `project` varchar(255) DEFAULT null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `kalender` (
  `id` int(11) NOT NULL,
  `datum` text NOT NULL,
  `uhrzeit` text NOT NULL,
  `ort` text NOT NULL,
  `notiz` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `schicht` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `schicht_gestartet_zeit` varchar(256) DEFAULT NULL,
  `schicht_ende_zeit` varchar(256) DEFAULT NULL,
  `schicht_datum` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `sick` (
  `id` int(11) NOT NULL,
  `username` varchar(256) NOT NULL,
  `start` text NOT NULL,
  `stop` text DEFAULT NULL,
  `status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `email_confirmed` tinyint(1) NOT NULL,
  `isAdmin` varchar(256) NOT NULL,
  `state` text DEFAULT NULL,
  `easymode` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `vacation` (
  `id` int(11) NOT NULL,
  `username` varchar(256) NOT NULL,
  `start` text NOT NULL,
  `stop` text DEFAULT NULL,
  `status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `scheme` (
	`v` INT(11) NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `arbeitszeiten`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `kalender`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `schicht`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sick`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `vacation`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `arbeitszeiten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `kalender`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `schicht`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `sick`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `vacation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;