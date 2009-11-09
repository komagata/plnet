-- MySQL dump 10.10
--
-- Host: localhost    Database: strictdb_development
-- ------------------------------------------------------
-- Server version	5.0.24a-Debian_4-log

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
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
CREATE TABLE `content` (
  `id` int(11) NOT NULL auto_increment,
  `content_category_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `uri` text NOT NULL,
  `icon` varchar(255) NOT NULL default 'contents-icon.png',
  `format` text NOT NULL,
  `description` text NOT NULL,
  `target` varchar(255) NOT NULL,
  `updatetime` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `content_category_id` (`content_category_id`),
  CONSTRAINT `content_ibfk_1` FOREIGN KEY (`content_category_id`) REFERENCES `content_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `content_category`
--

DROP TABLE IF EXISTS `content_category`;
CREATE TABLE `content_category` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `custom_template`
--

DROP TABLE IF EXISTS `custom_template`;
CREATE TABLE `custom_template` (
  `id` int(11) NOT NULL auto_increment,
  `member_id` int(11) NOT NULL default '0',
  `template` text NOT NULL,
  `updatedtime` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `custom_template_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `design`
--

DROP TABLE IF EXISTS `design`;
CREATE TABLE `design` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `file` varchar(255) NOT NULL default '',
  `thumbnail` varchar(255) NOT NULL default '',
  `updatetime` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `entry`
--

DROP TABLE IF EXISTS `entry`;
CREATE TABLE `entry` (
  `id` int(11) NOT NULL auto_increment,
  `feed_id` int(11) NOT NULL default '0',
  `uri` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `author` varchar(255) NOT NULL default '',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastupdatedtime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `feed_id` (`feed_id`),
  CONSTRAINT `entry_ibfk_1` FOREIGN KEY (`feed_id`) REFERENCES `feed` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `entry_to_tag`
--

DROP TABLE IF EXISTS `entry_to_tag`;
CREATE TABLE `entry_to_tag` (
  `entry_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY  (`entry_id`,`tag_id`),
  KEY `entry_id` (`entry_id`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `entry_to_tag_ibfk_1` FOREIGN KEY (`entry_id`) REFERENCES `entry` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `entry_to_tag_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `feed`
--

DROP TABLE IF EXISTS `feed`;
CREATE TABLE `feed` (
  `id` int(11) NOT NULL auto_increment,
  `uri` varchar(255) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `description` text,
  `favicon` varchar(255) NOT NULL default '',
  `lastupdatedtime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uri` (`uri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `id` int(11) NOT NULL auto_increment,
  `design_id` int(11) NOT NULL default '1',
  `account` varchar(32) NOT NULL default '',
  `password` varchar(40) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `createdtime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `account` (`account`),
  KEY `design_id` (`design_id`),
  CONSTRAINT `member_ibfk_1` FOREIGN KEY (`design_id`) REFERENCES `design` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `member_temporary`
--

DROP TABLE IF EXISTS `member_temporary`;
CREATE TABLE `member_temporary` (
  `id` int(11) NOT NULL auto_increment,
  `design_id` int(11) NOT NULL default '1',
  `account` varchar(32) NOT NULL default '',
  `password` varchar(40) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `createdtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `activate_key` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `member_to_feed`
--

DROP TABLE IF EXISTS `member_to_feed`;
CREATE TABLE `member_to_feed` (
  `member_id` int(11) NOT NULL default '0',
  `feed_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`member_id`,`feed_id`),
  KEY `member_id` (`member_id`),
  KEY `feed_id` (`feed_id`),
  CONSTRAINT `member_to_feed_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `member_to_feed_ibfk_2` FOREIGN KEY (`feed_id`) REFERENCES `feed` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `site`
--

DROP TABLE IF EXISTS `site`;
CREATE TABLE `site` (
  `id` int(11) NOT NULL auto_increment,
  `member_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `updatetime` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `createdtime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `site_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `updatedtime` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

