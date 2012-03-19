-- If there happens to be a category with id 5, assign it a different id
UPDATE `category` SET `id` = '999' WHERE `id` = '5';

-- Added Query: Update any incident tied to recently altered category with the new id
UPDATE `incident_category` set `category_id` = '999' where `category_id` = '5';

-- Check to ensure that child categories of recently altered parent category are also updated
UPDATE `category` set `parent_id` = '999' where `parent_id` = '5';

-- Insert orphaned reports category
INSERT INTO `category` (`id`, `category_type`, `category_title`, `category_description`, `category_color`, `category_visible`, `category_trusted`) VALUES
(5, 5, 'NONE', 'Holds orphaned reports', '009887', 1, 1);

-- Change incident_category table structure and set default value for category_id to orphaned reports category i.e 5
ALTER TABLE `incident_category` CHANGE `category_id` `category_id` int(11) NOT NULL default '5';

-- Remove incident category links that link to no category
DELETE FROM `incident_category` WHERE NOT EXISTS (select `category_title` from `category` where `id` = category_id);

-- Add incidents with no categories deleted above, and assign them to orphaned reports category	
INSERT into `incident_category` (`incident_id`) SELECT `e`.`id` FROM `incident` e	
WHERE NOT EXISTS ( SELECT DISTINCT (`i`.`id`) FROM `incident` i JOIN `incident_category` ic ON `ic`.`incident_id` = `i`.`id` WHERE `e`.`id` = `ic`.`incident_id`);

-- Delete updated entries tied to a non-orphaned report i.e a report with multiple categories
DELETE FROM `incident_category` WHERE `category_id` =5 AND `incident_id` IN (
SELECT `incident_id` FROM (
	SELECT `ic`.`incident_id`
	FROM `incident_category` ic
	GROUP BY `ic`.`incident_id`
	HAVING COUNT( `ic`.`category_id` ) >1
	) 
	AS X);

-- Unapprove orphaned reports
UPDATE `incident` SET `incident_active` = 0 WHERE `id` in (SELECT `incident_id` FROM `incident_category` WHERE `category_id` = 5);

-- Update the database version
UPDATE `settings` SET `db_version` = '73' WHERE `id` = 1 LIMIT 1;