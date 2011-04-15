<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * ORM Auth driver.
 *
 * $Id: ORM.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Auth_ORM_Driver extends Auth_Driver {

	/**
	 * Checks if a session is active.
	 *
	 * @param   string   role name
	 * @param   array    collection of role names
	 * @return  boolean
	 */
	public function logged_in($role)
	{
		$status = FALSE;

		// Get the user from the session
		$user = $this->session->get($this->config['session_key']);

		if (is_object($user) AND $user instanceof User_Model AND $user->loaded)
		{
			// Everything is okay so far
			$status = TRUE;

			if ( ! empty($role))
			{

				// If role is an array
				if (is_array($role))
				{
					// Check each role
					foreach ($role as $role_iteration)
					{
						if ( ! is_object($role_iteration))
						{
							$role_iteration = ORM::factory('role', $role_iteration);
						}
						// If the user doesn't have the role
						if( ! $user->has($role_iteration))
						{
							// Set the status false and get outta here
							$status = FALSE;
							break;
						}
					}
				}
				else
				{
				// Else just check the one supplied roles
					if ( ! is_object($role))
					{
						// Load the role
						$role = ORM::factory('role', $role);
					}

					// Check that the user has the given role
					$status = $user->has($role);
				}
			}
		}

		return $status;
	}

	/**
	 * Logs a user in.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable auto-login
	 * @return  boolean
	 */
	public function login($user, $password, $remember)
	{
		if ( ! is_object($user))
		{
			// Load the user
			$user = ORM::factory('user', $user);
		}

		// If the passwords match, perform a login
		if ($user->has(ORM::factory('role', 'login')) AND $user->password === $password)
		{
			if ($remember === TRUE)
			{
				// Create a new autologin token
				$token = ORM::factory('user_token');

				// Set token data
				$token->user_id = $user->id;
				$token->expires = time() + $this->config['lifetime'];
				$token->save();

				// Set the autologin cookie
				cookie::set('authautologin', $token->token, $this->config['lifetime']);
			}

			// Finish the login
			$this->complete_login($user);

			return TRUE;
		}

		// Login failed
		return FALSE;
	}

	/**
	 * Forces a user to be logged in, without specifying a password.
	 *
	 * @param   mixed    username
	 * @return  boolean
	 */
	public function force_login($user)
	{
		if ( ! is_object($user))
		{
			// Load the user
			$user = ORM::factory('user', $user);
		}

		// Mark the session as forced, to prevent users from changing account information
		$_SESSION['auth_forced'] = TRUE;

		// Run the standard completion
		$this->complete_login($user);
	}

	/**
	 * Logs a user in, based on the authautologin cookie.
	 *
	 * @return  boolean
	 */
	public function auto_login()
	{
		if ($token = cookie::get('authautologin'))
		{
			// Load the token and user
			$token = ORM::factory('user_token', $token);

			if ($token->loaded AND $token->user->loaded)
			{
				if ($token->user_agent === sha1(Kohana::$user_agent))
				{
					// Save the token to create a new unique token
					$token->save();

					// Set the new token
					cookie::set('authautologin', $token->token, $token->expires - time());

					// Complete the login with the found data
					$this->complete_login($token->user);

					// Automatic login was successful
					return TRUE;
				}

				// Token is invalid
				$token->delete();
			}
		}

		return FALSE;
	}

	/**
	 * Log a user out and remove any auto-login cookies.
	 *
	 * @param   boolean  completely destroy the session
	 * @return  boolean
	 */
	public function logout($destroy)
	{
		if (cookie::get('authautologin'))
		{
			// Delete the autologin cookie to prevent re-login
			cookie::delete('authautologin');
		}

		return parent::logout($destroy);
	}

	/**
	 * Get the stored password for a username.
	 *
	 * @param   mixed   username
	 * @return  string
	 */
	public function password($user)
	{
		if ( ! is_object($user))
		{
			// Load the user
			$user = ORM::factory('user', $user);
		}

		return $user->password;
	}

	/**
	 * Complete the login for a user by incrementing the logins and setting
	 * session data: user_id, username, roles
	 *
	 * @param   object   user model object
	 * @return  void
	 */
	protected function complete_login(User_Model $user)
	{
		// Update the number of logins
		$user->logins += 1;

		// Set the last login date
		$user->last_login = time();

		// Save the user
		$user->save();

		return parent::complete_login($user);
	}

} // End Auth_ORM_Driver