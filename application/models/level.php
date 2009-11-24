<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Reporter Levels
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Reporter Level Model  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Level_Model extends ORM
{	
	protected $has_many = array('reporter');
	
	// Database table name
	protected $table_name = 'level';
	
	static function get_array() 
	{
		// Level Array
    	$all_levels = ORM::factory('level')->find_all();
    	$level_array = array();
    	foreach ($all_levels as $level) {
    		$level_array[$level->id] = $level->level_title;
    	}
    	return $level_array;
	}
}
