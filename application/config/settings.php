<?php defined('SYSPATH') or die('No direct script access.');

/**
* SITE CONFIGURATIONS
*/

// Find the subdomain
$subdomain = '';
if(substr_count($_SERVER["HTTP_HOST"],'.') > 1) $subdomain = substr($_SERVER["HTTP_HOST"],0,strpos($_SERVER["HTTP_HOST"],'.'));

$config = array
(
	'site_name' => 'Ushahidi',
	'site_email' => '',
	'default_map' => '',
	'api_google' => '',
	'api_yahoo' => '',
	'default_city' => '',
	'default_country' => '',
	'default_lat' => '',
	'default_lon' => '',
	'default_zoom' => '',
	'items_per_page' => '5',
	'items_per_page_admin' => '20',
	'items_per_api_request' => '20',
	'api_url' => '',
	'api_url_all' => '',
	'subdomain' => $subdomain,
	'title_delimiter' => ' | '
);
