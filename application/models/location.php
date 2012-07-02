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
