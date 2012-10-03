-- Drop dormant field field_options
ALTER TABLE `form_field`
  DROP `field_options`;

-- Change default form_id to 1 instead of 0
ALTER TABLE  `form_field` CHANGE  `form_id`  `form_id` INT( 11 ) NOT NULL DEFAULT  '1';

-- UPDATE db_version
UPDATE `settings` SET `value` = 100 WHERE `key` = 'db_version';