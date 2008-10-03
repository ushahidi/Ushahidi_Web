<?php defined('SYSPATH') or die('No direct script access.');

/**
* Countries Table Model
*/

class Country_Model extends ORM
{
	protected $belongs_to = array('location');
	protected $has_many = array('city');
	
	// Database table name
	protected $table_name = 'country';
}