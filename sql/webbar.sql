-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 19. Okt 2021 um 15:04
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
('791b9e79260511f50807e5f80aa8bd52111b76b93c186174070909e5d8d9e61c', '[{"article_id":"4","article_variant":"0","article_amount":3}]', '2021-10-13 14:34:49'),
('63091f28a4a16dee939bc2a0ff08245a750b0cbb50f0e757d89fea564b0f63e1', '[]', '2021-10-19 05:54:34'),
('9d7113c610d3aeb766758646d98909c2087c4c619df7c1cd635e085571bc75f7', '[]', '2021-10-19 08:17:24');

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
  `order_content` longtext NOT NULL,
  `order_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `order_status` enum('0','1','2') NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Daten für Tabelle `orders`
--

INSERT INTO `orders` (`order_id`, `order_user_id`, `order_content`, `order_time`, `order_status`) VALUES
(1, 2, '[{"article_id":"4","article_name":"Coconut Kiss","article_variant":"0.4L","article_price":"5.00","article_amount":"1"}]', '2021-10-19 13:24:28', '1'),
(2, 2, '[{"article_id":"5","article_name":"Swimming Pool","article_variant":"0.4L","article_price":"5.00","article_amount":"1"}]', '2021-10-19 13:52:49', '0'),
(3, 2, '[{"article_id":"8","article_name":"Sportsman","article_variant":"0.4L","article_price":"5.00","article_amount":"1"}]', '2021-10-19 13:53:01', '0'),
(4, 2, '[{"article_id":"8","article_name":"Sportsman","article_variant":"0.4L","article_price":"5.00","article_amount":"1"}]', '2021-10-19 13:53:11', '0'),
(5, 2, '[{"article_id":"6","article_name":"Strawberry Kiss","article_variant":"0.4L","article_price":"5.00","article_amount":"1"}]', '2021-10-19 13:53:31', '0'),
(6, 2, '[{"article_id":"7","article_name":"Miami Dolphin","article_variant":"0.4L","article_price":"5.00","article_amount":"1"}]', '2021-10-19 13:54:05', '0'),
(7, 2, '[{"article_id":"4","article_name":"Coconut Kiss","article_variant":"0.4L","article_price":"5.00","article_amount":"1"},{"article_id":"6","article_name":"Strawberry Kiss","article_variant":"0.4L","article_price":"5.00","article_amount":"4"},{"article_id":"8","article_name":"Sportsman","article_variant":"0.4L","article_price":"5.00","article_amount":"1"}]', '2021-10-19 14:36:47', '2');

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
  `user_passwordtime` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `user_passwordcode` varchar(200) DEFAULT NULL,
  `user_activationtime` timestamp NULL DEFAULT '0000-00-00 00:00:00',
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
(1, 'wbadmin@gmail.com', 'wbadmin', 99.99, 'b5fdb4951be86a26b0fbff64d740393b823fc2ad187b072ab499601377c1e71b', '5OWEct3xug', '[]', 1, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL, '1', '1', 'wbadmin@gmail.com wbadmin'),
(2, 'alexanderbrosch1@gmail.com', 'broscha', 69.99, 'ccd05dddcdf34ec7e733cce30904a25aa00dcc31d98559b5865bfbe87ae97045', 'M0uhEkKmti', '[{"article_id":"4","article_variant":"0","article_amount":"1"}]', 6, '0000-00-00 00:00:00', NULL, '2021-10-18 09:40:09', 'bb72d2f7b8c7e186ce13c10b64865cda2cd11c2142187409f212d998828e5867', '1', '0', 'alexanderbrosch1@gmail.com broscha');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
