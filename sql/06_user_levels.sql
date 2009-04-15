--
-- Table structure for table `incident`
--

CREATE TABLE `incident` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `location_id` bigint(20) NOT NULL,
  `form_id` int(11) NOT NULL default '1',
  `locale` varchar(10) NOT NULL default 'en_US',
  `user_id` bigint(20) default NULL,
  `incident_title` varchar(255) default NULL,
  `incident_description` longtext,
  `incident_date` datetime default NULL,
  `incident_mode` tinyint(4) NOT NULL default '1' COMMENT '1 - WEB, 2 - SMS, 3 - EMAIL, 4 - TWITTER',
  `incident_active` tinyint(4) NOT NULL default '0',
  `incident_verified` tinyint(4) NOT NULL default '0',
  `incident_source` varchar(5) default NULL,
  `incident_information` varchar(5) default NULL,
  `incident_rating` varchar(15) NOT NULL default '0',
  `incident_dateadd` datetime default NULL,
  `incident_datemodify` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `location_id` (`location_id`)
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `level`
--

CREATE TABLE `level` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `level_title` varchar(200) default NULL,
  `level_description` varchar(200) default NULL,
  `level_weight` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB;

--
-- Dumping data for table `level`
--

INSERT INTO `level` (`id`, `level_title`, `level_description`, `level_weight`) VALUES
(1, 'SPAM + Delete', 'SPAM + Delete', -2),
(2, 'SPAM', 'SPAM', -1),
(3, 'Untrusted', 'Untrusted', 0),
(4, 'Trusted', 'Trusted', 1),
(5, 'Trusted + Verifiy', 'Trusted + Verify', 2);

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `parent_id` bigint(20) default '0',
  `incident_id` int(11) default '0',
  `user_id` int(11) default '0',
  `reporter_id` bigint(20) default NULL,
  `service_messageid` varchar(100) default NULL,
  `message_from` varchar(100) default NULL,
  `message_to` varchar(100) default NULL,
  `message_subject` TEXT NULL,
  `message` text,
  `message_type` tinyint(4) default '1' COMMENT '1 - INBOX, 2 - OUTBOX (From Admin)',
  `message_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `reporter`
--

CREATE TABLE `reporter` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `incident_id` bigint(20) default NULL,
  `location_id` bigint(20) default NULL,
  `user_id` int(11) default NULL,
  `service_id` int(11) default NULL,
  `service_userid` varchar(255) default NULL,
  `service_account` varchar(255) default NULL,
  `reporter_level` tinyint(4) default '3',
  `reporter_first` varchar(200) default NULL,
  `reporter_last` varchar(200) default NULL,
  `reporter_email` varchar(120) default NULL,
  `reporter_phone` varchar(60) default NULL,
  `reporter_ip` varchar(50) default NULL,
  `reporter_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `service_name` varchar(100) default NULL,
  `service_description` varchar(255) default NULL,
  `service_url` varchar(255) default NULL,
  `service_api` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`id`, `service_name`, `service_description`, `service_url`, `service_api`) VALUES
(1, 'SMS', 'Text messages from phones', NULL, NULL),
(2, 'Email', 'Text messages from phones', NULL, NULL),
(3, 'Twitter', 'Tweets tweets tweets', 'http://twitter.com', NULL),
(4, 'Laconica', 'Tweets tweets tweets', NULL, NULL);