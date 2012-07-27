-- Add enable_timeline as a setting to make it an off/on feature
INSERT INTO `settings` (`key`, `value`) values ('enable_timeline','0');

-- UPDATE db_version
UPDATE `settings` SET `value` = 95 WHERE `key` = 'db_version';
