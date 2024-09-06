ALTER TABLE arbeitszeiten
ADD COLUMN project VARCHAR(255);

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `users` text NOT NULL,
  `description` text NOT NULL,
  `note` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

ALTER TABLE projects
    ADD PRIMARY KEY (id)
ALTER TABLE projects
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

UPDATE scheme
SET v = '2';
