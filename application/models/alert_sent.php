<?php defined('SYSPATH') or die('No direct script access.');

/**
* Alerts Table Model
*/

class Alert_Sent_Model extends ORM
{
	protected $belongs_to = array('alert', 'incident');
	
	// Database table name
	protected $table_name = 'alert_sent';
}