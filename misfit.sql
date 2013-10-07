-- phpMyAdmin SQL Dump
-- version 4.0.6
-- http://www.phpmyadmin.net
--
-- Host: 0.0.0.0:3306
-- Generation Time: Oct 06, 2013 at 04:34 PM
-- Server version: 5.5.33
-- PHP Version: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `misfit`
--

-- --------------------------------------------------------

--
-- Table structure for table `group_exps`
--

CREATE TABLE IF NOT EXISTS `group_exps` (
  `id_group` int(10) unsigned NOT NULL,
  `id_exp` int(10) unsigned NOT NULL,
  `id_twitter` varchar(100) NOT NULL DEFAULT '',
  `goal` int(10) unsigned NOT NULL DEFAULT '0',
  `start_date` datetime DEFAULT NULL,
  `last_updated_date` datetime DEFAULT NULL,
  `old_score` int(10) unsigned DEFAULT '0',
  `current_score` int(10) unsigned DEFAULT '0',
  `day_of_week` tinyint(4) unsigned NOT NULL DEFAULT '1',
  `timezone` tinyint(4) NOT NULL DEFAULT '-7',
  UNIQUE KEY `id_group` (`id_group`,`id_exp`,`id_twitter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `group_users`
--

CREATE TABLE IF NOT EXISTS `group_users` (
  `id_group` int(10) unsigned NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_group`,`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `leaderboard`
--

CREATE TABLE IF NOT EXISTS `leaderboard` (
  `id_group` int(10) unsigned NOT NULL,
  `id_exp` int(10) unsigned NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `last_date` datetime NOT NULL,
  `points` int(10) unsigned NOT NULL,
  `weekly_points` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id_group`,`id_exp`,`id_user`,`last_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_shine` varchar(50) NOT NULL,
  `id_twitter` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `old_score` double unsigned NOT NULL DEFAULT '0',
  `current_score` double unsigned NOT NULL DEFAULT '0',
  `id_server` tinyint(3) unsigned DEFAULT '1',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
