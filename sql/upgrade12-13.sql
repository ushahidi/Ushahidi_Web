ALTER TABLE `settings` ADD `site_style` VARCHAR( 50 ) NOT NULL DEFAULT 'default' AFTER `site_language` ;

UPDATE `settings` SET `db_version` = '13' WHERE `id` =1 LIMIT 1 ;