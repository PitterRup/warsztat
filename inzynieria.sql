-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 09 Cze 2015, 17:26
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
-- Struktura tabeli dla tabeli `fun_prac`
--

CREATE TABLE IF NOT EXISTS `fun_prac` (
  `id` mediumint(9) NOT NULL,
  `Rola` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Zrzut danych tabeli `fun_prac`
--

INSERT INTO `fun_prac` (`id`, `Rola`, `permissions`) VALUES
(1, 'Administrator', '{"_admin":1}'),
(2, 'Mechanik', '{"_admin":{"index":1,"mechanik":1}}'),
(3, 'Obs?uga klienta', '{"_admin":{"index":1,"zarzadzaniezadaniami":1, "zarzadzanieklientem":1}}');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klient`
--

CREATE TABLE IF NOT EXISTS `klient` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `nazw` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `nip_pesel` int(11) NOT NULL,
  `adr_zameld` char(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nr_tel` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `mail` char(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `war_ubez` int(11) NOT NULL,
  `login` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `pass` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(23) COLLATE utf8_unicode_ci NOT NULL,
  `phpsessid` varchar(52) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Zrzut danych tabeli `klient`
--

INSERT INTO `klient` (`id`, `nazw`, `nip_pesel`, `adr_zameld`, `nr_tel`, `mail`, `war_ubez`, `login`, `pass`, `permissions`, `ip`, `phpsessid`) VALUES
(1, 'Damian Horyszów', 2147483647, 'Leopoldów 23-456 Północna 4/2', '789-456-789', 'mucha12@mgas.con', 1, 'Damian12', 'damian12', '{"_page":1}', '', '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `naprawa`
--

CREATE TABLE IF NOT EXISTS `naprawa` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Data` datetime NOT NULL,
  `Diagnoza` char(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Stanowisko_ID` mediumint(9) NOT NULL,
  `Samochod_ID` mediumint(9) NOT NULL,
  `Cena` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `naprawa_Samochod_FK` (`Samochod_ID`),
  KEY `naprawa_Stanowisko_FK` (`Stanowisko_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Zrzut danych tabeli `naprawa`
--

INSERT INTO `naprawa` (`id`, `Data`, `Diagnoza`, `Status`, `Stanowisko_ID`, `Samochod_ID`, `Cena`) VALUES
(1, '2015-06-09 00:00:00', 'Nie włącza się', 'zdiagnozowany', 2, 1, 550);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `naprawa_pracownik`
--

CREATE TABLE IF NOT EXISTS `naprawa_pracownik` (
  `naprawa_ID` mediumint(9) NOT NULL,
  `Pracownik_ID` mediumint(9) NOT NULL,
  PRIMARY KEY (`naprawa_ID`,`Pracownik_ID`),
  KEY `naprawa_Pracownik_Pra_FK` (`Pracownik_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `naprawa_pracownik`
--

INSERT INTO `naprawa_pracownik` (`naprawa_ID`, `Pracownik_ID`) VALUES
(1, 4);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownik`
--

CREATE TABLE IF NOT EXISTS `pracownik` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Imie` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `Nazw` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `pesel` decimal(38,0) NOT NULL,
  `adr_zam` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nr_tel` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `mail` char(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `specj` char(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dosw_zaw` decimal(38,0) DEFAULT NULL,
  `dyspoz` decimal(38,0) NOT NULL,
  `obsl_stan` char(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Fun_Prac_ID` mediumint(9) NOT NULL,
  `login` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `pass` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(23) COLLATE utf8_unicode_ci NOT NULL,
  `phpsessid` varchar(52) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Prac_Fun_Prac_FK` (`Fun_Prac_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Zrzut danych tabeli `pracownik`
--

INSERT INTO `pracownik` (`id`, `Imie`, `Nazw`, `pesel`, `adr_zam`, `nr_tel`, `mail`, `specj`, `dosw_zaw`, `dyspoz`, `obsl_stan`, `Fun_Prac_ID`, `login`, `pass`, `ip`, `phpsessid`) VALUES
(1, 'janek', 'kowalski', 12341243, NULL, '123345678', NULL, NULL, NULL, 1, 'fd', 1, 'Administrator', 'administrator', '', ''),
(2, 'Patryk', 'Nowak', 98123112345, 'Niemiecka 34/32 22-550 We', '789-574-131', 'fjksa@gsaa.pl', 'administracja', 5, 0, '', 3, 'Janek', 'janek', '', ''),
(3, 'Daniel', 'Buryło', 98101287654, 'Leopoldów 23-456 Północna 4/2', '789-456-789', 'poipoi@gmail.com', 'administracja', 4, 1, NULL, 3, 'DanielBurylo', 'daniel', '', ''),
(4, 'Damian', 'Prystupa', 98101287654, 'Niemiecka 34/32 22-550 Werbkowice', '789-456-789', 'mucha12@mgas.con', 'mechanika obrotowa koła', 5, 1, '1,2', 2, 'Prystupa', 'prystupa', '127.0.0.1', 'hb6b4lo131frliijun5e3rek51'),
(5, 'Andrzej', 'Niedbała', 78945612356, 'Leopoldów 23-456 Północna 4/2', '789-789-789', 'mucha12@mgas.con', 'mechanika', 5, 1, '1,2', 2, 'Niedbała', 'niedbała', '', ''),
(6, 'Bartek', 'Stanowił', 93123101223, 'Niemiecka 34/32 22-550 Werbkowice', '789-789-789', 'mucha12@mgas.con', 'administracja', 5, 1, NULL, 3, 'Kanabal', '12345', '', '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `samochod`
--

CREATE TABLE IF NOT EXISTS `samochod` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Model` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `Marka` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Rok_pr` decimal(38,0) NOT NULL,
  `Przeb` decimal(38,0) DEFAULT NULL,
  `Wer_wyp` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Rodz_nadw` char(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `poj_sil` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Moc_sil` decimal(38,0) DEFAULT NULL,
  `Rodz_sil` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `rodz_ol_sil` char(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Naped` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Klient_ID` mediumint(9) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Sam_Klient_FK` (`Klient_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Zrzut danych tabeli `samochod`
--

INSERT INTO `samochod` (`id`, `Model`, `Marka`, `Rok_pr`, `Przeb`, `Wer_wyp`, `Rodz_nadw`, `poj_sil`, `Moc_sil`, `Rodz_sil`, `rodz_ol_sil`, `Naped`, `Klient_ID`) VALUES
(1, 'A3', 'Audi', 1997, 100000, 'standard', 'hatch-back', '1,9', 120, 'TDI', 'diesel', 'przód', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `stanowisko`
--

CREATE TABLE IF NOT EXISTS `stanowisko` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Nazw` char(35) COLLATE utf8_unicode_ci NOT NULL,
  `ladow` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `wymiary` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `Posiad_tun` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `przezn` varchar(35) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Zrzut danych tabeli `stanowisko`
--

INSERT INTO `stanowisko` (`id`, `Nazw`, `ladow`, `wymiary`, `Posiad_tun`, `przezn`) VALUES
(1, 'Stanowisko 1', '20', '10x10x5', 'y', 'Naprawa pojazdów'),
(2, 'Stanowisko 2', '20', '10x5x5', 'y', 'Naprawa pojazdów');

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `naprawa`
--
ALTER TABLE `naprawa`
  ADD CONSTRAINT `naprawa_Samochod_FK` FOREIGN KEY (`Samochod_ID`) REFERENCES `samochod` (`id`),
  ADD CONSTRAINT `naprawa_Stanowisko_FK` FOREIGN KEY (`Stanowisko_ID`) REFERENCES `stanowisko` (`id`);

--
-- Ograniczenia dla tabeli `naprawa_pracownik`
--
ALTER TABLE `naprawa_pracownik`
  ADD CONSTRAINT `naprawa_Pracownik_N_FK` FOREIGN KEY (`naprawa_ID`) REFERENCES `naprawa` (`id`),
  ADD CONSTRAINT `naprawa_Pracownik_Pra_FK` FOREIGN KEY (`Pracownik_ID`) REFERENCES `pracownik` (`id`);

--
-- Ograniczenia dla tabeli `pracownik`
--
ALTER TABLE `pracownik`
  ADD CONSTRAINT `Prac_Fun_Prac_FK` FOREIGN KEY (`Fun_Prac_ID`) REFERENCES `fun_prac` (`id`);

--
-- Ograniczenia dla tabeli `samochod`
--
ALTER TABLE `samochod`
  ADD CONSTRAINT `Sam_Klient_FK` FOREIGN KEY (`Klient_ID`) REFERENCES `klient` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
