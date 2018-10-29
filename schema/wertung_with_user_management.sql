-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 29. Okt 2018 um 15:25
-- Server-Version: 5.7.24-0ubuntu0.16.04.1
-- PHP-Version: 7.2.11-3+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `wertung`
--
CREATE DATABASE IF NOT EXISTS `wertung` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `wertung`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `benutzer`
--

CREATE TABLE IF NOT EXISTS `benutzer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `benutzername` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `vorname` varchar(255) NOT NULL,
  `reg_datum` datetime NOT NULL,
  `ip` varchar(40) NOT NULL,
  `landkreis` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `benutzername` (`benutzername`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `benutzer`
--

INSERT INTO `benutzer` (`id`, `benutzername`, `email`, `passwort`, `name`, `vorname`, `reg_datum`, `ip`, `landkreis`) VALUES
(1, 'admin', 'admin@localhost', '$2y$10$wN/J1reEfimfhfk6/g529.As2WqCHu2f7GkTyPnVTlKD9d7eJ72x.', 'istrator', 'Admin', '2018-09-22 15:41:35', '127.0.0.1', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `benutzer_rolle`
--

CREATE TABLE IF NOT EXISTS `benutzer_rolle` (
  `benutzer` int(11) NOT NULL,
  `rolle` int(11) NOT NULL,
  PRIMARY KEY (`benutzer`,`rolle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `benutzer_rolle`
--

