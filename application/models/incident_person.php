<?php defined('SYSPATH') or die('No direct script access.');

/**
* Incident Person Table Model
*/

class Incident_Person_Model extends ORM
{
	protected $belongs_to = array('location', 'incident');
	
	// Database table name
	protected $table_name = 'incident_person';
}