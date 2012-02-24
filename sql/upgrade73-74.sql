-- Check for incidents that have no categories, and assign them to orphaned reports category
INSERT into `incident_category` (`incident_id`) SELECT `e`.`id` FROM `incident` e
WHERE NOT EXISTS ( SELECT DISTINCT (`i`.`id`) FROM `incident` i JOIN `incident_category` ic ON `ic`.`incident_id` = `i`.`id` WHERE `e`.`id` = `ic`.`incident_id`);

-- Unapprove orphaned reports that were imported
UPDATE `incident` SET `incident_active` = 0 WHERE `id` in (SELECT `incident_id` FROM `incident_category` WHERE `category_id` = 5);

-- Update the database version
UPDATE `settings` SET `db_version` = '74' WHERE `id` = 1 LIMIT 1;