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

	/**
	 * Attempt to log user in via HTTP BASIC AUTH
	 *
 	 * @return bool
 	 */
	public function http_auth_login() {

		//Get username and password
		if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
		{
			$username = filter_var($_SERVER['PHP_AUTH_USER'],
				FILTER_SANITIZE_STRING,
				FILTER_FLAG_ENCODE_HIGH|FILTER_FLAG_ENCODE_LOW);
 
			$password = filter_var($_SERVER['PHP_AUTH_PW'],
				FILTER_SANITIZE_STRING,
				FILTER_FLAG_ENCODE_HIGH|FILTER_FLAG_ENCODE_LOW);
 
			$email = FALSE;
			if(kohana::config('riverid.enable') == TRUE && filter_var($username, FILTER_VALIDATE_EMAIL))
			{
				$email = $username;
			}
 
			try
			{
				if ($this->login($username, $password, FALSE, $email))
				{
					return TRUE;
				}
			}
			catch (Exception $e)
			{
				return FALSE;
			}
 
		}
 
		return FALSE;
	}

	/**
     * Sends an HTTP AUTH prompt.
     *
     * @param int user_id - The currently logged in user id to be passed as the
     *                      realm value.
     * @return void
     */
	public function http_auth_prompt_login($user_id = 0)
	{
		header('WWW-Authenticate: Basic realm="'.$user_id.'"');
		header('HTTP/1.0 401 Unauthorized');
		die(sprintf("%s, %s",Kohana::lang('auth.username.required'),Kohana::lang('auth.password.required')));
    }

}
