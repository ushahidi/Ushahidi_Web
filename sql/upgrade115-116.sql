-- Add feed_geolocation_user as a setting
INSERT into settings(`key`, `value`) values('feed_geolocation_user', '');

-- Update DB Version --
UPDATE `settings` SET `value` = '116' WHERE `key` = 'db_version';
