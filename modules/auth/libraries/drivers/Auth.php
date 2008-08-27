<?php
/**
 * Auth driver interface.
 *
 * $Id: Auth.php 2482 2008-04-12 16:48:50Z Shadowhand $
 *
 * @package    Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
interface Auth_Driver {

   /**
	 * Checks if a user session is active. Optionally allows checking for a
	 * specific role.
	 *
	 * @param   string    role name
	 * @return  boolean
	 */
	public function logged_in($role);

	/**
	 * Logs a user in.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable auto-login
	 * @return  boolean
	 */
	public function login($username, $password, $remember);

	/**
	 * Logs a user in, based on stored credentials, typically cookies.
	 *
	 * @return  boolean
	 */
	public function auto_login();

	/**
	 * Forces a user login, without needing to specify a password.
	 *
	 * @param   mixed    username
	 * @return  boolean
	 */
	public function force_login($username);

	/**
	 * Log a user out.
	 *
	 * @param   boolean   completely destroy the session - also delete authautologin cookie
	 * @return  boolean
	 */
	public function logout($destroy);

	/**
	 * Get the stored password for a username.
	 *
	 * @param   mixed   user object or username string
	 * @return  string
	 */
	public function password($username);

} // End Auth_Driver Interface