<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Boolean TRUE/FALSE to turn on the saving of benchmarked figures
 * to a database
 */
$config['enable'] = FALSE;

/**
 * Connection details for the database where we want to save benchmark
 * figures. This can be the database for your site but we reccomend
 * using a different database (maybe on another server) since it can
 * use a lot of resources depending on how many times your deployment
 * is accessed. Please see the schema for this table at the end of the file.
 */
$config['db'] = array
(
	'user'     => 'username_here',
	'pass'     => 'password_here',
	'host'     => 'localhost',
	'port'     => FALSE,
	'database' => 'database_name_here',
	'table_prefix'  => ''
);

/**
 * Schema for benchmark table data:
 
 CREATE TABLE `benchmark` (
	`id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	`name` VARCHAR( 100 ) NOT NULL ,
	`time` DECIMAL(5,4) NOT NULL ,
	`memory` INT NOT NULL
	) ENGINE = MyISAM;
	
 *
 */
 
?>