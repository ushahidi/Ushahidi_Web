ALTER TABLE `users` ADD `approved` TINYINT NOT NULL ;
ALTER TABLE `users` ADD `riverid` VARCHAR( 128 ) NOT NULL AFTER `id`;
ALTER TABLE `users` ADD `needinfo` TINYINT NOT NULL ;
ALTER TABLE `user_tokens` CHANGE `token` `token` VARCHAR( 64 ) NOT NULL;

-- Update the database version
UPDATE `settings` SET `db_version` = '71' WHERE `id` = 1 LIMIT 1;