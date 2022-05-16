-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 18. Mrz 2022 um 12:46
-- Server-Version: 10.4.21-MariaDB
-- PHP-Version: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `blog_v1_oop`
--
CREATE DATABASE IF NOT EXISTS `blog_v1_oop` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `blog_v1_oop`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Blog`
--

DROP TABLE IF EXISTS `Blog`;
CREATE TABLE `Blog` (
  `blogID` int(11) NOT NULL,
  `blogHeadline` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `blogImagePath` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blogImageAlignment` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `blogContent` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `blogDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `catID` int(11) NOT NULL,
  `userID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `Blog`
--

INSERT INTO `Blog` (`blogID`, `blogHeadline`, `blogImagePath`, `blogImageAlignment`, `blogContent`, `blogDate`, `catID`, `userID`) VALUES
(1, 'a-z A-Z 123 öüä ', NULL, 'fleft', 'aäb cde fgh ijk lmn oöp qrsß tuü vwx yz AÄBC DEF GHI JKL MNO ÖPQ RST UÜV WXYZ ! \"§ $%& /() =? * \'<>\r\n\r\n|; ²³~ @`´ ©«» ¼× {} aäb cde fgh ijk lmn oöp qrsß tuü vwx yz AÄBC DEF GHI JKL MNO ÖPQ RST UÜV WXYZ ! \"§ $%& /() =? * \'<>\r\n\r\n|; ²³~ @`´ ©«» ¼× {} aäb cde fgh ijk lmn oöp qrsß tuü vwx yz AÄBC DEF GHI JKL MNO ÖPQ RST UÜV WXYZ ! \"§ $%& /() =? * \'<> |; ²³~ @`´ ©«» ¼× {} aäb cde fgh ijk lmn oöp qrsß tuü vwx yz AÄBC DEF GHI JKL MNO ÖPQ RST UÜV WXYZ ! \"§ $%& /() =?', '2017-08-24 12:37:07', 1, 1),
(2, 'Pangram', 'uploads/blogimages/375757AHJNDHUSGDIUNEZMT807_eselkarren.JPG', 'fleft', 'Zwei flinke Boxer jagen die quirlige Eva und ihren Mops durch Sylt. Franz jagt im komplett verwahrlosten Taxi quer durch Bayern. Zwölf Boxkämpfer jagen Viktor quer über den großen Sylter Deich. Vogel Quax zwickt Johnys Pferd Bim. Sylvia wagt quick den Jux bei Pforzheim.\r\n\r\nPolyfon zwitschernd aßen Mäxchens Vögel Rüben, Joghurt und Quark. \"Fix, Schwyz! \" quäkt Jürgen blöd vom Paß. Victor jagt zwölf Boxkämpfer quer über den großen Sylter Deich. Falsches Üben von Xylophonmusik quält jeden größeren Zwerg. Heizölrückstoßabdämpfung.', '2017-08-24 12:37:49', 2, 1),
(5, 'Typoblindtext', 'uploads/blogimages/467348HHTDJZADMNEGUISNU575425_03 - wolf.jpg', 'fright', 'Dies ist ein Typoblindtext. An ihm kann man sehen, ob alle Buchstaben da sind und wie sie aussehen. Manchmal benutzt man Worte wie Hamburgefonts, Rafgenduks oder Handgloves, um Schriften zu testen. Manchmal Sätze, die alle Buchstaben des Alphabets enthalten - man nennt diese Sätze »Pangrams«.\r\n\r\nSehr bekannt ist dieser: The quick brown fox jumps over the lazy old dog. Oft werden in Typoblindtexte auch fremdsprachige Satzteile eingebaut (AVAIL® and Wefox™ are testing aussi la Kerning), um die Wirkung in anderen Sprachen zu testen. In Lateinisch sieht zum Beispiel fast jede Schrift gut aus.\r\n\r\nQuod erat demonstrandum. Seit 1975 fehlen in den meisten Testtexten die Zahlen, weswegen nach TypoGb. 204 § ab dem Jahr 2034 Zahlen in 86 der Texte zur Pflicht werden. Nichteinhaltung wird mit bis zu 245 € oder 368 $ bestraft.', '2017-08-24 12:41:22', 1, 1),
(6, 'Trappatoni \'98', 'uploads/blogimages/744482DUUSMEATHZNJDHGNI857006_01 - calvin.gif', 'fleft', 'Es gibt im Moment in diese Mannschaft, oh, einige Spieler vergessen ihnen Profi was sie sind. Ich lese nicht sehr viele Zeitungen, aber ich habe gehört viele Situationen. Erstens: wir haben nicht offensiv gespielt.\r\n\r\nEs gibt keine deutsche Mannschaft spielt offensiv und die Name offensiv wie Bayern. Letzte Spiel hatten wir in Platz drei Spitzen: Elber, Jancka und dann Zickler. Wir müssen nicht vergessen Zickler. Zickler ist eine Spitzen mehr, Mehmet eh mehr Basler.\r\n\r\nIst klar diese Wörter, ist möglich verstehen, was ich hab gesagt? Danke. Offensiv, offensiv ist wie machen wir in Platz. Zweitens: ich habe erklärt mit diese zwei Spieler: nach Dortmund brauchen vielleicht Halbzeit Pause. Ich habe auch andere Mannschaften gesehen in Europa nach diese Mittwoch.\r\n\r\nIch habe gesehen auch zwei Tage die Training. Ein Trainer ist nicht ein Idiot! Ein Trainer sei sehen was passieren in Platz. In diese Spiel es waren zwei, drei diese Spieler waren schwach wie eine Flasche leer! Haben Sie gesehen Mittwoch, welche Mannschaft hat gespielt Mittwoch? Hat gespielt Mehmet oder gespielt Basler oder hat gespielt Trapattoni? Diese Spieler beklagen mehr als sie spielen! Wissen Sie, warum die Italienmannschaften kaufen nicht diese Spieler?', '2017-08-24 12:47:54', 2, 1),
(9, 'Er hörte leise', 'uploads/blogimages/840059SDIAUNHTUZMGDJHEN363488_04 - Katze.jpg', 'fright', 'Er hörte leise Schritte hinter sich. Das bedeutete nichts Gutes. Wer würde ihm schon folgen, spät in der Nacht und dazu noch in dieser engen Gasse mitten im übel beleumundeten Hafenviertel? Gerade jetzt, wo er das Ding seines Lebens gedreht hatte und mit der Beute verschwinden wollte!\r\n\r\nHatte einer seiner zahllosen Kollegen dieselbe Idee gehabt, ihn beobachtet und abgewartet, um ihn nun um die Früchte seiner Arbeit zu erleichtern? Oder gehörten die Schritte hinter ihm zu einem der unzähligen Gesetzeshüter dieser Stadt, und die stählerne Acht um seine Handgelenke würde gleich zuschnappen?', '2017-08-24 12:44:53', 1, 1),
(14, 'Ein neuer Blog Eintrag', NULL, 'fleft', 'Dies ist ein sehr interessanter Blog!', '2022-03-18 11:45:33', 7, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Category`
--

DROP TABLE IF EXISTS `Category`;
CREATE TABLE `Category` (
  `catID` int(11) NOT NULL,
  `catLabel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `Category`
--

INSERT INTO `Category` (`catID`, `catLabel`) VALUES
(1, 'Lifestyle'),
(2, 'Food'),
(7, 'News'),
(8, 'Sport'),
(9, 'Travel'),
(10, 'Technology'),
(11, 'Surfing'),
(15, 'Entertainment'),
(16, 'Politics'),
(17, 'Health'),
(18, 'Science'),
(23, 'Test34');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `User`
--

DROP TABLE IF EXISTS `User`;
CREATE TABLE `User` (
  `userID` int(11) NOT NULL,
  `userFirstName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `userLastName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `userEmail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `userCity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `userPassword` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `User`
--

INSERT INTO `User` (`userID`, `userFirstName`, `userLastName`, `userEmail`, `userCity`, `userPassword`) VALUES
(1, 'Peter', 'Petersen', 'pepe@gmail.com', 'Bremen', '$2y$10$tbCYcuHF/flLur6pSSpMheR5DKA2io7T9TcE/Gw3Q/2aulfoQiGD2'),
(2, 'Paul', 'Paulsen', 'paul@paulsen.net', 'Paris', '$2y$10$3vC0YKbOcGVXevncK82iFuUGP611c8Es1DxHVuDZ3652veoAFA2kO');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `Blog`
--
ALTER TABLE `Blog`
  ADD PRIMARY KEY (`blogID`),
  ADD KEY `blogs_ibfk_1` (`userID`),
  ADD KEY `blogs_ibfk_2` (`catID`);

--
-- Indizes für die Tabelle `Category`
--
ALTER TABLE `Category`
  ADD PRIMARY KEY (`catID`);

--
-- Indizes für die Tabelle `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `Blog`
--
ALTER TABLE `Blog`
  MODIFY `blogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT für Tabelle `Category`
--
ALTER TABLE `Category`
  MODIFY `catID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT für Tabelle `User`
--
ALTER TABLE `User`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `Blog`
--
ALTER TABLE `Blog`
  ADD CONSTRAINT `blog_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`),
  ADD CONSTRAINT `blog_ibfk_2` FOREIGN KEY (`catID`) REFERENCES `category` (`catID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
