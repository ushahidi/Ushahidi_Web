-- UPDATE db_version
UPDATE `settings` SET `value` = 96 WHERE `key` = 'db_version';

-- UPDATE ushahidi_version
UPDATE `settings` SET `value` = '2.5b' WHERE `key` = 'ushahidi_version';
