ALTER TABLE `settings` ADD `cache_pages` tinyint(4) NOT NULL DEFAULT '0'  AFTER `allow_clustering`;
ALTER TABLE `settings` ADD `cache_pages_lifetime` int(4) NOT NULL DEFAULT '1800'  AFTER `cache_pages`;

UPDATE `settings` SET `db_version` = '33' WHERE `id`=1 LIMIT 1;