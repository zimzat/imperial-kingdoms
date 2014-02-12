-- MySQL dump 10.13  Distrib 5.5.35, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: imperialkingdoms
-- ------------------------------------------------------
-- Server version	5.5.35-0ubuntu0.12.10.1

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
-- Table structure for table `armygroups`
--

DROP TABLE IF EXISTS `armygroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `armygroups` (
  `armygroup_id` int(11) NOT NULL AUTO_INCREMENT,
  `round_id` int(11) NOT NULL DEFAULT '0',
  `kingdom_id` int(11) NOT NULL DEFAULT '0',
  `player_id` int(11) NOT NULL DEFAULT '0',
  `planet_id` int(11) NOT NULL DEFAULT '0',
  `navygroup_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL DEFAULT '',
  `units` text NOT NULL,
  `targets` text NOT NULL,
  `size` bigint(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`armygroup_id`),
  KEY `game_id` (`round_id`,`kingdom_id`,`planet_id`),
  KEY `game_id_2` (`round_id`,`kingdom_id`,`navygroup_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33765 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `buildings`
--

DROP TABLE IF EXISTS `buildings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `buildings` (
  `building_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `image` varchar(32) NOT NULL DEFAULT '',
  `foodrate` int(11) NOT NULL DEFAULT '0',
  `workersrate` int(11) NOT NULL DEFAULT '0',
  `energyrate` int(11) NOT NULL DEFAULT '0',
  `mineralsrate` int(11) NOT NULL DEFAULT '0',
  `workers` int(11) unsigned NOT NULL DEFAULT '0',
  `energy` int(11) unsigned NOT NULL DEFAULT '0',
  `minerals` int(11) unsigned NOT NULL DEFAULT '0',
  `mineralspread` text NOT NULL,
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `maxbuildable` int(11) unsigned NOT NULL DEFAULT '0',
  `demolishable` tinyint(4) NOT NULL DEFAULT '0',
  `features` text NOT NULL,
  `score` double(20,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`building_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `combat`
--

DROP TABLE IF EXISTS `combat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `combat` (
  `combat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `round_id` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_id` int(10) unsigned NOT NULL DEFAULT '0',
  `rounds` int(10) unsigned NOT NULL DEFAULT '0',
  `scores` text NOT NULL,
  `completion` double(15,4) NOT NULL DEFAULT '0.0000',
  `beingupdated` double(15,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`combat_id`),
  KEY `round_id` (`round_id`),
  KEY `planet_id` (`planet_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22616 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `combatreports`
--

DROP TABLE IF EXISTS `combatreports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `combatreports` (
  `combatreport_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `round_id` int(10) unsigned NOT NULL DEFAULT '0',
  `kingdom_id` int(10) unsigned NOT NULL DEFAULT '0',
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` enum('0','1','2') NOT NULL DEFAULT '0',
  `report` blob NOT NULL,
  `date` double(15,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`combatreport_id`),
  KEY `combatlisting` (`round_id`,`kingdom_id`,`date`,`planet_id`,`player_id`),
  KEY `dupescan` (`planet_id`,`date`,`combatreport_id`),
  KEY `kingdom_id` (`kingdom_id`)
) ENGINE=InnoDB AUTO_INCREMENT=156275 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `concepts`
--

DROP TABLE IF EXISTS `concepts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `concepts` (
  `concept_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `image` varchar(32) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  `grants` text NOT NULL,
  `workers` int(11) NOT NULL DEFAULT '0',
  `energy` int(11) NOT NULL DEFAULT '0',
  `minerals` int(11) NOT NULL DEFAULT '0',
  `mineralspread` text NOT NULL,
  `score` double(20,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`concept_id`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `errorlog`
--

DROP TABLE IF EXISTS `errorlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `errorlog` (
  `error_id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `file` varchar(255) NOT NULL DEFAULT '',
  `line` smallint(6) NOT NULL DEFAULT '0',
  `type` varchar(64) NOT NULL DEFAULT '',
  `backtrace` text NOT NULL,
  `remote_address` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`error_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1949 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forum_messages`
--

DROP TABLE IF EXISTS `forum_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_messages` (
  `forum_message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `round_id` int(10) unsigned NOT NULL DEFAULT '0',
  `forum_topic_id` int(10) unsigned NOT NULL DEFAULT '0',
  `kingdom_id` int(10) unsigned NOT NULL DEFAULT '0',
  `poster_id` int(10) unsigned NOT NULL DEFAULT '0',
  `posttime` double(15,4) DEFAULT NULL,
  `message` text NOT NULL,
  `marked` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`forum_message_id`),
  KEY `game_id` (`round_id`,`forum_topic_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12118 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forum_topics`
--

DROP TABLE IF EXISTS `forum_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_topics` (
  `forum_topic_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `round_id` int(10) unsigned NOT NULL DEFAULT '0',
  `kingdom_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lastposter_id` int(10) unsigned NOT NULL DEFAULT '0',
  `replies` int(10) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(64) NOT NULL DEFAULT '',
  `time_lastpost` double(15,4) DEFAULT NULL,
  PRIMARY KEY (`forum_topic_id`),
  KEY `kingdom_id` (`round_id`,`kingdom_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1990 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game_options`
--

DROP TABLE IF EXISTS `game_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_options` (
  `option` varchar(32) NOT NULL DEFAULT '',
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `group_units`
--

DROP TABLE IF EXISTS `group_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_units` (
  `group_unit_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_type` enum('army','navy') NOT NULL DEFAULT 'army',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0',
  `blueprint_id` int(10) unsigned NOT NULL DEFAULT '0',
  `count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_unit_id`),
  KEY `group_type` (`group_type`,`group_id`,`blueprint_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `guide`
--

DROP TABLE IF EXISTS `guide`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guide` (
  `guide_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_guide_id` int(10) unsigned NOT NULL DEFAULT '0',
  `shadow_guide_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(40) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`guide_id`),
  KEY `parent_guide_id` (`parent_guide_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `guide_submissions`
--

DROP TABLE IF EXISTS `guide_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guide_submissions` (
  `guide_submission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guide_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(40) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  PRIMARY KEY (`guide_submission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kingdoms`
--

DROP TABLE IF EXISTS `kingdoms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kingdoms` (
  `kingdom_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `round_id` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `image` varchar(32) NOT NULL DEFAULT '0-0.gif',
  `buildings` text NOT NULL,
  `units` text NOT NULL,
  `concepts` text NOT NULL,
  `researched` text NOT NULL,
  `planets` text NOT NULL,
  `allies` text NOT NULL,
  `enemies` text NOT NULL,
  `food` int(11) unsigned NOT NULL DEFAULT '0',
  `foodrate` int(11) NOT NULL DEFAULT '0',
  `workers` int(11) unsigned NOT NULL DEFAULT '0',
  `workersrate` int(11) NOT NULL DEFAULT '0',
  `energy` int(11) unsigned NOT NULL DEFAULT '0',
  `energyrate` int(11) NOT NULL DEFAULT '0',
  `minerals` int(10) unsigned NOT NULL DEFAULT '0',
  `mineralsrate` int(11) NOT NULL DEFAULT '0',
  `score` double(20,3) unsigned NOT NULL DEFAULT '0.000',
  `score_peak` double(20,3) unsigned NOT NULL DEFAULT '0.000',
  `last_active` double(15,4) unsigned NOT NULL DEFAULT '0.0000',
  `members` text NOT NULL,
  `starting_starsystem_id` int(10) unsigned NOT NULL DEFAULT '0',
  `code` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`kingdom_id`),
  KEY `game` (`round_id`),
  KEY `kingdom_name` (`name`(10))
) ENGINE=InnoDB AUTO_INCREMENT=3063 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `round_id` int(10) unsigned NOT NULL DEFAULT '0',
  `kingdom_id` int(10) unsigned NOT NULL DEFAULT '0',
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `logtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` varchar(32) NOT NULL DEFAULT '',
  `log` longtext NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2133 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mail`
--

DROP TABLE IF EXISTS `mail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail` (
  `mail_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mailbox_id` int(10) unsigned NOT NULL DEFAULT '0',
  `round_id` int(11) unsigned NOT NULL DEFAULT '0',
  `to_player_id` int(11) unsigned NOT NULL DEFAULT '0',
  `from_player_id` int(11) unsigned NOT NULL DEFAULT '0',
  `status` enum('0','1','2') NOT NULL DEFAULT '1',
  `time` double(15,4) unsigned NOT NULL DEFAULT '0.0000',
  `subject` varchar(120) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  PRIMARY KEY (`mail_id`),
  KEY `toplayer` (`round_id`,`to_player_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=22056 DEFAULT CHARSET=latin1;
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
-- Table structure for table `navygroups`
--

DROP TABLE IF EXISTS `navygroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `navygroups` (
  `navygroup_id` int(11) NOT NULL AUTO_INCREMENT,
  `round_id` int(11) NOT NULL DEFAULT '0',
  `kingdom_id` int(11) NOT NULL DEFAULT '0',
  `player_id` int(11) NOT NULL DEFAULT '0',
  `planet_id` int(10) unsigned NOT NULL DEFAULT '0',
  `x_current` smallint(6) NOT NULL DEFAULT '0',
  `y_current` smallint(6) NOT NULL DEFAULT '0',
  `x_destination` smallint(6) NOT NULL DEFAULT '0',
  `y_destination` smallint(6) NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL DEFAULT '',
  `units` text NOT NULL,
  `targets` text NOT NULL,
  `cargo` text NOT NULL,
  `cargo_current` bigint(11) unsigned NOT NULL DEFAULT '0',
  `cargo_max` bigint(11) unsigned NOT NULL DEFAULT '0',
  `size` bigint(11) unsigned NOT NULL DEFAULT '0',
  `speed` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`navygroup_id`),
  KEY `planet_id` (`planet_id`,`kingdom_id`),
  KEY `x_destination` (`x_destination`,`y_destination`,`x_current`,`y_current`)
) ENGINE=InnoDB AUTO_INCREMENT=44461 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `news_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `round_id` int(10) unsigned NOT NULL DEFAULT '0',
  `kingdom_id` int(10) unsigned NOT NULL DEFAULT '0',
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` int(1) unsigned NOT NULL DEFAULT '0',
  `posted` double(15,4) unsigned NOT NULL DEFAULT '0.0000',
  `subject` varchar(72) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46660 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `news_entries`
--

DROP TABLE IF EXISTS `news_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news_entries` (
  `news_entry_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `entry` text NOT NULL,
  `lastmodified` double(15,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`news_entry_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `permission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `round_id` int(10) unsigned NOT NULL DEFAULT '0',
  `owner_id` int(10) unsigned NOT NULL DEFAULT '0',
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` enum('0','1','2','3') NOT NULL DEFAULT '0',
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `research` enum('0','1') NOT NULL DEFAULT '0',
  `build` enum('0','1') NOT NULL DEFAULT '0',
  `commission` enum('0','1') NOT NULL DEFAULT '0',
  `military` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`permission_id`),
  KEY `player_id` (`player_id`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=6468 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `planets`
--

DROP TABLE IF EXISTS `planets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `planets` (
  `planet_id` int(11) NOT NULL AUTO_INCREMENT,
  `round_id` int(11) NOT NULL DEFAULT '0',
  `quadrant_id` int(11) NOT NULL DEFAULT '0',
  `starsystem_id` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `x` tinyint(4) NOT NULL DEFAULT '0',
  `y` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL DEFAULT '',
  `player_id` int(11) NOT NULL DEFAULT '0',
  `kingdom_id` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `buildings` text NOT NULL,
  `cranes` tinyint(4) NOT NULL DEFAULT '1',
  `planning` tinyint(4) NOT NULL DEFAULT '1',
  `researching` tinyint(4) NOT NULL DEFAULT '0',
  `buildingbonus` int(11) NOT NULL DEFAULT '0',
  `researchbonus` tinyint(4) NOT NULL DEFAULT '0',
  `warptime_construction` double(15,4) unsigned NOT NULL DEFAULT '0.0000',
  `warptime_research` double(15,4) unsigned NOT NULL DEFAULT '0.0000',
  `production` text NOT NULL,
  `units` text NOT NULL,
  `food` int(11) unsigned NOT NULL DEFAULT '0',
  `foodrate` int(11) NOT NULL DEFAULT '0',
  `workers` int(11) unsigned NOT NULL DEFAULT '0',
  `workersrate` int(11) NOT NULL DEFAULT '0',
  `energy` int(11) unsigned NOT NULL DEFAULT '0',
  `energyrate` int(11) NOT NULL DEFAULT '0',
  `minerals` text NOT NULL,
  `mineralsrate` int(11) NOT NULL DEFAULT '0',
  `mineralsremaining` text NOT NULL,
  `extractionrates` text NOT NULL,
  `resistance` int(1) unsigned NOT NULL DEFAULT '0',
  `score` double(20,3) unsigned NOT NULL DEFAULT '0.000',
  `score_peak` double(20,3) unsigned NOT NULL DEFAULT '0.000',
  `lastupdated` double(15,4) NOT NULL DEFAULT '0.0000',
  `code` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`planet_id`),
  KEY `game_id` (`round_id`,`kingdom_id`,`player_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12938 DEFAULT CHARSET=latin1 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `players`
--

DROP TABLE IF EXISTS `players`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `players` (
  `player_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `round_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `npc` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(40) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `image` varchar(32) NOT NULL DEFAULT '0-0.gif',
  `kingdom_id` int(11) unsigned NOT NULL DEFAULT '0',
  `planet_current` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_permission_current` int(10) unsigned NOT NULL DEFAULT '0',
  `planets` text NOT NULL,
  `planets_permissions` text NOT NULL,
  `rank` int(11) NOT NULL DEFAULT '100',
  `warptime` double(15,4) NOT NULL DEFAULT '0.0000',
  `score` double(20,3) unsigned NOT NULL DEFAULT '0.000',
  `score_peak` double(20,3) unsigned NOT NULL DEFAULT '0.000',
  `lastactive` double(15,4) NOT NULL DEFAULT '0.0000',
  `mail` int(1) NOT NULL DEFAULT '0',
  `forum` int(1) NOT NULL DEFAULT '0',
  `combat` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`player_id`),
  KEY `game` (`round_id`,`user_id`),
  KEY `player_name` (`name`(10))
) ENGINE=InnoDB AUTO_INCREMENT=3464 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `propositions`
--

DROP TABLE IF EXISTS `propositions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `propositions` (
  `proposition_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `round_id` int(11) NOT NULL DEFAULT '0',
  `kingdom_id` int(11) NOT NULL DEFAULT '0',
  `player_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(30) NOT NULL DEFAULT '',
  `statement` text NOT NULL,
  `status` enum('0','1','2','3') NOT NULL DEFAULT '0',
  `voted` varchar(255) NOT NULL DEFAULT '',
  `for` tinyint(4) NOT NULL DEFAULT '0',
  `against` tinyint(4) NOT NULL DEFAULT '0',
  `neutral` tinyint(4) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `storage` mediumblob NOT NULL,
  `target_id` int(11) NOT NULL DEFAULT '0',
  `expires` double(15,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`proposition_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4607 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quadrants`
--

DROP TABLE IF EXISTS `quadrants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quadrants` (
  `quadrant_id` int(11) NOT NULL AUTO_INCREMENT,
  `round_id` int(11) NOT NULL DEFAULT '0',
  `x` tinyint(4) NOT NULL DEFAULT '0',
  `y` tinyint(4) NOT NULL DEFAULT '0',
  `active` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`quadrant_id`),
  KEY `game_id` (`round_id`,`quadrant_id`),
  KEY `game_id_2` (`round_id`,`x`,`y`)
) ENGINE=InnoDB AUTO_INCREMENT=1324 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rounds`
--

DROP TABLE IF EXISTS `rounds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rounds` (
  `round_id` int(11) NOT NULL AUTO_INCREMENT,
  `public` tinyint(4) NOT NULL DEFAULT '1',
  `round_engine` varchar(32) NOT NULL DEFAULT 'development',
  `combat_engine` varchar(32) NOT NULL DEFAULT 'x^2',
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `starttime` double(15,4) DEFAULT NULL,
  `stoptime` double(15,4) DEFAULT NULL,
  `pause_time` double(15,4) NOT NULL DEFAULT '0.0000',
  `pause_message` varchar(255) NOT NULL DEFAULT '',
  `quadrants` text NOT NULL,
  `starsystems` tinyint(4) NOT NULL DEFAULT '14',
  `planets` tinyint(4) NOT NULL DEFAULT '7',
  `min_planets` tinyint(4) NOT NULL DEFAULT '1',
  `max_planets` tinyint(4) NOT NULL DEFAULT '3',
  `merging` tinyint(4) NOT NULL DEFAULT '0',
  `teams` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `bonus` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `warptime` decimal(3,2) unsigned NOT NULL DEFAULT '0.00',
  `buildings` text NOT NULL,
  `concepts` text NOT NULL,
  `researched` text NOT NULL,
  `food` int(11) unsigned NOT NULL DEFAULT '100',
  `workers` int(11) unsigned NOT NULL DEFAULT '100',
  `energy` int(11) unsigned NOT NULL DEFAULT '100',
  `minerals` int(11) unsigned NOT NULL DEFAULT '0',
  `resistance` int(11) NOT NULL DEFAULT '7500',
  `speed` int(11) NOT NULL DEFAULT '1000',
  `resourcetick` int(11) NOT NULL DEFAULT '900000',
  `combattick` int(11) NOT NULL DEFAULT '1800000',
  PRIMARY KEY (`round_id`),
  KEY `time` (`starttime`,`stoptime`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `score_kingdoms`
--

DROP TABLE IF EXISTS `score_kingdoms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `score_kingdoms` (
  `round_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `score` double(20,3) NOT NULL DEFAULT '0.000',
  `score_peak` double(20,3) NOT NULL DEFAULT '0.000',
  KEY `kingdom_id` (`round_id`),
  KEY `player_id` (`round_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `score_players`
--

DROP TABLE IF EXISTS `score_players`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `score_players` (
  `player_id` int(11) NOT NULL DEFAULT '0',
  `round_id` int(11) NOT NULL DEFAULT '0',
  `kingdom_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `score` double(20,3) NOT NULL DEFAULT '0.000',
  `score_peak` double(20,3) NOT NULL DEFAULT '0.000',
  KEY `kingdom_id` (`round_id`,`kingdom_id`),
  KEY `player_id` (`round_id`,`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `starsystems`
--

DROP TABLE IF EXISTS `starsystems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `starsystems` (
  `starsystem_id` int(11) NOT NULL AUTO_INCREMENT,
  `round_id` int(11) NOT NULL DEFAULT '0',
  `quadrant_id` int(11) NOT NULL DEFAULT '0',
  `x` tinyint(4) NOT NULL DEFAULT '0',
  `y` tinyint(4) NOT NULL DEFAULT '0',
  `total` tinyint(4) NOT NULL DEFAULT '0',
  `available` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`starsystem_id`),
  KEY `game_id` (`round_id`,`quadrant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2404 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `styles`
--

DROP TABLE IF EXISTS `styles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `styles` (
  `style_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `engine` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(32) NOT NULL DEFAULT '',
  `style` varchar(32) NOT NULL DEFAULT '',
  `creator_id` int(10) unsigned NOT NULL DEFAULT '0',
  `private` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`style_id`),
  KEY `engine` (`engine`,`style`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks` (
  `task_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `round_id` int(11) unsigned NOT NULL DEFAULT '0',
  `kingdom_id` int(11) unsigned NOT NULL DEFAULT '0',
  `player_id` int(11) unsigned NOT NULL DEFAULT '0',
  `planet_id` int(11) unsigned NOT NULL DEFAULT '0',
  `target_kingdom_id` int(11) unsigned NOT NULL DEFAULT '0',
  `type` int(1) unsigned NOT NULL DEFAULT '0',
  `building_id` int(11) unsigned NOT NULL DEFAULT '0',
  `concept_id` int(11) unsigned NOT NULL DEFAULT '0',
  `design_id` int(11) unsigned NOT NULL DEFAULT '0',
  `unit_id` int(11) unsigned NOT NULL DEFAULT '0',
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `attribute` varchar(32) NOT NULL DEFAULT '',
  `number` int(11) NOT NULL DEFAULT '0',
  `planning` tinyint(4) NOT NULL DEFAULT '0',
  `start` double(15,4) NOT NULL DEFAULT '0.0000',
  `completion` double(15,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`task_id`),
  KEY `completion` (`completion`),
  KEY `kingdom` (`planet_id`,`completion`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=1778987 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `style` varchar(8) NOT NULL DEFAULT 'default',
  `preferences` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `lastlogin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lastip` varchar(23) NOT NULL DEFAULT '',
  `created` double(15,4) DEFAULT NULL,
  `activated` varchar(32) NOT NULL DEFAULT '',
  `resetkey` varchar(32) NOT NULL DEFAULT '',
  `tos_hash` varchar(32) NOT NULL DEFAULT '',
  `admin` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  KEY `usercheck` (`username`,`password`)
) ENGINE=InnoDB AUTO_INCREMENT=2583 DEFAULT CHARSET=latin1 PACK_KEYS=0;
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

-- Dump completed on 2014-02-12 14:22:36
