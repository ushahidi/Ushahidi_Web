CREATE TABLE `actions_log` (
`id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`action_id` INT NOT NULL ,
`user_id` INT NOT NULL ,
`time` INT( 10 ) NOT NULL
) ENGINE = MYISAM ;

UPDATE `settings` SET `db_version` = '62' WHERE `id`=1 LIMIT 1;