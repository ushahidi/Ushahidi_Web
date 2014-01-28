-- UPDATE db_version
UPDATE `settings` SET `value` = 113 WHERE `key` = 'db_version';

-- UPDATE ushahidi_version
UPDATE `settings` SET `value` = '2.7.2' WHERE `key` = 'ushahidi_version';
