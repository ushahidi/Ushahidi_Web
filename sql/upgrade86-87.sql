ALTER TABLE `sessions` CHANGE COLUMN `session_id` `session_id` VARCHAR(127) NOT NULL  ;
UPDATE `settings` SET `db_version` = '87' WHERE `id`=1 LIMIT 1;
UPDATE `settings` SET `ushahidi_version` = '2.3.1' WHERE `id`=1 LIMIT 1;

