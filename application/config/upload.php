<?php defined('SYSPATH') or die('No direct script access.');

$config['directory'] = DOCROOT.'media/uploads';
$config['relative_directory'] = 'media/uploads';
$config['create_directories'] = TRUE;
$conif['remove_spaces'] = TRUE;

// If MHI is on, find the subdomain (for MHI) and add a slash to the beginning of it
if(Kohana::config('config.enable_mhi') == TRUE)
{
	if(substr_count($_SERVER["HTTP_HOST"],'.') > 1)
	{
		$subdomain = substr($_SERVER["HTTP_HOST"],0,strpos($_SERVER["HTTP_HOST"],'.'));
		$config['directory'] = DOCROOT.'media/uploads'.'/'.$subdomain;
		$config['relative_directory'] = 'media/uploads'.'/'.$subdomain;
	}
}