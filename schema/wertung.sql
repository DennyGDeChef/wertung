SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE IF NOT EXISTS `bundesland` (
  `id` tinyint(2) UNSIGNED ZEROFILL NOT NULL,
  `name` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

CREATE TABLE IF NOT EXISTS `leistungsspange` (
  `id` varchar(16) NOT NULL,
  `bundesland` tinyint(2) UNSIGNED ZEROFILL NOT NULL,
  `mzf` tinyint(2) UNSIGNED ZEROFILL NOT NULL,
  `stempel` tinyint(3) UNSIGNED ZEROFILL NOT NULL,
  `datum` date NOT NULL,
  `ort` varchar(50) NOT NULL,
  `kreis` varchar(50) NOT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `lsp_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `abnahme` varchar(16) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(20) NOT NULL,
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

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

CREATE TABLE IF NOT EXISTS `mannschaft` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wettbewerb` int(11) NOT NULL,
  `startnummer` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `typ` int(11) NOT NULL,
  `alter` int(11) DEFAULT NULL,
  `vorgabezeit_a` int(11) DEFAULT NULL,
  `vorgabezeit_b` int(11) DEFAULT NULL,
  `punkte_a` int(11) DEFAULT NULL,
  `punkte_b` int(11) DEFAULT NULL,
  `punkte_gesamt` decimal(5,1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mannschaftsmitglieder` (
  `mannschaft` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `einsatz` tinyint(1) NOT NULL,
  `name` varchar(50) NOT NULL,
  `vorname` varchar(50) NOT NULL,
  `geburt` date NOT NULL,
  PRIMARY KEY (`mannschaft`,`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mannschaftstyp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `mannschaftstyp` (`id`, `name`) VALUES
(1, 'Jungen'),
(2, 'M&auml;dchen'),
(3, 'Au&szlig;er Konkurrez');

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

CREATE TABLE IF NOT EXISTS `wettbewerb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `land` varchar(50) NOT NULL,
  `kreis` varchar(50) NOT NULL,
  `ort` varchar(50) NOT NULL,
  `art` int(11) NOT NULL,
  `typ` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='Veranstaltugstabelle';

CREATE TABLE IF NOT EXISTS `wettbewerbsart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mannschaft` varchar(50) NOT NULL,
  `anzahl` int(11) NOT NULL,
  `ersatz` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `wettbewerbsart` (`id`, `mannschaft`, `anzahl`, `ersatz`, `name`) VALUES
(1, 'Staffel', 6, 1, 'Staffelwettbewerb der Hessischen Jugendfeuerwehr'),
(2, 'Gruppe', 9, 1, 'Bundeswettbewerb der Deutschen Jugendfeuerwehr');

CREATE TABLE IF NOT EXISTS `wettbewerbstyp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Grunddaten Wettbewerbstypen';

INSERT INTO `wettbewerbstyp` (`id`, `name`) VALUES
(1, 'offenes Gew&auml;sser'),
(2, 'Unterflurhydrandt');

CREATE TABLE IF NOT EXISTS `wettbewerbsvorgaben` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `art` int(11) NOT NULL,
  `typ` int(11) NOT NULL,
  `vorgabezeit_a` int(11) NOT NULL,
  `vorgabezeit_b_10` int(11) NOT NULL,
  `vorgabezeit_intervall` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `wettbewerbsvorgaben` (`id`, `art`, `typ`, `vorgabezeit_a`, `vorgabezeit_b_10`, `vorgabezeit_intervall`) VALUES
(1, 1, 1, 360, 160, 5),
(2, 1, 2, 300, 160, 5),
(3, 2, 1, 420, 160, 5),
(4, 2, 2, 360, 160, 5);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
