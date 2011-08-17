ALTER TABLE `media` ADD `badge_id` int(11) default NULL AFTER `message_id`;

ALTER TABLE `users` ADD `public_profile` TINYINT( 1 ) NOT NULL DEFAULT '1';

CREATE TABLE `badge` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 250 ) NOT NULL ,
`description` TEXT NOT NULL
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `badge_users` (
`user_id` INT NOT NULL ,
`badge_id` INT NOT NULL ,
PRIMARY KEY ( `user_id` , `badge_id` )
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


UPDATE `settings` SET `db_version` = '63' WHERE `id`=1 LIMIT 1;