-- MySQL dump 10.13  Distrib 5.5.31, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: imperialkingdoms
-- ------------------------------------------------------
-- Server version	5.5.31-0ubuntu0.12.10.1

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
-- Table structure for table `armyconcepts`
--

DROP TABLE IF EXISTS `armyconcepts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `armyconcepts` (
  `armyconcept_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `workers` int(11) NOT NULL DEFAULT '0',
  `energy` int(11) NOT NULL DEFAULT '0',
  `minerals` int(11) NOT NULL DEFAULT '0',
  `mineralspread` text NOT NULL,
  `techlevel_max` tinyint(4) NOT NULL DEFAULT '20',
  `attack_base` int(11) NOT NULL DEFAULT '0',
  `attack_max` int(11) NOT NULL DEFAULT '0',
  `attack_inc` int(11) NOT NULL DEFAULT '0',
  `attack_per` int(11) NOT NULL DEFAULT '0',
  `attack_size` int(11) NOT NULL DEFAULT '0',
  `attack_sizeinc` int(11) NOT NULL DEFAULT '0',
  `defense_base` int(11) NOT NULL DEFAULT '0',
  `defense_max` int(11) NOT NULL DEFAULT '0',
  `defense_inc` int(11) NOT NULL DEFAULT '0',
  `defense_per` int(11) NOT NULL DEFAULT '0',
  `defense_size` int(11) NOT NULL DEFAULT '0',
  `defense_sizeinc` int(11) NOT NULL DEFAULT '0',
  `armor_base` int(11) NOT NULL DEFAULT '0',
  `armor_max` int(11) NOT NULL DEFAULT '0',
  `armor_inc` int(11) NOT NULL DEFAULT '0',
  `armor_per` int(11) NOT NULL DEFAULT '0',
  `armor_size` int(11) NOT NULL DEFAULT '0',
  `armor_sizeinc` int(11) NOT NULL DEFAULT '0',
  `hull_base` int(11) NOT NULL DEFAULT '0',
  `hull_max` int(11) NOT NULL DEFAULT '0',
  `hull_inc` int(11) NOT NULL DEFAULT '0',
  `hull_per` int(11) NOT NULL DEFAULT '0',
  `hull_size` int(11) NOT NULL DEFAULT '0',
  `hull_sizeinc` int(11) NOT NULL DEFAULT '0',
  `weaponslots` tinyint(4) NOT NULL DEFAULT '0',
  `weaponsperslot` tinyint(4) NOT NULL DEFAULT '0',
  `weaponsload_base` int(11) NOT NULL DEFAULT '0',
  `weaponsload_max` int(11) NOT NULL DEFAULT '0',
  `weaponsload_inc` int(11) NOT NULL DEFAULT '0',
  `weaponsload_per` int(11) NOT NULL DEFAULT '0',
  `weaponsload_size` int(11) NOT NULL DEFAULT '0',
  `weaponsload_sizeinc` int(11) NOT NULL DEFAULT '0',
  `size_base` int(11) NOT NULL DEFAULT '0',
  `size_max` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`armyconcept_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `armyconcepts`
--

LOCK TABLES `armyconcepts` WRITE;
/*!40000 ALTER TABLE `armyconcepts` DISABLE KEYS */;
INSERT INTO `armyconcepts` VALUES (1,'Regular Infantry',170400,900,800,100,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:10;i:7;i:10;i:0;i:20;}',20,60,100,0,10,6,0,60,100,0,10,6,0,1,8,1,0,6,0,20,66,0,20,5,0,2,1,20,130,1,25,6,0,100,300),(2,'Assault droid',133800,870,1250,400,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:10;i:7;i:10;i:0;i:20;}',20,50,94,0,10,5,0,40,80,0,10,5,0,1,13,1,15,5,0,50,300,0,20,5,0,4,1,50,300,0,20,5,0,300,750),(3,'Halftrack APC',300000,1670,2300,3000,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:10;i:7;i:10;i:0;i:20;}',20,30,74,0,15,7,0,30,65,0,15,7,0,2,15,1,17,7,0,150,530,0,20,7,0,6,2,200,1000,0,20,7,0,1000,3750),(4,'Light tank',390000,5000,5500,5700,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:10;i:7;i:10;i:0;i:20;}',20,25,73,0,20,0,25,20,46,0,20,0,25,5,27,2,18,7,0,600,1800,0,15,7,0,4,2,1000,3500,0,20,7,0,5000,19000),(5,'Heavy tank',660000,13000,12000,15000,'a:8:{i:1;i:25;i:2;i:25;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:5;i:7;i:5;i:0;i:10;}',20,10,89,3,20,0,100,5,28,3,25,0,100,15,96,2,21,3,0,3000,8000,3,15,3,0,6,2,10000,19400,0,10,3,0,50000,100000),(6,'Stationary Artillery',324000,1900,1750,4000,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:10;i:7;i:10;i:0;i:20;}',20,43,88,2,25,0,50,4,20,2,20,0,70,1,10,1,19,2,0,300,600,0,20,2,0,3,3,784,21000,0,40,26,0,1500,25000),(7,'Medium Tank',522000,8500,7400,8000,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:10;i:7;i:10;i:0;i:20;}',20,20,70,2,15,0,50,10,36,2,20,0,50,10,56,1,20,5,0,2000,3540,0,10,5,0,4,2,5000,9700,0,10,5,0,20000,51000),(8,'Bunker',516000,7600,8500,6500,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:10;i:7;i:10;i:0;i:20;}',20,39,80,2,25,1,0,8,15,2,20,1,0,5,18,0,25,5,0,1000,9000,0,25,18,0,3,3,345,5500,0,20,5,0,2000,30000);
/*!40000 ALTER TABLE `armyconcepts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `navyconcepts`
--

