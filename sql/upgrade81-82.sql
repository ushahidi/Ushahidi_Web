ALTER TABLE `settings` ADD `default_map_all_icon_id` INT( 11 ) DEFAULT NULL AFTER `default_map_all`;

UPDATE `settings` SET `db_version` = '82' WHERE `id` = 1 LIMIT 1;
