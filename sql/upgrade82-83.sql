ALTER TABLE `message` ADD `latitude` DOUBLE NULL DEFAULT NULL;

ALTER TABLE `message` ADD `longitude` DOUBLE NULL DEFAULT NULL;

UPDATE `settings` SET `db_version` = '83' WHERE `id` = 1 LIMIT 1;
