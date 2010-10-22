ALTER TABLE `category` ADD `category_image_thumb` varchar(100) NULL DEFAULT NULL  AFTER `category_image`;

UPDATE `settings` SET `db_version` = '32' WHERE `id`=1 LIMIT 1;