<?php defined('SYSPATH') OR die('No direct access allowed.');

class Role_Model extends Auth_Role_Model {

	protected $has_and_belongs_to_many = array('permissions', 'users');
	
	/**
	 * Returns true if any of the roles in the roles table are marked as 1, 
	 *   essentially saying there's a good chance that there are features
	 *   in the admin panel they could access.
	 */
	public function allow_admin()
	{
		foreach($this->permissions as $permission)
		{
			// Ignore these fields because they contain data that doesn't involve access
			if($permission->name == 'checkin')
			{
				// Checkin is a special case because they are allowed access to the front end
				//   but not necessarily the back end so we will continue looping
				continue;
			}
			
			return TRUE;
		}
		
		// None of the fields allowed access to anything specific. This is just a login account.
		return FALSE;
	}

} // End Role Model