<?php defined('SYSPATH') or die('No direct script access.');

/**
* Categories Table Model
*/

class Category_Model extends ORM
{	
	protected $has_many = array('incident' => 'incident_category');
	
	// Database table name
	protected $table_name = 'category';
}