-- Delete orphaned reports category
DELETE FROM `category` WHERE `id` = '5';

-- Delete entries tied NONE category
DELETE FROM `incident_category` WHERE `category_id` = 5;

-- Change incident_category table structure and set default value for category_id to orphaned reports category i.e 5
ALTER TABLE `incident_category` CHANGE `category_id` `category_id` int(11) NOT NULL default '0';

-- UPDATE db_version
UPDATE `settings` SET `value` = 98 WHERE `key` = 'db_version';
