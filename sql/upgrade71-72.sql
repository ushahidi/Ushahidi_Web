CREATE TABLE IF NOT EXISTS `externalapp` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL ,
`url` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM ;

INSERT INTO `externalapp` (`id`, `name`, `url`) VALUES (NULL, 'iPhone', 'http://download.ushahidi.com/track_download.php?download=ios'), (NULL, 'Android', 'http://download.ushahidi.com/track_download.php?download=android');

-- Update the database version
UPDATE `settings` SET `db_version` = '72' WHERE `id` = 1 LIMIT 1;