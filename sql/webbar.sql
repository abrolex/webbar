-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 20. Okt 2021 um 22:29
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
(10, 'Lynchburg Lemonade', '0.4L', '6.00', 'Lynchburg Lemonade Cocktail Sprite Whisky Zitrone Limetten OrangenlikÃ¶r\n');

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
('dd84a7b12a8f04bc1782288d2fe271e88c95e71ebeea77bcb9beb324fbe75c40', '[]', '2021-10-20 18:18:10');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `location`
--

CREATE TABLE IF NOT EXISTS `location` (
  `location_id` int(255) NOT NULL AUTO_INCREMENT,
  `location_name` varchar(200) NOT NULL,
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Daten für Tabelle `location`
--

INSERT INTO `location` (`location_id`, `location_name`) VALUES
(1, 'keine'),
(4, 'Tisch 01'),
(5, 'Tisch 02'),
(6, 'Tisch 03'),
(7, 'Tisch 04'),
(8, 'Tisch 05');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int(255) NOT NULL AUTO_INCREMENT,
  `order_user_id` int(255) NOT NULL,
  `order_location_id` int(255) NOT NULL,
  `order_content` longtext NOT NULL,
  `order_time` timestamp NULL DEFAULT NULL,
  `order_status` enum('0','1','2') NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Daten für Tabelle `orders`
--

INSERT INTO `orders` (`order_id`, `order_user_id`, `order_location_id`, `order_content`, `order_time`, `order_status`) VALUES
(8, 2, 0, '[{"article_id":"4","article_name":"Coconut Kiss","article_variant":"0.4L","article_price":"5.00","article_amount":2},{"article_id":"10","article_name":"Lynchburg Lemonade","article_variant":"0.4L","article_price":"6.00","article_amount":"1"}]', '2021-10-20 18:05:10', '0'),
(9, 2, 6, '[{"article_id":"7","article_name":"Miami Dolphin","article_variant":"0.4L","article_price":"5.00","article_amount":"1"},{"article_id":"4","article_name":"Coconut Kiss","article_variant":"0.4L","article_price":"5.00","article_amount":"1"}]', '2021-10-20 20:16:44', '0');

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
  `user_location_id` int(255) NOT NULL DEFAULT '1',
  `user_passwordtime` timestamp NULL DEFAULT NULL,
  `user_passwordcode` varchar(200) DEFAULT NULL,
  `user_activationtime` timestamp NULL DEFAULT NULL,
  `user_activationcode` varchar(200) DEFAULT NULL,
  `user_active` enum('1','0') NOT NULL DEFAULT '0',
  `user_admin` enum('1','0') NOT NULL DEFAULT '0',
  `user_keywords` longtext NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`user_id`, `user_email`, `user_username`, `user_credit`, `user_password`, `user_salt`, `user_cart`, `user_location_id`, `user_passwordtime`, `user_passwordcode`, `user_activationtime`, `user_activationcode`, `user_active`, `user_admin`, `user_keywords`) VALUES
(1, 'wbadmin@gmail.com', 'wbadmin', 99.99, 'b5fdb4951be86a26b0fbff64d740393b823fc2ad187b072ab499601377c1e71b', '5OWEct3xug', '[]', 1, NULL, NULL, NULL, NULL, '1', '1', 'wbadmin@gmail.com wbadmin'),
(2, 'alexanderbrosch1@gmail.com', 'Pimmelkopf', 43.99, 'ccd05dddcdf34ec7e733cce30904a25aa00dcc31d98559b5865bfbe87ae97045', 'M0uhEkKmti', '[]', 7, NULL, NULL, NULL, NULL, '1', '0', 'alexanderbrosch1@gmail.com Pimmelkopf');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
