--
-- Table structure for table `api_banned`
--

CREATE TABLE IF NOT EXISTS `api_banned` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `banned_ipaddress` varchar(50) NOT NULL,
  `banned_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='For logging banned API IP addresses' AUTO_INCREMENT=8 ;

--
-- Table structure for table `api_log`
--

CREATE TABLE IF NOT EXISTS `api_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `api_task` varchar(10) NOT NULL,
  `api_parameters` varchar(50) NOT NULL,
  `api_records` tinyint(11) NOT NULL,
  `api_ipaddress` varchar(50) NOT NULL,
  `api_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='For logging API activities' AUTO_INCREMENT=19 ;

UPDATE `settings` SET `db_version` = '22' WHERE `id`=1 LIMIT 1;