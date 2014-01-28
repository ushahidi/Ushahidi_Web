-- UPDATE db_version
UPDATE `settings` SET `value` = 111 WHERE `key` = 'db_version';

-- Setting default theme for any themes that might be using the removed check in theme
UPDATE `settings` SET `value` = 'default' WHERE `key` = 'site_style' AND `value` = 'ci_cumulus';