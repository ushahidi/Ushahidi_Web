ALTER TABLE `users` ADD `code` VARCHAR(30) NULL DEFAULT NULL AFTER `color`;
ALTER TABLE `users` ADD `confirmed` TINYINT(1) NOT NULL DEFAULT '0' AFTER `code`;
UPDATE `settings` SET `db_version` = '59' WHERE `id`=1 LIMIT 1;