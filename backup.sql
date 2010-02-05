-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.1.36-community-log


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


DROP TABLE IF EXISTS `admin_user`;
CREATE TABLE `admin_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(32) NOT NULL,
  `last_name` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL DEFAULT '',
  `user_name` varchar(40) NOT NULL,
  `password` varchar(40) NOT NULL DEFAULT '',
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `modified` int(10) unsigned NOT NULL DEFAULT '0',
  `logdate` int(10) unsigned NOT NULL DEFAULT '0',
  `lognum` smallint(5) unsigned NOT NULL DEFAULT '0',
  `acc_flag` varchar(20) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `extra` text,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='Users';

--
-- Dumping data for table `admin_user`
--

/*!40000 ALTER TABLE `admin_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_user` ENABLE KEYS */;


--
-- Definition of table `domain_interests`
--

DROP TABLE IF EXISTS `domain_interests`;
CREATE TABLE `domain_interests` (
  `category_name` varchar(45) NOT NULL,
  `domains` text NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`category_name`,`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `domain_interests`
--

/*!40000 ALTER TABLE `domain_interests` DISABLE KEYS */;
INSERT INTO `domain_interests` VALUES  ('first','',10),
 ('group2','www.imdb.com\nen.wikipedia.org\nwww.netflix.com\nwww.people.com\nmovies.yahoo.com\nwww.rottentomatoes.com\nwww.answers.com\nwww.jenaniston.net\nwww.pittwatch.com',5);
/*!40000 ALTER TABLE `domain_interests` ENABLE KEYS */;


--
-- Definition of table `keyword_group`
--

DROP TABLE IF EXISTS `keyword_group`;
CREATE TABLE `keyword_group` (
  `group_name` varchar(45) NOT NULL,
  `keywords` text NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`group_name`,`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `keyword_group`
--

/*!40000 ALTER TABLE `keyword_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `keyword_group` ENABLE KEYS */;


--
-- Definition of table `search_info`
--

DROP TABLE IF EXISTS `search_info`;
CREATE TABLE `search_info` (
  `idsearch_info` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(45) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `engine_id` varchar(45) NOT NULL,
  PRIMARY KEY (`idsearch_info`)
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `search_info`
--

/*!40000 ALTER TABLE `search_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `search_info` ENABLE KEYS */;


--
-- Definition of table `search_results`
--

DROP TABLE IF EXISTS `search_results`;
CREATE TABLE `search_results` (
  `timestamp` int(10) unsigned NOT NULL,
  `engine_id` varchar(45) NOT NULL,
  `SERP` longtext NOT NULL,
  `keyword` varchar(45) NOT NULL,
  PRIMARY KEY (`keyword`,`engine_id`,`timestamp`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `search_results`
--

/*!40000 ALTER TABLE `search_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `search_results` ENABLE KEYS */;




/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
