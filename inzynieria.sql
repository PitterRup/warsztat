--
-- Struktura tabeli dla tabeli `fun_prac`
--

CREATE TABLE IF NOT EXISTS `fun_prac` (
  `id` mediumint(9) NOT NULL,
  `Rola` char(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE `fun_prac`
  ADD PRIMARY KEY (`id`);
--
-- Zrzut danych tabeli `fun_prac`
--

INSERT INTO `fun_prac` (`id`, `Rola`) VALUES
(1, 'Administrator'),
(2, 'Mechanik'),
(3, 'Obs?uga klienta');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klient`
--

CREATE TABLE IF NOT EXISTS `klient` (
  `id` mediumint(9) NOT NULL,
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
  `phpsessid` varchar(52) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE `klient`
  ADD PRIMARY KEY (`id`);
-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownik`
--

CREATE TABLE IF NOT EXISTS `pracownik` (
  `id` mediumint(9) NOT NULL,
  `Imie` char(25) NOT NULL,
  `Nazw` char(25) NOT NULL,
  `pesel` decimal(38,0) NOT NULL,
  `adr_zam` char(25) DEFAULT NULL,
  `nr_tel` varchar(15) NOT NULL,
  `mail` char(25) DEFAULT NULL,
  `specj` char(25) DEFAULT NULL,
  `dosw_zaw` decimal(38,0) DEFAULT NULL,
  `dyspoz` decimal(38,0) NOT NULL,
  `obsl_stan` char(35) NOT NULL,
  `Fun_Prac_ID` mediumint(9) NOT NULL,
  `login` char(25) NOT NULL,
  `pass` char(25) NOT NULL,
  `permissions` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `ip` varchar(23) NOT NULL,
  `phpsessid` varchar(52) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE `pracownik`
  ADD PRIMARY KEY (`id`);



INSERT INTO `pracownik` (`id`, `Imie`, `Nazw`, `pesel`, `adr_zam`, `nr_tel`, `mail`, `specj`, `dosw_zaw`, `dyspoz`, `obsl_stan`, `Fun_Prac_ID`, `login`, `pass`, `permissions`, `ip`, `phpsessid`) VALUES
(1, 'janek', 'kowalski', '12341243', NULL, '123345678', NULL, NULL, NULL, '1', 'fd', 1, 'Administrator', 'administrator', '{"_admin":1}', '::1', '1op3l56crhp40jduid37trrbb7');
-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `samochod`
--

CREATE TABLE IF NOT EXISTS `samochod` (
  `id` mediumint(9) NOT NULL,
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
  `Klient_ID` mediumint(9) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE `samochod`
  ADD PRIMARY KEY (`id`);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `stanowisko`
--

CREATE TABLE IF NOT EXISTS `stanowisko` (
  `id` mediumint(9) NOT NULL,
  `Nazw` char(35) NOT NULL,
  `ladow` char(25) NOT NULL,
  `wymiary` char(25) NOT NULL,
  `Posiad_tun` char(1) NOT NULL,
  `przezn` varchar(35) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE `stanowisko`
  ADD PRIMARY KEY (`id`);


CREATE TABLE naprawa
  (
    `id` mediumint(9) NOT NULL,
    `Data` datetime NOT NULL,
    `Diagnoza` char(25) DEFAULT NULL,
    `Status` varchar(20) DEFAULT NULL,
    `Stanowisko_ID` mediumint(9) NOT NULL,
    `Samochod_ID` mediumint(9) NOT NULL,
    `Cena` DOUBLE NOT NULL 
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;


ALTER TABLE `naprawa`
    ADD PRIMARY KEY (`id`) ;

CREATE TABLE naprawa_pracownik
(
    `Naprawa_ID` mediumint(9) NOT NULL,
    `Pracownik_ID` mediumint(9) NOT NULL
);

ALTER TABLE `naprawa_pracownik`
    ADD CONSTRAINT naprawa_pracownik_PK PRIMARY KEY (`Naprawa_ID`, `Pracownik_ID`) ;



ALTER TABLE `klient`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

ALTER TABLE `pracownik`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;

ALTER TABLE `samochod`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

ALTER TABLE `stanowisko`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

ALTER TABLE `naprawa`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- Ograniczenia dla zrzutów tabel
--

ALTER TABLE `pracownik`
ADD CONSTRAINT `Prac_Fun_Prac_FK` FOREIGN KEY (`Fun_Prac_ID`) REFERENCES `fun_prac` (`id`);

ALTER TABLE `naprawa_pracownik`
ADD CONSTRAINT `Naprawa_Pracownik_N_FK` FOREIGN KEY (`Naprawa_ID`) REFERENCES `naprawa` (`id`),
ADD CONSTRAINT `Naprawa_Pracownik_Pra_FK` FOREIGN KEY (`Pracownik_ID`) REFERENCES `pracownik` (`id`);

ALTER TABLE `samochod`
ADD CONSTRAINT `Sam_Klient_FK` FOREIGN KEY (`Klient_ID`) REFERENCES `klient` (`id`);

ALTER TABLE `Naprawa`
ADD CONSTRAINT `Naprawa_Samochod_FK` FOREIGN KEY (`Samochod_ID`) REFERENCES `samochod` (`id`),
ADD CONSTRAINT `Naprawa_Stanowisko_FK` FOREIGN KEY (`Stanowisko_ID`) REFERENCES `stanowisko` (`id`);


