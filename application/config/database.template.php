<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Sample database configuration file for Ushahidi.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Dashboard Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

$config['default'] = array
(
	'benchmark'     => TRUE,
	'persistent'    => FALSE,
	'connection'    => array
	(
		'type'     => 'mysql',
		'user'     => 'username',
		'pass'     => 'password',
		'host'     => 'localhost',
		'port'     => FALSE,
		'socket'   => FALSE,
		'database' => 'db'
	),
	'character_set' => 'utf8',
	'table_prefix'  => '',
	'object'        => TRUE,
	'cache'         => FALSE,
	'escape'        => TRUE
);



/*
 *  ----  MHI db routing below.
 *  ----  Everything below this line shouldn't be touched unless you know what you're doing.
 */

$subdomain = '';
if(substr_count($_SERVER["HTTP_HOST"],'.') > 1) $subdomain = substr($_SERVER["HTTP_HOST"],0,strpos($_SERVER["HTTP_HOST"],'.'));

/**
* If MHI is enabled, determine the appropriate database settings
*/

if($subdomain != '' && Kohana::config('config.enable_mhi') == TRUE)
{
	$mhi_db = $config['default'];
	$new_connection_config = $mhi_db;
	
	// Connect to the MHI database
	$link = mysql_connect($mhi_db['connection']['host'], $mhi_db['connection']['user'], $mhi_db['connection']['pass']) or die();
	mysql_select_db($mhi_db['connection']['database']) or die();

	// Query for the database settings for this subdomain
	$tp = $mhi_db['table_prefix'];
	$query = 'SELECT '.$tp.'mhi_site_database.user as user, '.$tp.'mhi_site_database.pass as pass, '.$tp.'mhi_site_database.host as host, '.$tp.'mhi_site_database.port as port, '.$tp.'mhi_site_database.database as db  FROM '.$tp.'mhi_site LEFT JOIN '.$tp.'mhi_site_database ON '.$tp.'mhi_site.id = '.$tp.'mhi_site_database.mhi_id WHERE '.$tp.'mhi_site.site_domain = \''.mysql_escape_string($subdomain).'\' AND '.$tp.'mhi_site.site_active = 1';
	$result = mysql_query($query);
	
	// If this subdomain exists as an MHI instance...
	if(mysql_num_rows($result) != 0)
	{

		// Overwrite database settings so the subdomain will work properly
		
		$config['default'] = array
			(
				'benchmark'     => TRUE,
				'persistent'    => FALSE,
				'connection'    => array
				(
					'type'     => 'mysql',
					'user'     => mysql_result($result,0,'user'),
					'pass'     => mysql_result($result,0,'pass'),
					'host'     => mysql_result($result,0,'host'),
					'port'     => FALSE,
					'socket'   => FALSE,
					'database' => mysql_result($result,0,'db')
				),
				'character_set' => 'utf8',
				'table_prefix'  => '',
				'object'        => TRUE,
				'cache'         => FALSE,
				'escape'        => TRUE
			);

	}else{

		// If this site doesn't exist as an instance, we will reset our subdomain and ignore it.

		$base = url::base();
		$url = parse_url($base);
		$domain = strstr($url['host'],'.');
		if($domain != FALSE)
		{
			// Redirect to the homepage if the subdomain isn't a valid deployment

			$domain = ltrim($domain,'.');
			url::redirect($url['scheme'].'://'.$domain);
		}

		Kohana::config_set('settings.subdomain', '');
	}

	mysql_close($link);
}
