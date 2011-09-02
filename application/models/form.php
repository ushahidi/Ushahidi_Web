<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Forms
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

class Form_Model extends ORM
{
	/**
	 * One-to-many relationship definition
	 * @var array
	 */
	protected $has_many = array('form_field');
	
	/**
	 * Database table name
	 * @var string
	 */
	protected $table_name = 'form';
	
	/**
	 * Given the database id, checks if a form record exists in the database
	 *
	 * @param int $form_id Database id of the form
	 * @return bool
	 */
	public static function is_valid_form($form_id)
	{
		return (intval($form_id) > 0)? ORM::factory('form', $form_id)->loaded : FALSE;
	}
}
