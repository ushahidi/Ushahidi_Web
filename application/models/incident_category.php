<?php defined('SYSPATH') or die('No direct script access.');

/**
* Report/Incidents + Category Table Model
*/

class Incident_Category_Model extends ORM
{
	protected $belongs_to = array('incident', 'category');
	
	// Database table name
	protected $table_name = 'incident_category';
}