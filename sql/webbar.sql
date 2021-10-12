-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 12. Okt 2021 um 15:17
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
-- Tabellenstruktur für Tabelle `cart`
--

CREATE TABLE IF NOT EXISTS `cart` (
  `cart_id` varchar(200) NOT NULL,
  `cart_content` longtext NOT NULL,
  `cart_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `cart`
--

INSERT INTO `cart` (`cart_id`, `cart_content`, `cart_time`) VALUES
('d810f0e847c55befc5ae79f5d7297d54f863540136ba80317a422e5edfeb9973', '[{"article_id":"7","article_variant":"0","article_amount":36},{"article_id":"4","article_variant":"0","article_amount":"7"},{"article_id":"6","article_variant":"0","article_amount":"1"},{"article_id":"8","article_variant":"0","article_amount":"1"},{"article_id":"9","article_variant":"0","article_amount":"11"}]', '2021-10-12 15:05:09');

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
  `user_cart` longtext NOT NULL,
  `user_passwordtime` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `user_passwordcode` varchar(200) DEFAULT NULL,
  `user_activationtime` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `user_activationcode` varchar(200) DEFAULT NULL,
  `user_active` enum('1','0') NOT NULL DEFAULT '0',
  `user_admin` enum('1','0') NOT NULL DEFAULT '0',
  `user_keywords` longtext NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`user_id`, `user_email`, `user_username`, `user_credit`, `user_password`, `user_salt`, `user_cart`, `user_passwordtime`, `user_passwordcode`, `user_activationtime`, `user_activationcode`, `user_active`, `user_admin`, `user_keywords`) VALUES
(1, 'wbadmin@gmail.com', 'wbadmin', 99.99, 'b5fdb4951be86a26b0fbff64d740393b823fc2ad187b072ab499601377c1e71b', '5OWEct3xug', '[]', '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL, '1', '1', 'wbadmin@gmail.com wbadmin');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
