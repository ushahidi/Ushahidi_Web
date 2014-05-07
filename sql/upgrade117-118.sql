-- Add max_upload_size as a setting
INSERT into settings(`key`, `value`) values('max_upload_size', '10');

-- UPDATE db_version
UPDATE `settings` SET `value` = 118 WHERE `key` = 'db_version';
