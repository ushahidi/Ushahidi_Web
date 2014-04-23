-- UPDATE db_version
UPDATE `settings` SET `value` = 117 WHERE `key` = 'db_version';

-- UPDATE ushahidi_version
UPDATE `settings` SET `value` = '2.7.3' WHERE `key` = 'ushahidi_version';
