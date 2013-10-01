-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 27, 2013 at 10:48 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
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
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_shine` varchar(50) NOT NULL,
  `id_twitter` varchar(20) NOT NULL,
  `id_group` tinyint(3) unsigned NOT NULL,
  `email` varchar(100) NOT NULL,
  `old_score` double unsigned NOT NULL DEFAULT '0',
  `current_score` double unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids_unique` (`id_shine`,`id_twitter`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `id_shine`, `id_twitter`, `id_group`, `email`, `old_score`, `current_score`) VALUES
(1, '51c0907251381005bb000003', 'boyd4y', 1, 'winston@misfitwearables.com', 650, 750),
(2, '523ad606513810d653000fdc', 'loc02', 1, 'loc02@smisfit.com', 827, 827),
(3, '524545815138103988000001', 'misfitphan', 1, 'quan@misfitwearables.com', 700, 700);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
