ALTER TABLE `users` ADD `color` VARCHAR( 6 ) NOT NULL DEFAULT 'FF0000';

# Bump up version
UPDATE `settings` SET `db_version` = '49' WHERE `id`=1 LIMIT 1;