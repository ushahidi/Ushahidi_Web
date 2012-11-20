-- UPDATE db_version
UPDATE `settings` SET `value` = 102 WHERE `key` = 'db_version';

-- UPDATE ushahidi_version
UPDATE `settings` SET `value` = '2.6.1' WHERE `key` = 'ushahidi_version';
