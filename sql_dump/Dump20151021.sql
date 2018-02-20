-- MySQL dump 10.13  Distrib 5.6.24, for osx10.8 (x86_64)
--
-- Host: 127.0.0.1    Database: recipe_db
-- ------------------------------------------------------
-- Server version	5.5.42

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
-- Table structure for table `recipes`
--

DROP TABLE IF EXISTS `recipes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recipes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` varchar(10000) NOT NULL,
  `country` varchar(250) NOT NULL,
  `userID` int(11) NOT NULL,
  `date_added` timestamp NULL DEFAULT NULL,
  `photo` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_recipes_userID` (`userID`),
  CONSTRAINT `FK_recipes_userID` FOREIGN KEY (`userID`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipes`
--

LOCK TABLES `recipes` WRITE;
/*!40000 ALTER TABLE `recipes` DISABLE KEYS */;
INSERT INTO `recipes` VALUES (9,'Cousous','This is just a tests updates','Nepal',1,NULL,NULL),(10,'Coucous','dsfsfdsf','Germany',1,NULL,NULL),(11,'Eba and Okro Soup','When you want to make this, you follow the following steps:\r\n\r\n1. Do this.\r\n2. Do that.\r\n3. Do this.\r\n4. Do that.','Nigeria',1,'2015-10-20 22:09:49',NULL),(13,'Sample','Last Sample','China',2,'2015-10-20 23:36:32',NULL),(14,'fsdfsf','sdffsdf','France',1,'2015-10-20 23:59:31',''),(15,'Tega is a Food','I want to eat him','Nigeria',1,'2015-10-21 00:03:00','39956025uml2015102102030011112583_10205713949184203_7677062320913306884_n.jpg');
/*!40000 ALTER TABLE `recipes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recipetags`
--

DROP TABLE IF EXISTS `recipetags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recipetags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tagID` int(11) NOT NULL,
  `recipeID` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_recipetags_tagID` (`tagID`),
  KEY `FK_recipetags_recipeID` (`recipeID`),
  CONSTRAINT `FK_recipetags_recipeID` FOREIGN KEY (`recipeID`) REFERENCES `recipes` (`id`),
  CONSTRAINT `FK_recipetags_tagID` FOREIGN KEY (`tagID`) REFERENCES `tags` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipetags`
--

LOCK TABLES `recipetags` WRITE;
/*!40000 ALTER TABLE `recipetags` DISABLE KEYS */;
INSERT INTO `recipetags` VALUES (7,1,9),(8,6,9),(13,1,11),(14,2,11),(15,3,11),(16,6,11),(17,8,11),(23,12,13),(24,1,14),(25,6,15);
/*!40000 ALTER TABLE `recipetags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(250) NOT NULL,
  `userID` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `description_UNIQUE` (`description`),
  KEY `FK_tags_userID` (`userID`),
  CONSTRAINT `FK_tags_userID` FOREIGN KEY (`userID`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES (1,'Chinese',1),(2,'Nigerian',1),(3,'Spicy',1),(4,'Salad',1),(5,'Desert',1),(6,'Appetizer',1),(7,'Vegetarian',1),(8,'Vegetables',1),(11,'Fruits',4),(12,'Chinoises',2);
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` varchar(200) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 means the person is an admin while 0 means the person is not an admin',
  `country` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'tega','$2y$11$V5WZgx/cdwaH9ubjZb.R3u8BMYxuXeFJwZsMHv9ex6atwttIJDUpG',0,'Nigeria'),(2,'lilian','$2y$11$P4Vgd2525/M.sS8JhsbDreUyiTgoLE0Ka86yIsxELsxQAAhyqVNcS',0,'China'),(3,'weibo','$2y$11$mwQSFxgtAfz9xUHSiZELwO6Wi0gCyNlPXRWbmQEzF/UoYGVwx1KyG',0,'China'),(4,'admin','$2y$11$myq/jgcjRR6jHH4xguD9MOPHNA0D4tPxuMjyuGU5lKCu3Zo97mx6S',1,'France'),(5,'samuel','$2y$11$qpBY4uNzqo8cH1huTb4zgeclbySkFpaAMwR3a6.zm1YdEtgPPWEg.',0,'China'),(6,'kasali','$2y$11$VZafGI3eJHDLenGbFCGSR.A2LZFfsSJ8fu1kP.xDFAil/cRjQ1YLC',1,'Nigeria'),(7,'kasali2','$2y$11$MqJJQG/7jnzZUzlgWUxtL.erlk/nQimSvUXa907LabYC5Q/BzAkMm',1,'China');
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

-- Dump completed on 2015-10-21  2:04:18
