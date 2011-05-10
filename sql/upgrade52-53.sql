ALTER TABLE `category` ADD `category_position` tinyint(4) NOT NULL DEFAULT '0'  AFTER `category_type`;
UPDATE `settings` SET `db_version` = '53' WHERE `id`=1 LIMIT 1;