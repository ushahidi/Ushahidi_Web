<?php defined('SYSPATH') OR die('No direct access allowed.');

class Permission_Model extends ORM {
	
	protected $has_and_belongs_to_many = array('roles');

} // End Permission Model