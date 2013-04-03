-- Location indexes
ALTER TABLE `location`
	ADD INDEX `latitude` (`latitude`),
	ADD INDEX `longitude` (`longitude`);

-- Category indexes
ALTER TABLE `category`
	ADD INDEX `parent_id` (`parent_id`);

-- Incident_category indexes
ALTER TABLE `incident_category`
	ADD INDEX `incident_id` (`incident_id`),
	ADD INDEX `category_id` (`category_id`);

-- Incident indexes
ALTER TABLE `incident`
	ADD INDEX `incident_mode` (`incident_mode`),
	ADD INDEX `incident_verified` (`incident_verified`);

-- Update DB Version
UPDATE `settings` SET `value` = 104 WHERE `key` = 'db_version';
