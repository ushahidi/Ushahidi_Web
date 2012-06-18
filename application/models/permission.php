<?php defined('SYSPATH') OR die('No direct access allowed.');

class Permission_Model extends ORM {
	
	protected $has_and_belongs_to_many = array('roles');
	
	public function delete()
	{
		$table_prefix = Kohana::config('database.default.table_prefix');
		
		// Remove records referencing this permission
		// Have to use db->query() since we don't have an ORM model for permissions_roles
		$this->db->query('DELETE FROM '.$table_prefix.'permissions_roles WHERE permission_id = ?',$this->id);

		parent::delete();
	}
	
	/**
	 * Allows finding permissions by name.
	 */
	public function unique_key($id)
	{
		if ( ! empty($id) AND is_string($id) AND ! ctype_digit($id))
		{
			return 'name';
		}

		return parent::unique_key($id);
	}

} // End Permission Model