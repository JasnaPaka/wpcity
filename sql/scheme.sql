CREATE TABLE IF NOT EXISTS `{{PREFIX}}autor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jmeno` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `prijmeni` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `titul_pred` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `titul_za` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `datum_narozeni` date DEFAULT NULL,
  `datum_umrti` date DEFAULT NULL,
  `obsah` text COLLATE utf8_czech_ci NOT NULL,
  `web` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `zpracovano` tinyint(4) NOT NULL DEFAULT '0',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ;

CREATE TABLE IF NOT EXISTS `{{PREFIX}}fotografie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `img_original` varchar(1024) COLLATE utf8_czech_ci NOT NULL,
  `img_thumbnail` varchar(1024) COLLATE utf8_czech_ci NOT NULL,
  `img_medium` varchar(1024) COLLATE utf8_czech_ci NOT NULL,
  `img_large` varchar(1024) COLLATE utf8_czech_ci NOT NULL,
  `img_512` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `img_100` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `objekt` int(11) NOT NULL,
  `primarni` tinyint(4) NOT NULL DEFAULT '0',
  `soukroma` tinyint(4) NOT NULL DEFAULT '0',
  `url` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `autor` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `datum_nahrani` datetime DEFAULT NULL,
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `skryta` tinyint(4) NOT NULL DEFAULT '0',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `rok` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `objekt` (`objekt`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ;

CREATE TABLE IF NOT EXISTS `{{PREFIX}}kategorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `ikona` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `barva` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `checked` int(11) DEFAULT '1',
  `zoom` int(11) NOT NULL DEFAULT '1',
  `systemova` int(11) NOT NULL DEFAULT '0',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `poradi` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE IF NOT EXISTS `{{PREFIX}}objekt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `kategorie` int(11) NOT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  `schvaleno` int(11) NOT NULL DEFAULT '1',
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `obsah` text COLLATE utf8_czech_ci NOT NULL,
  `prezdivka` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `material` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `rok_vzniku` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `pamatkova_ochrana` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `pristupnost` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `zruseno` tinyint(4) NOT NULL DEFAULT '0',
  `zpracovano` tinyint(4) NOT NULL DEFAULT '0',
  `pridal_autor` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `pridal_datum` datetime NOT NULL,
  `upravil_autor` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `upravil_datum` datetime NOT NULL,
  `interni` text COLLATE utf8_czech_ci NOT NULL,
  `pridano_osm` tinyint(4) NOT NULL DEFAULT '0',
  `pridano_vv` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `kategorie` (`kategorie`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ;

CREATE TABLE IF NOT EXISTS `{{PREFIX}}objekt2autor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `objekt` int(11) NOT NULL,
  `autor` int(11) NOT NULL,
  `spoluprace` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `objekt` (`objekt`),
  KEY `autor` (`autor`),
  KEY `objekt_2` (`objekt`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ;

CREATE TABLE IF NOT EXISTS `{{PREFIX}}objekt2soubor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `objekt` int(11) NOT NULL,
  `soubor` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `objekt` (`objekt`),
  KEY `soubor` (`soubor`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ;

CREATE TABLE IF NOT EXISTS `{{PREFIX}}objekt2stitek` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `objekt` int(11) NOT NULL,
  `stitek` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `stitek` (`stitek`),
  KEY `objekt` (`objekt`),
  KEY `objekt_2` (`objekt`),
  KEY `stitek_2` (`stitek`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ;

CREATE TABLE IF NOT EXISTS `{{PREFIX}}soubor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(255) COLLATE utf8_czech_ci DEFAULT '',
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `popis` varchar(255) COLLATE utf8_czech_ci DEFAULT '',
  `obsah` text COLLATE utf8_czech_ci,
  `zpracovano` tinyint(4) NOT NULL DEFAULT '0',
  `interni` text COLLATE utf8_czech_ci,
  `zruseno` tinyint(4) NOT NULL DEFAULT '0',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `pridal_autor` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `pridal_datum` datetime NOT NULL,
  `upravil_autor` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `upravil_datum` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE IF NOT EXISTS `{{PREFIX}}stitek` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci  ;

CREATE TABLE IF NOT EXISTS `{{PREFIX}}zdroj` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `isbn` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `cerpano` int(11) NOT NULL DEFAULT '0',
  `deleted` int(11) NOT NULL DEFAULT '0',
  `objekt` int(11) DEFAULT NULL,
  `autor` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `objekt` (`objekt`),
  KEY `autor` (`autor`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ;

ALTER TABLE `{{PREFIX}}fotografie`
  ADD CONSTRAINT `{{PREFIX}}fotografie_ibfk_1` FOREIGN KEY (`objekt`) REFERENCES `{{PREFIX}}objekt` (`id`);

ALTER TABLE `{{PREFIX}}objekt`
  ADD CONSTRAINT `{{PREFIX}}objekt_ibfk_1` FOREIGN KEY (`kategorie`) REFERENCES `{{PREFIX}}kategorie` (`id`);

ALTER TABLE `{{PREFIX}}objekt2autor`
  ADD CONSTRAINT `{{PREFIX}}objekt2autor_ibfk_1` FOREIGN KEY (`objekt`) REFERENCES `{{PREFIX}}objekt` (`id`),
  ADD CONSTRAINT `{{PREFIX}}objekt2autor_ibfk_2` FOREIGN KEY (`autor`) REFERENCES `{{PREFIX}}autor` (`id`);

ALTER TABLE `{{PREFIX}}objekt2soubor`
  ADD CONSTRAINT `{{PREFIX}}objekt2soubor_ibfk_1` FOREIGN KEY (`objekt`) REFERENCES `{{PREFIX}}objekt` (`id`),
  ADD CONSTRAINT `{{PREFIX}}objekt2soubor_ibfk_2` FOREIGN KEY (`soubor`) REFERENCES `{{PREFIX}}soubor` (`id`);

ALTER TABLE `{{PREFIX}}objekt2stitek`
  ADD CONSTRAINT `{{PREFIX}}objekt2stitek_ibfk_1` FOREIGN KEY (`objekt`) REFERENCES `{{PREFIX}}objekt` (`id`),
  ADD CONSTRAINT `{{PREFIX}}objekt2stitek_ibfk_2` FOREIGN KEY (`stitek`) REFERENCES `{{PREFIX}}stitek` (`id`);

ALTER TABLE `{{PREFIX}}zdroj`
  ADD CONSTRAINT `{{PREFIX}}zdroj_ibfk_1` FOREIGN KEY (`objekt`) REFERENCES `{{PREFIX}}objekt` (`id`),
  ADD CONSTRAINT `{{PREFIX}}zdroj_ibfk_2` FOREIGN KEY (`autor`) REFERENCES `{{PREFIX}}autor` (`id`);


