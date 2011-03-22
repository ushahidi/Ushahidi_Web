ALTER TABLE `geometry` ADD `geometry_label` varchar(150) NULL DEFAULT NULL  AFTER `geometry`;
ALTER TABLE `geometry` ADD `geometry_comment` varchar(255) NULL DEFAULT NULL  AFTER `geometry_label`;
ALTER TABLE `geometry` ADD `geometry_strokewidth` varchar(5) NULL DEFAULT NULL  AFTER `geometry_color`;

# Bump up version
UPDATE `settings` SET `db_version` = '48' WHERE `id`=1 LIMIT 1;