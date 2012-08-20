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

		if(kohana::config('riverid.enable') == true)
		{
			// RiverID is being used so we need to go through some extra
			//   steps to authenticate before moving forward
			self::auto_login();
		}

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
	 * Determines the function to use when logging in a user
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable auto-login
	 * @param   string   email
	 * @return  boolean
	 */
	public function login($user, $password, $remember, $email = FALSE)
	{
		if(kohana::config('riverid.enable') == true)
		{
			return $this->login_riverid($user, $password, $remember, $email);
		}
		else
		{
			return $this->login_standard($user, $password, $remember);
		}
	}

	/**
	 * Performs a login by setting token, etc.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable auto-login
	 * @return  boolean
	 */
	public function perform_login($user,$remember,$riverid=false)
	{
		// In case we need to check if the user has confirmed their address, do that here
		if (Kohana::config('settings.require_email_confirmation') == 1)
		{
			if ($user->confirmed == 0)
			{
				// User has not confirmed email so kill auth cookies and fail login
				$this->logout(TRUE);
				
				return FALSE;
			}
		}

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

		// If we are using RiverID, we want to save the object so other sites
		//   on the same domain can use it for single signon
		if (kohana::config('riverid.enable') == true)
		{
			session::set('riverid',$riverid);
		}

		// Finish the login
		$this->complete_login($user);

		return TRUE;
	}

	/**
	 * Logs a user in using the standard method.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable auto-login
	 * @return  boolean
	 */
	public function login_standard($user, $password, $remember)
	{
		if ( ! is_object($user))
		{
			// Load the user
			$user = ORM::factory('user', $user);
		}

		if ( ! $user->id)
		{
			// user doesn't exist. Login Failed.
			return FALSE;
		}

		// If the passwords match, perform a login
		if ($user->has(ORM::factory('role', 'login')) AND $user->password === $password)
		{
			return $this->perform_login($user,$remember);
		}

		// Passwords don't match. Login Failed.
		return FALSE;
	}

	/**
	 * Logs a user in using the RiverID method.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable auto-login
	 * @param   string   email
	 * @param   object   a riverid object, not required
	 * @return  boolean
	 */
	public function login_riverid($user, $password, $remember, $email, $riverid=false)
	{
		// First check for exemptions

		if ( ! is_object($user))
		{
			// Load the user
			$user = ORM::factory('user', $user);
		}

		if (isset($user->id) AND in_array($user->id,kohana::config('riverid.exempt')))
		{
			// Looks like this is an exempted account
			return $this->login_standard($user, $password, $remember);
		}

		// Get down to business since there were no exemptions

		if ($riverid == false)
		{
			$riverid = new RiverID;
			$riverid->email = $email;
			$riverid->password = $password;
		}

		$is_registered = $riverid->is_registered();

		// See if the request even fired off.
		if ($riverid->error)
		{
			throw new Exception($riverid->error[0]);
		}

		if($is_registered == true)
		{
			// RiverID is registered on RiverID Server

			if ($riverid->authenticated != true)
			{
				// Attempt to sign in if our riverid object hasn't already authenticated
				$riverid->signin();
			}

			if ($riverid->authenticated == true)
			{
				// Correct email/pass

				// Collect the RiverID user_id and connect that with a user in the local system

				$user = User_Model::get_user_by_river_id($riverid->user_id);

				if ( ! $user->id)
				{
					// User not found locally with that RiverID, we need to see if they are already registered
					//   and convert their account or add them as a new user to the system

					// This may be a brand new user, but we need to figure out if
					//    the email has already been registered
					$user = User_Model::get_user_by_email($riverid->email);

					if ( ! $user->id)
					{
						// Email isn't in our system, create a new user.
						$user = User_Model::create_user($riverid->email,$riverid->password,$riverid->user_id);
					}
					else
					{
						// Email already exists. Put the RiverID on that account.
						$user->riverid = $riverid->user_id;
						$user->save();
					}

				}
				else
				{
					// We authenticated and we matched a RiverID, lets just makes sure the email
					//   addresses are both up to date

					if ($user->email != $riverid->email)
					{
						// We don't have a match for this user account. We need to see if we should
						//   be updating this account by first checking to see if another account
						//   already uses this email address
						$user_check = User_Model::get_user_by_email($riverid->email);
						if ( ! $user_check->id)
						{
							$user->email = $riverid->email;
							$user->username = $riverid->email;
							$user->save();
						}
						else
						{
							// Conflicting accounts

							// TODO: Figure out what to do when we need to update an email address on
							//   one account but it's already in use on another.
						}
					}
				}

				// Now that we have our user account tied to their RiverID, approve their authentication

				return $this->perform_login($user,$remember,$riverid);

			}
			else
			{
				// Incorrect email/pass, but registered on RiverID. Failed login.

				if ($riverid->error)
				{
					throw new Exception($riverid->error[0]);
				}

				return FALSE;
			}
		}
		else
		{

			// Email is not registerd on RiverID Server, could be registered locally

			// First see if they used the correct user/pass on their local account

			$user = User_Model::get_user_by_email($riverid->email);

			if ( ! $user->id)
			{
				// User doesn't exist locally or on RiverID. Fail login.

				if ($riverid->error)
				{
					throw new Exception($riverid->error[0]);
				}

				return FALSE;
			}
			else
			{
				// User exists locally but doesn't yet exist on the RiverID server

				// Check if they got the password correct

				if ($user->has(ORM::factory('role', 'login'))
					AND User_Model::check_password($user->id,$password,TRUE))
				{
					// Correct password! Create RiverID account
					$riverid->register();

					// If something went wrong with registration, catch it here
					if ($riverid->error)
					{
						throw new Exception($riverid->error[0]);
					}

					// Our user is now registered, let's assign the riverid user to the db.
					$user->riverid = $riverid->user_id;

					// Now lets sign them in
					$riverid->signin();

					// If something went wrong with signin, catch it here
					if ($riverid->error)
					{
						throw new Exception($riverid->error[0]);
					}

					return $this->perform_login($user,$remember,$riverid);

				}
				else
				{
					// Incorrect user/pass. Fail login.

					if ($riverid->error)
					{
						throw new Exception($riverid->error[0]);
					}

					return FALSE;
				}
			}
		}
	}

	/**
	 * Simply check to see if a password is valid for a user. NOT COMPATIBLE WITH RIVERID.
	 *
	 * @param   integer  user id of the user to check
	 * @param   string   password to check against
	 * @return  boolean
	 */
	public function check_password($user_id, $password)
	{
		$user = ORM::factory('user', $user_id);

		if ( ! $user->id OR empty($password) )
		{
			return FALSE;
		}

		// If the passwords match, return true
		if ($user->password === $password)
		{
			return TRUE;
		}else{
			return FALSE;
		}
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
	 * @param bool Set to true to force standard login
	 * @return  boolean
	 */
	public function auto_login($force_standard=false)
	{
		// If we are using RiverID

		if (kohana::config('riverid.enable') == true
		    AND $force_standard != true)
		{
			$riverid = session::get('riverid');

			// Check if we have the RiverID model, fail auto login if we don't
			if ( ! $riverid )
			{
				return FALSE;
			}

			// user, password, remember, email, riverid object
			return $this->login_riverid($riverid->email, false, true, $riverid->email, $riverid);

		}

		// If we are not using RiverID

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

		if (session::get('riverid'))
		{
			// Delete the riverid object in case it's set
			session::delete('riverid');
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