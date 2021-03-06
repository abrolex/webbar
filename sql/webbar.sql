-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 21. Nov 2021 um 16:32
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

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
(10, 'Lynchburg Lemonade', '0.4L', '6.00', 'Lynchburg Lemonade Cocktail Sprite Whisky Zitrone Limetten OrangenlikÃ¶r\n'),
(11, 'Bier', '0.3L/0.5L', '1.80/3.60', 'Bier Hopfen Malz Gerste');

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
('dd84a7b12a8f04bc1782288d2fe271e88c95e71ebeea77bcb9beb324fbe75c40', '[]', '2021-10-20 18:18:10'),
('dd095415350d5c55c4e288c445a60407ceb973d2dfa45afef39167283e3cd476', '[]', '2021-11-17 15:49:38'),
('26ed54083cf13556670ee1f7f849c5c10c1664785abce12ed6c2052f35e42847', '[]', '2021-11-19 07:03:04'),
('75c0f5e883cdc0baa037c8138386117adab9cd346b67f237bdf3f4e7d7c4ed00', '[]', '2021-11-20 21:33:13'),
('b4caa08a2fc2a79816864099600e94ad4ee21b6eb7d14be2ef9ab096239ab571', '[]', '2021-11-20 22:12:32');

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
  `order_cart` longtext NOT NULL,
  `order_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `order_state` enum('0','1','2') NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Daten für Tabelle `orders`
--

INSERT INTO `orders` (`order_id`, `order_user_id`, `order_location_id`, `order_cart`, `order_time`, `order_state`) VALUES
(11, 2, 6, '[{"id":"5","name":"Swimming Pool","variant":"0.4L","price":"5.00","amount":"1"}]', '2021-11-19 09:11:56', '1'),
(12, 2, 6, '[{"id":"9","name":"Pina Colada","variant":"0.4L","price":"5.00","amount":2}]', '2021-11-20 22:27:19', '0'),
(13, 2, 6, '[{"id":"10","name":"Lynchburg Lemonade","variant":"0.4L","price":"6.00","amount":1}]', '2021-11-20 22:31:40', '0');

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
(2, 'alexanderbrosch1@gmail.com', 'abrolex', 10.45, '9fa59c90dea564cd573ee63fce7f06d963f54936c355fe996d317233739327c3', 'S373QpDvvK', '[]', 6, NULL, NULL, NULL, NULL, '1', '1', 'alexanderbrosch1@gmail.com abrolex');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
