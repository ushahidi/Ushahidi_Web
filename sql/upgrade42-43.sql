/** Add a new column to roles that shall differentiate superadmins from normal admins */
ALTER TABLE `roles` ADD COLUMN `manage_roles` tinyint(4) NOT NULL default 0;

-- Enable this privilege for the super admin role
UPDATE `roles` SET `manage_roles` = 1 WHERE `name` = 'superadmin';

-- Update the database version
UPDATE `settings` SET `db_version` = 43 WHERE `id` = 1 LIMIT 1;