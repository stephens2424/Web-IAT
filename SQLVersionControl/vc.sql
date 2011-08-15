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
) ENGINE=InnoDB AUTO_INCREMENT=342 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blockComponents`
--

LOCK TABLES `blockComponents` WRITE;
/*!40000 ALTER TABLE `blockComponents` DISABLE KEYS */;
INSERT INTO `blockComponents` VALUES (320,106,81,1),(321,106,82,2),(322,107,83,1),(323,107,84,2),(324,108,81,1),(325,108,82,2),(326,108,83,3),(327,108,84,4),(328,109,81,1),(329,109,82,2),(330,109,83,3),(331,109,84,4),(332,110,82,1),(333,110,81,2),(334,111,82,1),(335,111,81,2),(336,111,83,3),(337,111,84,4),(338,112,82,1),(339,112,81,2),(340,112,83,3),(341,112,84,4);
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
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blocks`
--

LOCK TABLES `blocks` WRITE;
/*!40000 ALTER TABLE `blocks` DISABLE KEYS */;
INSERT INTO `blocks` VALUES (106,20,'Block 1, Practice',21),(107,20,'Block 2, Practice',21),(108,20,'Block 3, Practice',21),(109,20,'Block 4, Test',21),(110,20,'Block 5, Practice',21),(111,20,'Block 6, Practice',21),(112,20,'Block 7, Test',21);
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
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoryPairs`
--

LOCK TABLES `categoryPairs` WRITE;
/*!40000 ALTER TABLE `categoryPairs` DISABLE KEYS */;
INSERT INTO `categoryPairs` VALUES (41,81,82,42,21),(42,83,84,41,21);
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
  `secondEndUrl` text,
  `checkAnswers` tinyint(1) NOT NULL DEFAULT '0',
  `autoBalance` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `experiments`
--

LOCK TABLES `experiments` WRITE;
/*!40000 ALTER TABLE `experiments` DISABLE KEYS */;
INSERT INTO `experiments` VALUES (21,'\0','New Experiment',NULL,'z8gkl','',0,0);
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stimulusCategories`
--

LOCK TABLES `stimulusCategories` WRITE;
/*!40000 ALTER TABLE `stimulusCategories` DISABLE KEYS */;
INSERT INTO `stimulusCategories` VALUES (81,'Category1',21,42),(82,'Category2',21,42),(83,'Category3',21,42),(84,'Category4',21,42);
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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

-- Dump completed on 2011-08-15 16:58:34
