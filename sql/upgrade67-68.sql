-- Remove incident_source and incident_information columns from the incident table
ALTER TABLE `incident` DROP COLUMN `incident_source`;
ALTER TABLE `incident` DROP COLUMN `incident_information`;

-- Update the database version
UPDATE `settings` SET `db_version` = '68' WHERE `id` = 1 LIMIT 1;
