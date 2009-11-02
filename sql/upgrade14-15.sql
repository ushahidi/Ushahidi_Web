CREATE TABLE IF NOT EXISTS `page` (
  `id` int(11) NOT NULL auto_increment,
  `page_title` varchar(255) NOT NULL,
  `page_description` longtext,
  `page_tab` varchar(100) NOT NULL,
  `page_active` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `page` (`id`, `page_title`, `page_description`, `page_tab`, `page_active`) VALUES
(1, 'About Us', '<p>This is the default about us page.</p>', 'About Us', 1);

UPDATE `settings` SET `db_version` = '15' WHERE `id` =1 LIMIT 1 ;