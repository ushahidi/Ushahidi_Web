<?php
/**
 * Model for roles for the roles users.
 *
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