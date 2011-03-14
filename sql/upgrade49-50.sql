ALTER TABLE `settings` ADD `checkins` tinyint(4) NOT NULL DEFAULT '0'  AFTER `alerts_email`;

UPDATE `settings` SET `db_version` = '50' WHERE `id`=1 LIMIT 1;