-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Čtv 29. říj 2015, 13:34
-- Verze serveru: 5.6.24
-- Verze PHP: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `vpp`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `kv_autor`
--

CREATE TABLE IF NOT EXISTS `kv_autor` (
  `id` int(11) NOT NULL,
  `jmeno` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `prijmeni` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `titul_pred` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `titul_za` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `datum_narozeni` date DEFAULT NULL,
  `datum_umrti` date DEFAULT NULL,
  `obsah` text COLLATE utf8_czech_ci NOT NULL,
  `web` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `zpracovano` tinyint(4) NOT NULL DEFAULT '0',
  `deleted` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `kv_fotografie`
--

CREATE TABLE IF NOT EXISTS `kv_fotografie` (
  `id` int(11) NOT NULL,
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
  `deleted` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=1710 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `kv_kategorie`
--

CREATE TABLE IF NOT EXISTS `kv_kategorie` (
  `id` int(11) NOT NULL,
  `nazev` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `ikona` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `barva` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `checked` int(11) DEFAULT '1',
  `zoom` int(11) NOT NULL DEFAULT '1',
  `systemova` int(11) NOT NULL DEFAULT '0',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `poradi` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `kv_objekt`
--

CREATE TABLE IF NOT EXISTS `kv_objekt` (
  `id` int(11) NOT NULL,
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
  `interni` text COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1288 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `kv_objekt2autor`
--

CREATE TABLE IF NOT EXISTS `kv_objekt2autor` (
  `id` int(11) NOT NULL,
  `objekt` int(11) NOT NULL,
  `autor` int(11) NOT NULL,
  `spoluprace` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=570 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `kv_objekt2soubor`
--

CREATE TABLE IF NOT EXISTS `kv_objekt2soubor` (
  `id` int(11) NOT NULL,
  `objekt` int(11) NOT NULL,
  `soubor` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `kv_objekt2stitek`
--

CREATE TABLE IF NOT EXISTS `kv_objekt2stitek` (
  `id` int(11) NOT NULL,
  `objekt` int(11) NOT NULL,
  `stitek` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=149 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `kv_soubor`
--

CREATE TABLE IF NOT EXISTS `kv_soubor` (
  `id` int(11) NOT NULL,
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
  `upravil_datum` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `kv_stitek`
--

CREATE TABLE IF NOT EXISTS `kv_stitek` (
  `id` int(11) NOT NULL,
  `nazev` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `kv_zdroj`
--

CREATE TABLE IF NOT EXISTS `kv_zdroj` (
  `id` int(11) NOT NULL,
  `nazev` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `isbn` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `cerpano` int(11) NOT NULL DEFAULT '0',
  `deleted` int(11) NOT NULL DEFAULT '0',
  `objekt` int(11) DEFAULT NULL,
  `autor` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `kv_autor`
--
ALTER TABLE `kv_autor`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `kv_fotografie`
--
ALTER TABLE `kv_fotografie`
  ADD PRIMARY KEY (`id`), ADD KEY `objekt` (`objekt`);

--
-- Klíče pro tabulku `kv_kategorie`
--
ALTER TABLE `kv_kategorie`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `url` (`url`);

--
-- Klíče pro tabulku `kv_objekt`
--
ALTER TABLE `kv_objekt`
  ADD PRIMARY KEY (`id`), ADD KEY `kategorie` (`kategorie`);

--
-- Klíče pro tabulku `kv_objekt2autor`
--
ALTER TABLE `kv_objekt2autor`
  ADD PRIMARY KEY (`id`), ADD KEY `objekt` (`objekt`), ADD KEY `autor` (`autor`), ADD KEY `objekt_2` (`objekt`);

--
-- Klíče pro tabulku `kv_objekt2soubor`
--
ALTER TABLE `kv_objekt2soubor`
  ADD PRIMARY KEY (`id`), ADD KEY `objekt` (`objekt`), ADD KEY `soubor` (`soubor`);

--
-- Klíče pro tabulku `kv_objekt2stitek`
--
ALTER TABLE `kv_objekt2stitek`
  ADD PRIMARY KEY (`id`), ADD KEY `stitek` (`stitek`), ADD KEY `objekt` (`objekt`), ADD KEY `objekt_2` (`objekt`), ADD KEY `stitek_2` (`stitek`);

--
-- Klíče pro tabulku `kv_soubor`
--
ALTER TABLE `kv_soubor`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `kv_stitek`
--
ALTER TABLE `kv_stitek`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `kv_zdroj`
--
ALTER TABLE `kv_zdroj`
  ADD PRIMARY KEY (`id`), ADD KEY `objekt` (`objekt`), ADD KEY `autor` (`autor`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `kv_autor`
--
ALTER TABLE `kv_autor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=160;
--
-- AUTO_INCREMENT pro tabulku `kv_fotografie`
--
ALTER TABLE `kv_fotografie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1710;
--
-- AUTO_INCREMENT pro tabulku `kv_kategorie`
--
ALTER TABLE `kv_kategorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT pro tabulku `kv_objekt`
--
ALTER TABLE `kv_objekt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1288;
--
-- AUTO_INCREMENT pro tabulku `kv_objekt2autor`
--
ALTER TABLE `kv_objekt2autor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=570;
--
-- AUTO_INCREMENT pro tabulku `kv_objekt2soubor`
--
ALTER TABLE `kv_objekt2soubor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=105;
--
-- AUTO_INCREMENT pro tabulku `kv_objekt2stitek`
--
ALTER TABLE `kv_objekt2stitek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=149;
--
-- AUTO_INCREMENT pro tabulku `kv_soubor`
--
ALTER TABLE `kv_soubor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT pro tabulku `kv_stitek`
--
ALTER TABLE `kv_stitek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT pro tabulku `kv_zdroj`
--
ALTER TABLE `kv_zdroj`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=107;
--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `kv_fotografie`
--
ALTER TABLE `kv_fotografie`
ADD CONSTRAINT `kv_fotografie_ibfk_1` FOREIGN KEY (`objekt`) REFERENCES `kv_objekt` (`id`);

--
-- Omezení pro tabulku `kv_objekt`
--
ALTER TABLE `kv_objekt`
ADD CONSTRAINT `kv_objekt_ibfk_1` FOREIGN KEY (`kategorie`) REFERENCES `kv_kategorie` (`id`);

--
-- Omezení pro tabulku `kv_objekt2autor`
--
ALTER TABLE `kv_objekt2autor`
ADD CONSTRAINT `kv_objekt2autor_ibfk_1` FOREIGN KEY (`objekt`) REFERENCES `kv_objekt` (`id`),
ADD CONSTRAINT `kv_objekt2autor_ibfk_2` FOREIGN KEY (`autor`) REFERENCES `kv_autor` (`id`);

--
-- Omezení pro tabulku `kv_objekt2soubor`
--
ALTER TABLE `kv_objekt2soubor`
ADD CONSTRAINT `kv_objekt2soubor_ibfk_1` FOREIGN KEY (`objekt`) REFERENCES `kv_objekt` (`id`),
ADD CONSTRAINT `kv_objekt2soubor_ibfk_2` FOREIGN KEY (`soubor`) REFERENCES `kv_soubor` (`id`);

--
-- Omezení pro tabulku `kv_objekt2stitek`
--
ALTER TABLE `kv_objekt2stitek`
ADD CONSTRAINT `kv_objekt2stitek_ibfk_1` FOREIGN KEY (`objekt`) REFERENCES `kv_objekt` (`id`),
ADD CONSTRAINT `kv_objekt2stitek_ibfk_2` FOREIGN KEY (`stitek`) REFERENCES `kv_stitek` (`id`);

--
-- Omezení pro tabulku `kv_zdroj`
--
ALTER TABLE `kv_zdroj`
ADD CONSTRAINT `kv_zdroj_ibfk_1` FOREIGN KEY (`objekt`) REFERENCES `kv_objekt` (`id`),
ADD CONSTRAINT `kv_zdroj_ibfk_2` FOREIGN KEY (`autor`) REFERENCES `kv_autor` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
