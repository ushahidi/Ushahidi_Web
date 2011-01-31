/**
* Table structure for table `geometry`
*/
CREATE TABLE IF NOT EXISTS `geometry` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `incident_id` bigint(20) NOT NULL,
  `geometry` geometry NOT NULL,
  `geometry_color` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  SPATIAL KEY `geometry` (`geometry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Update the database version
UPDATE `settings` SET `db_version` = 44 WHERE `id` = 1 LIMIT 1;