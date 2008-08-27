<?php defined('SYSPATH') or die('No direct script access.');

class User_Edit_Model extends User_Model {

	// Overload the class
	protected $class = 'user';

	// Forge instance
	protected $form;

	public function __construct($action, $title, $id = FALSE)
	{
		// Load the user
		parent::__construct($id);

		// Create the form
		$this->form = new Forge($action, $title);

		$this->form->input('username')->label(TRUE)->rules('required|length[5,32]')->value($this->object->username);
		$this->form->input('email')->label(TRUE)->rules('required|length[5,127]|valid_email')->value($this->object->email);
		$this->form->password('password')->label(TRUE)->rules('length[5,64]');
		$this->form->password('confirm')->label(TRUE)->matches($this->form->password);

		// Make sure that the username does not already exist
		$this->form->username->callback(array($this, 'is_existing_user'));

		if ($this->object->id == 0)
		{
			// Password fields are required for new users
			$this->form->password->rules('+required');
		}

		// // Find all roles
		// $roles = new Role_Model();
		// $roles = $roles->find(ALL);
		// 
		// $options = array();
		// foreach($roles as $role)
		// {
		// 	// Add each role to the options
		// 	$options[$role->name] = isset($this->roles[$role->id]);
		// }
		// 
		// // Create a checklist of roles
		// $this->form->checklist('roles')->options($options)->label(TRUE);

		// Add the save button
		$this->form->submit('Save');
	}

	public function is_existing_user($input)
	{
		if ($this->object->username == $input->value)
			return TRUE;

		if (self::$db->count_records($this->table, array('username' => $input->value)) > 0)
		{
			$input->add_error(__FUNCTION__, 'The username <strong>'.$input->value.'</strong> is already in use.');
			return FALSE;
		}

		return TRUE;
	}

	public function save()
	{
		if ($this->form->validate() AND $data = $this->form->as_array())
		{
			if (empty($data['password']))
			{
				// Remove the empty password so it's not reset
				unset($data['password'], $data['confirm']);
			}

			// Need to set this before saving
			$new_user = ($this->object->id == 0);

			// Remove the roles from data
			isset($data['roles']) and $roles = arr::remove('roles', $data);

			foreach($data as $field => $val)
			{
				// Set object data from the form
				$this->$field = $val;
			}

			if ($status = parent::save())
			{
				// if ($new_user)
				// {
				// 	foreach($roles as $role)
				// 	{
				// 		// Add the user roles
				// 		$this->add_role($role);
				// 	}
				// }
				// else
				// {
				// 	foreach(array_diff($this->roles, $roles) as $role)
				// 	{
				// 		// Remove roles that were deactivated
				// 		$this->remove_role($role);
				// 	}
				// 
				// 	foreach(array_diff($roles, $this->roles) as $role)
				// 	{
				// 		// Add new roles
				// 		$this->add_role($role);
				// 	}
				// }
			}

			// Return the save status
			return $status;
		}

		return FALSE;
	}

	public function html()
	{
		// Proxy to form html
		return $this->form->html();
	}

	public function __toString()
	{
		// Proxy to form html
		return $this->form->html();
	}

} // End User Edit Model