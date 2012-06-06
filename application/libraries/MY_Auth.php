<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Custom extensions to the User authorization library. 
 *
 * @package	   Auth
 * @author	   Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license	   http://www.ushahidi.com/license.html
 */
class Auth extends Auth_Core {

	/**
	 * Check if user has specified permission
	 * @param $user User_Model
	 * @param $permission String permission name
	 **/
	public function has_permission($permission = FALSE, $user = FALSE)
	{
		// Get current user if none passed
		if (!$user)
		{
			$user = $this->get_user();
		}
		
		if ($user AND $user instanceof User_Model AND $permission)
		{
			return $user->has_permission($permission);
		}
		
		return FALSE;
	}

	/**
	 * Check if user has admin_access
	 * 
	 * @param object $user
	 * @return bool TRUE if has any permission to access anything. FALSE if not (essentially login only level)
	 */
	public function admin_access($user = FALSE)
	{
		return $this->has_permission('admin_ui', $user);
	}

}
