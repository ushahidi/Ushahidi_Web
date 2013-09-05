-- UPDATE db_version
UPDATE `settings` SET `value` = 109 WHERE `key` = 'db_version';

-- DELETE Viddler from plugin list in case
DELETE FROM `plugin` WHERE `plugin_name` = 'viddler';
