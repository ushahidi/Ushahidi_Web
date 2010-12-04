/* This update converts user and roles tables to MyISAM */

/* UPDATE ROLES TABLES */

CREATE TABLE IF NOT EXISTS `roles_temporary` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `name` varchar(32) NOT NULL,
    `description` varchar(255) NOT NULL,
	`reports_view` tinyint(4) NOT NULL default '0',
	`reports_edit` tinyint(4) NOT NULL default '0',
	`reports_evaluation` tinyint(4) NOT NULL default '0',
	`reports_comments` tinyint(4) NOT NULL default '0',
	`reports_download` tinyint(4) NOT NULL default '0',
	`reports_upload` tinyint(4) NOT NULL default '0',
	`messages` tinyint(4) NOT NULL default '0',
	`messages_reporters` tinyint(4) NOT NULL default '0',
	`stats` tinyint(4) NOT NULL default '0',
	`settings` tinyint(4) NOT NULL default '0',
	`manage` tinyint(4) NOT NULL default '0',
	`users` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `roles_users_temporary` (
    `user_id` int(11) unsigned NOT NULL,
    `role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `roles_temporary` SELECT * FROM `roles`;
INSERT INTO `roles_users_temporary` SELECT * FROM `roles_users`;

DROP TABLE `roles_users`;
DROP TABLE `roles`;

RENAME TABLE `roles_temporary` TO `roles`;
RENAME TABLE `roles_users_temporary` TO `roles_users`;

/* UPDATE USERS TABLES */

CREATE TABLE IF NOT EXISTS `users_temporary` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `name` varchar(200) default NULL,
    `email` varchar(127) NOT NULL,
    `username` varchar(31) NOT NULL default '',
    `password` char(50) NOT NULL,
    `logins` int(10) unsigned NOT NULL default '0',
    `last_login` int(10) unsigned default NULL,
    `notify` tinyint(1) NOT NULL default '0' COMMENT 'Flag incase admin opts in for email notifications',
    `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_tokens_temporary` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `user_id` int(11) unsigned NOT NULL,
    `user_agent` varchar(40) NOT NULL,
    `token` varchar(32) NOT NULL,
    `created` int(10) unsigned NOT NULL,
    `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `users_temporary` SELECT * FROM `users`;
INSERT INTO `user_tokens_temporary` SELECT * FROM `user_tokens`;

DROP TABLE `user_tokens`;
DROP TABLE `users`;

RENAME TABLE `users_temporary` TO `users`;
RENAME TABLE `user_tokens_temporary` TO `user_tokens`;

/* Step up the database version number */

UPDATE `settings` SET `db_version` = '40' WHERE `id`=1 LIMIT 1;
