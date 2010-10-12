/* Remove "UNIQUE" Index Key that prevented multiple reports from sharing one location */
ALTER TABLE `incident` DROP INDEX `location_id`;
ALTER TABLE `incident` ADD INDEX `location_id` (`location_id`);

/* Connect Attached Images to Message via Message_ID */
ALTER TABLE `media` ADD `message_id` bigint(20) NULL DEFAULT NULL  AFTER `incident_id`;

/* Added trusted category column */
ALTER TABLE `category` ADD `category_trusted` tinyint(4) NOT NULL default '0' AFTER `category_visible`;

INSERT INTO `category` (`category_type`, `category_title`, `category_description`, `category_color`, `category_visible`, `category_trusted`) VALUES (5, 'Trusted Reports', 'Reports from trusted reporters', '339900', 1, 1);

UPDATE `settings` SET `db_version` = '31' WHERE `id`=1 LIMIT 1;