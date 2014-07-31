-- UPDATE db_version
UPDATE `settings` SET `value` = 119 WHERE `key` = 'db_version';

-- UPDATE ushahidi_version
UPDATE `settings` SET `value` = '2.7.4b' WHERE `key` = 'ushahidi_version';
