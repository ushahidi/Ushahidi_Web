<?php defined('SYSPATH') or die('No direct script access.');

/**
* Check for maintenance mode
*   In order to put your site in maintenance mode using this method, add at least one
*   row to the `maintenance` table. If you don't want to allow any IP addresses, simply
*   add a random string to the allowed_ip field. A better method for putting your
*   site into maintenance mode is to simply change the filename of your maintenance_off.php
*   to maintenance.php and all users will be told the site is undergoing maintenance.
*/

// running at the command line, fake some server values
if (php_sapi_name() == 'cli')
{
	$_SERVER['SERVER_PROTOCOL'] = "HTTP/1.1";
	$_SERVER['HTTP_HOST'] = 'localhost';
	$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
	$_SERVER['REQUEST_URI'] = Router::$current_uri;
}

// Grab the IP address in case we need to use it for maintenance mode
$ip_address = FALSE;
if ( ! empty($_SERVER['HTTP_CLIENT_IP']))
{
	$ip_address = $_SERVER['HTTP_CLIENT_IP'];
}
elseif ( ! empty($_SERVER['HTTP_X_FORWARDED_FOR']))
{
	$ip_address  = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
else
{
	$ip_address = $_SERVER['REMOTE_ADDR'];
}

$maintenance = FALSE;
try {
	$db = Database::instance();
	$maintenance_ips = $db->query("SELECT `allowed_ip` FROM `".Kohana::config('database.default.table_prefix')."maintenance`;");
	foreach ($maintenance_ips as $row)
	{
		// Assume we will be in maintenance mode now
		$maintenance = TRUE;

		// Check if we should be allowed to bypass maintenance
		if ($ip_address == $row->allowed_ip)
		{
			$maintenance = FALSE;
			// Since we already matched an IP, no need to keep looping
			break;
		}
	}
}
catch (Exception $e)
{}

// If we are in maintenance mode and didn't match the IP, show maintenance message
if ($maintenance == TRUE)
{
	// Find out maintenance file and output or output a simple message

	header("Status: 503 Service Temporarily Unavailable");

	if(file_exists('maintenance_off.php'))
	{
		// maintenance_off.php is our default maintenance message file
		$contents = file_get_contents('maintenance_off.php');
	}
	elseif(file_exists('maintenance.php'))
	{
		// maintenance.php shouldn't exist if we've gotten this far but check anyway
		$contents = file_get_contents('maintenance.php');
	}
	else
	{
		$contents = Kohana::lang('maintenance.message');
	}

	// Kill the script and tell the user the site is down for maintenance
	die($contents);

}

?>
