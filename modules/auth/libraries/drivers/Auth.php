<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Abstract Auth driver, must be extended by all drivers.
 *
 * $Id: Auth.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
abstract class Auth_Driver {

	// Session instance
	protected $session;

	// Configuration
	protected $config;

	/**
	 * Creates a new driver instance, loading the session and storing config.
	 *
	 * @param   array  configuration
	 * @return  void
	 */
	public function __construct(array $config)
	{
		// Load Session
		$this->session = Session::instance();

		// Store config
		$this->config = $config;
	}

	/**
	 * Checks if a session is active.
	 *
	 * @param   string   role name (not supported)
	 * @return  boolean
	 */
	public function logged_in($role)
	{
		return isset($_SESSION[$this->config['session_key']]);
	}

	/**
	 * Gets the currently logged in user from the session.
	 * Returns FALSE if no user is currently logged in.
	 *
	 * @return  mixed
	 */
	public function get_user()
	{
		if ($this->logged_in(NULL))
		{
			return $_SESSION[$this->config['session_key']];
		}

		return FALSE;
	}

	/**
	 * Logs a user in.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable auto-login
	 * @return  boolean
	 */
	abstract public function login($username, $password, $remember);

	/**
	 * Forces a user to be logged in, without specifying a password.
	 *
	 * @param   mixed    username
	 * @return  boolean
	 */
	abstract public function force_login($username);

	/**
	 * Logs a user in, based on stored credentials, typically cookies.
	 * Not supported by default.
	 *
	 * @return  boolean
	 */
	public function auto_login()
	{
		return FALSE;
	}

	/**
	 * Log a user out.
	 *
	 * @param   boolean  completely destroy the session
	 * @return  boolean
	 */
	public function logout($destroy)
	{
		if ($destroy === TRUE)
		{
			// Destroy the session completely
			Session::instance()->destroy();
		}
		else
		{
			// Remove the user from the session
			$this->session->delete($this->config['session_key']);

			// Regenerate session_id
			$this->session->regenerate();
		}

		// Double check
		return ! $this->logged_in(NULL);
	}

	/**
	 * Get the stored password for a username.
	 *
	 * @param   mixed   username
	 * @return  string
	 */
	abstract public function password($username);

	/**
	 * Completes a login by assigning the user to the session key.
	 *
	 * @param   string   username
	 * @return  TRUE
	 */
	protected function complete_login($user)
	{
		// Regenerate session_id
		$this->session->regenerate();

		// Store username in session
		$_SESSION[$this->config['session_key']] = $user;

		return TRUE;
	}

} // End Auth_Driver