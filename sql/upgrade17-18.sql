ALTER TABLE `settings` ADD `stat_key` VARCHAR( 30 ) NOT NULL AFTER `stat_id` ;

UPDATE `settings` SET `db_version` = '18' WHERE `id` =1 LIMIT 1 ;