<?php defined('SYSPATH') or die('No direct script access.');

/**
* Messages Table Model
*/

class Message_Model extends ORM
{
	protected $belongs_to = array('incident');
	
	// Database table name
	protected $table_name = 'message';
}