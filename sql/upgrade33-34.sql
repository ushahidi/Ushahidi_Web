ALTER TABLE `settings` ADD `sms_provider` varchar(100) NULL DEFAULT NULL  AFTER `items_per_page_admin`;

ALTER TABLE `settings` DROP `clickatell_api`;
ALTER TABLE `settings` DROP `clickatell_username`;
ALTER TABLE `settings` DROP `clickatell_password`;
ALTER TABLE `settings` DROP `frontlinesms_key`;

UPDATE `settings` SET `db_version` = '34' WHERE `id`=1 LIMIT 1;