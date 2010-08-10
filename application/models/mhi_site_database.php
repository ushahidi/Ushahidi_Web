<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for MHI databases
 *
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Incident Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class mhi_site_database_Model extends ORM
{
	protected $table_name = 'mhi_site_database';

	protected $primary_key = 'mhi_id';

	protected $primary_val = 'database';

	static function db_assigned($db_name)
	{
		// Check if the database name has already been reserved

		$count = ORM::factory('mhi_site_database')->where('database',$db_name)->count_all();
		if ($count != 0)
			return true;

		return false;
	}

	static function assign_db($db_name,$site_id)
	{
		$mhi_db = Kohana::config('database.default');

		$mhi_site_database = ORM::factory('mhi_site_database');
		$mhi_site_database->mhi_id = $site_id;
		$mhi_site_database->user = $mhi_db['connection']['user'];
		$mhi_site_database->pass = $mhi_db['connection']['pass'];
		$mhi_site_database->host = $mhi_db['connection']['host'];
		$mhi_site_database->port = $mhi_db['connection']['port'];
		$mhi_site_database->database = $db_name;
		$mhi_site_database->save();

		return true;
	}

	static function get_all_db_details()
	{
		$result = ORM::factory('mhi_site_database')->find_all();

		$array = array();
		foreach ($result as $res)
			$array[] = $res->database;

		return $array;
	}

	static function update_db($db)
	{
		// Check if the db is even assigned to anyone. This is a requirement.
		if (Mhi_Site_Database_Model::db_assigned($db) == false)
		{
			return false;
		}

		$mhi_db = Kohana::config('database.default');
		$table_prefix = $mhi_db['table_prefix'];
		$mhi_db_name = $mhi_db['connection']['database'];

		$settings = kohana::config('settings');
		$current_version = $settings['db_version'];

		// Switch to new DB for a moment

		mysql_query('USE '.$db.';');

		// START: Everything that happens in the deployment DB happens below
		$settings = ORM::factory('settings', 1);
		$db_version = $settings->db_version;
		$upgrade_to = $db_version + 1;

		// Check if we even need to apply this update
		if ($db_version >= $current_version)
		{
			mysql_query('USE '.$mhi_db_name);
			return false;
		}

		// Check if the update script exists
		$upgrade_schema = @file_get_contents('sql/upgrade'.$db_version.'-'.$upgrade_to.'.sql');
		if ($upgrade_schema == false) {
			mysql_query('USE '.$mhi_db_name);
			return false;
		}

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
			$upgrade_schema = str_replace($find, $replace, $upgrade_schema);
		}

		// Split by ; to get the sql statement for creating individual tables.

		$queries = explode(';',$upgrade_schema);

		//Put a custom mysql_query() here in case you want to run something outside of the sql files.

		foreach ($queries as $query)
		{
			$result = mysql_query($query);
		}

		// END: Everything that happens in the deployment DB happens above

		//Switch back to our db, otherwise we would be running off some other deployments DB and that wouldn't work
		mysql_query('USE '.$mhi_db_name);

	}
}
