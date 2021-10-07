-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 07. Okt 2021 um 15:16
-- Server Version: 5.6.13
-- PHP-Version: 5.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `webbar`
--
CREATE DATABASE IF NOT EXISTS `webbar` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `webbar`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `article`
--

CREATE TABLE IF NOT EXISTS `article` (
  `article_id` int(255) NOT NULL AUTO_INCREMENT,
  `article_name` varchar(200) NOT NULL,
  `article_variant` longtext NOT NULL,
  `article_price` longtext NOT NULL,
  `article_keywords` longtext NOT NULL,
  PRIMARY KEY (`article_id`),
  UNIQUE KEY `article_name` (`article_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Daten für Tabelle `article`
--

INSERT INTO `article` (`article_id`, `article_name`, `article_variant`, `article_price`, `article_keywords`) VALUES
(4, 'Coconut Kiss', '0.4L', '5.00', 'Coconut Kiss Cocktail'),
(5, 'Swimming Pool', '0.4L', '5.00', 'Swimming Pool Cocktail'),
(6, 'Strawberry Kiss', '0.4L', '5.00', 'Strawberry Kiss Cocktail'),
(7, 'Miami Dolphin', '0.4L', '5.00', 'Miami Dolphin Cocktail'),
(8, 'Sportsman', '0.4L/0.3L', '5.00/3.00', 'Sportsman Cocktail alkoholfrei'),
(9, 'Pina Colada', '0.4L', '5.00', 'Pina Colada Cocktail Ananas Kokos weiÃŸer Rum brauner Rum'),
(10, 'Lynchburg Lemonade', '0.4L', '6.00', 'Lynchburg Lemonade Sprite Whisky Zitrone Limetten OrangenlikÃ¶r');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(255) NOT NULL AUTO_INCREMENT,
  `user_email` varchar(200) NOT NULL,
  `user_username` varchar(200) NOT NULL,
  `user_credit` float(4,2) NOT NULL DEFAULT '0.00',
  `user_password` varchar(200) NOT NULL,
  `user_salt` varchar(10) NOT NULL,
  `user_passwordtime` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `user_passwordcode` varchar(200) DEFAULT NULL,
  `user_activationtime` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `user_activationcode` varchar(200) DEFAULT NULL,
  `user_active` enum('1','0') NOT NULL DEFAULT '0',
  `user_admin` enum('1','0') NOT NULL DEFAULT '0',
  `user_keywords` longtext NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`user_id`, `user_email`, `user_username`, `user_credit`, `user_password`, `user_salt`, `user_passwordtime`, `user_passwordcode`, `user_activationtime`, `user_activationcode`, `user_active`, `user_admin`, `user_keywords`) VALUES
(1, 'wbadmin@web.de', 'wbadmin', 99.99, 'b5fdb4951be86a26b0fbff64d740393b823fc2ad187b072ab499601377c1e71b', '5OWEct3xug', '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL, '1', '1', 'wbadmin@web.de wbadmin');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
