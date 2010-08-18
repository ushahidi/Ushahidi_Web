ALTER TABLE `settings` DROP `twitter_username`;
ALTER TABLE `settings` DROP `twitter_password`;
UPDATE `settings` SET `db_version` = '28' WHERE `id`=1 LIMIT 1;