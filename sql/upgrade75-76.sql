CREATE TABLE `maintenance` (
`allowed_ip` VARCHAR( 15 ) NOT NULL ,
PRIMARY KEY ( `allowed_ip` )
) ENGINE = MYISAM ;

-- Update the database version
UPDATE `settings` SET `db_version` = '76' WHERE `id` = 1 LIMIT 1;