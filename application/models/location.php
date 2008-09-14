<?php defined('SYSPATH') or die('No direct script access.');

/**
* Locations Table Model
*/

class Location_Model extends ORM
{
	protected $has_many = array('incident', 'media', 'incident_person');
	protected $has_one = array('country');
	
	// Database table name
	protected $table_name = 'location';
}