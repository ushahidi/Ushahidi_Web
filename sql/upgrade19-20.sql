ALTER TABLE `feed_item` ADD `incident_id` INT( 11 ) NOT NULL DEFAULT '0' AFTER `location_id`;
ALTER TABLE `feed_item` CHANGE `location_id` `location_id` BIGINT( 20 ) NULL DEFAULT '0';

UPDATE `settings` SET `db_version` = '20' WHERE `id` =1 LIMIT 1 ;