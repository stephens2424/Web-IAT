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
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blockComponents`
--

LOCK TABLES `blockComponents` WRITE;
/*!40000 ALTER TABLE `blockComponents` DISABLE KEYS */;
INSERT INTO `blockComponents` VALUES (1,1,9,1),(2,1,10,2),(3,2,12,2),(4,3,9,1),(5,3,10,2),(6,3,11,3),(7,3,12,4),(8,4,9,1),(9,4,10,2),(10,4,11,3),(11,4,12,4),(12,5,10,1),(13,5,9,2),(14,6,10,1),(15,6,9,2),(16,6,11,3),(17,6,12,4),(18,7,10,1),(19,7,9,2),(20,7,11,3),(21,7,12,4),(22,8,13,1),(23,8,14,2),(24,9,16,2),(25,10,13,1),(26,10,14,2),(27,10,15,3),(28,10,16,4),(29,11,13,1),(30,11,14,2),(31,11,15,3),(32,11,16,4),(33,12,14,1),(34,12,13,2),(35,13,14,1),(36,13,13,2),(37,13,15,3),(38,13,16,4),(39,14,14,1),(40,14,13,2),(41,14,15,3),(42,14,16,4);
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blocks`
--

LOCK TABLES `blocks` WRITE;
/*!40000 ALTER TABLE `blocks` DISABLE KEYS */;
INSERT INTO `blocks` VALUES (1,20,'Block 1, Practice',0),(2,20,'Block 2, Practice',0),(3,20,'Block 3, Practice',0),(4,20,'Block 4, Test',0),(5,20,'Block 5, Practice',0),(6,20,'Block 6, Practice',0),(7,20,'Block 7, Test',0),(8,20,'Block 1, Practice',0),(9,20,'Block 2, Practice',0),(10,20,'Block 3, Practice',0),(11,20,'Block 4, Test',0),(12,20,'Block 5, Practice',0),(13,20,'Block 6, Practice',0),(14,20,'Block 7, Test',0);
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoryPairs`
--

LOCK TABLES `categoryPairs` WRITE;
/*!40000 ALTER TABLE `categoryPairs` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `experiments`
--

LOCK TABLES `experiments` WRITE;
/*!40000 ALTER TABLE `experiments` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stimulusCategories`
--

LOCK TABLES `stimulusCategories` WRITE;
/*!40000 ALTER TABLE `stimulusCategories` DISABLE KEYS */;
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

-- Dump completed on 2011-07-26 11:19:19
