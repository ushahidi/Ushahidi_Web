CREATE TABLE `mhi_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `category_title` varchar(100) CHARACTER SET utf8 NOT NULL,
  `category_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `mhi_site` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `site_domain` varchar(32) NOT NULL,
  `site_privacy` tinyint(4) NOT NULL DEFAULT '0',
  `site_active` tinyint(4) DEFAULT '1',
  `site_dateadd` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `mhi_site_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `mhi_site_database` (
  `mhi_id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(50) CHARACTER SET utf8 NOT NULL,
  `pass` varchar(50) CHARACTER SET utf8 NOT NULL,
  `host` varchar(100) CHARACTER SET utf8 NOT NULL,
  `port` smallint(6) NOT NULL,
  `database` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`mhi_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='This table holds DB credentials for MHI instances';

CREATE TABLE `mhi_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) CHARACTER SET utf8 NOT NULL,
  `firstname` varchar(30) CHARACTER SET utf8 NOT NULL,
  `lastname` varchar(30) CHARACTER SET utf8 NOT NULL,
  `password` varchar(40) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

UPDATE `settings` SET `db_version` = '23' WHERE `id`=1 LIMIT 1;
UPDATE `settings` SET `ushahidi_version` = '1.0.1' WHERE `id`=1 LIMIT 1;