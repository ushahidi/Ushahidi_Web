<?php defined('SYSPATH') or die('No direct script access.');

$config['default'] = array
(
	'benchmark'     => TRUE,
	'persistent'    => FALSE,
	'connection'    => array
	(
		'type'     => 'mysql',
		'user'     => 'ushahi2_beta',
		'pass'     => 'kenya',
		'host'     => 'mysql4.ushahididev.com',
		'port'     => FALSE,
		'socket'   => FALSE,
		'database' => 'ushahi2_beta1-1'
	),
	'character_set' => 'utf8',
	'table_prefix'  => '',
	'object'        => TRUE,
	'cache'         => FALSE,
	'escape'        => TRUE
);
