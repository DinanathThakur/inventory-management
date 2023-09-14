# ************************************************************
# Sequel Ace SQL dump
# Version 20031
#
# https://sequel-ace.com/
# https://github.com/Sequel-Ace/Sequel-Ace
#
# Host: 127.0.0.1 (MySQL 5.7.43)
# Database: inventory_management
# Generation Time: 2023-09-14 12:01:44 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE='NO_AUTO_VALUE_ON_ZERO', SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `items`;

CREATE TABLE `items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item` varchar(50) DEFAULT NULL,
  `item_type` int(2) NOT NULL COMMENT '1= Office Supply, 2= Equipment, 3= Furniture',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;

INSERT INTO `items` (`id`, `item`, `item_type`)
VALUES
	(1,'Pen',1),
	(2,'Printer',2),
	(3,'Marker',1),
	(4,'Scanner',2),
	(5,'Clear Tape',1),
	(6,'Standing Table',2),
	(7,'Shredder',2),
	(8,'Thumbtack',1),
	(9,'Paper Clip',1),
	(10,'A4 Sheet',1),
	(11,'Notebook',1),
	(12,'Chair',3);

/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table requests
# ------------------------------------------------------------

DROP TABLE IF EXISTS `requests`;

CREATE TABLE `requests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `requested_by` varchar(100) DEFAULT NULL,
  `requested_on` date NOT NULL,
  `ordered_on` date NOT NULL,
  `items` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `requests` WRITE;
/*!40000 ALTER TABLE `requests` DISABLE KEYS */;

INSERT INTO `requests` (`id`, `requested_by`, `requested_on`, `ordered_on`, `items`)
VALUES
	(10,'kumar','2023-09-14','0000-00-00','[[\"1\", \"1\"], [\"3\", \"1\"], [\"5\", \"1\"], [\"9\", \"1\"], [\"10\", \"1\"], [\"11\", \"1\"]]'),
	(11,'Dina','2023-09-14','0000-00-00','[[\"9\", \"1\"], [\"10\", \"1\"]]'),
	(12,'Alex','2023-09-14','0000-00-00','[[\"11\", \"1\"]]'),
	(13,'Ajeet','2023-09-14','0000-00-00','[[\"2\", \"2\"], [\"4\", \"2\"]]'),
	(16,'Dina','2023-09-14','0000-00-00','[[\"2\", \"2\"], [\"4\", \"2\"]]'),
	(17,'Alex','2023-09-14','0000-00-00','[[\"1\", \"1\"]]'),
	(19,'kumar','2023-09-14','0000-00-00','[[\"2\", \"2\"], [\"4\", \"2\"]]');

/*!40000 ALTER TABLE `requests` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table summary
# ------------------------------------------------------------

DROP TABLE IF EXISTS `summary`;

CREATE TABLE `summary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `requested_by` varchar(100) DEFAULT NULL,
  `ordered_on` date DEFAULT NULL,
  `items` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `summary` WRITE;
/*!40000 ALTER TABLE `summary` DISABLE KEYS */;

INSERT INTO `summary` (`id`, `requested_by`, `ordered_on`, `items`)
VALUES
	(1,'kumar','2023-09-14','[[[\"1\", [\"1\", \"3\", \"5\", \"9\", \"10\", \"11\"]]], [[\"2\", [\"2\", \"4\"]]]]'),
	(2,'Dina','2023-09-14','[[[\"1\", [\"9\", \"10\"]]], [[\"2\", [\"2\", \"4\"]]]]'),
	(3,'Alex','2023-09-14','[[\"1\", [[\"1\", [\"11\"]], [\"1\"]]]]'),
	(4,'Ajeet','2023-09-14','[[[\"2\", [\"2\", \"4\"]]]]');

/*!40000 ALTER TABLE `summary` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
