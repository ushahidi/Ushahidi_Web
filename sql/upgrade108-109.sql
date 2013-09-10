-- UPDATE db_version
UPDATE `settings` SET `value` = 109 WHERE `key` = 'db_version';

-- UPDATE ushahidi_version
UPDATE `settings` SET `value` = '2.7.1' WHERE `key` = 'ushahidi_version';
