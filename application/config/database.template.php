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

// If we are actually looking at a custom domain, figure it out and set that as the subdomain, and then we will switch it

$subdomain = Kohana::config('settings.subdomain');

if(Kohana::config('config.enable_mhi') == TRUE)
{
	$mhi_db = $config['default'];
	$new_connection_config = $mhi_db;
	$tp = $mhi_db['table_prefix'];
	
	// Connect to the MHI database
	$link = mysql_connect($mhi_db['connection']['host'], $mhi_db['connection']['user'], $mhi_db['connection']['pass']) or die();
	mysql_select_db($mhi_db['connection']['database']) or die();

	// If there is no subdomain and the host doesn't match the main mhi domain, this might be a custom domain
	//   ... if this is the case, we need to find the subdomain that the site was originally set up as.
	if($subdomain == '')
	{
		if(Kohana::config('mhi.main_mhi_domain') != $_SERVER['HTTP_HOST'])
		{
			// Looks like the domain accessing MHI is different, let's look up the custom domain
			//    in the database to see if it's registered with a certain subdomain
			$query = 'SELECT site_domain FROM '.$tp.'mhi_site WHERE custom_domain = \''.mysql_real_escape_string($_SERVER['HTTP_HOST']).'\' LIMIT 1;';
			
			$result = mysql_query($query);
			if(mysql_num_rows($result) != 0)
			{
				// Bingo, we found a match
				$subdomain = mysql_result($result,0,'site_domain');
				// Set the subdomain globally
				Kohana::config_set('settings.subdomain', $subdomain);
			}
		}
	}
	
	/**
	* If MHI is enabled, determine the appropriate database settings
	*/
	
	if($subdomain != '')
	{
	
		// Query for the database settings for this subdomain
		$query = 'SELECT '.$tp.'mhi_site_database.user as user, '.$tp.'mhi_site_database.pass as pass, '.$tp.'mhi_site_database.host as host, '.$tp.'mhi_site_database.port as port, '.$tp.'mhi_site_database.database as db  FROM '.$tp.'mhi_site LEFT JOIN '.$tp.'mhi_site_database ON '.$tp.'mhi_site.id = '.$tp.'mhi_site_database.mhi_id WHERE '.$tp.'mhi_site.site_domain = \''.mysql_real_escape_string($subdomain).'\' AND '.$tp.'mhi_site.site_active = 1';
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
	}
	
	mysql_close($link);
	
}
