<?php defined('SYSPATH') or die('No direct script access.');
/**
* Initiate Instance. Verify Install
* If we can't find application/config/database.php, we assume Ushahidi
* is not installed so redirect user to installer
*/

if (!file_exists(DOCROOT."application/config/database.php"))
{
	if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") {
		$url = $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$url = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}

	$installer = "http://${url}installer/";

	if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
		$installer = str_replace('http://', 'https://', $installer);
	}

	url::redirect($installer);

}
