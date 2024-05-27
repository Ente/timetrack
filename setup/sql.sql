SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `arbeitszeiten`
--

CREATE TABLE `arbeitszeiten` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `schicht_tag` varchar(256) NOT NULL,
  `schicht_anfang` varchar(256) NOT NULL,
  `schicht_ende` varchar(256) NOT NULL,
  `username` varchar(255) NOT NULL,
  `ort` text DEFAULT NULL,
  `type` text DEFAULT 'worktime',
  `pause_start` varchar(256) NOT NULL,
  `pause_stop` varchar(256) NOT NULL,
  `attachement` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kalender`
--

CREATE TABLE `kalender` (
  `id` int(11) NOT NULL,
  `datum` text NOT NULL,
  `uhrzeit` text NOT NULL,
  `ort` text NOT NULL,
  `notiz` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mailboxes`
--

CREATE TABLE `mailboxes` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `description` text DEFAULT NULL,
  `file` text DEFAULT NULL,
  `user` varchar(255) NOT NULL,
  `seen` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mailboxes_files`
--

CREATE TABLE `mailboxes_files` (
  `id` int(11) NOT NULL COMMENT 'attachement id',
  `m_id` int(11) NOT NULL COMMENT 'mailbox notif id',
  `name` varchar(255) NOT NULL,
  `type` text NOT NULL,
  `url` text NOT NULL,
  `username` varchar(255) NOT NULL,
  `secret` int(10) NOT NULL COMMENT 'Defines if the file preview should be covered\r\n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `schicht`
--

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
  `date` text NOT NULL,
  `status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `sick`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `sick`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `vacation` (
  `id` int(11) NOT NULL,
  `username` varchar(256) NOT NULL,
  `date_start` text NOT NULL,
  `date_end` text NOT NULL,
  `status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `vacation`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `vacation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `email_confirmed` tinyint(1) NOT NULL,
  `isAdmin` varchar(256) NOT NULL,
  `state` text DEFAULT NULL,
  `easymode` boolean DEFAULT 0;
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `arbeitszeiten`
--
ALTER TABLE `arbeitszeiten`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `kalender`
--
ALTER TABLE `kalender`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `mailboxes`
--
ALTER TABLE `mailboxes`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `mailboxes_files`
--
ALTER TABLE `mailboxes_files`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `schicht`
--
ALTER TABLE `schicht`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `arbeitszeiten`
--
ALTER TABLE `arbeitszeiten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kalender`
--
ALTER TABLE `kalender`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `mailboxes`
--
ALTER TABLE `mailboxes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `schicht`
--
ALTER TABLE `schicht`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
