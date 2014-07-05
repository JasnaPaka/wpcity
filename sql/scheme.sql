SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE DATABASE IF NOT EXISTS `krizky-vetrelci` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci;
USE `krizky-vetrelci`;

CREATE TABLE IF NOT EXISTS `kv_autor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jmeno` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `datum_narozeni` date DEFAULT NULL,
  `datum_umrti` date DEFAULT NULL,
  `obsah` text COLLATE utf8_czech_ci NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE IF NOT EXISTS `kv_fotografie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `img_original` varchar(1024) COLLATE utf8_czech_ci NOT NULL,
  `img_thumbnail` varchar(1024) COLLATE utf8_czech_ci NOT NULL,
  `img_medium` varchar(1024) COLLATE utf8_czech_ci NOT NULL,
  `img_large` varchar(1024) COLLATE utf8_czech_ci NOT NULL,
  `objekt` int(11) NOT NULL,
  `primarni` tinyint(4) NOT NULL DEFAULT '0',
  `autor` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `objekt` (`objekt`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE IF NOT EXISTS `kv_kategorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `ikona` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE IF NOT EXISTS `kv_objekt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `kategorie` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `obsah` text COLLATE utf8_czech_ci NOT NULL,
  `prezdivka` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `material` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `rok_vzniku` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `pamatkova_ochrana` varchar(50) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `pristupnost` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `pridal_autor` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `pridal_datum` datetime NOT NULL,
  `upravil_autor` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `upravil_datum` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kategorie` (`kategorie`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE IF NOT EXISTS `kv_objekt2autor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `objekt` int(11) NOT NULL,
  `autor` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `objekt` (`objekt`),
  KEY `autor` (`autor`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


ALTER TABLE `kv_fotografie`
  ADD CONSTRAINT `kv_fotografie_ibfk_1` FOREIGN KEY (`objekt`) REFERENCES `kv_objekt` (`id`);

ALTER TABLE `kv_objekt`
  ADD CONSTRAINT `kv_objekt_ibfk_1` FOREIGN KEY (`kategorie`) REFERENCES `kv_kategorie` (`id`);

ALTER TABLE `kv_objekt2autor`
  ADD CONSTRAINT `kv_objekt2autor_ibfk_1` FOREIGN KEY (`objekt`) REFERENCES `kv_objekt` (`id`),
  ADD CONSTRAINT `kv_objekt2autor_ibfk_2` FOREIGN KEY (`autor`) REFERENCES `kv_autor` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
