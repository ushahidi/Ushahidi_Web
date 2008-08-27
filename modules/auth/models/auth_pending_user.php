<?php
/**
 * Model for pending users (yet to confirm their account) for the Auth Module
 *
 * $Id: auth_pending_user.php - 3352 2008-08-18 09:43:56BST atomless $
 */
class Auth_Pending_User_Model extends Auth_User_Model {

	// Relationships - override the relationships defined in the auth_user_model
	// because pending users don't have those relationships!
	protected $has_many = array();
	protected $has_and_belongs_to_many = array();
	/**
	 * Confirm pending user to full user status - removes pending user from pending_users table
	 * and saves to users table.
	 *
	 */
	public function confirm()
	{
		// Create new user
		$user = ORM::factory('user');

		$user->username = $this->username;
		$user->email	= $this->email;

		// Bypass the user set method to avoid double hashing the password
		$user->set_prehashed_password($this->password);

		if ($user->save())
		{
			// Grant user login permissions
			$user->add(ORM::factory('role', 'login'));

			// Clear the pending user from the pending_users table
			$this->delete();
		}
	}

	/**
	 * Generates and sets an encrypted key that is used later to retrieve this pending user during
	 * the confirmation process.
	 *
	 */
	public function set_encrypted_key()
	{
		$this->key = md5($_SERVER['REMOTE_ADDR'].'|'.date("d/m/y : H:i:s", time()));
	}

}
