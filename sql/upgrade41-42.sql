/** Increase the size of the api_parameters column in api_log */
ALTER TABLE `api_log` MODIFY COLUMN `api_parameters` VARCHAR(100) NOT NULL;

UPDATE `settings` SET `db_version` = '42' WHERE `id`=1 LIMIT 1;
