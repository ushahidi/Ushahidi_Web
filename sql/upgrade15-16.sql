ALTER TABLE `settings` ADD `site_contact_page` TINYINT NOT NULL DEFAULT '1' AFTER `site_timezone` ;
ALTER TABLE `settings` ADD `site_help_page` TINYINT NOT NULL DEFAULT '1' AFTER `site_contact_page` ;

UPDATE `settings` SET `db_version` = '16' WHERE `id` =1 LIMIT 1 ;