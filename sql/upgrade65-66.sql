ALTER TABLE `settings` ADD `alerts_email` tinyint(4) NOT NULL DEFAULT '0';

UPDATE `settings` SET `db_version` = '66' WHERE `id`=1 LIMIT 1;
