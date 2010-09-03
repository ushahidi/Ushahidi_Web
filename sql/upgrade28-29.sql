ALTER TABLE `roles` ADD `reports_view` tinyint(4) NOT NULL default '0' AFTER `description`;
ALTER TABLE `roles` ADD `reports_edit` tinyint(4) NOT NULL default '0' AFTER `reports_view`;
ALTER TABLE `roles` ADD `reports_evaluation` tinyint(4) NOT NULL default '0' AFTER `reports_edit`;
ALTER TABLE `roles` ADD `reports_comments` tinyint(4) NOT NULL default '0' AFTER `reports_evaluation`;
ALTER TABLE `roles` ADD `reports_download` tinyint(4) NOT NULL default '0' AFTER `reports_comments`;
ALTER TABLE `roles` ADD `reports_upload` tinyint(4) NOT NULL default '0' AFTER `reports_download`;
ALTER TABLE `roles` ADD `messages` tinyint(4) NOT NULL default '0' AFTER `reports_upload`;
ALTER TABLE `roles` ADD `messages_reporters` tinyint(4) NOT NULL default '0' AFTER `messages`;
ALTER TABLE `roles` ADD `stats` tinyint(4) NOT NULL default '0' AFTER `messages_reporters`;
ALTER TABLE `roles` ADD `settings` tinyint(4) NOT NULL default '0' AFTER `stats`;
ALTER TABLE `roles` ADD `manage` tinyint(4) NOT NULL default '0' AFTER `settings`;
ALTER TABLE `roles` ADD `users` tinyint(4) NOT NULL default '0' AFTER `manage`;

UPDATE `roles` SET
	`reports_view` = 1,
	`reports_edit` = 1,
	`reports_evaluation` = 1,
	`reports_comments` = 1,
	`reports_download` = 1,
	`reports_upload` = 1,
	`messages` = 1,
	`messages_reporters` = 1,
	`stats` = 1,
	`settings` = 1,
	`manage` = 1,
	`users` = 1
WHERE `id` = 2 OR `id` = 3;

UPDATE `settings` SET `db_version` = '29' WHERE `id`=1 LIMIT 1;