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
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Level_Model extends ORM
{	
	/**
	 * One-many relationship definition
	 * @var array
	 */
	protected $has_many = array('reporter');
	
	/**
	 * Database table name
	 * @var string
	 */
	protected $table_name = 'level';
	
	/**
	 * Validates and optionally saves a new level record from an array
	 *
	 * @param array $array Values to check
	 * @param save $save Saves the level record when validation succeeds
	 * @return bool
	 */
	public function validate(array & $array, $save = FALSE)
	{
		// Setup validation
		$array = Validation::factory($array)
					->pre_filter('trim')
					->add_rules('level_title','required', 'length[3,80]')
					->add_rules('level_description','required')
					->add_rules('level_weight','required');
		
		// Pass validation to parent and return
		return parent::validate($array, $save);
	}
	
	/**
	 * Gets the levels as a key => value array
	 *
	 * @return array
	 */
	public static function get_array() 
	{
		return self::factory('level')->select_list('id', 'level_title');
	}
	
	/**
	 * Checks if the specified level id is valid and exists in the database
	 *
	 * @param int $level_id Level to be verified
	 * @return bool
	 */
	public static function is_valid_level($level_id)
	{
		return (preg_match('/[1-9](\d*)/', $level_id) > 0)
			? self::factory('level', $level_id)->loaded
			: FALSE;
	}
}
