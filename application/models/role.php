<?php defined('SYSPATH') OR die('No direct access allowed.');

class Role_Model extends Auth_Role_Model {

	protected $has_and_belongs_to_many = array('permissions', 'users');
	
	public function delete()
	{
		$table_prefix = Kohana::config('database.default.table_prefix');
		
		// Remove assigned users
		// Have to use db->query() since we don't have an ORM model for roles_users
		$this->db->query('DELETE FROM '.$table_prefix.'roles_users WHERE role_id = ?',$this->id);
		
		// Remove assigned permissions
		// Have to use db->query() since we don't have an ORM model for permissions_roles
		$this->db->query('DELETE FROM '.$table_prefix.'permissions_roles WHERE role_id = ?',$this->id);

		parent::delete();
	}

} // End Role Model