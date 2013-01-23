-- Alter form field field_name index to accomodate form_id
ALTER TABLE `form_field` DROP INDEX  `field_name` ,
ADD UNIQUE `field_name` (  `field_name` ,  `form_id` );

-- Add form_title unique constraint
ALTER TABLE `form` ADD UNIQUE (`form_title`);

-- Update DB Version
UPDATE `settings` SET `value` = 103 WHERE `key` = 'db_version';
