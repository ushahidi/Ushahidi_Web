-- Drop  the feedback table
DROP TABLE IF EXISTS `feedback`;

-- Drop  the feedback_person table
DROP TABLE IF EXISTS `feedback_person`;

-- Drop  the idp table
DROP TABLE IF EXISTS `idp`;

-- Drop  the pending_users table
DROP TABLE IF EXISTS `pending_users`;

-- Remove idp-related column from the verified table
ALTER TABLE `verified` DROP COLUMN `idp_id`;

-- Update the database version
UPDATE `settings` SET db_version = '52' WHERE `id` = 1 LIMIT 1;
