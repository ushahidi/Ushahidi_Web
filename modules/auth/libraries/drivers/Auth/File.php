<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * File Auth driver.
 * Note: this Auth driver does not support roles nor auto-login.
 *
 * $Id: File.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Auth_File_Driver extends Auth_Driver {

	// User list
	protected $users;

	/**
	 * Constructor loads the user list into the class.
	 */
	public function __construct(array $config)
	{
		parent::__construct($config);

		// Load user list
		$this->users = empty($config['users']) ? array() : $config['users'];
	}

	/**
	 * Logs a user in.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable auto-login (not supported)
	 * @param   string   email (ignored but kept for consistency across login driver functions)
	 * @return  boolean
	 */
	public function login($username, $password, $remember, $email)
	{
		if (isset($this->users[$username]) AND $this->users[$username] === $password)
		{
			// Complete the login
			return $this->complete_login($username);
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
	public function force_login($username)
	{
		// Complete the login
		return $this->complete_login($username);
	}

	/**
	 * Get the stored password for a username.
	 *
	 * @param   mixed   username
	 * @return  string
	 */
	public function password($username)
	{
		return isset($this->users[$username]) ? $this->users[$username] : FALSE;
	}

} // End Auth_File_Driver