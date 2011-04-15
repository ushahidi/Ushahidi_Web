ALTER TABLE `category` ADD `parent_id` INT NOT NULL DEFAULT '0' AFTER `id`;

UPDATE `settings` SET `db_version` = '12' WHERE `id` =1 LIMIT 1 ;