ALTER TABLE `media` ADD `media_medium` varchar(255) default NULL  AFTER `media_link`;

UPDATE `settings` SET `db_version` = '35' WHERE `id`=1 LIMIT 1;