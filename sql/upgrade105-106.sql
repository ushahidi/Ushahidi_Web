-- Alter form_field.field_default to TEXT column
-- Alter field_ispublic fields to match role id
ALTER TABLE `form_field`
	MODIFY COLUMN `field_default` TEXT,
	MODIFY COLUMN `field_ispublic_visible` INT(11) NOT NULL DEFAULT '0',
	MODIFY COLUMN `field_ispublic_submit` INT(11) NOT NULL DEFAULT '0';

-- Update DB Version
UPDATE `settings` SET `value` = 106 WHERE `key` = 'db_version';
