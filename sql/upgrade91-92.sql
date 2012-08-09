-- Create the new_settings table
CREATE TABLE IF NOT EXISTS `new_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL DEFAULT '' COMMENT 'Unique identifier for the configuration parameter',
  `value` text COMMENT 'Value for the settings parameter',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_settings_key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Populate
INSERT INTO `new_settings`(`key`, `value`) 
SELECT 'site_name' AS `key`, `site_name` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'site_tagline' AS `key`, `site_tagline` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'site_banner_id' AS `key`, `site_banner_id` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'site_email' AS `key`, `site_email` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'site_key' AS `key`, `site_key` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'site_language' AS `key`, `site_language` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'site_style' AS `key`, `site_style` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'site_timezone' AS `key`, `site_timezone` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'site_contact_page' AS `key`, `site_contact_page` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'site_help_page' AS `key`, `site_help_page` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'site_message' AS `key`, `site_message` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'site_copyright_statement' AS `key`, `site_copyright_statement` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'site_submit_report_message' AS `key`, `site_submit_report_message` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'allow_reports' AS `key`, `allow_reports` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'allow_comments' AS `key`, `allow_comments` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'allow_feed' AS `key`, `allow_feed` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'allow_stat_sharing' AS `key`, `allow_stat_sharing` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'allow_clustering' AS `key`, `allow_clustering` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'cache_pages' AS `key`, `cache_pages` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'cache_pages_lifetime' AS `key`, `cache_pages_lifetime` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'private_deployment' AS `key`, `private_deployment` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'default_map' AS `key`, `default_map` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'default_map_all' AS `key`, `default_map_all` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'default_map_all_icon_id' AS `key`, `default_map_all_icon_id` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'api_google' AS `key`, `api_google` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'api_live' AS `key`, `api_live` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'api_akismet' AS `key`, `api_akismet` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'default_country' AS `key`, `default_country` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'multi_country' AS `key`, `multi_country` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'default_city' AS `key`, `default_city` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'default_lat' AS `key`, `default_lat` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'default_lon' AS `key`, `default_lon` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'default_zoom' AS `key`, `default_zoom` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'items_per_page' AS `key`, `items_per_page` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'items_per_page_admin' AS `key`, `items_per_page_admin` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'sms_provider' AS `key`, `sms_provider` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'sms_no1' AS `key`, `sms_no1` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'sms_no2' AS `key`, `sms_no2` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'sms_no3' AS `key`, `sms_no3` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'google_analytics' AS `key`, `google_analytics` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'twitter_hashtags' AS `key`, `twitter_hashtags` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'blocks' AS `key`, `blocks` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'blocks_per_row' AS `key`, `blocks_per_row` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'date_modify' AS `key`, `date_modify` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'stat_id' AS `key`, `stat_id` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'stat_key' AS `key`, `stat_key` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'email_username' AS `key`, `email_username` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'email_password' AS `key`, `email_password` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'email_port' AS `key`, `email_port` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'email_host' AS `key`, `email_host` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'email_servertype' AS `key`, `email_servertype` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'email_ssl' AS `key`, `email_ssl` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'ftp_server' AS `key`, `ftp_server` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'ftp_user_name' AS `key`, `ftp_user_name` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'alerts_email' AS `key`, `alerts_email` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'checkins' AS `key`, `checkins` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'facebook_appid' AS `key`, `facebook_appid` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'facebook_appsecret' AS `key`, `facebook_appsecret` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'db_version' AS `key`, `db_version` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'ushahidi_version' AS `key`, `ushahidi_version` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'allow_alerts' AS `key`, `allow_alerts` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'require_email_confirmation' AS `key`, `require_email_confirmation` AS `value` FROM `settings` WHERE `id` = 1
UNION
SELECT 'manually_approve_users' AS `key`, `manually_approve_users` AS `value` FROM `settings` WHERE `id` = 1;

-- Drop the existing settings table
DROP TABLE IF EXISTS `settings`;

-- Rename the new settings table to `settings`
RENAME TABLE `new_settings` TO `settings`;

-- Update the DB version
UPDATE `settings` SET `value` = 92 WHERE `key` = 'db_version';
