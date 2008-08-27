<?php defined('SYSPATH') or die('No direct script access.');

/**
* Additional Settings From Database
*/

$db = new Database();
$db->select('*');
$db->from('settings');
$settings = $db->get()->current();

Kohana::config_set('settings.site_name', 'Ushahidi Beta');