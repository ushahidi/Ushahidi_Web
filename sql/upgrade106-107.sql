-- UPDATE db_version
UPDATE `settings` SET `value` = 107 WHERE `key` = 'db_version';

-- UPDATE ushahidi_version
UPDATE `settings` SET `value` = '2.7' WHERE `key` = 'ushahidi_version';
