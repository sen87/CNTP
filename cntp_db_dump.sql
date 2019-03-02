-- MySQL dump 10.17  Distrib 10.3.13-MariaDB, for Linux (x86_64)

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bookmark_category`
--

DROP TABLE IF EXISTS `bookmark_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookmark_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `position` tinyint(1) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bookmark_category_user_FK` (`user_id`),
  CONSTRAINT `bookmark_category_user_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookmark_category`
--

LOCK TABLES `bookmark_category` WRITE;
/*!40000 ALTER TABLE `bookmark_category` DISABLE KEYS */;
INSERT INTO `bookmark_category` VALUES (1,1,0,'Music'),(2,1,0,'Video'),(3,1,1,'Search');
/*!40000 ALTER TABLE `bookmark_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookmarks`
--

DROP TABLE IF EXISTS `bookmarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookmarks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_id` int(10) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `url` varchar(200) NOT NULL,
  `favicon` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bookmarks_favicon_FK` (`favicon`),
  KEY `bookmarks_bookmark_category_FK` (`cat_id`),
  CONSTRAINT `bookmarks_bookmark_category_FK` FOREIGN KEY (`cat_id`) REFERENCES `bookmark_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookmarks`
--

LOCK TABLES `bookmarks` WRITE;
/*!40000 ALTER TABLE `bookmarks` DISABLE KEYS */;
INSERT INTO `bookmarks` VALUES (1,3,'DuckDuckGo','https://duckduckgo.com',NULL),(2,3,'Google','https://www.google.com',NULL),(3,3,'Wikipedia','https://www.wikipedia.org',NULL),(4,1,'SoundCloud','https://soundcloud.com/stream',NULL),(5,1,'Discogs','https://www.discogs.com',NULL),(6,2,'IMDb','https://www.imdb.com/',NULL),(7,2,'YouTube','https://www.youtube.com/feed/subscriptions',NULL);
/*!40000 ALTER TABLE `bookmarks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feed`
--

DROP TABLE IF EXISTS `feed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feed` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `url` varchar(200) NOT NULL,
  `website_url` varchar(200) DEFAULT NULL,
  `img` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `cache` longblob DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feed`
--

LOCK TABLES `feed` WRITE;
/*!40000 ALTER TABLE `feed` DISABLE KEYS */;
INSERT INTO `feed` VALUES (0,'Lorem Ipsum','dummy feed','https://cntp.nya.pub/lipsum.xml','https://lipsum.com',0,NULL,NULL),(1,'Slashdot','tech news','http://rss.slashdot.org/Slashdot/slashdotMain','https://slashdot.org',0,NULL,NULL),(2,'phoronix','linux news','https://www.phoronix.com/rss.php','https://www.phoronix.com',0,NULL,NULL),(3,'heise','[ger] tech news','https://www.heise.de/newsticker/heise-atom.xml','https://www.heise.de',0,NULL,NULL),(4,'LWN','linux news','https://lwn.net/headlines/rss','https://lwn.net',0,NULL,NULL),(5,'Netzpolitik','[ger] IT Politics','https://netzpolitik.org/feed','https://netzpolitik.org',1,NULL,NULL),(6,'Arch Linux','official news','https://www.archlinux.org/feeds/news/','https://www.archlinux.org',0,NULL,NULL),(7,'Arch Wiki','wiki updates','https://wiki.archlinux.org/api.php?hidebots=1&urlversion=1&days=7&limit=50&action=feedrecentchanges&feedformat=rss','https://www.wiki.archlinux.org',0,NULL,NULL),(8,'Arch Linux','Planet Arch Linux','https://planet.archlinux.org/atom.xml','https://planet.archlinux.org',0,NULL,NULL),(9,'deviantart','popular in the last 24 hours','https://backend.deviantart.com/rss.xml?q=boost%3Apopular+max_age%3A24h+meta%3Aall&type=deviation','https://www.deviantart.com/popular-24-hours/',1,NULL,NULL),(10,'TorrentFreak','p2p news','https://feeds.feedburner.com/Torrentfreak','https://torrentfreak.com',0,NULL,NULL),(11,'AnandTech','tech news','https://www.anandtech.com/rss/','https://www.anandtech.com',0,NULL,NULL),(12,'Krebs on Security','security news','https://krebsonsecurity.com/feed/','https://krebsonsecurity.com',0,NULL,NULL),(13,'9GAG','hot','https://9gag-rss.com/api/rss/get?code=9GAGHot','https://9gag.com/hot',1,NULL,NULL),(14,'9GAG','trending','https://9gag-rss.com/api/rss/get?code=9GAG','https://9gag.com/trending',1,NULL,NULL),(15,'9GAG','fresh','https://9gag-rss.com/api/rss/get?code=9GAGFresh','https://9gag.com/fresh',1,NULL,NULL),(16,'9GAG','nsfw','https://9gag-rss.com/api/rss/get?code=9GAGNSFW','https://9gag.com/nsfw',1,NULL,NULL),(17,'4Chan /w/','Wallpapers/Anime','http://boards.4channel.org/w/index.rss','http://boards.4channel.org/w/',1,NULL,NULL),(18,'4Chan /a/','Anime & Manga','http://boards.4channel.org/a/index.rss','http://boards.4channel.org/a/',1,NULL,NULL),(19,'4Chan /diy/','Do It Yourself','http://boards.4channel.org/diy/index.rss','http://boards.4channel.org/diy/',1,NULL,NULL),(20,'4Chan /g/','Technology','http://boards.4channel.org/g/index.rss','http://boards.4channel.org/g/',1,NULL,NULL),(21,'4Chan /wg/','Wallpapers/General','http://boards.4chan.org/wg/index.rss','http://boards.4chan.org/wg/',1,NULL,NULL),(22,'4Chan /v/','Video Games','http://boards.4channel.org/v/index.rss','http://boards.4channel.org/v/',1,NULL,NULL),(23,'/R/Hot','reddit hot','https://www.reddit.com/hot/.rss','https://www.reddit.com/hot/',1,NULL,NULL),(24,'/R/Funny','reddit funny','https://www.reddit.com/r/funny/.rss','https://www.reddit.com/r/funny/',1,NULL,NULL),(25,'/R/Linux','reddit linux','https://www.reddit.com/r/linux/.rss','https://www.reddit.com/r/linux/',0,NULL,NULL),(26,'/R/SciFi','reddit scifi','https://www.reddit.com/r/scifi/.rss','https://www.reddit.com/r/scifi/',0,NULL,NULL),(27,'/R/Technology','reddit technology','https://www.reddit.com/r/technology/.rss','https://www.reddit.com/r/technology/',0,NULL,NULL),(28,'IsNichWahr','fun','https://www.isnichwahr.de/feed/rss.xml','https://www.isnichwahr.de/',1,NULL,NULL),(29,'WDR','[ger] news','https://www1.wdr.de/nachrichten/uebersicht-nachrichten-100.feed','https://www1.wdr.de/nachrichten/index.html',0,NULL,NULL),(30,'Google News','Top Stories','https://news.google.com/news/rss','https://news.google.com',0,NULL,NULL),(31,'/R/TFTS','reddit tales from tech support','https://www.reddit.com/r/talesfromtechsupport/.rss','https://www.reddit.com/r/talesfromtechsupport/',0,NULL,NULL),(32,'Debian','official news','https://www.debian.org/News/news','https://www.debian.org',0,NULL,NULL),(33,'Debian Security','security advisories','https://www.debian.org/security/dsa','https://www.debian.org',0,NULL,NULL),(34,'PFsense','release notes (blog)','https://www.netgate.com/feed.xml','https://www.netgate.com/blog/',0,NULL,NULL),(35,'OPNsense','release notes (blog)','https://opnsense.org/blog/feed/','https://opnsense.org/blog/',0,NULL,NULL),(36,'Steam','news & deals','https://store.steampowered.com/feeds','https://store.steampowered.com',0,NULL,NULL),(37,'deviantart wallpaper','popular last 24h','https://backend.deviantart.com/rss.xml?q=boost%3Apopular+max_age%3A24h+in%3Acustomization%2Fwallpaper&type=deviation','https://www.deviantart.com/customization/wallpaper/popular-24-hours/',1,NULL,NULL);
/*!40000 ALTER TABLE `feed` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feed_select`
--

DROP TABLE IF EXISTS `feed_select`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feed_select` (
  `user_id` int(10) unsigned NOT NULL,
  `set` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `index` tinyint(2) unsigned NOT NULL,
  `feed_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`set`,`index`),
  KEY `feed_select_feed_FK` (`feed_id`),
  CONSTRAINT `feed_select_feed_FK` FOREIGN KEY (`feed_id`) REFERENCES `feed` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `feed_select_user_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feed_select`
--

LOCK TABLES `feed_select` WRITE;
/*!40000 ALTER TABLE `feed_select` DISABLE KEYS */;
INSERT INTO `feed_select` VALUES (1,1,1,1),(1,1,5,2),(1,2,2,6),(1,1,0,9),(1,1,4,10),(1,1,3,11),(1,1,6,12),(1,2,0,14),(1,2,3,26),(1,1,2,30),(1,2,5,31),(1,2,4,33),(1,2,6,35),(1,2,1,36);
/*!40000 ALTER TABLE `feed_select` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `layout`
--

DROP TABLE IF EXISTS `layout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `layout` (
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `table` longtext NOT NULL,
  `feed_count` tinyint(2) unsigned NOT NULL,
  `b` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `n` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `w` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `favicon_switch` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `bookmark_cache` longtext DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `layout_user_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `layout`
--

LOCK TABLES `layout` WRITE;
/*!40000 ALTER TABLE `layout` DISABLE KEYS */;
INSERT INTO `layout` VALUES (1,'▬▬▶','<tr><td id=\"weather\"></td><td class=\"feed_box\" rowspan=\"3\"></td><td class=\"feed_box\"></td><td class=\"feed_box\"></td><td id=\"bookmarks\" rowspan=\"3\"></td></tr><tr><td id=\"notes\" rowspan=\"2\"></td><td class=\"feed_box\"></td><td class=\"feed_box\"></td></tr><tr><td class=\"feed_box\"></td><td class=\"feed_box\"></td></tr>',7,1,1,1,1,NULL);
/*!40000 ALTER TABLE `layout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notes` (
  `user_id` int(10) unsigned NOT NULL,
  `text` longtext DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `notes_user_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
INSERT INTO `notes` VALUES (1,'<h1><img src=\"favicon/icon128_wg.png\"></img>\nCustom New Tab Page\nv2.0</h1>\n\n<h1>Settings & Logout</h1>\n<li> buttons in the top right corner\n\n<h1>Switch Feed Set</h1>\n<li> buttons in the top left corner\n\n<h1>Edit Notes</h1>\n<li> click on the title of this window (Notes)\n\n\n\n\nhttps://cntp.nya.pub');
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `theme`
--

DROP TABLE IF EXISTS `theme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `theme` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `file` varchar(100) NOT NULL,
  `min` varchar(100) DEFAULT NULL,
  `author` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `theme`
--

LOCK TABLES `theme` WRITE;
/*!40000 ALTER TABLE `theme` DISABLE KEYS */;
INSERT INTO `theme` VALUES (1,'Solid','Solid.css','_min_Solid_v0.5.css','sen'),(2,'Solid Max','Solid_Max.css','_min_Solid_Max_v0.5.css','sen'),(3,'Coffee','Coffee.css','_min_Coffee_v0.5.css','sen'),(4,'PreciousPurple','PreciousPurple.css','_min_PreciousPurple_v0.5.css','SiCKHEAD'),(5,'Clean&Simple','Clean&Simple.css','_min_Clean&Simple_v0.5.css','sen'),(6,'Solid Max Red','Solid_Max_Red.css','_min_Solid_Max_Red_v0.5.css','sen'),(7,'Solid Max Green','Solid_Max_Green.css','_min_Solid_Max_Green_v0.5.css','sen'),(8,'Solid Max Orange','Solid_Max_Orange.css','_min_Solid_Max_Orange_v0.5.css','sen'),(9,'Solid Green','Solid_Green.css','_min_Solid_Green_v0.5.css','sen'),(10,'Solid Red','Solid_Red.css','_min_Solid_Red_v0.5.css','sen');
/*!40000 ALTER TABLE `theme` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `theme_select`
--

DROP TABLE IF EXISTS `theme_select`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `theme_select` (
  `user_id` int(10) unsigned NOT NULL,
  `theme_id` int(10) unsigned NOT NULL DEFAULT 1,
  `ext_css` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `theme_select_theme_FK` (`theme_id`),
  CONSTRAINT `theme_select_theme_FK` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`id`),
  CONSTRAINT `theme_select_user_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `theme_select`
--

LOCK TABLES `theme_select` WRITE;
/*!40000 ALTER TABLE `theme_select` DISABLE KEYS */;
INSERT INTO `theme_select` VALUES (1,1,'');
/*!40000 ALTER TABLE `theme_select` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mail` varchar(50) NOT NULL,
  `pwd_hash` varchar(64) NOT NULL,
  `token` longtext DEFAULT NULL,
  `verified` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `version` varchar(4) DEFAULT NULL,
  `last_visit` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'test@testomatik.org','$2y$10$XIBcutjoK6fynKacULYeFuvTHILinhYGaFhGX2LlixtQ9ZVGNxKmC',NULL,1,'2.0','2019-03-02');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weather`
--

DROP TABLE IF EXISTS `weather`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weather` (
  `user_id` int(10) unsigned NOT NULL,
  `place_id` varchar(200) NOT NULL,
  `place` varchar(100) DEFAULT NULL,
  `cache` longtext DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  `forecast` tinyint(1) NOT NULL DEFAULT 1,
  `forecast_h` int(4) unsigned NOT NULL,
  `icons` int(10) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`user_id`),
  KEY `weather_weather_icons_FK` (`icons`),
  CONSTRAINT `weather_user_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `weather_weather_icons_FK` FOREIGN KEY (`icons`) REFERENCES `weather_icons` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weather`
--

LOCK TABLES `weather` WRITE;
/*!40000 ALTER TABLE `weather` DISABLE KEYS */;
INSERT INTO `weather` VALUES (1,'https://www.yr.no/place/Japan/Tokyo/Tokyo/','',NULL,NULL,1,160,2);
/*!40000 ALTER TABLE `weather` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weather_icons`
--

DROP TABLE IF EXISTS `weather_icons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weather_icons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `img_format` varchar(5) NOT NULL,
  `url` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weather_icons`
--

LOCK TABLES `weather_icons` WRITE;
/*!40000 ALTER TABLE `weather_icons` DISABLE KEYS */;
INSERT INTO `weather_icons` VALUES (1,'yr.no','svg','https://github.com/YR/weather-symbols'),(2,'VClouds','png','https://www.deviantart.com/vclouds/art/VClouds-Weather-Icons-179152045');
/*!40000 ALTER TABLE `weather_icons` ENABLE KEYS */;
UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;