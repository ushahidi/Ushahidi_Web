<?php
/**
 * In the beginning there was a MySQL database... Used primarily for MHI
 *
 * @package    Distance
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class DBGenesis_Core {

	function dbgenesis()
	{

	}

	public static function current_db()
	{
		$result = mysql_query('SELECT DATABASE() as db_name;');
		return mysql_result($result,0,'db_name');
	}

	public static function db_exists($db_name)
	{
		$query = 'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = \''.mysql_escape_string($db_name).'\'';
		$result = mysql_query($query);
		if (mysql_num_rows($result) == 0)
		{
			return false;
		}else{
			return true;
		}
	}

	public static function create_db($db_name)
	{
		$query = 'CREATE DATABASE IF NOT EXISTS '.mysql_escape_string($db_name).';';
		$result = mysql_query($query) or die('DB not created.');

		// TODO: Throw an error if we couldn't create the database
		// TODO: Grant permissions to access the newly created database (even though a full dba user will have access anyway)

		return true;
	}

	//db_names must be an array of names
	public static function change_admin_password($db_names,$new_password)
	{
		$mhi_db = Kohana::config('database.default');
		$table_prefix = $mhi_db['table_prefix'];
		$mhi_db_name = $mhi_db['connection']['database'];

		// Switch to new DB for a moment

		$base_db = DBGenesis::current_db();
		foreach($db_names as $name)
		{
			mysql_query('USE '.$base_db.'_'.$name.';');

			// START: Everything that happens in the deployment DB happens below

			$usr = ORM::factory('user','1');

			// Only if the user with id of 1 exists will we attempt to update the password

			if($usr->loaded === true)
			{
				$usr->password = $new_password;
				$usr->save();
			}

			// END: Everything that happens in the deployment DB happens above
		}

		//Switch back to our db, otherwise we would be running off some other deployments DB and that wouldn't work
		mysql_query('USE '.$mhi_db_name);

		return true;

	}

	// User is assoc array (username, name, password, email)
	// Settings is assoc array (site_name, site_tagline)

	public static function populate_db($db_name,$user,$settings)
	{
		$mhi_db = Kohana::config('database.default');
		$table_prefix = $mhi_db['table_prefix'];
		$mhi_db_name = $mhi_db['connection']['database'];

		// Switch to new DB for a moment

		mysql_query('USE '.$db_name);

		$db_schema = file_get_contents('sql/ushahidi.sql');

		// If a table prefix is specified, add it to sql

		if ($table_prefix)
		{
			$find = array(
				'CREATE TABLE IF NOT EXISTS `',
				'INSERT INTO `',
				'ALTER TABLE `',
				'UPDATE `'
				);
			$replace = array(
				'CREATE TABLE IF NOT EXISTS `'.$table_prefix.'_',
				'INSERT INTO `'.$table_prefix.'_',
				'ALTER TABLE `'.$table_prefix.'_',
				'UPDATE `'.$table_prefix.'_'
				);
			$db_schema = str_replace($find, $replace, $db_schema);
		}

		// Split by ; to get the sql statement for creating individual tables.

		$tables = explode(';',$db_schema);

		foreach ($tables as $query)
		{
			$result = mysql_query($query);
		}

		// Set up admin user on new site

		$usr = ORM::factory('user','1');
		$usr->username = $user['username'];
		$usr->name = $user['name'];
		$usr->password = $user['password'];
		$usr->email = $user['email'];
		$usr->save();

		// Save site settings (name, tagline, etc)

		$setgs = new Settings_Model(1);
		$setgs->site_name = $settings['site_name'];
		$setgs->site_tagline = $settings['site_tagline'];
		$setgs->api_google = Kohana::config('settings.api_google');
		$setgs->date_modify = date("Y-m-d H:i:s",time());
		$setgs->save();

		// Set up stats

		$domain = str_ireplace(array('http://','https://'),'',url::base());

		Stats_Model::create_site( $settings['site_name'], 'http://'.$settings['site_domain'].'.'.$domain );

		// Switch back to the appropriate DB

		mysql_query('USE '.$mhi_db_name);

		return true;
	}

}