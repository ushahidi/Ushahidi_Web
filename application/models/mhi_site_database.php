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
}
