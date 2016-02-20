CREATE TABLE IF NOT EXISTS `{{PREFIX}}nastaveni` (
  `id` int(11) NOT NULL,
  `nazev` text COLLATE utf8_czech_ci NOT NULL,
  `hodnota` text COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


ALTER TABLE `{{PREFIX}}nastaveni`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{{PREFIX}}nastaveni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
