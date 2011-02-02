ALTER TABLE `settings` ADD `private_deployment` tinyint(4) NOT NULL DEFAULT '0'  AFTER `cache_pages_lifetime`;

UPDATE `settings` SET `db_version` = '46' WHERE `id`=1 LIMIT 1;