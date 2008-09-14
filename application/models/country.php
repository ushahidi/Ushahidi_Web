<?php defined('SYSPATH') or die('No direct script access.');

/**
* Countries Table Model
*/

class Country_Model extends ORM
{
	protected $belongs_to = array('location');
	
	// Database table name
	protected $table_name = 'country';
}