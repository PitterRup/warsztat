-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Czas generowania: 19 Maj 2015, 11:57
-- Wersja serwera: 5.6.24
-- Wersja PHP: 5.6.8

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
  `ID` mediumint(9) NOT NULL,
  `Nazw_cz` char(25) NOT NULL,
  `Nr_fab_czesci` decimal(38,0) NOT NULL,
  `cena` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `fun_prac`
--

CREATE TABLE IF NOT EXISTS `fun_prac` (
  `ID` mediumint(9) NOT NULL,
  `Rola` char(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `fun_prac`
--

INSERT INTO `fun_prac` (`ID`, `Rola`) VALUES
(1, 'administrator');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klient`
--

CREATE TABLE IF NOT EXISTS `klient` (
  `ID` mediumint(9) NOT NULL,
  `nazw` char(25) NOT NULL,
  `nip_pesel` int(11) NOT NULL,
  `adr_zameld` char(40) DEFAULT NULL,
  `nr_tel` int(11) NOT NULL,
  `mail` char(25) DEFAULT NULL,
  `war_ubez` int(11) NOT NULL,
  `Login` char(25) NOT NULL,
  `haslo` char(25) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `klient`
--

INSERT INTO `klient` (`ID`, `nazw`, `nip_pesel`, `adr_zameld`, `nr_tel`, `mail`, `war_ubez`, `Login`, `haslo`) VALUES
(7, 'Baran', 147852, 'Gda?sk 745', 147852, 'damian@gmail.com', 4, 'afgdafg', 'ferrte');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownik`
--

CREATE TABLE IF NOT EXISTS `pracownik` (
  `ID` mediumint(9) NOT NULL,
  `Imie` char(25) NOT NULL,
  `Nazw` char(25) NOT NULL,
  `pesel` decimal(38,0) NOT NULL,
  `adr_zam` char(25) DEFAULT NULL,
  `nr_tel` decimal(38,0) NOT NULL,
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
  `ID_Zadania` mediumint(9) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `pracownik`
--

INSERT INTO `pracownik` (`ID`, `Imie`, `Nazw`, `pesel`, `adr_zam`, `nr_tel`, `mail`, `specj`, `dosw_zaw`, `dyspoz`, `obsl_stan`, `Fun_Prac_ID`, `login`, `pass`, `permissions`, `ip`, `phpsessid`, `Zad_ID_Zad`, `ID_Zadania`) VALUES
(1, 'janek', 'kowalski', '12341243', NULL, '123345678', NULL, NULL, NULL, '1', 'fd', 1, 'Administrator', 'administrator', '{"_admin":1}', '', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `relation_8`
--

CREATE TABLE IF NOT EXISTS `relation_8` (
  `Zadania_ID_Zadania` mediumint(9) NOT NULL,
  `Samochod_ID` mediumint(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `relation_14`
--

CREATE TABLE IF NOT EXISTS `relation_14` (
  `Czesci_ID` mediumint(9) NOT NULL,
  `Samochod_ID` mediumint(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `relation_16`
--

CREATE TABLE IF NOT EXISTS `relation_16` (
  `Czesci_ID` mediumint(9) NOT NULL,
  `Pracownik_ID` mediumint(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `samochod`
--

CREATE TABLE IF NOT EXISTS `samochod` (
  `ID` mediumint(9) NOT NULL,
  `Model` char(25) NOT NULL,
  `Marka` char(1) NOT NULL,
  `Rok_pr` decimal(38,0) NOT NULL,
  `Przeb` decimal(38,0) DEFAULT NULL,
  `Wer_wyp` decimal(38,0) DEFAULT NULL,
  `Rodz_nadw` char(25) DEFAULT NULL,
  `poj_sil` decimal(38,0) DEFAULT NULL,
  `Moc_sil` decimal(38,0) DEFAULT NULL,
  `Rodz_sil` char(25) NOT NULL,
  `rodz_ol_sil` char(25) DEFAULT NULL,
  `Naped` decimal(38,0) DEFAULT NULL,
  `Klient_ID` mediumint(9) DEFAULT NULL,
  `ID1` mediumint(9) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `samochod`
--

INSERT INTO `samochod` (`ID`, `Model`, `Marka`, `Rok_pr`, `Przeb`, `Wer_wyp`, `Rodz_nadw`, `poj_sil`, `Moc_sil`, `Rodz_sil`, `rodz_ol_sil`, `Naped`, `Klient_ID`, `ID1`) VALUES
(6, 'A8', 'A', '2013', '30000', '4', 'Sedan', '2800', '285', 'benzynowy', 'syntetyczny', '4', NULL, 1),
(7, 'Acord', 'H', '2009', '150000', '4', 'Sedan', '2000', '150', 'benzynowy', 'syntetyczny', '0', 7, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `stanowisko`
--

CREATE TABLE IF NOT EXISTS `stanowisko` (
  `ID` mediumint(9) NOT NULL,
  `Nazw` char(25) NOT NULL,
  `ladow` char(25) NOT NULL,
  `wymiary` char(25) NOT NULL,
  `Posiad_tun` char(1) NOT NULL,
  `przezn` varchar(20) NOT NULL
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
  `Stanowisko_ID` mediumint(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indexes for table `czesci`
--
ALTER TABLE `czesci`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `fun_prac`
--
ALTER TABLE `fun_prac`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `klient`
--
ALTER TABLE `klient`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `pracownik`
--
ALTER TABLE `pracownik`
  ADD PRIMARY KEY (`ID`), ADD KEY `Prac_Fun_Prac_FK` (`Fun_Prac_ID`), ADD KEY `Pracownik_Zadania_FK` (`Zad_ID_Zad`);

--
-- Indexes for table `relation_8`
--
ALTER TABLE `relation_8`
  ADD PRIMARY KEY (`Zadania_ID_Zadania`,`Samochod_ID`), ADD KEY `FK_ASS_12` (`Samochod_ID`);

--
-- Indexes for table `relation_14`
--
ALTER TABLE `relation_14`
  ADD PRIMARY KEY (`Czesci_ID`,`Samochod_ID`), ADD KEY `FK_ASS_6` (`Samochod_ID`);

--
-- Indexes for table `relation_16`
--
ALTER TABLE `relation_16`
  ADD PRIMARY KEY (`Czesci_ID`,`Pracownik_ID`), ADD KEY `FK_ASS_8` (`Pracownik_ID`);

--
-- Indexes for table `samochod`
--
ALTER TABLE `samochod`
  ADD PRIMARY KEY (`ID`), ADD KEY `Samochod_Klient_FK` (`Klient_ID`);

--
-- Indexes for table `stanowisko`
--
ALTER TABLE `stanowisko`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `zadania`
--
ALTER TABLE `zadania`
  ADD PRIMARY KEY (`ID_Zadania`), ADD KEY `Zadania_Stanowisko_FK` (`Stanowisko_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `klient`
--
ALTER TABLE `klient`
  MODIFY `ID` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT dla tabeli `pracownik`
--
ALTER TABLE `pracownik`
  MODIFY `ID` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT dla tabeli `samochod`
--
ALTER TABLE `samochod`
  MODIFY `ID` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `pracownik`
--
ALTER TABLE `pracownik`
ADD CONSTRAINT `Prac_Fun_Prac_FK` FOREIGN KEY (`Fun_Prac_ID`) REFERENCES `fun_prac` (`ID`),
ADD CONSTRAINT `Pracownik_Zadania_FK` FOREIGN KEY (`Zad_ID_Zad`) REFERENCES `zadania` (`ID_Zadania`);

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
