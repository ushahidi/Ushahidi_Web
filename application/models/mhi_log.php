<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for MHI activity log
 *
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Incident Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class mhi_log_Model extends ORM
{
	protected $table_name = 'mhi_log';

	protected $primary_key = 'id';
	
	protected $sorting = array('time' => 'desc', 'id' => 'asc');

	/**
	 * Record an action in the log
	 *
	 * @param   int mhi_user_id
	 * @param   int log_action_id
	 * @param   int notes
	 * @return  true on success, false on failure
	 */
	function log ($mhi_user_id, $log_action_id, $notes = '')
	{
		$ip = ip2long($this->input->ip_address());

		$table_prefix = Kohana::config('database.default.table_prefix');
		
		$query = "INSERT INTO `".$table_prefix."mhi_log` (`id`,`user_id`,`action_id`,`notes`,`ip`,`time`) VALUES (NULL,'".mysql_escape_string($mhi_user_id)."','".mysql_escape_string($log_action_id)."','".mysql_escape_string($notes)."','".mysql_escape_string($ip)."',CURRENT_TIMESTAMP);";
		
		mysql_query($query);
		
		return true;
	}
	
	public static function get_actions($limit='100',$offset='0')
	{
		$actions = Mhi_Log_Model::get_log_actions();
		$users = Mhi_User_Model::get_all_users();
		
		$result = ORM::factory('mhi_log')->find_all($limit,$offset);
		foreach ($result as $res)
		{
			if($res->user_id != 0)
			{
				$array[$res->id]['email'] = $users[$res->user_id]['email'];
			} else {
				$array[$res->id]['email'] = 'unknown';
			}
			$array[$res->id]['action'] = $actions[$res->action_id];
			$array[$res->id]['notes'] = $res->notes;
			$array[$res->id]['ip'] = Mhi_Log_Model::_int_oct($res->ip);
			$array[$res->id]['time'] = $res->time;
		}
		
		return $array;
	}
	
	// Returns an array of the action ids and their names
	public static function get_log_actions()
	{
		$result = ORM::factory('mhi_log_actions')->find_all();
		$array = array();
		foreach ($result as $res)
		{
			$array[$res->id] = $res->description;
		}
		
		return $array;
	}
	
	public function _int_oct($ip) {
		
		/* Set variable to float */
		$ip = (float) $ip;
		
		/* FIX for silly PHP integer syndrome */
		$fix = 0;
		if($ip > 2147483647) {
			$fix = 16777216;
		}
		
		if(is_numeric($ip))
		{
			return sprintf("%u.%u.%u.%u",
				$ip / 16777216,
				(($ip % 16777216) + $fix) / 65536,
				(($ip % 65536) + $fix / 256) / 256,
				($ip % 256) + $fix / 256 / 256
				);
		} else {
			return '';
		}
	}
}
