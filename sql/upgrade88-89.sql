UPDATE `roles` SET `access_level` = '100' WHERE `name` = 'superadmin';
UPDATE `roles` SET `access_level` = '90' WHERE `name` = 'admin';
UPDATE `roles` SET `access_level` = '10' WHERE `name` = 'member';
UPDATE `settings` SET `db_version` = '89' WHERE `id`=1 LIMIT 1;
