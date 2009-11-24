ALTER TABLE `level` CHANGE `id` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `reporter` DROP `reporter_level`;
ALTER TABLE `reporter` ADD `level_id` INT( 11 ) NULL AFTER `service_id`;

UPDATE `settings` SET `db_version` = '19' WHERE `id` =1 LIMIT 1 ;