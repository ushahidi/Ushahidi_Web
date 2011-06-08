# Checkins will be going here
CREATE TABLE IF NOT EXISTS `checkin`
(
`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` INT UNSIGNED NOT NULL,
`location_id` BIGINT UNSIGNED NOT NULL,
`checkin_description` VARCHAR(255),
`checkin_date` DATETIME NOT NULL,
`checkin_auto` ENUM('0','1') DEFAULT '0',
PRIMARY KEY (`id`)
);

# Mobile devices will be going here
CREATE TABLE IF NOT EXISTS `user_devices` (
  `id` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Add new column to roles and media tables
ALTER TABLE `roles` ADD `checkin` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `roles` ADD `checkin_admin` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `media` ADD `checkin_id` BIGINT NULL DEFAULT NULL AFTER `incident_id`;

# set roles for existing accounts, if they exist
UPDATE `roles` SET `checkin` = '1', `checkin_admin` = '1' WHERE `name` = 'superadmin';
UPDATE `roles` SET `checkin` = '1', `checkin_admin` = '1' WHERE `name` = 'admin';
UPDATE `roles` SET `checkin` = '1' WHERE `name` = 'login';

# Bump up version
UPDATE `settings` SET `db_version` = '47' WHERE `id`=1 LIMIT 1;
UPDATE `settings` SET `ushahidi_version` = '2.0.2' WHERE `id`=1 LIMIT 1;