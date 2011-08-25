ALTER TABLE `settings` ADD `site_submit_report_message` TEXT NOT NULL AFTER `site_copyright_statement`;
ALTER TABLE `settings` ADD `site_banner_id` int(11) default NULL AFTER `site_tagline`;

UPDATE `settings` SET `db_version` = '64' WHERE `id`=1 LIMIT 1;
