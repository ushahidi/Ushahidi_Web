ALTER TABLE `settings` ADD `ftp_server` varchar(100) NULL DEFAULT NULL  AFTER `email_ssl`;
ALTER TABLE `settings` ADD `ftp_user_name` varchar(100) NULL DEFAULT NULL  AFTER `ftp_server`;

UPDATE `settings` SET `db_version` = '39' WHERE `id`=1 LIMIT 1;