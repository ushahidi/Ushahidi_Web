ALTER TABLE `users` ADD `notify` tinyint(1) NOT NULL default '0' COMMENT 'Flag incase admin opts in for email notifications' AFTER `last_login` ;

UPDATE `settings` SET `db_version` = '17' WHERE `id` =1 LIMIT 1 ;