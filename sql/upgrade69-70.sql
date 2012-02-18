ALTER TABLE `comment` CHANGE `incident_id` `incident_id` BIGINT NULL DEFAULT NULL;
ALTER TABLE `comment` ADD `checkin_id` BIGINT NULL DEFAULT NULL AFTER `incident_id`;

-- Update the database version
UPDATE `settings` SET `db_version` = '70' WHERE `id` = 1 LIMIT 1;