DROP TABLE IF EXISTS `navyconcepts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `navyconcepts` (
  `navyconcept_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `workers` int(11) NOT NULL DEFAULT '0',
  `energy` int(11) NOT NULL DEFAULT '0',
  `minerals` int(11) NOT NULL DEFAULT '0',
  `mineralspread` text NOT NULL,
  `techlevel_max` tinyint(4) NOT NULL DEFAULT '20',
  `attack_base` int(11) NOT NULL DEFAULT '0',
  `attack_max` int(11) NOT NULL DEFAULT '0',
  `attack_inc` int(11) NOT NULL DEFAULT '0',
  `attack_per` int(11) NOT NULL DEFAULT '0',
  `attack_size` int(11) NOT NULL DEFAULT '0',
  `attack_sizeinc` int(11) NOT NULL DEFAULT '0',
  `defense_base` int(11) NOT NULL DEFAULT '0',
  `defense_max` int(11) NOT NULL DEFAULT '0',
  `defense_inc` int(11) NOT NULL DEFAULT '0',
  `defense_per` int(11) NOT NULL DEFAULT '0',
  `defense_size` int(11) NOT NULL DEFAULT '0',
  `defense_sizeinc` int(11) NOT NULL DEFAULT '0',
  `armor_base` int(11) NOT NULL DEFAULT '0',
  `armor_max` int(11) NOT NULL DEFAULT '0',
  `armor_inc` int(11) NOT NULL DEFAULT '0',
  `armor_per` int(11) NOT NULL DEFAULT '0',
  `armor_size` int(11) NOT NULL DEFAULT '0',
  `armor_sizeinc` int(11) NOT NULL DEFAULT '0',
  `hull_base` int(11) NOT NULL DEFAULT '0',
  `hull_max` int(11) NOT NULL DEFAULT '0',
  `hull_inc` int(11) NOT NULL DEFAULT '0',
  `hull_per` int(11) NOT NULL DEFAULT '0',
  `hull_size` int(11) NOT NULL DEFAULT '0',
  `hull_sizeinc` int(11) NOT NULL DEFAULT '0',
  `weaponslots` tinyint(4) NOT NULL DEFAULT '0',
  `weaponsperslot` tinyint(4) NOT NULL DEFAULT '0',
  `weaponsload_base` int(11) NOT NULL DEFAULT '0',
  `weaponsload_max` int(11) NOT NULL DEFAULT '0',
  `weaponsload_inc` int(11) NOT NULL DEFAULT '0',
  `weaponsload_per` int(11) NOT NULL DEFAULT '0',
  `weaponsload_size` int(11) NOT NULL DEFAULT '0',
  `weaponsload_sizeinc` int(11) NOT NULL DEFAULT '0',
  `size_base` int(11) NOT NULL DEFAULT '0',
  `size_max` int(11) NOT NULL DEFAULT '0',
  `cargo_base` int(11) NOT NULL DEFAULT '0',
  `cargo_max` int(11) NOT NULL DEFAULT '0',
  `cargo_inc` int(11) NOT NULL DEFAULT '0',
  `cargo_per` int(11) NOT NULL DEFAULT '0',
  `cargo_size` int(11) NOT NULL DEFAULT '0',
  `cargo_sizeinc` int(11) NOT NULL DEFAULT '0',
  `speed_base` int(11) NOT NULL DEFAULT '0',
  `speed_max` int(11) NOT NULL DEFAULT '0',
  `speed_inc` int(11) NOT NULL DEFAULT '0',
  `speed_per` int(11) NOT NULL DEFAULT '0',
  `speed_size` int(11) NOT NULL DEFAULT '0',
  `speed_sizeinc` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`navyconcept_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `navyconcepts`
--

LOCK TABLES `navyconcepts` WRITE;
/*!40000 ALTER TABLE `navyconcepts` DISABLE KEYS */;
INSERT INTO `navyconcepts` VALUES (1,'Transporter',162000,900,850,1800,'a:8:{i:1;i:15;i:2;i:30;i:3;i:20;i:4;i:10;i:5;i:20;i:6;i:20;i:7;i:5;i:0;i:5;}',20,3,78,2,21,0,1500,3,78,2,21,0,1000,2,160,4,20,0,0,1000,8700,100,20,0,0,4,1,1000,2000,10,20,0,0,40000,70000000,16000,50000000,30000,45,42,60000,1,200,10,16,0,7500),(2,'Fighter',732000,9500,7000,10000,'a:8:{i:1;i:15;i:2;i:30;i:3;i:20;i:4;i:10;i:5;i:20;i:6;i:15;i:7;i:10;i:0;i:15;}',20,30,70,0,20,0,310,51,90,0,20,0,420,3,63,2,20,6,0,500,3700,0,25,6,0,2,2,1000,9200,1,25,6,0,10000,100000,200,1120,100,10,6,850,116,220,20,25,0,250),(3,'Heavy Fighter',1020000,13000,10000,14000,'a:8:{i:1;i:15;i:2;i:30;i:3;i:20;i:4;i:10;i:5;i:20;i:6;i:15;i:7;i:10;i:0;i:15;}',20,33,75,0,20,0,2510,20,80,0,20,0,3520,5,94,2,20,6,0,2500,10000,0,15,6,0,3,2,5000,46000,1,25,6,0,100000,500000,1000,9500,200,15,6,750,72,220,13,22,0,4250),(4,'Corvette',1320000,17000,21000,20000,'a:8:{i:1;i:15;i:2;i:30;i:3;i:20;i:4;i:10;i:5;i:20;i:6;i:15;i:7;i:10;i:0;i:15;}',20,21,80,0,20,0,12000,15,75,0,20,0,17220,14,150,2,20,2,0,5000,78000,100,25,2,0,4,2,10000,93000,1,25,2,0,500000,1000000,5000,40000,200,15,2,950,54,250,10,20,0,14550),(5,'Destroyer',1608000,25000,30000,30000,'a:8:{i:1;i:15;i:2;i:30;i:3;i:20;i:4;i:10;i:5;i:20;i:6;i:15;i:7;i:10;i:0;i:15;}',20,25,80,1,22,0,25000,10,70,1,22,0,30000,15,202,2,20,5,0,50000,220000,100,12,5,0,4,4,30000,158000,200,16,5,0,1000000,4000000,40000,100000,200,14,5,150,50,300,10,16,0,35000),(6,'Frigate',1920000,34000,40000,40000,'a:8:{i:1;i:15;i:2;i:30;i:3;i:20;i:4;i:10;i:5;i:20;i:6;i:15;i:7;i:10;i:0;i:15;}',20,25,85,1,22,0,40000,10,60,1,22,0,20000,20,244,2,20,4,0,300000,757000,100,8,4,0,5,4,50000,300000,200,16,4,0,4000000,9000000,50000,165000,200,14,4,150,35,250,10,16,0,45000),(7,'Cruiser',2346000,60000,50000,55000,'a:8:{i:1;i:15;i:2;i:30;i:3;i:20;i:4;i:10;i:5;i:20;i:6;i:15;i:7;i:10;i:0;i:15;}',20,27,90,2,21,0,57000,6,55,2,21,0,90000,25,350,2,20,4,0,800000,1600000,100,6,4,0,5,10,100000,735000,200,18,4,0,9000000,20000000,100000,238000,200,10,4,20150,35,300,10,16,0,60000),(8,'Battleship',2640000,80000,90000,70000,'a:8:{i:1;i:15;i:2;i:30;i:3;i:20;i:4;i:10;i:5;i:20;i:6;i:15;i:7;i:5;i:0;i:15;}',20,34,100,2,21,0,300000,6,50,2,21,0,400000,30,500,4,20,7,0,1000000,8800000,100,20,7,0,6,20,600000,12200000,120000,20,7,0,20000000,100000000,150000,778000,200,20,7,0,11,220,10,16,0,700000),(9,'Orbital defense platform',300000,5000,2800,2300,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:10;i:7;i:10;i:0;i:20;}',20,43,90,0,20,0,12000,5,40,3,20,0,17220,8,50,2,20,2,0,1000,15000,100,25,2,0,5,5,15627,70000,1,25,2,0,500000,1000000,0,0,200,15,2,950,0,0,10,20,0,14550),(10,'Space Station',1710000,80000,70000,60000,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:10;i:7;i:10;i:0;i:20;}',20,43,100,2,21,3,0,9,45,2,21,3,0,100,825,4,16,3,0,3000000,20000000,1000,16,3,0,6,70,3500000,75000000,220000,25,3,0,125000000,250000000,0,0,200,20,3,0,0,0,10,16,3,0);
/*!40000 ALTER TABLE `navyconcepts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weaponconcepts`
--

DROP TABLE IF EXISTS `weaponconcepts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weaponconcepts` (
  `weaponconcept_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `workers` int(11) NOT NULL DEFAULT '0',
  `energy` int(11) NOT NULL DEFAULT '0',
  `minerals` int(11) NOT NULL DEFAULT '0',
  `mineralspread` text NOT NULL,
  `techlevel_max` tinyint(4) NOT NULL DEFAULT '20',
  `accuracy_base` int(11) NOT NULL DEFAULT '0',
  `accuracy_max` int(11) NOT NULL DEFAULT '0',
  `accuracy_inc` int(11) NOT NULL DEFAULT '0',
  `accuracy_per` int(11) NOT NULL DEFAULT '0',
  `accuracy_size` int(11) NOT NULL DEFAULT '0',
  `accuracy_sizeinc` int(11) NOT NULL DEFAULT '0',
  `areadamage_base` int(11) NOT NULL DEFAULT '0',
  `areadamage_max` int(11) NOT NULL DEFAULT '0',
  `areadamage_inc` int(11) NOT NULL DEFAULT '0',
  `areadamage_per` int(11) NOT NULL DEFAULT '0',
  `areadamage_size` int(11) NOT NULL DEFAULT '0',
  `areadamage_sizeinc` int(11) NOT NULL DEFAULT '0',
  `rateoffire_base` int(11) NOT NULL DEFAULT '0',
  `rateoffire_max` int(11) NOT NULL DEFAULT '0',
  `rateoffire_inc` int(11) NOT NULL DEFAULT '0',
  `rateoffire_per` int(11) NOT NULL DEFAULT '0',
  `rateoffire_size` int(11) NOT NULL DEFAULT '0',
  `rateoffire_sizeinc` int(11) NOT NULL DEFAULT '0',
  `power_base` int(11) NOT NULL DEFAULT '0',
  `power_max` int(11) NOT NULL DEFAULT '0',
  `power_inc` int(11) NOT NULL DEFAULT '0',
  `power_per` int(11) NOT NULL DEFAULT '0',
  `power_size` int(11) NOT NULL DEFAULT '0',
  `power_sizeinc` int(11) NOT NULL DEFAULT '0',
  `damage_base` int(11) NOT NULL DEFAULT '0',
  `damage_max` int(11) NOT NULL DEFAULT '0',
  `damage_inc` int(11) NOT NULL DEFAULT '0',
  `damage_per` int(11) NOT NULL DEFAULT '0',
  `damage_size` int(11) NOT NULL DEFAULT '0',
  `damage_sizeinc` int(11) NOT NULL DEFAULT '0',
  `size_base` int(11) NOT NULL DEFAULT '0',
  `size_max` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`weaponconcept_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weaponconcepts`
