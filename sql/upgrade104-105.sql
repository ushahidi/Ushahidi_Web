-- Add twitter api key column
INSERT IGNORE INTO `settings` VALUES (' ','twitter_api_key',null);

-- Add twitter api key secret column
INSERT IGNORE INTO `settings` VALUES (' ','twitter_api_key_secret',null);

-- Add twitter token column
INSERT IGNORE INTO `settings` VALUES (' ','twitter_token',null);

-- Add twitter token secret column
INSERT IGNORE INTO `settings` VALUES (' ','twitter_token_secret',null);

-- Update DB Version
UPDATE `settings` SET `value` = 105 WHERE `key` = 'db_version';
