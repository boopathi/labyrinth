-- phpMyAdmin SQL Dump
-- version 3.4.3.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 06, 2012 at 07:14 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `labyrinth`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE IF NOT EXISTS `answers` (
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `key` varchar(200) NOT NULL,
  PRIMARY KEY (`from`,`to`),
  UNIQUE KEY `SECONDARY` (`from`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`from`, `to`, `key`) VALUES
(1, 3, 'abss'),
(1, 4, 'key1'),
(2, 3, 'key24'),
(3, 5, 'key18'),
(3, 10, 'key27'),
(3, 1, 'sks'),
(4, 1, 'key2'),
(4, 7, 'key3'),
(4, 9, 'key5'),
(4, 11, 'key7'),
(4, 6, 'key9'),
(5, 10, 'key19'),
(5, 2, 'key23'),
(5, 3, 'key26'),
(6, 4, 'key11'),
(6, 12, 'key15'),
(7, 4, 'key4'),
(8, 12, 'key12'),
(8, 3, 'key17'),
(8, 9, 'key21'),
(9, 5, 'key22'),
(9, 4, 'key6'),
(10, 8, 'key20'),
(11, 4, 'key8'),
(12, 6, 'key13'),
(12, 8, 'key16');

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `key` varchar(100) NOT NULL,
  `value` varchar(500) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `level` int(11) NOT NULL AUTO_INCREMENT,
  `header` varchar(100) NOT NULL DEFAULT 'Labyrinth',
  `question` text NOT NULL,
  `posX` int(11) NOT NULL,
  `posY` int(11) NOT NULL,
  PRIMARY KEY (`level`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`level`, `header`, `question`, `posX`, `posY`) VALUES
(1, 'node1', '<img src=''/labyrinth/admin/index.php/../../images/questions/arrows16__HhT8cTr3UZpk6PQ.jpg'' />', 175, 151),
(2, 'node2', '<img src=''/labyrinth/admin/index.php/../../images/questions/arrows32__ZrLuoSinZmsrrq2.jpg'' />', 262, 118),
(3, 'node3', '<img src=''/labyrinth/admin/index.php/../../images/questions/bar90__q7jGSC3qbQrVesd.jpg'' />', 283, 219),
(4, 'node4', '<img src=''/labyrinth/admin/index.php/../../images/questions/balls16__2WRwzd0PJ6nP9dT.jpg'' />', 368, 191),
(5, 'node5', '<img src=''/labyrinth/admin/index.php/../../images/questions/bar90__ZFJw97p95BX0rLy.jpg'' />', 343, 87),
(6, 'node7', '<img src=''/labyrinth/admin/index.php/../../images/questions/bar180__abgKywcdyyI2ph6.jpg'' />', 444, 153),
(7, 'node8', '<img src=''/labyrinth/admin/index.php/../../images/questions/dots64__ZhGSmbZ20KUOMfZ.jpg'' />', 417, 49),
(8, 'node9', '<img src=''/labyrinth/admin/index.php/../../images/questions/globe64__T06331c3wosiA4T.jpg'' />', 522, 117),
(9, 'node10', '<img src=''/labyrinth/admin/index.php/../../images/questions/loader16__LsckTdpQRBynRRy.jpg'' />', 365, 307),
(10, 'node11', '<img src=''/labyrinth/admin/index.php/../../images/questions/globe16__pCcnJ2sCiONSNdg.jpg'' />', 444, 263),
(11, 'node12', '<img src=''/labyrinth/admin/index.php/../../images/questions/bar180__kDFnmXh7MxhJcXL.jpg'' />', 527, 224),
(12, 'node13', '<img src=''/labyrinth/admin/index.php/../../images/questions/loadera32__PZz4CHkIICK5W21.jpg'' />', 597, 177);

-- --------------------------------------------------------

--
-- Table structure for table `user_level`
--

CREATE TABLE IF NOT EXISTS `user_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
