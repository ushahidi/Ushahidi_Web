<?php defined('SYSPATH') or die('No direct script access.');

/**
* Model for Categories for each Incident
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

class Incident_Category_Model extends ORM
{
	protected $belongs_to = array('incident', 'category');
	
	// Database table name
	protected $table_name = 'incident_category';
	
	/**
	 * Assigns a category id to an incident if it hasn't already been assigned
	 * @param int $incident_id incident to assign the category to
	 * @param int $category_id category id of the category you want to assign to the incident
	 * @return array
	 */
	public static function assign_category_to_incident($incident_id,$category_id)
	{	
		
		// Check to see if it is already added to that category
		//    If it's not, add it.
		
		$incident_category = ORM::factory('incident_category')->where(array('incident_id'=>$incident_id,'category_id'=>$category_id))->find_all();
		
		if( ! $incident_category->count() )
		{
			$new_incident_category = ORM::factory('incident_category');
			$new_incident_category->category_id = $category_id;
			$new_incident_category->incident_id = $incident_id;
			$new_incident_category->save();
		}
		
		return true;
	}
}
