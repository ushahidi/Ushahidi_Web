<?php defined('SYSPATH') or die('No direct script access.');

/**
* Cities Table Model
*/

class City_Model extends ORM
{
	protected $belongs_to = array('country');
	
	// Database table name
	protected $table_name = 'city';
}