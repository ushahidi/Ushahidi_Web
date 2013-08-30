-- UPDATE db_version
UPDATE `settings` SET `value` = 108 WHERE `key` = 'db_version';

-- UPDATE ushahidi_version
UPDATE `settings` SET `value` = '2.7.1b' WHERE `key` = 'ushahidi_version';

-- DELETE Viddler from plugin list in case
DELETE FROM `plugin` WHERE `plugin_name` = 'viddler';