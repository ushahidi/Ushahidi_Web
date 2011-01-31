ALTER TABLE `incident` ADD `incident_zoom` tinyint NULL DEFAULT NULL  AFTER `incident_alert_status`;

-- Update the database version
UPDATE `settings` SET `db_version` = 45 WHERE `id` = 1 LIMIT 1;