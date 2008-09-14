<?php defined('SYSPATH') or die('No direct script access.');

/**
* Locations Table Model
*/

class Media_Model extends ORM
{
	protected $belongs_to = array('location', 'incident');
	
	// Database table name
	protected $table_name = 'media';
}