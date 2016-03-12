-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 12, 2016 at 04:13 AM
-- Server version: 5.5.47-0ubuntu0.14.04.1
-- PHP Version: 5.6.19-1+deb.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `discord`
--

-- --------------------------------------------------------

--
-- Table structure for table `authUsers`
--

CREATE TABLE IF NOT EXISTS `authUsers` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `eveName` varchar(365) COLLATE utf8_unicode_ci NOT NULL,
  `characterID` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `discordID` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `role` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `active` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `addedOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=56 ;

-- --------------------------------------------------------

--
-- Table structure for table `pendingUsers`
--

CREATE TABLE IF NOT EXISTS `pendingUsers` (
  `id` int(56) NOT NULL AUTO_INCREMENT,
  `characterID` varchar(128) NOT NULL,
  `corporationID` varchar(128) NOT NULL,
  `allianceID` varchar(128) NOT NULL,
  `groups` varchar(128) NOT NULL,
  `authString` varchar(128) NOT NULL,
  `active` varchar(128) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=65 ;

-- --------------------------------------------------------

--
-- Table structure for table `shipFits`
--

CREATE TABLE IF NOT EXISTS `shipFits` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `fitName` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `fit` varchar(1800) COLLATE utf8_unicode_ci NOT NULL,
  `fitAuthor` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `dateAdded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=23 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
