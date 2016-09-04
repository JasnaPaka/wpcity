CREATE TABLE IF NOT EXISTS `{{PREFIX}}stitek_skupina` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci  ;

ALTER TABLE `{{PREFIX}}stitek` ADD CONSTRAINT `fk_skupina` FOREIGN KEY (`skupina`)
  REFERENCES `{{PREFIX}}stitek_skupina`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
