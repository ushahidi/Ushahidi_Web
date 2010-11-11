ALTER TABLE `settings` ADD `site_copyright_statement` TEXT DEFAULT NULL AFTER `site_message`; 

UPDATE `settings` SET `db_version` = '37' WHERE `id`=1 LIMIT 1;