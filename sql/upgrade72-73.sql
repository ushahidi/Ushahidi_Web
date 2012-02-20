-- If there happens to be a category with id 5, assign it a different id
UPDATE `category` SET `id` = '100' WHERE `id` = '5';

-- Update entries tied to non existent categories in the incident_category table
UPDATE `incident_category` SET `category_id` = '5' WHERE NOT EXISTS (select `category_title` from category where `id` = category_id);

-- Insert orphaned reports category
INSERT INTO `category` (`id`, `category_type`, `category_title`, `category_description`, `category_color`, `category_visible`, `category_trusted`) VALUES
(5, 5, 'NONE', 'Holds orphaned reports', '009887', 1, 1);

-- Change incident_category table structure and set default value for category_id to orphaned reports category i.e 5
ALTER TABLE `incident_category` CHANGE `category_id` `category_id` int(11) NOT NULL default '5',   

-- Update the database version
UPDATE `settings` SET `db_version` = '73' WHERE `id` = 1 LIMIT 1;