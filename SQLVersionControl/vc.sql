-- MySQL dump 10.13  Distrib 5.5.10, for osx10.6 (i386)
--
-- Host: localhost    Database: testIAT
-- ------------------------------------------------------
-- Server version	5.5.10

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `blockComponents`
--

DROP TABLE IF EXISTS `blockComponents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blockComponents` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `block` int(11) DEFAULT NULL,
  `category` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=320 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blockComponents`
--

LOCK TABLES `blockComponents` WRITE;
/*!40000 ALTER TABLE `blockComponents` DISABLE KEYS */;
INSERT INTO `blockComponents` VALUES (298,99,77,1),(299,99,78,2),(300,100,79,1),(301,100,80,2),(302,101,77,1),(303,101,78,2),(304,101,79,3),(305,101,80,4),(306,102,77,1),(307,102,78,2),(308,102,79,3),(309,102,80,4),(310,103,78,1),(311,103,77,2),(312,104,78,1),(313,104,77,2),(314,104,79,3),(315,104,80,4),(316,105,78,1),(317,105,77,2),(318,105,79,3),(319,105,80,4);
/*!40000 ALTER TABLE `blockComponents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blocks`
--

DROP TABLE IF EXISTS `blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blocks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `trials` int(11) DEFAULT NULL,
  `description` text,
  `experiment` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blocks`
--

LOCK TABLES `blocks` WRITE;
/*!40000 ALTER TABLE `blocks` DISABLE KEYS */;
INSERT INTO `blocks` VALUES (99,20,'Block 1, Practice',20),(100,20,'Block 2, Practice',20),(101,20,'Block 3, Practice',20),(102,20,'Block 4, Test',20),(103,20,'Block 5, Practice',20),(104,20,'The test',20),(105,15,'My test',20);
/*!40000 ALTER TABLE `blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categoryPairs`
--

DROP TABLE IF EXISTS `categoryPairs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categoryPairs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `positiveCategory` int(11) DEFAULT NULL,
  `negativeCategory` int(11) DEFAULT NULL,
  `associatedPair` int(11) DEFAULT NULL,
  `experiment` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `positiveCategory` (`positiveCategory`),
  UNIQUE KEY `negativeCateogry` (`negativeCategory`),
  UNIQUE KEY `associatedPair` (`associatedPair`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoryPairs`
--

LOCK TABLES `categoryPairs` WRITE;
/*!40000 ALTER TABLE `categoryPairs` DISABLE KEYS */;
INSERT INTO `categoryPairs` VALUES (39,77,78,40,20),(40,79,80,39,20);
/*!40000 ALTER TABLE `categoryPairs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `experiments`
--

DROP TABLE IF EXISTS `experiments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `experiments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` bit(1) NOT NULL DEFAULT b'0',
  `name` text,
  `endUrl` text,
  `hash` text,
  `secondEndUrl` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `experiments`
--

LOCK TABLES `experiments` WRITE;
/*!40000 ALTER TABLE `experiments` DISABLE KEYS */;
INSERT INTO `experiments` VALUES (20,'\0','New Experiment',NULL,'czhhw','');
/*!40000 ALTER TABLE `experiments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `responses`
--

DROP TABLE IF EXISTS `responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `responses` (
  `response_id` int(11) NOT NULL AUTO_INCREMENT,
  `subj` int(11) NOT NULL,
  `stimulus` int(11) DEFAULT NULL,
  `response` text,
  `response_time` int(11) DEFAULT NULL,
  `timeShown` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`response_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2801 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `responses`
--

LOCK TABLES `responses` WRITE;
/*!40000 ALTER TABLE `responses` DISABLE KEYS */;
/*!40000 ALTER TABLE `responses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stimuli`
--

DROP TABLE IF EXISTS `stimuli`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stimuli` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `experiment` int(11) NOT NULL,
  `word` text,
  `correct_response` int(11) DEFAULT NULL,
  `stimulusCategory` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stimuli`
--

LOCK TABLES `stimuli` WRITE;
/*!40000 ALTER TABLE `stimuli` DISABLE KEYS */;
/*!40000 ALTER TABLE `stimuli` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stimulusCategories`
--

DROP TABLE IF EXISTS `stimulusCategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stimulusCategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text,
  `experiment` int(11) DEFAULT NULL,
  `inPair` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stimulusCategories`
--

LOCK TABLES `stimulusCategories` WRITE;
/*!40000 ALTER TABLE `stimulusCategories` DISABLE KEYS */;
INSERT INTO `stimulusCategories` VALUES (77,'New Category',20,40),(78,'New Category',20,40),(79,'New Category',20,40),(80,'New Category',20,40);
/*!40000 ALTER TABLE `stimulusCategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `beginTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `qualtrics_id` text,
  `score` double DEFAULT NULL,
  `experiment` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=329 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `username` text NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `passwordHash` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('stephen',1,'5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-07-26 20:48:19
