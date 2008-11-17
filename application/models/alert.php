<?php defined('SYSPATH') or die('No direct script access.');

/**
* Alerts Table Model
*/

class Alert_Model extends ORM
{
	protected $has_many = array('incident' => 'alert_sent');
	
	// Database table name
	protected $table_name = 'alert';
}