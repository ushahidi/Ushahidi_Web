-- Change structure of foreign key role_id to match referenced field roles.id
ALTER TABLE `permissions_roles` CHANGE  `role_id`  `role_id` INT( 11 ) UNSIGNED NOT NULL;

-- Update the DB version
UPDATE `settings` SET `value` = 94 WHERE `key` = 'db_version';
