ALTER TABLE `sessions` CHANGE COLUMN `session_id` `session_id` VARCHAR(127) NOT NULL  ;
UPDATE `settings` SET `db_version` = '87' WHERE `id`=1 LIMIT 1;
