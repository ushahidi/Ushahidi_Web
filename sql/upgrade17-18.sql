ALTER TABLE `alert_sent` DROP INDEX `uniq_incident_id`;
ALTER TABLE `alert` ADD `alert_radius` TINYINT NOT NULL DEFAULT '20' AFTER `alert_lon`;
ALTER TABLE `incident` ADD `incident_alert_status` TINYINT NOT NULL DEFAULT '0' COMMENT '0 - Not Tagged for Sending, 1 - Tagged for Sending, 2 - Alerts Have Been Sent' AFTER `incident_datemodify`;

ALTER TABLE `settings` ADD `stat_key` VARCHAR( 30 ) NOT NULL AFTER `stat_id` ;

UPDATE `settings` SET `db_version` = '18' WHERE `id` =1 LIMIT 1 ;