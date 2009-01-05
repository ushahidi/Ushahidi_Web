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
 * @module     Role User Model  
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
	
	public function delete_role( $user_id)
	{
		
		$this->db->where('user_id', $user_id );
		$this->db->delete('roles_users' );
	}
}