INSERT INTO `benutzer_rolle` (`benutzer`, `rolle`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bezirk`
--

CREATE TABLE IF NOT EXISTS `bezirk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bundesland` tinyint(2) UNSIGNED ZEROFILL NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `bezirk`
--

INSERT INTO `bezirk` (`id`, `bundesland`, `name`) VALUES
(1, 06, 'Darmstadt'),
(2, 06, 'Gießen'),
(3, 06, 'Kassel'),
(4, 11, 'Berlin');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bundesland`
--

CREATE TABLE IF NOT EXISTS `bundesland` (
  `id` tinyint(2) UNSIGNED ZEROFILL NOT NULL,
  `name` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `bundesland`
--

INSERT INTO `bundesland` (`id`, `name`) VALUES
(01, 'Schleswig-Holstein'),
(02, 'Hamburg'),
(03, 'Niedersachsen'),
(04, 'Bremen'),
(05, 'Nordrhein-Westfalen'),
(06, 'Hessen'),
(07, 'Rheinland-Pfalz'),
(08, 'Baden-Württemberg'),
(09, 'Bayern'),
(10, 'Saarland'),
(11, 'Berlin'),
(12, 'Brandenburg'),
(13, 'Mecklenburg-Vorpommern'),
(14, 'Sachsen'),
(15, 'Sachsen-Anhalt'),
(16, 'Thüringen'),
(99, 'Testland');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gruppenwertung`
--

CREATE TABLE IF NOT EXISTS `gruppenwertung` (
  `mannschaft` int(11) NOT NULL,
  `ef_e` int(11) NOT NULL DEFAULT '1',
  `ef_f` int(11) NOT NULL DEFAULT '0',
  `ma_e` int(11) NOT NULL DEFAULT '1',
  `ma_f` int(11) NOT NULL DEFAULT '0',
  `at_e` int(11) NOT NULL DEFAULT '1',
  `at_f` int(11) NOT NULL DEFAULT '0',
  `wt_e` int(11) NOT NULL DEFAULT '1',
  `wt_f` int(11) NOT NULL DEFAULT '0',
  `st_e` int(11) NOT NULL DEFAULT '1',
  `st_f` int(11) NOT NULL DEFAULT '0',
  `zeit_a` int(11) NOT NULL DEFAULT '0',
  `zeittakt_a` int(11) NOT NULL DEFAULT '0',
  `l1_e` int(11) NOT NULL DEFAULT '1',
  `l1_f` int(11) NOT NULL DEFAULT '0',
  `l2_e` int(11) NOT NULL DEFAULT '1',
  `l2_f` int(11) NOT NULL DEFAULT '0',
  `l3_e` int(11) NOT NULL DEFAULT '1',
  `l3_f` int(11) NOT NULL DEFAULT '0',
  `l4_e` int(11) NOT NULL DEFAULT '1',
  `l4_f` int(11) NOT NULL DEFAULT '0',
  `l5_e` int(11) NOT NULL DEFAULT '1',
  `l5_f` int(11) NOT NULL DEFAULT '0',
  `l6_e` int(11) NOT NULL DEFAULT '1',
  `l6_f` int(11) NOT NULL DEFAULT '0',
  `l7_e` int(11) NOT NULL DEFAULT '1',
  `l7_f` int(11) NOT NULL DEFAULT '0',
  `l8_e` int(11) NOT NULL DEFAULT '1',
  `l8_f` int(11) NOT NULL DEFAULT '0',
  `l9_e` int(11) NOT NULL DEFAULT '1',
  `l9_f` int(11) NOT NULL DEFAULT '0',
  `zeit_b` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mannschaft`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `landkreis`
--

CREATE TABLE IF NOT EXISTS `landkreis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezirk` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `landkreis`
--

INSERT INTO `landkreis` (`id`, `bezirk`, `name`) VALUES
(1, 1, 'Wiesbaden'),
(2, 1, 'Kreis Bergstraße'),
(3, 1, 'Landkreis Darmstadt-Dieburg'),
(4, 1, 'Kreis Groß-Gerau'),
(5, 1, 'Hochtaunuskreis'),
(6, 1, 'Main-Kinzig-Kreis'),
(7, 1, 'Main-Taunus-Kreis'),
(8, 1, 'Odenwaldkreis'),
(9, 1, 'Landkreis Offenbach'),
(10, 1, 'Rheingau-Taunus-Kreis'),
(11, 1, 'Wetteraukreis'),
(12, 1, 'Frankfurt am Main'),
(13, 1, 'Offenbach'),
(14, 1, 'Darmstadt'),
(15, 2, 'Landkreis Gießen'),
(16, 2, 'Lahn-Dill-Kreis'),
(17, 2, 'Landkreis Limburg-Weilburg'),
(18, 2, 'Landkreis Marburg-Biedenkopf'),
(19, 2, 'Vogelsbergkreis'),
(20, 3, 'Landkreis Fulda'),
(21, 3, 'Landkreis Hersfeld-Rotenburg'),
(22, 3, 'Landkreis Kassel'),
(23, 3, 'Schwalm-Eder-Kreis'),
(24, 3, 'Landkreis Waldeck-Frankenberg'),
(25, 3, 'Werra-Meißner-Kreis'),
(26, 3, 'Kassel'),
(27, 4, 'Mitte'),
(28, 4, 'Friedrichshain-Kreuzberg'),
(29, 4, 'Pankow'),
(30, 4, 'Charlottenburg-Wilmersdorf'),
(31, 4, 'Spandau'),
(32, 4, 'Steglitz-Zehlendorf'),
(33, 4, 'Tempelhof-Schöneberg'),
(34, 4, 'Neukölln'),
(35, 4, 'Treptow-Köpenick'),
(36, 4, 'Marzahn-Hellersdorf'),
(37, 4, 'Lichtenberg'),
(38, 4, 'Reinickendorf');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `leistungsspange`
--

CREATE TABLE IF NOT EXISTS `leistungsspange` (
  `id` varchar(16) NOT NULL,
  `bundesland` tinyint(2) UNSIGNED ZEROFILL NOT NULL,
  `mzf` tinyint(2) UNSIGNED ZEROFILL NOT NULL,
  `stempel` tinyint(3) UNSIGNED ZEROFILL NOT NULL,
  `datum` date NOT NULL,
  `ort` varchar(50) NOT NULL,
  `kreis` int(11) NOT NULL,
  `ab_name` varchar(50) NOT NULL,
  `ab_vorname` varchar(50) NOT NULL,
  `ab_ort` varchar(50) NOT NULL,
  `wr1_name` varchar(50) DEFAULT NULL,
  `wr1_vorname` varchar(50) DEFAULT NULL,
  `wr1_ort` varchar(50) DEFAULT NULL,
  `wr2_name` varchar(50) DEFAULT NULL,
  `wr2_vorname` varchar(50) DEFAULT NULL,
  `wr2_ort` varchar(50) DEFAULT NULL,
  `wr3_name` varchar(50) DEFAULT NULL,
  `wr3_vorname` varchar(50) DEFAULT NULL,
  `wr3_ort` varchar(50) DEFAULT NULL,
  `wr4_name` varchar(50) DEFAULT NULL,
  `wr4_vorname` varchar(50) DEFAULT NULL,
  `wr4_ort` varchar(50) DEFAULT NULL,
  `wr5_name` varchar(50) DEFAULT NULL,
  `wr5_vorname` varchar(50) DEFAULT NULL,
  `wr5_ort` varchar(50) DEFAULT NULL,
  `besitzer` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lsp_gruppe`
--

CREATE TABLE IF NOT EXISTS `lsp_gruppe` (
  `abnahme` varchar(16) NOT NULL,
  `id` tinyint(2) UNSIGNED ZEROFILL NOT NULL,
  `token` varchar(20) DEFAULT NULL,
  `startnummer` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `bundesland` tinyint(2) UNSIGNED ZEROFILL NOT NULL,
  `bezirk` varchar(50) NOT NULL,
  `kreis` varchar(50) NOT NULL,
  `ort` varchar(50) NOT NULL,
  PRIMARY KEY (`abnahme`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lsp_teilnehmer`
--

CREATE TABLE IF NOT EXISTS `lsp_teilnehmer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `abnahme` varchar(16) NOT NULL,
  `gruppe` tinyint(2) UNSIGNED ZEROFILL NOT NULL,
  `einsatz` tinyint(1) NOT NULL,
  `position` smallint(2) UNSIGNED ZEROFILL NOT NULL,
  `bewerber` varchar(1) NOT NULL,
  `name` varchar(50) NOT NULL,
  `vorname` varchar(50) NOT NULL,
  `geburtstag` date NOT NULL,
  `eintritt` date NOT NULL,
  `ausweisnr` mediumint(6) UNSIGNED ZEROFILL NOT NULL,
  `geschlecht` varchar(1) NOT NULL,
  `auslaender` tinyint(1) NOT NULL DEFAULT '0',
  `bundesland` smallint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lsp_token`
--

CREATE TABLE IF NOT EXISTS `lsp_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `abnahme` varchar(16) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(20) NOT NULL,
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lsp_wertung`
--

CREATE TABLE IF NOT EXISTS `lsp_wertung` (
  `abnahme` varchar(16) NOT NULL,
  `gruppe` tinyint(2) NOT NULL,
  `schnelligkeit_zeit` time NOT NULL,
  `schnelligkeit_eindruck` tinyint(1) NOT NULL,
  `schnelligkeit_gueltig` tinyint(1) NOT NULL,
  `schnelligkeit_zeit2` time NOT NULL,
  `schnelligkeit_eindruck2` tinyint(1) NOT NULL,
  `kugel_weite` decimal(3,0) NOT NULL,
  `kugel_eindruck` tinyint(1) NOT NULL,
  `kugel_gueltig` tinyint(1) NOT NULL,
  `kugel_weite2` decimal(3,0) NOT NULL,
  `kugel_eindruck2` tinyint(1) NOT NULL,
  `staffel_zeit` time NOT NULL,
  `staffel_eindruck` tinyint(1) NOT NULL,
  `staffel_gueltig` tinyint(1) NOT NULL,
  `staffel_zeit2` time NOT NULL,
  `staffel_eindruck2` tinyint(1) NOT NULL,
  `loeschangriff_punkte` tinyint(1) NOT NULL,
  `loeschangriff_eindruck` tinyint(1) NOT NULL,
  `fragen_punkte` tinyint(1) NOT NULL,
  `fragen_eindruck` tinyint(1) NOT NULL,
  PRIMARY KEY (`abnahme`,`gruppe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mannschaft`
--

CREATE TABLE IF NOT EXISTS `mannschaft` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wettbewerb` int(11) NOT NULL,
  `startnummer` int(11) DEFAULT NULL,
  `name` varchar(250) NOT NULL,
  `typ` int(11) NOT NULL,
  `alter` int(11) DEFAULT NULL,
  `vorgabezeit_a` int(11) DEFAULT NULL,
  `vorgabezeit_b` int(11) DEFAULT NULL,
  `punkte_a` int(11) DEFAULT NULL,
  `punkte_b` int(11) DEFAULT NULL,
  `punkte_gesamt` decimal(5,1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mannschaftsmitglieder`
--

CREATE TABLE IF NOT EXISTS `mannschaftsmitglieder` (
  `mannschaft` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `einsatz` tinyint(1) NOT NULL,
  `name` varchar(50) NOT NULL,
  `vorname` varchar(50) NOT NULL,
  `geburt` date NOT NULL,
  PRIMARY KEY (`mannschaft`,`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mannschaftstyp`
--

CREATE TABLE IF NOT EXISTS `mannschaftstyp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `mannschaftstyp`
--

INSERT INTO `mannschaftstyp` (`id`, `name`) VALUES
(1, 'Jungen'),
(2, 'M&auml;dchen'),
(3, 'Au&szlig;er Konkurrez');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `recht`
--

CREATE TABLE IF NOT EXISTS `recht` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `recht`
--

INSERT INTO `recht` (`id`, `name`) VALUES
(1, 'Benutzer anlegen'),
(2, 'Wettbewerb anlegen'),
(3, 'Leistungsspange anlegen'),
(4, 'Benutzer bearbeiten'),
(5, 'Wettbewerb landesweit'),
(6, 'Leistungsspange landesweit'),
(7, 'Rollen bearbeiten');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rolle`
--

CREATE TABLE IF NOT EXISTS `rolle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `rolle`
--

INSERT INTO `rolle` (`id`, `name`) VALUES
(0, 'keine'),
(1, 'Administrator'),
(2, 'Normaler Benutzer');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rolle_recht`
--

CREATE TABLE IF NOT EXISTS `rolle_recht` (
  `rolle` int(11) NOT NULL,
  `recht` int(11) NOT NULL,
  PRIMARY KEY (`rolle`,`recht`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `rolle_recht`
--

INSERT INTO `rolle_recht` (`rolle`, `recht`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(2, 2),
(2, 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `id` char(32) NOT NULL,
  `benutzer` int(11) NOT NULL,
  `start` int(11) NOT NULL,
  `ip` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `staffelwertung`
--

CREATE TABLE IF NOT EXISTS `staffelwertung` (
  `mannschaft` int(11) NOT NULL,
  `ef_e` int(11) NOT NULL DEFAULT '1',
  `ef_f` int(11) NOT NULL DEFAULT '0',
  `ma_e` int(11) NOT NULL DEFAULT '1',
  `ma_f` int(11) NOT NULL DEFAULT '0',
  `at_e` int(11) NOT NULL DEFAULT '1',
  `at_f` int(11) NOT NULL DEFAULT '0',
  `wt_e` int(11) NOT NULL DEFAULT '1',
  `wt_f` int(11) NOT NULL DEFAULT '0',
  `zeit_a` int(11) NOT NULL DEFAULT '0',
  `zeittakt_a` int(11) NOT NULL DEFAULT '0',
  `l1_e` int(11) NOT NULL DEFAULT '1',
  `l1_f` int(11) NOT NULL DEFAULT '0',
  `l2_e` int(11) NOT NULL DEFAULT '1',
  `l2_f` int(11) NOT NULL DEFAULT '0',
  `l3_e` int(11) NOT NULL DEFAULT '1',
  `l3_f` int(11) NOT NULL DEFAULT '0',
  `l4_e` int(11) NOT NULL DEFAULT '1',
  `l4_f` int(11) NOT NULL DEFAULT '0',
  `l5_e` int(11) NOT NULL DEFAULT '1',
  `l5_f` int(11) NOT NULL DEFAULT '0',
  `l6_e` int(11) NOT NULL DEFAULT '1',
  `l6_f` int(11) NOT NULL DEFAULT '0',
  `zeit_b` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mannschaft`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wettbewerb`
--

CREATE TABLE IF NOT EXISTS `wettbewerb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `land` tinyint(2) UNSIGNED ZEROFILL NOT NULL,
  `kreis` int(11) NOT NULL,
  `ort` varchar(50) NOT NULL,
  `art` int(11) NOT NULL,
  `typ` int(11) NOT NULL,
  `besitzer` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Veranstaltugstabelle';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wettbewerbsart`
--

CREATE TABLE IF NOT EXISTS `wettbewerbsart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mannschaft` varchar(50) NOT NULL,
  `anzahl` int(11) NOT NULL,
  `ersatz` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `wettbewerbsart`
--

INSERT INTO `wettbewerbsart` (`id`, `mannschaft`, `anzahl`, `ersatz`, `name`) VALUES
(1, 'Staffel', 6, 1, 'Staffelwettbewerb der Hessischen Jugendfeuerwehr'),
(2, 'Gruppe', 9, 1, 'Bundeswettbewerb der Deutschen Jugendfeuerwehr');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wettbewerbstyp`
--

CREATE TABLE IF NOT EXISTS `wettbewerbstyp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Grunddaten Wettbewerbstypen';

--
-- Daten für Tabelle `wettbewerbstyp`
--

INSERT INTO `wettbewerbstyp` (`id`, `name`) VALUES
(1, 'offenes Gew&auml;sser'),
(2, 'Unterflurhydrandt');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wettbewerbsvorgaben`
--

CREATE TABLE IF NOT EXISTS `wettbewerbsvorgaben` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `art` int(11) NOT NULL,
  `typ` int(11) NOT NULL,
  `vorgabezeit_a` int(11) NOT NULL,
  `vorgabezeit_b_10` int(11) NOT NULL,
  `vorgabezeit_intervall` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `wettbewerbsvorgaben`
--

INSERT INTO `wettbewerbsvorgaben` (`id`, `art`, `typ`, `vorgabezeit_a`, `vorgabezeit_b_10`, `vorgabezeit_intervall`) VALUES
(1, 1, 1, 360, 160, 5),
(2, 1, 2, 300, 160, 5),
(3, 2, 1, 420, 160, 5),
(4, 2, 2, 360, 160, 5);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
