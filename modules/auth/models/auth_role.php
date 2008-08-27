<?php
/**
 * Model for roles for the Auth Module
 *
 * $Id: auth_role.php 3352 2008-08-18 09:43:56BST atomless $
 */
class Auth_Role_Model extends ORM {

	protected $has_and_belongs_to_many = array('users');

	/**
	 * Allows finding roles by name.
	 */
	public function unique_key($id)
	{
		if ( ! empty($id) AND is_string($id) AND ! ctype_digit($id))
		{
			return 'name';
		}

		return parent::unique_key($id);
	}

} // End Role_Model