--
-- Table structure for table `layer`
--

CREATE TABLE `layer` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`layer_name` VARCHAR( 255 ) NULL ,
	`layer_url` VARCHAR( 255 ) NULL ,
	`layer_file` VARCHAR( 100 ) NULL ,
	`layer_color` VARCHAR( 20 ) NULL ,
	`layer_visible` TINYINT NOT NULL DEFAULT '1'
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;


UPDATE `settings` SET `db_version` = '21' WHERE `id`=1 LIMIT 1;