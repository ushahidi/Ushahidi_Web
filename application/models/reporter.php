<?php defined('SYSPATH') or die('No direct script access.');

/**
* Model for Reporters
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

class Reporter_Model extends ORM
{
	protected $belongs_to = array('service','level','location');
	protected $has_many = array('incident','message');
	
	// Database table name
	protected $table_name = 'reporter';
	
	// Create a Reporter if they do not already exist
	function add($reporter_attrs)
	{
		if (count($this->where('service_id', $reporter_attrs['service_id'])->
		                 where('service_account', $reporter_attrs['service_account'])->
		                 find_all()) == 0)
		{
			$this->db->insert('reporter', $reporter_attrs);
		}
	}
}
