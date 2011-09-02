<?php defined('SYSPATH') or die('No direct script access.');

/**
* Model for Pages
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

class Page_Model extends ORM
{
	/**
	 * Database table name
	 * @var string
	 */
	protected $table_name = 'page';
	
	/**
	 * Validates and optionally saves a new page record from an array
	 *
	 * @param array $array
	 * @param $save bool
	 * @return bool
	 */
	public function validate(array & $array, $save = FALSE)
	{
		// Setup validation
		$array = Validation::factory($array)
					->pre_filter('trim', TRUE)
					->add_rules('page_title','required', 'length[3,150]')
					->add_rules('page_tab', 'required')
					->add_rules('page_description','required');
		
		if (empty($array->page_tab))
		{
			$array->page_tab = $array->page_title;
		}
				
		// Pass validation to parent and return
		return parent::validate($array, $save);
	}
	
	/**
	 * Checks if a page id is non-zero and exists in the database
	 *
	 * @param int $page_id
	 * @return bool
	 */
	public static function is_valid_page($page_id)
	{
		return (preg_match('/^[1-9](\d*)$/', $page_id) > 0)
			? self::factory('page', $page_id)->loaded
			: FALSE;
	}
}
