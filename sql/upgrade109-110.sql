-- UPDATE db_version
UPDATE `settings` SET `value` = 110 WHERE `key` = 'db_version';

-- Not including id in case current instalation already has a custom permissions.
INSERT INTO `permissions` (`name`) VALUES ('delete_all_reports');

-- Adding permission to superadmin role - @Robbie: will the ORM pick up on the subqueries?
INSERT INTO `permission_roles` (`role_id`, `permission_id`) VALUES (
	(SELECT `id` FROM `roles` WHERE `name` = 'superadmin' LIMIT 1),
	(SELECT `id` FROM `permission` WHERE `name` = 'delete_all_reports' LIMIT 1),
);
