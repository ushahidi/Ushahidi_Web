<?php defined('SYSPATH') or die('No direct script access.');

/**
* Model for Services
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

class Service_Model extends ORM
{
	protected $has_many = array('reporter');
	//protected $has_many = array('incident');
	
	// Database table name
	protected $table_name = 'service';
	
	function find_by_id($service_id) {
		return ORM::factory('service', $service_id);
	}
	
	function name() {
		return $this->service_name;
	}
	
	/**
	 * Gets the list of services as an array
	 * @return array
	 */
	public static function get_array() 
	{
    	return ORM::factory('service')->select_list('id', 'service_name');
	}
}
