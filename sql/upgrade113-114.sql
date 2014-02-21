-- Add feed_category as a setting
INSERT into settings(`key`, `value`) values('allow_feed_category',0);

-- Update DB Version --
UPDATE `settings` SET `value` = 114 WHERE `key` = 'db_version';
