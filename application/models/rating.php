<?php defined('SYSPATH') or die('No direct script access.');

/**
* Rating Table Model
*/

class Rating_Model extends ORM
{
	protected $belongs_to = array('incident', 'comment');
	
	// Database table name
	protected $table_name = 'rating';
}