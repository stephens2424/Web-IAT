-- MySQL dump 10.13  Distrib 5.1.53, for apple-darwin10.3.0 (i386)
--
-- Host: localhost    Database: testIAT
-- ------------------------------------------------------
-- Server version	5.1.53

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
-- Table structure for table `experiments`
--

DROP TABLE IF EXISTS `experiments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `experiments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stimuli_set` int(11) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `name` tinytext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `experiments`
--

LOCK TABLES `experiments` WRITE;
/*!40000 ALTER TABLE `experiments` DISABLE KEYS */;
INSERT INTO `experiments` VALUES (1,1,1,'Test 1'),(2,2,1,'Test 2');
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
  PRIMARY KEY (`response_id`)
) ENGINE=MyISAM AUTO_INCREMENT=202 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `responses`
--

LOCK TABLES `responses` WRITE;
/*!40000 ALTER TABLE `responses` DISABLE KEYS */;
INSERT INTO `responses` VALUES (1,1,1,'m',559),(2,1,2,'k',531),(3,1,3,'o',580),(4,1,4,'i',613),(5,1,5,'u',729),(6,2,1,'m',902),(7,2,2,'h',652),(8,2,3,'i',813),(9,2,4,'o',861),(10,2,5,'p',772),(11,5,1,'s',501),(12,5,2,'d',3882),(13,5,3,'s',30397),(14,5,4,'d',163),(15,5,5,'f',277),(16,5,6,'s',3164),(17,5,7,'d',4052),(18,5,8,'f',156),(19,5,9,'a',229),(20,5,10,'d',316),(21,6,1,',',2290),(22,6,2,'j',1860),(23,6,3,'j',292),(24,6,4,'j',404),(25,6,5,'j',588),(26,7,6,'k',552),(27,7,7,'i',340),(28,7,8,'o',388),(29,7,9,'l',484),(30,7,10,'n',460),(31,8,1,'m',518),(32,8,2,'.',14371),(33,8,3,',',452),(34,8,4,'.',461),(35,8,5,',',85),(36,9,14,'x',468),(37,9,13,'x',573),(38,14,6,'n',1060),(39,14,15,'j',1866),(40,14,12,'j',1204),(41,15,6,'m',1272),(42,16,6,'m',1182),(43,16,6,'k',2989),(44,16,6,'j',3565),(45,16,6,'j',3781),(46,16,6,'j',4013),(47,16,6,'j',14510),(48,16,6,'k',29478),(49,16,6,'k',29710),(50,16,6,'k',29941),(51,16,6,'k',30165),(52,16,6,'m',30414),(53,16,6,'k',30582),(54,16,6,'k',30854),(55,16,6,'n',30942),(56,16,6,'j',31077),(57,16,6,'n',31173),(58,16,6,'j',31302),(59,16,6,'n',31414),(60,16,6,'j',31542),(61,16,6,'n',31670),(62,16,6,'j',31765),(63,16,6,'j',32006),(64,16,6,'n',32166),(65,16,6,'j',32230),(66,16,6,'n',32342),(67,16,6,'j',32493),(68,16,6,'n',32574),(69,16,6,'j',32702),(70,16,6,'n',32806),(71,16,6,'f',84300),(72,16,6,'f',84594),(73,16,6,'f',84826),(74,16,6,'r',85026),(75,16,6,'r',85242),(76,16,6,'f',109603),(77,16,6,'f',109802),(78,16,6,'d',109995),(79,16,6,'d',110259),(80,16,6,'f',110466),(81,16,6,'f',110771),(82,16,6,'d',110794),(83,16,6,'f',110946),(84,16,6,'d',111154),(85,16,6,'f',111242),(86,16,6,'d',111539),(87,16,6,'f',111603),(88,16,6,'d',111867),(89,16,6,'f',111930),(90,16,6,'m',126907),(91,16,6,'m',127179),(92,16,6,'m',127459),(93,16,6,'m',127731),(94,16,6,'m',128011),(95,16,6,'m',128291),(96,16,6,'k',152102),(97,16,6,'k',152318),(98,16,6,'m',178599),(99,16,6,'m',178959),(100,19,6,'m',1083),(101,19,15,'m',1722),(102,20,6,',',10815),(103,21,6,'m',4138),(104,23,6,'d',837),(105,24,6,'k',606),(106,24,15,'k',1458),(107,24,12,'k',1430),(108,25,6,'p',1055),(109,25,15,'p',1793),(110,25,12,'p',780),(111,26,6,'d',612),(112,26,15,'d',998),(113,26,12,'d',605),(114,27,6,'d',3454),(115,27,15,'d',2285),(116,28,6,'d',838),(117,28,15,'d',1054),(118,28,12,'d',709),(119,29,6,'k',806),(120,30,6,'j',661),(121,31,6,',',452),(122,32,6,'m',436),(123,32,15,' ',1782),(124,33,6,'m',846),(125,33,15,'m',1117),(126,34,6,'d',739),(127,35,6,'d',564),(128,36,6,'d',777),(129,37,6,'f',572),(130,38,6,'d',541),(131,39,6,'d',758),(132,40,6,'f',1030),(133,41,6,'d',693),(134,42,6,'d',1036),(135,43,6,'m',634),(136,44,6,'n',799),(137,45,6,'n',916),(138,46,6,' ',604),(139,46,15,' ',2286),(140,46,12,' ',1676),(141,47,6,'m',628),(142,48,6,'m',3877),(143,49,6,'m',851),(144,49,15,'m',9166),(145,50,6,'m',517),(146,51,6,',',569),(147,52,6,'m',414),(148,53,6,'d',774),(149,53,15,'w',2044),(150,54,6,'m',2015),(151,54,15,'m',1568),(152,59,6,'d',789),(153,59,15,'d',1570),(154,60,6,'d',543),(155,60,15,'d',976),(156,61,6,'d',1122),(157,61,15,'d',919),(158,62,6,'m',1914),(159,62,15,'m',1320),(160,63,6,'m',23078),(161,63,15,'m',658),(162,64,6,'s',1986),(163,64,15,'s',879),(164,66,6,'s',1972),(165,66,15,'s',790),(166,66,12,'s',663),(167,67,1,'S',-2147483648),(168,67,2,'S',-2147483648),(169,67,3,'S',-2147483648),(170,67,4,'S',-2147483648),(171,67,5,'S',-2147483648),(172,68,1,'J',-2147483648),(173,68,2,'J',-2147483648),(174,68,3,'J',-2147483648),(175,68,4,'J',-2147483648),(176,68,5,'J',-2147483648),(177,69,1,'K',-2147483648),(178,69,2,'right',-2147483648),(179,69,3,'up',-2147483648),(180,69,4,'left',-2147483648),(181,69,5,'down',-2147483648),(182,70,1,'up',1547),(183,70,2,'down',344),(184,70,3,'left',352),(185,70,4,'right',344),(186,70,5,'up',352),(187,71,1,'up',1241),(188,71,2,'left',778),(189,71,3,'down',754),(190,71,4,'up',760),(191,71,5,'right',863),(192,71,1,'up',1152),(193,71,2,'left',222),(194,71,3,'down',217),(195,71,4,'right',200),(196,71,5,'up',215),(197,73,1,'h',922),(198,73,2,'o',595),(199,73,3,'u',604),(200,73,4,'d',1524),(201,73,5,'u',1373);
/*!40000 ALTER TABLE `responses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stimuli`
--

DROP TABLE IF EXISTS `stimuli`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stimuli` (
  `stimulus_id` int(11) NOT NULL AUTO_INCREMENT,
  `set` int(11) NOT NULL,
  `category1` text,
  `category2` text,
  `subcategory1` text,
  `subcategory2` text,
  `word` text,
  `correct_response` tinytext,
  `instruction` tinytext,
  `mask` bit(1) NOT NULL DEFAULT b'0',
  `order` int(11) DEFAULT NULL,
  `group` int(11) NOT NULL,
  PRIMARY KEY (`stimulus_id`),
  KEY `order` (`order`),
  KEY `group` (`group`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stimuli`
--

LOCK TABLES `stimuli` WRITE;
/*!40000 ALTER TABLE `stimuli` DISABLE KEYS */;
INSERT INTO `stimuli` VALUES (1,1,'cat1','cat 2','subcat1','subcat2','word 1',NULL,'','',1,1),(3,1,'cat1','cat2','subcat1','subcat2','word3',NULL,'','',3,2),(4,1,'cat3','cat4','subcat3','subcat4','word4',NULL,'','',1,2),(6,2,'cat2','','','','',NULL,'','',1,3),(15,2,'','','','','',NULL,'','\0',2,4),(12,2,'cat5','cat6','subcat5','subcat6','blah',NULL,'','\0',3,4),(25,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'\0',NULL,0),(26,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'\0',NULL,0),(27,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'\0',NULL,0);
/*!40000 ALTER TABLE `stimuli` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stimuliGroups`
--

DROP TABLE IF EXISTS `stimuliGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stimuliGroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stimuliSet` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `name` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stimuliSet` (`stimuliSet`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stimuliGroups`
--

LOCK TABLES `stimuliGroups` WRITE;
/*!40000 ALTER TABLE `stimuliGroups` DISABLE KEYS */;
INSERT INTO `stimuliGroups` VALUES (1,1,1,'group1'),(2,1,2,'group2'),(3,2,1,'group1'),(4,2,2,'');
/*!40000 ALTER TABLE `stimuliGroups` ENABLE KEYS */;
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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'2010-11-28 21:46:11'),(2,'2010-11-28 21:52:01'),(3,'2010-11-29 00:14:34'),(4,'2010-11-29 21:24:45'),(5,'2010-11-29 21:27:49'),(6,'2010-11-29 21:34:20'),(7,'2010-11-29 21:34:43'),(8,'2010-11-30 00:32:11'),(9,'2010-12-01 00:22:34'),(10,'2010-12-01 01:06:36'),(11,'2010-12-01 01:08:30'),(12,'2010-12-01 01:10:19'),(13,'2010-12-01 01:10:40'),(14,'2010-12-01 01:11:47'),(15,'2010-12-01 01:18:18'),(16,'2010-12-01 01:18:48'),(17,'2010-12-01 01:25:55'),(18,'2010-12-01 01:30:44'),(19,'2010-12-01 01:33:12'),(20,'2010-12-01 01:33:31'),(21,'2010-12-01 01:37:58'),(22,'2010-12-01 01:39:12'),(23,'2010-12-01 01:42:19'),(24,'2010-12-01 01:43:00'),(25,'2010-12-01 01:43:14'),(26,'2010-12-01 01:44:00'),(27,'2010-12-01 01:45:28'),(28,'2010-12-01 01:47:00'),(29,'2010-12-01 01:59:57'),(30,'2010-12-01 02:03:21'),(31,'2010-12-01 02:05:11'),(32,'2010-12-01 02:10:27'),(33,'2010-12-01 02:14:12'),(34,'2010-12-01 02:21:04'),(35,'2010-12-01 02:21:46'),(36,'2010-12-01 02:26:24'),(37,'2010-12-01 02:29:04'),(38,'2010-12-01 02:30:05'),(39,'2010-12-01 02:30:51'),(40,'2010-12-01 02:31:16'),(41,'2010-12-01 02:43:01'),(42,'2010-12-01 02:43:30'),(43,'2010-12-01 02:44:46'),(44,'2010-12-01 02:45:05'),(45,'2010-12-01 02:48:33'),(46,'2010-12-01 02:50:49'),(47,'2010-12-01 02:51:18'),(48,'2010-12-01 02:51:25'),(49,'2010-12-01 03:14:29'),(50,'2010-12-01 03:15:10'),(51,'2010-12-01 03:18:42'),(52,'2010-12-01 03:19:17'),(53,'2010-12-01 03:27:52'),(54,'2010-12-01 04:06:00'),(55,'2010-12-02 22:31:41'),(56,'2010-12-02 22:35:16'),(57,'2010-12-02 22:36:12'),(58,'2010-12-02 22:37:06'),(59,'2010-12-02 22:38:43'),(60,'2010-12-02 22:39:04'),(61,'2010-12-02 22:39:22'),(62,'2010-12-02 22:42:53'),(63,'2010-12-02 22:43:06'),(64,'2010-12-02 22:43:55'),(65,'2010-12-02 22:44:08'),(66,'2010-12-02 22:44:52'),(67,'2010-12-07 01:12:55'),(68,'2010-12-07 01:15:22'),(69,'2010-12-07 01:16:24'),(70,'2010-12-07 01:18:54'),(71,'2010-12-07 01:19:54'),(72,'2010-12-16 19:57:39'),(73,'2010-12-30 18:17:36');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-01-14 13:34:15
