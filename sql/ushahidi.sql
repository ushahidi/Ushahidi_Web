-- Ushahidi Engine
-- version 0.1
-- http://www.ushahidi.com


SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `category_type` tinyint(4) default NULL,
  `category_title` varchar(255) default NULL,
  `category_description` text,
  `category_color` varchar(20) default NULL,
  `category_image` varchar(255) default NULL,
  `category_visible` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `category`
--


-- --------------------------------------------------------

--
-- Table structure for table `idp`
--

CREATE TABLE IF NOT EXISTS `idp` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `incident_id` bigint(20) NOT NULL,
  `verified_id` bigint(20) default NULL,
  `idp_idnumber` varchar(100) default NULL,
  `idp_orig_idnumber` varchar(100) default NULL,
  `idp_fname` varchar(50) default NULL,
  `idp_lname` varchar(50) default NULL,
  `idp_email` varchar(100) default NULL,
  `idp_phone` varchar(50) default NULL,
  `current_location_id` bigint(20) default NULL,
  `displacedfrom_location_id` bigint(20) default NULL,
  `movedto_location_id` bigint(20) default NULL,
  `idp_move_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `idp`
--


-- --------------------------------------------------------

--
-- Table structure for table `incident`
--

CREATE TABLE IF NOT EXISTS `incident` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `location_id` bigint(20) NOT NULL,
  `user_id` bigint(20) default NULL,
  `incident_title` varchar(255) default NULL,
  `incident_description` longtext,
  `incident_date` datetime default NULL,
  `incident_active` tinyint(4) NOT NULL default '0',
  `incident_verified` tinyint(4) NOT NULL default '0',
  `incident_dateadd` datetime default NULL,
  `incident_datemodify` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `location_id` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `incident`
--


-- --------------------------------------------------------

--
-- Table structure for table `incident_category`
--

CREATE TABLE IF NOT EXISTS `incident_category` (
  `incident_id` bigint(20) default NULL,
  `category_id` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `incident_category`
--


-- --------------------------------------------------------

--
-- Table structure for table `incident_person`
--

CREATE TABLE IF NOT EXISTS `incident_person` (
  `person_id` bigint(20) unsigned NOT NULL auto_increment,
  `incident_id` bigint(20) default NULL,
  `location_id` bigint(20) default NULL,
  `person_details` text,
  `person_date` datetime default NULL,
  PRIMARY KEY  (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `incident_person`
--


-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE IF NOT EXISTS `location` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `location_name` varchar(255) default NULL,
  `location_country` varchar(100) default NULL,
  `latitude` varchar(50) default NULL,
  `longitude` varchar(50) default NULL,
  `location_visible` tinyint(4) NOT NULL default '1',
  `location_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `location`
--


-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE IF NOT EXISTS `media` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `location_id` bigint(20) default NULL,
  `incident_id` bigint(20) default NULL,
  `media_type` tinyint(4) default NULL,
  `media_title` varchar(255) default NULL,
  `media_description` longtext,
  `media_link` varchar(255) default NULL,
  `media_date` datetime default NULL,
  `media_active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `media`
--


-- --------------------------------------------------------

--
-- Table structure for table `organization`
--

CREATE TABLE IF NOT EXISTS `organization` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `organization_name` varchar(255) default NULL,
  `organization_description` longtext,
  `organization_website` varchar(255) default NULL,
  `organization_address` varchar(255) default NULL,
  `organization_country` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `organization`
--


-- --------------------------------------------------------

--
-- Table structure for table `organization_incident`
--

CREATE TABLE IF NOT EXISTS `organization_incident` (
  `organization_id` bigint(20) default NULL,
  `incident_id` bigint(20) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `organization_incident`
--


-- --------------------------------------------------------

--
-- Table structure for table `pending_users`
--

CREATE TABLE IF NOT EXISTS `pending_users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `key` varchar(32) NOT NULL,
  `email` varchar(127) NOT NULL,
  `username` varchar(31) NOT NULL default '',
  `password` char(50) default NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `pending_users`
--


-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'login', 'Login privileges, granted after account confirmation'),
(2, 'admin', 'Administrative user, has access to everything.');

-- --------------------------------------------------------

--
-- Table structure for table `roles_users`
--

CREATE TABLE IF NOT EXISTS `roles_users` (
  `user_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles_users`
--

INSERT INTO `roles_users` (`user_id`, `role_id`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(40) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sessions`
--


-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `site_name` varchar(255) default NULL,
  `default_map` tinyint(4) NOT NULL default '1' COMMENT '1 - GOOGLE MAPS, 2 - LIVE MAPS, 3 - YAHOO MAPS, 4 - OPEN STREET MAPS',
  `api_google` varchar(200) default NULL,
  `api_yahoo` varchar(200) default NULL,
  `api_live` varchar(200) default NULL,
  `default_country` varchar(150) default NULL,
  `default_city` varchar(150) default NULL,
  `default_lat` varchar(100) default NULL,
  `default_lon` varchar(100) default NULL,
  `default_zoom` tinyint(4) NOT NULL default '10',
  `date_modify` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `default_map`, `api_google`, `api_yahoo`, `api_live`, `default_country`, `default_city`, `default_lat`, `default_lon`, `default_zoom`, `date_modify`) VALUES
(1, 'Ushahidi', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10, '2008-08-25 10:25:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  `email` varchar(127) NOT NULL,
  `username` varchar(31) NOT NULL default '',
  `password` char(50) NOT NULL,
  `logins` int(10) unsigned NOT NULL default '0',
  `last_login` int(10) unsigned default NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `password`) VALUES
(1, 'Administrator', 'admin@ushahidi.com', 'admin', 'bae4b17e9acbabf959654a4c496e577003e0b887c6f52803d7');

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(32) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `user_tokens`
--


-- --------------------------------------------------------

--
-- Table structure for table `verified`
--

CREATE TABLE IF NOT EXISTS `verified` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `incident_id` bigint(20) default NULL,
  `idp_id` bigint(20) default NULL,
  `user_id` int(11) default NULL,
  `verified_comment` longtext,
  `verified_date` datetime default NULL,
  `verified_status` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `verified`
--


--
-- Constraints for dumped tables
--

--
-- Constraints for table `roles_users`
--
ALTER TABLE `roles_users`
  ADD CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;