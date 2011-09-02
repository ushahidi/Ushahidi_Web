<?php defined('SYSPATH') or die('No direct script access.');

/**
* Model for Locations
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Location_Model extends ORM
{
	/**
	 * One-to-many relationship definition
	 * @var array
	 */
	protected $has_many = array('incident', 'media', 'incident_person', 'feed_item', 'reporter', 'checkin');
	
	/**
	 * Many-to-one relationship definition
	 * @var array
	 */
	protected $has_one = array('country');
	
	/**
	 * Database table name
	 * @var string
	 */
	protected $table_name = 'location';
		
	/**
	 * Gets the list of all locations
	 *
	 * @param array $where Key value array with extra predicates for the query
	 * @param int $limit Number of records to fetch
	 * @return Result
	 */
	public static function get_locations($where = array(), $limit = 0)
	{
		// Database table prefix
		$table_prefix = Kohana::config('database.default.table_prefix');
		
		// SQL query
		$sql = 'SELECT id, location_name AS name, country_id, latitude, longitude '
			. 'FROM '.$table_prefix.'location '
			. 'WHERE location_visible = 1 ';
		
		// Check for parameters
		if ( ! empty($where) AND count($where) > 0)
		{
			foreach ($where as $column => $value)
			{
				if ($predicate_items = explode("=", $value))
				{
					if (count($predicate_items) == 2)
					{
						$column = $predicate_items[0];
						$value = $predicate_items[1];
					}
					else
					{
						// Exception handling
						throw new Kohana_Exception('Invalid value in "where" parameter');
					}
				}
				
				$sql .= 'AND '.$column.' = '.$value.' ';	
			}
		}
		
		// Order the records by database ID
		$sql .= 'ORDER BY id DESC ';
		
		// Check if the record limit has been specified
		if ((int)$limit > 0)
		{
			$sql .= 'LIMIT 0, '.$limit;
		}
		
		$db = new Database();
		return $db->query($sql);
	}
	
	/**
	 * Checks if a location id exists in the database
	 * @param int $location_id Database ID of the the location
	 * @return bool
	 */
	public static function is_valid_location($location_id)
	{
		return (intval($location_id) > 0)
			? ORM::factory('location', intval($location_id))->loaded
			: FALSE;
	}
}
