-- Drop organization-related tables
DROP TABLE IF EXISTS `organization_incident`;
DROP TABLE IF EXISTS `organization`;

-- Update the database version
UPDATE `settings` SET `db_version` = 54 WHERE `id` = 1 LIMIT 1;