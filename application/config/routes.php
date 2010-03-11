<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package  Core
 *
 * Sets the default route to "welcome"
 */

$config['_default'] = 'main';
$config['feed/atom'] = 'feed/index/atom';

// If MHI is set and we are hitting the main site, forward to the welcome, instance signup page
if(Kohana::config('config.enable_mhi') == TRUE && Kohana::config('settings.subdomain') == '') {
	$config['_default'] = 'mhi';
}