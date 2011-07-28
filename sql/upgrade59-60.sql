ALTER TABLE `settings` ADD `facebook_appid` VARCHAR(150)  NULL  DEFAULT NULL  AFTER `checkins`;
ALTER TABLE `settings` ADD `facebook_appsecret` VARCHAR(150)  NULL  DEFAULT NULL  AFTER `facebook_appid`;
UPDATE `settings` SET `db_version` = '60' WHERE `id`=1 LIMIT 1;

