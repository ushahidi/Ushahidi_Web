<?php defined('SYSPATH') or die('No direct script access.');
/**
* User Roles Table Model
*/
class Role_Model extends ORM {

	protected $belongs_to = array('users');
	
	protected $table_name = 'roles';
} // End Role_Model