--

LOCK TABLES `weaponconcepts` WRITE;
/*!40000 ALTER TABLE `weaponconcepts` DISABLE KEYS */;
INSERT INTO `weaponconcepts` VALUES (1,'Laser Rifle',233400,1100,1950,2600,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:10;i:7;i:10;i:0;i:20;}',20,35,100,1,19,0,3,1,1,1,0,0,5,1,5,1,0,0,5,4,26,1,20,8,0,1,9,1,15,8,0,80,1470),(2,'SMG',360000,5000,4500,5000,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:10;i:7;i:10;i:0;i:20;}',20,11,100,2,22,0,3,1,1,1,0,0,5,11,20,1,0,5,1,3,14,1,20,5,1,1,8,1,15,5,1,70,280),(3,'Blaster',233400,1950,1100,2600,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:10;i:7;i:10;i:0;i:20;}',20,25,56,1,20,0,1,1,1,1,0,0,5,1,3,1,0,0,5,4,18,1,18,10,1,2,11,1,15,10,1,15,1300),(4,'LMG',420000,6000,6500,7000,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:10;i:7;i:10;i:0;i:20;}',20,10,100,2,20,0,3,1,1,1,0,0,5,14,25,1,7,4,1,4,17,1,20,4,1,2,11,1,15,4,1,150,470),(5,'HMG',468000,7500,8000,9000,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:20;i:6;i:5;i:7;i:5;i:0;i:20;}',20,10,100,3,23,0,5,1,1,1,0,0,5,17,57,1,12,5,1,6,27,2,20,5,1,2,34,2,20,5,1,450,1650),(6,'Flame thrower',492000,8000,8500,9500,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:5;i:7;i:5;i:0;i:30;}',20,10,100,3,23,0,10,4,20,2,0,5,5,1,3,1,9,5,1,5,11,1,20,5,1,33,350,5,25,5,1,400,1900),(7,'Mortar',540000,8500,9000,10000,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:20;i:6;i:15;i:7;i:15;i:0;i:30;}',20,11,100,3,23,0,15,5,23,3,0,6,5,1,3,1,9,6,1,8,31,1,17,6,1,11,170,2,24,6,1,500,2500),(8,'RPG Launcher',600000,10000,9500,12000,'a:8:{i:1;i:15;i:2;i:15;i:3;i:20;i:4;i:20;i:5;i:10;i:6;i:5;i:7;i:5;i:0;i:10;}',20,25,100,1,21,0,5,1,1,1,0,4,5,1,1,1,9,4,1,15,44,1,17,4,0,8,26,1,18,4,0,250,1600),(9,'Phase Cannon',690000,3000,5000,8900,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:15;i:7;i:5;i:0;i:20;}',20,29,100,3,25,0,2,2,5,1,19,3,5,3,7,1,19,3,1,15,100,1,18,3,1,20,172,1,20,3,1,5000,10000),(10,'Bolt Cannon',660000,9000,12000,10000,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:15;i:7;i:5;i:0;i:20;}',20,23,100,2,20,0,2,2,8,2,19,3,5,2,8,2,19,3,1,20,160,1,25,3,1,25,290,1,20,3,1,10000,20000),(11,'Pulse Cannon',780000,10000,14000,13000,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:15;i:7;i:5;i:0;i:20;}',20,50,100,1,27,0,2,1,7,1,19,2,5,1,7,1,19,2,1,30,300,1,25,2,1,20,200,1,20,2,1,18000,30000),(12,'Proton Torpedo',870000,13000,16000,15600,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:15;i:7;i:5;i:0;i:20;}',20,60,100,10,0,2,1,1,1,1,0,0,5,1,1,1,19,1,1,30,290,10,20,2,1,40,490,10,20,2,1,30000,50000),(13,'Gatling CIWS',900000,14000,17000,19000,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:15;i:7;i:5;i:0;i:20;}',20,14,100,2,20,0,50,1,1,1,0,0,5,37,350,1,22,2,1,15,60,1,17,2,1,10,60,1,17,2,1,50000,78000),(14,'Autocannon',1050000,30000,26000,30000,'a:8:{i:1;i:15;i:2;i:15;i:3;i:10;i:4;i:10;i:5;i:10;i:6;i:15;i:7;i:5;i:0;i:20;}',20,25,100,2,19,0,500,1,3,2,0,2,5,3,40,2,20,2,1,35,230,1,20,2,1,30,200,1,20,2,1,70000,130000),(15,'Disruptor Beam',1380000,40000,50000,50000,'a:8:{i:1;i:15;i:2;i:15;i:3;i:15;i:4;i:15;i:5;i:15;i:6;i:15;i:7;i:5;i:0;i:5;}',20,50,100,10,0,0,5000,2,7,1,0,2,5,2,7,1,19,2,1,70,800,5,20,2,1,35,410,5,20,2,1,90000,150000),(16,'Blast Cannon',1188000,27000,21000,26000,'a:8:{i:1;i:5;i:2;i:20;i:3;i:20;i:4;i:20;i:5;i:20;i:6;i:10;i:7;i:25;i:0;i:5;}',20,40,100,10,0,0,5000,2,21,2,10,2,5,2,21,2,10,2,1,50,467,10,15,2,1,65,533,2,10,2,1,140000,250000),(17,'Turbolaser',1320000,30000,23000,30000,'a:8:{i:1;i:5;i:2;i:20;i:3;i:20;i:4;i:20;i:5;i:20;i:6;i:30;i:7;i:5;i:0;i:5;}',20,60,100,10,0,0,2000,1,5,1,0,5,5,3,23,2,10,5,1,100,1144,25,16,5,1,80,1000,50,22,5,1,200000,600000),(18,'Meson Cannon',1440000,25000,35000,35000,'a:8:{i:1;i:5;i:2;i:20;i:3;i:20;i:4;i:20;i:5;i:20;i:6;i:30;i:7;i:5;i:0;i:5;}',20,24,100,5,19,0,3000,2,10,2,0,3,5,2,10,2,0,2,1,200,765,4,15,3,1,300,5600,50,25,3,1,450000,900000),(19,'Hyperon Cannon',1620000,30000,40000,40000,'a:8:{i:1;i:5;i:2;i:20;i:3;i:20;i:4;i:20;i:5;i:20;i:6;i:30;i:7;i:5;i:0;i:5;}',20,24,100,5,19,0,6000,10,63,3,19,2,5,15,170,4,19,2,1,300,702,0,10,2,1,40,1460,50,25,2,1,800000,1500000),(20,'Fusion Cannon',1812000,45000,60000,60000,'a:8:{i:1;i:5;i:2;i:20;i:3;i:20;i:4;i:20;i:5;i:20;i:6;i:30;i:7;i:5;i:0;i:5;}',20,38,100,5,19,0,9000,2,10,2,0,2,5,2,10,2,0,2,1,200,765,4,15,2,1,500,15000,50,25,2,1,1000000,1700000),(21,'Antiproton Cannon',2100000,68000,80000,90000,'a:8:{i:1;i:5;i:2;i:20;i:3;i:20;i:4;i:20;i:5;i:10;i:6;i:30;i:7;i:15;i:0;i:5;}',20,38,100,5,19,0,15000,1,3,1,0,1,5,2,6,1,19,1,1,300,2000,51,10,1,1,1500,30000,200,25,1,1,1200000,2000000);
/*!40000 ALTER TABLE `weaponconcepts` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-05-20 11:33:25
