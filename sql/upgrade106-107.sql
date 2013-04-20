-- UPDATE db_version
UPDATE `settings` SET `value` = 107 WHERE `key` = 'db_version';

-- UPDATE ushahidi_version
UPDATE `settings` SET `value` = '2.7b' WHERE `key` = 'ushahidi_version';
