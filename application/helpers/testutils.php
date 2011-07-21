<?php defined('SYSPATH') or die('No direct script access');
/**
 * Helper library for the unit tests
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

// Table prefix for the tables
define('TABLE_PREFIX', Kohana::config('database.default.table_prefix'));

class testutils_Core {	
	
	/**
	 * Gets a random value from the id column of the specified database table
	 *
	 * @param string $table_name Database table name from which to fetch the id
	 * @return int
	 */
	public static function get_random_id($table_name, $where = '')
	{
		// Database instance for the query
		$db = new Database();
		
		// Fetch all values from the ID column of the table
		$result = $db->query('SELECT id FROM '.TABLE_PREFIX.$table_name.' '.$where)->as_array();
		
		// Get a random id
		return (count($result) > 0) ? $result[array_rand($result)]->id : count($result);
	}
}
?>