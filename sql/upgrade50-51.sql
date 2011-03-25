-- Drop  the Laconica table
DROP TABLE IF EXISTS `laconica`;

-- Remove Laconica service from the list of message services
DELETE FROM `service` WHERE `id` = 4;

-- Remove all Laconica-related columns from the settings table
ALTER TABLE `settings` DROP COLUMN `laconica_username`;
ALTER TABLE `settings` DROP COLUMN `laconica_password`;
ALTER TABLE `settings` DROP COLUMN `laconica_site`;

-- Update the database version
UPDATE `settings` SET db_version = '51' WHERE `id` = 1 LIMIT 1;