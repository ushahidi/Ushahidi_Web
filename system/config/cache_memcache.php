<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Cache:Memcache
 *
 * memcache server configuration.
 */
$config['servers'] = array
(
	array
	(
		'host' => '127.0.0.1',
		'port' => 11211,
		'persistent' => FALSE,
	)
);

/**
 * Enable cache data compression.
 */
$config['compression'] = FALSE;
