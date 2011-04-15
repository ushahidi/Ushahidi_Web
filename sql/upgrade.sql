ALTER TABLE category ENGINE = MyISAM;
ALTER TABLE feedback ENGINE = MyISAM;
ALTER TABLE feedback_person ENGINE = MyISAM;
ALTER TABLE idp ENGINE = MyISAM;
ALTER TABLE incident ENGINE = MyISAM;
ALTER TABLE incident_category ENGINE = MyISAM;
ALTER TABLE incident_person ENGINE = MyISAM;
ALTER TABLE level ENGINE = MyISAM;
ALTER TABLE location ENGINE = MyISAM;
ALTER TABLE media ENGINE = MyISAM;
ALTER TABLE organization ENGINE = MyISAM;
ALTER TABLE organization_incident ENGINE = MyISAM;
ALTER TABLE pending_users ENGINE = MyISAM;
ALTER TABLE reporter ENGINE = MyISAM;
ALTER TABLE service ENGINE = MyISAM;
ALTER TABLE sessions ENGINE = MyISAM;
ALTER TABLE sharing ENGINE = MyISAM;
ALTER TABLE sharing_log ENGINE = MyISAM;
ALTER TABLE verified ENGINE = MyISAM;
 
ALTER TABLE alert_sent CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE category_lang CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE cluster CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE comment CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE feed CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE feedback CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE feedback_person CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE feed_item CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE incident_lang CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE laconica CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE level CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE message CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE rating CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE reporter CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE scheduler CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE scheduler_log CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE service CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE twitter CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
 
ALTER TABLE `settings` ADD `db_version` VARCHAR( 20 ) NOT NULL ,
ADD `ushahidi_version` VARCHAR( 20 ) NOT NULL ;
 
UPDATE `settings` SET `db_version` = '11',
`ushahidi_version` = '0.9' WHERE `id` =1 LIMIT 1 ;


--- Take care of very old ushahidi installs.
ALTER TABLE `incident` ADD `form_id` INT( 11 ) NOT NULL DEFAULT '1' AFTER `location_id`,
   ADD  `incident_source` varchar(5) default NULL,
   ADD  `incident_information` varchar(5) default NULL;



   CREATE TABLE IF NOT EXISTS `form` (
     `id` int(11) NOT NULL auto_increment,
     `form_title` varchar(200) NOT NULL,
     `form_description` text,
     `form_active` tinyint(4) default '1',
     PRIMARY KEY  (`id`)
   ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



   INSERT INTO `form` (`id`, `form_title`, `form_description`, `form_active`) VALUES
   (1, 'Default Form', 'Default form, for report entry', 1);



   CREATE TABLE IF NOT EXISTS `form_field` (
     `id` int(11) NOT NULL auto_increment,
     `form_id` int(11) NOT NULL default '0',
     `field_name` varchar(200) default NULL,
     `field_type` tinyint(4) NOT NULL default '1' COMMENT '1 - TEXTFIELD, 2 - TEXTAREA (FREETEXT), 3 - DATE, 4 - PASSWORD, 5 - RADIO, 6 - CHECKBOX',
     `field_required` tinyint(4) default '0',
     `field_options` text,
     `field_position` tinyint(4) NOT NULL default '0',
     `field_default` varchar(200) default NULL,
     `field_maxlength` int(11) NOT NULL default '0',
     `field_width` smallint(6) NOT NULL default '0',
     `field_height` tinyint(4) default '5',
     `field_isdate` tinyint(4) NOT NULL default '0',
     PRIMARY KEY  (`id`),
     KEY `fk_form_id` (`form_id`)
   ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



   CREATE TABLE IF NOT EXISTS `form_response` (
     `id` bigint(20) NOT NULL auto_increment,
     `form_field_id` int(11) NOT NULL,
     `incident_id` bigint(20) NOT NULL,
     `form_response` text NOT NULL,
     PRIMARY KEY  (`id`),
     KEY `fk_form_field_id` (`form_field_id`)
   ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

   --
   -- Constraints for table `form_field`
   --
   ALTER TABLE `form_field`
     ADD CONSTRAINT `form_field_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `form` (`id`) ON DELETE CASCADE;

   --
   -- Constraints for table `form_response`
   --
   ALTER TABLE `form_response`
     ADD CONSTRAINT `form_response_ibfk_1` FOREIGN KEY (`form_field_id`) REFERENCES `form_field` (`id`) ON DELETE CASCADE;

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

-- --------------------------------------------------------

--
-- Table structure for table `mhi_category`
--

CREATE TABLE `mhi_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `category_title` varchar(100) CHARACTER SET utf8 NOT NULL,
  `category_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mhi_site`
--

CREATE TABLE `mhi_site` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `site_domain` varchar(100) NOT NULL,
  `site_privacy` tinyint(4) NOT NULL DEFAULT '0',
  `site_active` tinyint(4) DEFAULT '1',
  `site_dateadd` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mhi_site_category`
--

CREATE TABLE `mhi_site_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mhi_site_database`
--

CREATE TABLE `mhi_site_database` (
  `mhi_id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(50) CHARACTER SET utf8 NOT NULL,
  `pass` varchar(50) CHARACTER SET utf8 NOT NULL,
  `host` varchar(100) CHARACTER SET utf8 NOT NULL,
  `port` smallint(6) NOT NULL,
  `database` varchar(30) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`mhi_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 COMMENT='This table holds DB credentials for MHI instances';

-- --------------------------------------------------------

--
-- Table structure for table `mhi_users`
--

CREATE TABLE `mhi_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) CHARACTER SET utf8 NOT NULL,
  `firstname` varchar(30) CHARACTER SET utf8 NOT NULL,
  `lastname` varchar(30) CHARACTER SET utf8 NOT NULL,
  `password` varchar(40) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Update table `message`
--

ALTER TABLE `message`
ADD `reporter_id` bigint(20) default NULL AFTER `user_id`,
ADD `service_messageid` varchar(100) default NULL AFTER `reporter_id`,
ADD `message_detail` text NULL AFTER `message`;


-- --------------------------------------------------------

--
-- Update table `alert`
--

ALTER TABLE `alert`
ADD UNIQUE KEY `uniq_alert_code` (`alert_code`);


-- --------------------------------------------------------

--
-- Update table `alert_sent`
--

ALTER TABLE `alert_sent`
ADD UNIQUE KEY `uniq_incident_id` (`incident_id`);

