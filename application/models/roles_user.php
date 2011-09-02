<?php

/**
* Model for Roles of each User
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 * $Id: $
 */
class Roles_User_Model extends Model {
	
	public function __construct($id = NULL)
	{
		parent::__construct($id);
	}
	
	/**
	 * fetch role names. 
	 */
	public function get_role_id( $user_id ){
		//TODO write necessary code to fetch role id specific to a user id.
		$this->db->where('user_id', $user_id );
		$query = $this->db->select('role_id')->from('roles_users')->get();
		return $query->current();
	}
	
	/**
	 * insert new role into the roles_users table.
	 */
	public function insert_role( $data )
	{
		$this->db->insert('roles_users', $data );
	}
	
	/**
	 * update existing entry 
	 */
	public function update_role( $user_id, $data )
	{
		$this->db->where('user_id',$user_id);
		$this->db->update('roles_users',$data);
	}
	
	public function delete_role($user_id)
	{
		$this->db->where('user_id', $user_id);
		$this->db->delete('roles_users');
	}
	
	/**
	 * Returns true if any of the roles in the roles table are marked as 1, 
	 *   essentially saying there's a good chance that there are features
	 *   in the admin panel they could access.
	 */
	public function role_allow_admin($roll_id)
	{
		$roles = ORM::factory("role")->find($roll_id)->as_array();

		foreach($roles as $key => $allowed)
		{
			
			// Ignore these fields because they contain data that doesn't involve access
			
			if($key == 'id' OR $key == 'name' OR $key == 'description') continue;
			
			if($key == 'checkin')
			{
				// Checkin is a special case because they are allowed access to the front end
				//   but not necessarily the back end so we will continue looping
				continue;
			}
			
			if($allowed == 1)
			{
				return TRUE;
			}
		}
		
		// None of the fields allowed access to anything specific. This is just a login account.
		
		return FALSE;
	}
}
