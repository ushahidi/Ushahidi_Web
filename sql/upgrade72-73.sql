-- Insert orphaned reports category
INSERT INTO `category` (`id`, `category_type`, `category_title`, `category_description`, `category_color`, `category_visible`, `category_trusted`) VALUES
(5, 5, 'NONE', 'Holds orphaned reports', '009887', 1, 1);

-- Change incident_category table structure and set default value for category_id to orphaned reports category i.e 5
ALTER TABLE `incident_category` CHANGE `category_id` `category_id` int(11) NOT NULL default '5',   

-- Update the database version
UPDATE `settings` SET `db_version` = '73' WHERE `id` = 1 LIMIT 1;