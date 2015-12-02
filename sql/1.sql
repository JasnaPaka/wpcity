CREATE TABLE IF NOT EXISTS `{{PREFIX}}bod` (
  `id` int(11) NOT NULL,
  `objekt` int(11) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `nazev` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `popis` text COLLATE utf8_czech_ci,
  `deleted` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

ALTER TABLE `{{PREFIX}}bod`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{{PREFIX}}bod`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
