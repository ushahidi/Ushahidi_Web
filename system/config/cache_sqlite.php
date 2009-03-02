<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Cache:SQLite
 */
$config['schema'] =
'CREATE TABLE caches(
	id varchar(127) PRIMARY KEY,
	hash char(40) NOT NULL,
	tags varchar(255),
	expiration int,
	cache blob);';