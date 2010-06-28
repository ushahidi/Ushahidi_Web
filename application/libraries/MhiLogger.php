<?php
/**
 * MHI Logger Library
 * Uses a variety of methods to geocode locations and feeds
 * 
 * @package    GeoCoder
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */

class MhiLogger_Core {
	
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
	
}