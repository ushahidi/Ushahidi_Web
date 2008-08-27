<?php defined('SYSPATH') or die('No direct script access.');

/**
* Report/Incidents Table Model
*/

class Incident_Model extends ORM
{
	protected $has_many = array('category', 'media', 'incident_person');
	protected $belongs_to = array('location', 'users');
	
	// Database table name
	protected $table_name = 'incident';
}