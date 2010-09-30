ALTER TABLE `settings` ADD `site_message` TEXT NOT NULL AFTER `site_help_page`;

UPDATE `settings` SET `db_version` = '30' WHERE `id`=1 LIMIT 1;