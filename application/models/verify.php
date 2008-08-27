<?php defined('SYSPATH') or die('No direct script access.');

/**
* Verifications Table Model
*/

class Verify_Model extends ORM
{
	protected $belongs_to = array('incident', 'users');
	
	// Database table name
	protected $table_name = 'verified';

}