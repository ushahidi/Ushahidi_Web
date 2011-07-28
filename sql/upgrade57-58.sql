CREATE TABLE IF NOT EXISTS `form_field_option` (
	`id` int(11) NOT NULL auto_increment,
	`form_field_id` int(11) NOT NULL default '0',
	`option_name` varchar(200) default NULL,
	`option_value` text default NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `roles` ADD `access_level` tinyint(4) NOT NULL default '0';

ALTER TABLE `form_field` ADD `field_ispublic_visible` tinyint(4) NOT NULL default '0';
 
ALTER TABLE `form_field` ADD  `field_ispublic_submit` tinyint(4) NOT NULL default '0';


UPDATE `settings` SET `db_version` = '58' WHERE `id`=1 LIMIT 1;