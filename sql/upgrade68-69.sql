-- Remove incident_source and incident_information columns from the incident table
ALTER TABLE `users` CHANGE `username` `username` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

-- Update the database version
UPDATE `settings` SET `db_version` = '69' WHERE `id` = 1 LIMIT 1;
