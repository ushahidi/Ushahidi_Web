ALTER TABLE `settings` CHANGE `default_map` `default_map` varchar(100) NOT NULL DEFAULT 'google_normal';

UPDATE `settings` SET `db_version` = '26' WHERE `id`=1 LIMIT 1;
UPDATE `settings` SET `ushahidi_version` = '1.1.0' WHERE `id`=1 LIMIT 1;