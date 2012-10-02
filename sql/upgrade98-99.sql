-- Add unique constraint on custom form field name/title
ALTER TABLE  `form_field` ADD UNIQUE (
`field_name`
);

-- UPDATE db_version
UPDATE `settings` SET `value` = 99 WHERE `key` = 'db_version';