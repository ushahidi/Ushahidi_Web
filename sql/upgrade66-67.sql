ALTER TABLE `settings` ADD `enable_scheduler_js` tinyint(4) NOT NULL DEFAULT '0';

UPDATE `settings` SET `db_version` = '67' WHERE `id`=1 LIMIT 1;
