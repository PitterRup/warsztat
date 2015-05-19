-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 19 Maj 2015, 18:57
-- Wersja serwera: 5.5.43-0ubuntu0.14.04.1
-- Wersja PHP: 5.5.9-1ubuntu4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `inzynieria`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `czesci`
--

CREATE TABLE IF NOT EXISTS `czesci` (
  `id` mediumint(9) NOT NULL,
  `Nazw_cz` char(25) NOT NULL,
  `Nr_fab_czesci` decimal(38,0) NOT NULL,
  `cena` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `fun_prac`
--

CREATE TABLE IF NOT EXISTS `fun_prac` (
  `id` mediumint(9) NOT NULL,
  `Rola` char(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `fun_prac`
--

INSERT INTO `fun_prac` (`id`, `Rola`) VALUES
(1, 'Administrator');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klient`
--

CREATE TABLE IF NOT EXISTS `klient` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `nazw` char(25) NOT NULL,
  `nip_pesel` int(11) NOT NULL,
  `adr_zameld` char(40) DEFAULT NULL,
  `nr_tel` varchar(15) NOT NULL,
  `mail` char(25) DEFAULT NULL,
  `war_ubez` int(11) NOT NULL,
  `login` char(25) NOT NULL,
  `pass` char(25) NOT NULL,
  `permissions` text NOT NULL,
  `ip` varchar(23) NOT NULL,
  `phpsessid` varchar(52) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Zrzut danych tabeli `klient`
--

INSERT INTO `klient` (`id`, `nazw`, `nip_pesel`, `adr_zameld`, `nr_tel`, `mail`, `war_ubez`, `login`, `pass`, `permissions`, `ip`, `phpsessid`) VALUES
(8, 'Jurek Jarek', 789789789, 'Leopoldów 23-456 Pó?nocna 4/2', '789-789-789', 'fsaf@mgas.con', 1, 'Jurek', 'juras', '{"_page":1}', '', '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownik`
--

CREATE TABLE IF NOT EXISTS `pracownik` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Imie` char(25) NOT NULL,
  `Nazw` char(25) NOT NULL,
  `pesel` decimal(38,0) NOT NULL,
  `adr_zam` char(25) DEFAULT NULL,
  `nr_tel` varchar(15) NOT NULL,
  `mail` char(25) DEFAULT NULL,
  `specj` char(25) DEFAULT NULL,
  `dosw_zaw` decimal(38,0) DEFAULT NULL,
  `dyspoz` decimal(38,0) NOT NULL,
  `obsl_stan` char(25) NOT NULL,
  `Fun_Prac_ID` mediumint(9) NOT NULL,
  `login` char(25) NOT NULL,
  `pass` char(25) NOT NULL,
  `permissions` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `ip` varchar(23) NOT NULL,
  `phpsessid` varchar(52) NOT NULL,
  `Zad_ID_Zad` mediumint(9) DEFAULT NULL,
  `ID_Zadania` mediumint(9) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Prac_Fun_Prac_FK` (`Fun_Prac_ID`),
  KEY `Pracownik_Zadania_FK` (`Zad_ID_Zad`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Zrzut danych tabeli `pracownik`
--

INSERT INTO `pracownik` (`id`, `Imie`, `Nazw`, `pesel`, `adr_zam`, `nr_tel`, `mail`, `specj`, `dosw_zaw`, `dyspoz`, `obsl_stan`, `Fun_Prac_ID`, `login`, `pass`, `permissions`, `ip`, `phpsessid`, `Zad_ID_Zad`, `ID_Zadania`) VALUES
(1, 'janek', 'kowalski', 12341243, NULL, '123345678', NULL, NULL, NULL, 1, 'fd', 1, 'Administrator', 'administrator', '{"_admin":1}', '127.0.0.1', 'qp5m2laq4kufu54cggtdg7aki1', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `relation_8`
--

CREATE TABLE IF NOT EXISTS `relation_8` (
  `Zadania_ID_Zadania` mediumint(9) NOT NULL,
  `Samochod_ID` mediumint(9) NOT NULL,
  PRIMARY KEY (`Zadania_ID_Zadania`,`Samochod_ID`),
  KEY `FK_ASS_12` (`Samochod_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `relation_14`
--

CREATE TABLE IF NOT EXISTS `relation_14` (
  `Czesci_ID` mediumint(9) NOT NULL,
  `Samochod_ID` mediumint(9) NOT NULL,
  PRIMARY KEY (`Czesci_ID`,`Samochod_ID`),
  KEY `FK_ASS_6` (`Samochod_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `relation_16`
--

CREATE TABLE IF NOT EXISTS `relation_16` (
  `Czesci_ID` mediumint(9) NOT NULL,
  `Pracownik_ID` mediumint(9) NOT NULL,
  PRIMARY KEY (`Czesci_ID`,`Pracownik_ID`),
  KEY `FK_ASS_8` (`Pracownik_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `samochod`
--

CREATE TABLE IF NOT EXISTS `samochod` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Model` varchar(25) NOT NULL,
  `Marka` varchar(20) NOT NULL,
  `Rok_pr` decimal(38,0) NOT NULL,
  `Przeb` decimal(38,0) DEFAULT NULL,
  `Wer_wyp` varchar(40) DEFAULT NULL,
  `Rodz_nadw` char(25) DEFAULT NULL,
  `poj_sil` varchar(5) DEFAULT NULL,
  `Moc_sil` decimal(38,0) DEFAULT NULL,
  `Rodz_sil` char(25) NOT NULL,
  `rodz_ol_sil` char(25) DEFAULT NULL,
  `Naped` varchar(15) DEFAULT NULL,
  `Klient_ID` mediumint(9) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Samochod_Klient_FK` (`Klient_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Zrzut danych tabeli `samochod`
--

INSERT INTO `samochod` (`id`, `Model`, `Marka`, `Rok_pr`, `Przeb`, `Wer_wyp`, `Rodz_nadw`, `poj_sil`, `Moc_sil`, `Rodz_sil`, `rodz_ol_sil`, `Naped`, `Klient_ID`) VALUES
(8, 'A8', 'A', 2004, 123144, '0', 'sedan', '2', 200, '', 'diesel', '0', 8),
(9, 'A3', 'Audi', 2001, 100000, 'standard', 'hatch-back', '1,9', 120, 'TDI', 'diesel', 'przód', 8);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `stanowisko`
--

CREATE TABLE IF NOT EXISTS `stanowisko` (
  `id` mediumint(9) NOT NULL,
  `Nazw` char(25) NOT NULL,
  `ladow` char(25) NOT NULL,
  `wymiary` char(25) NOT NULL,
  `Posiad_tun` char(1) NOT NULL,
  `przezn` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zadania`
--

CREATE TABLE IF NOT EXISTS `zadania` (
  `ID_Zadania` mediumint(9) NOT NULL,
  `Data` datetime NOT NULL,
  `Diagnoza` char(25) DEFAULT NULL,
  `Status` varchar(20) DEFAULT NULL,
  `Stanowisko_ID` mediumint(9) NOT NULL,
  PRIMARY KEY (`ID_Zadania`),
  KEY `Zadania_Stanowisko_FK` (`Stanowisko_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `pracownik`
--
ALTER TABLE `pracownik`
  ADD CONSTRAINT `Pracownik_Zadania_FK` FOREIGN KEY (`Zad_ID_Zad`) REFERENCES `zadania` (`ID_Zadania`),
  ADD CONSTRAINT `Prac_Fun_Prac_FK` FOREIGN KEY (`Fun_Prac_ID`) REFERENCES `fun_prac` (`ID`);

--
-- Ograniczenia dla tabeli `relation_8`
--
ALTER TABLE `relation_8`
  ADD CONSTRAINT `FK_ASS_11` FOREIGN KEY (`Zadania_ID_Zadania`) REFERENCES `zadania` (`ID_Zadania`),
  ADD CONSTRAINT `FK_ASS_12` FOREIGN KEY (`Samochod_ID`) REFERENCES `samochod` (`ID`);

--
-- Ograniczenia dla tabeli `relation_14`
--
ALTER TABLE `relation_14`
  ADD CONSTRAINT `FK_ASS_5` FOREIGN KEY (`Czesci_ID`) REFERENCES `czesci` (`ID`),
  ADD CONSTRAINT `FK_ASS_6` FOREIGN KEY (`Samochod_ID`) REFERENCES `samochod` (`ID`);

--
-- Ograniczenia dla tabeli `relation_16`
--
ALTER TABLE `relation_16`
  ADD CONSTRAINT `FK_ASS_7` FOREIGN KEY (`Czesci_ID`) REFERENCES `czesci` (`ID`),
  ADD CONSTRAINT `FK_ASS_8` FOREIGN KEY (`Pracownik_ID`) REFERENCES `pracownik` (`ID`);

--
-- Ograniczenia dla tabeli `samochod`
--
ALTER TABLE `samochod`
  ADD CONSTRAINT `Samochod_Klient_FK` FOREIGN KEY (`Klient_ID`) REFERENCES `klient` (`ID`);

--
-- Ograniczenia dla tabeli `zadania`
--
ALTER TABLE `zadania`
  ADD CONSTRAINT `Zadania_Stanowisko_FK` FOREIGN KEY (`Stanowisko_ID`) REFERENCES `stanowisko` (`ID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
