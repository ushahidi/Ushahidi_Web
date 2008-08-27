<?php
/**
 * Model for users for the Auth Module
 *
 * $Id: auth_user_model.php 3352 2008-08-18 09:43:56BST atomless $
 */
class Auth_User_Model extends ORM {

	// Relationships
	protected $has_many = array('user_tokens');
	protected $has_and_belongs_to_many = array('roles');

	public function __set($key, $value)
	{
		if ($key === 'password')
		{
			// Use Auth to hash the password
			$value = Auth::instance()->hash_password($value);
		}

		parent::__set($key, $value);
	}

	/**
	 * Used instead of the __set method ($user->password=x) to avoid double hashing of password.
	 * For example when confirming pending_user.
	 *
	 * @param String   pre-hashed password string
	 * @return void
	 */
	public function set_prehashed_password($value)
	{
		parent::__set('password', $value);
	}

	/**
	 * Tests if a user already exists in the database by checking
	 * submitted data against any db fields with type set to unique.
	 * NOTE: Only tested with the mysql db driver.
	 *
	 * @param   string   array of key value pairs to check
	 * @param   Validation Object  *Optional - enables adding of relevant errors to the
	 * 								validation object's errors array.
	 * @return  bool
	 *
	 */
	public function exists($user_data_array, $validation_object = FALSE)
	{
		$userexists = FALSE;

		foreach($this->db->field_data($this->table_name) as $column)
		{
			if ($column->Key=='UNI')
			{
				if (array_key_exists($column->Field, $user_data_array))
				{
					if ($this->db->where($column->Field, $user_data_array[$column->Field])->count_records($this->table_name) > 0)
					{
						if (get_Class($validation_object) == 'Validation')
						{
							// Add already exists errors to the referenced POST validation error array
							$validation_object->add_error($column->Field, 'exists');
						}

						$userexists = TRUE;
					}
				}
			}
		}
		return $userexists;
	}

	/**
	 * Tests if a user email already exists in the database.
	 *
	 * @param   string   email to check
	 * @return  bool
	 */
	public function email_exists($email)
	{
		return (bool) $this->db->where('email', $email)->count_records($this->table_name);
	}

	/**
	 * Tests if a username already exists in the database.
	 *
	 * @param   string   username to check
	 * @return  bool
	 */
	public function username_exists($name)
	{
		return (bool) $this->db->where('username', $name)->count_records($this->table_name);
	}

	/**
	 * Allows a model to be loaded by username or email address.
	 *
	 * @param   string   database table field name
	 * @return  string
	 */
	public function unique_key($id)
	{
		if ( ! empty($id) AND is_string($id) AND ! ctype_digit($id))
		{
			return valid::email($id) ? 'email' : 'username';
		}

		return parent::unique_key($id);
	}
}