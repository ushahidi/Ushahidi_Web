ALTER TABLE `settings` ADD `map_point_reports` tinyint(4) NOT NULL DEFAULT '0' AFTER `allow_clustering`;
UPDATE `settings` SET `db_version` = '88' WHERE `id`=1 LIMIT 1;
UPDATE `settings` SET `ushahidi_version` = '2.3.2' WHERE `id`=1 LIMIT 1;
