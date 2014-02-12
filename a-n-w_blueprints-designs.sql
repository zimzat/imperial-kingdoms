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
-- Table structure for table `armyblueprints`
--

DROP TABLE IF EXISTS `armyblueprints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `armyblueprints` (
  `armyblueprint_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `armydesign_id` int(11) unsigned NOT NULL DEFAULT '0',
  `armyconcept_id` int(10) unsigned NOT NULL DEFAULT '0',
  `round_id` int(11) unsigned NOT NULL DEFAULT '0',
  `kingdom_id` int(11) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(4) unsigned NOT NULL DEFAULT '1',
  `name` varchar(48) NOT NULL DEFAULT '',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `workers` int(11) unsigned NOT NULL DEFAULT '0',
  `energy` int(11) unsigned NOT NULL DEFAULT '0',
  `minerals` int(11) unsigned NOT NULL DEFAULT '0',
  `mineralspread` text NOT NULL,
  `techlevel` tinyint(4) unsigned NOT NULL DEFAULT '1',
  `attack` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `weapons` text NOT NULL,
  `defense` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `armor` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `hull` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `size` int(11) unsigned NOT NULL DEFAULT '0',
  `score` double(20,3) unsigned NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`armyblueprint_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5693 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `armydesigns`
--

DROP TABLE IF EXISTS `armydesigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `armydesigns` (
  `armydesign_id` int(11) NOT NULL AUTO_INCREMENT,
  `armyconcept_id` int(11) NOT NULL DEFAULT '0',
  `round_id` int(11) NOT NULL DEFAULT '0',
  `kingdom_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `workers` int(11) NOT NULL DEFAULT '0',
  `energy` int(11) NOT NULL DEFAULT '0',
  `minerals` int(11) NOT NULL DEFAULT '0',
  `mineralspread` text NOT NULL,
  `techlevel_current` tinyint(4) NOT NULL DEFAULT '1',
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
  PRIMARY KEY (`armydesign_id`),
  KEY `game_id` (`round_id`,`kingdom_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2798 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `navyblueprints`
--

DROP TABLE IF EXISTS `navyblueprints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `navyblueprints` (
  `navyblueprint_id` int(11) NOT NULL AUTO_INCREMENT,
  `navydesign_id` int(11) NOT NULL DEFAULT '0',
  `navyconcept_id` int(10) unsigned NOT NULL DEFAULT '0',
  `round_id` int(11) NOT NULL DEFAULT '0',
  `kingdom_id` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `name` varchar(48) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `workers` int(11) NOT NULL DEFAULT '0',
  `energy` int(11) NOT NULL DEFAULT '0',
  `minerals` int(11) NOT NULL DEFAULT '0',
  `mineralspread` text NOT NULL,
  `techlevel` tinyint(4) NOT NULL DEFAULT '1',
  `attack` tinyint(4) NOT NULL DEFAULT '0',
  `weapons` text NOT NULL,
  `defense` tinyint(4) NOT NULL DEFAULT '0',
  `armor` mediumint(9) NOT NULL DEFAULT '0',
  `hull` mediumint(9) NOT NULL DEFAULT '0',
  `speed` smallint(6) NOT NULL DEFAULT '0',
  `size` int(11) NOT NULL DEFAULT '0',
  `cargo` int(11) NOT NULL DEFAULT '0',
  `engine_id` int(11) NOT NULL DEFAULT '0',
  `score` double(20,3) unsigned NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`navyblueprint_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5905 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `navydesigns`
--

DROP TABLE IF EXISTS `navydesigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `navydesigns` (
  `navydesign_id` int(11) NOT NULL AUTO_INCREMENT,
  `navyconcept_id` int(11) NOT NULL DEFAULT '0',
  `round_id` int(11) NOT NULL DEFAULT '0',
  `kingdom_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `workers` int(11) NOT NULL DEFAULT '0',
  `energy` int(11) NOT NULL DEFAULT '0',
  `minerals` int(11) NOT NULL DEFAULT '0',
  `mineralspread` text NOT NULL,
  `techlevel_current` tinyint(4) NOT NULL DEFAULT '1',
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
  PRIMARY KEY (`navydesign_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2266 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weaponblueprints`
--

DROP TABLE IF EXISTS `weaponblueprints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weaponblueprints` (
  `weaponblueprint_id` int(11) NOT NULL AUTO_INCREMENT,
  `weapondesign_id` int(11) NOT NULL DEFAULT '0',
  `weaponconcept_id` int(10) unsigned NOT NULL DEFAULT '0',
  `round_id` int(11) NOT NULL DEFAULT '0',
  `kingdom_id` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `name` varchar(32) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `workers` int(11) NOT NULL DEFAULT '0',
  `energy` int(11) NOT NULL DEFAULT '0',
  `minerals` int(11) NOT NULL DEFAULT '0',
  `mineralspread` text NOT NULL,
  `techlevel` tinyint(4) NOT NULL DEFAULT '1',
  `targets` text NOT NULL,
  `accuracy` int(11) NOT NULL DEFAULT '0',
  `areadamage` int(11) NOT NULL DEFAULT '0',
  `rateoffire` int(11) NOT NULL DEFAULT '0',
  `power` int(11) NOT NULL DEFAULT '0',
  `damage` int(11) NOT NULL DEFAULT '0',
  `size` int(11) NOT NULL DEFAULT '0',
  `score` double(20,3) unsigned NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`weaponblueprint_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8302 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weapondesigns`
--

DROP TABLE IF EXISTS `weapondesigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weapondesigns` (
  `weapondesign_id` int(11) NOT NULL AUTO_INCREMENT,
  `weaponconcept_id` int(11) NOT NULL DEFAULT '0',
  `round_id` int(11) NOT NULL DEFAULT '0',
  `kingdom_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `workers` int(11) NOT NULL DEFAULT '0',
  `energy` int(11) NOT NULL DEFAULT '0',
  `minerals` int(11) NOT NULL DEFAULT '0',
  `mineralspread` text NOT NULL,
  `techlevel_current` tinyint(4) NOT NULL DEFAULT '1',
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
  PRIMARY KEY (`weapondesign_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3654 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-05-20 11:35:33
