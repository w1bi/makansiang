-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.1.73 - Source distribution
-- Server OS:                    redhat-linux-gnu
-- HeidiSQL Version:             9.1.0.4879
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for wb_msg
CREATE DATABASE IF NOT EXISTS `wb_msg` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_bin */;
USE `wb_msg`;


-- Dumping structure for table wb_msg.ms_batas_jam
CREATE TABLE IF NOT EXISTS `ms_batas_jam` (
  `hari_id` int(2) NOT NULL,
  `hari_jam` varchar(5) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  PRIMARY KEY (`hari_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Dumping data for table wb_msg.ms_batas_jam: 7 rows
/*!40000 ALTER TABLE `ms_batas_jam` DISABLE KEYS */;
INSERT INTO `ms_batas_jam` (`hari_id`, `hari_jam`) VALUES
	(0, '0000'),
	(1, '1030'),
	(2, '1030'),
	(3, '1030'),
	(4, '1030'),
	(5, '1000'),
	(6, '0000');
/*!40000 ALTER TABLE `ms_batas_jam` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
