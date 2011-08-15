CREATE TABLE `actions` (
	`action_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`action` VARCHAR( 75 ) NOT NULL ,
	`qualifiers` TEXT NOT NULL ,
	`response` VARCHAR( 75 ) NOT NULL ,
	`response_vars` TEXT NOT NULL,
	`active` TINYINT NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

UPDATE `settings` SET `db_version` = '61' WHERE `id`=1 LIMIT 1;
UPDATE `settings` SET `ushahidi_version` = '2.1' WHERE `id`=1 LIMIT 1;