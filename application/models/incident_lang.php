<?php defined('SYSPATH') or die('No direct script access.');

/**
* Category Localization Table Model
*/

class Incident_Lang_Model extends ORM
{
	protected $belongs_to = array('incident');
	
	// Database table name
	protected $table_name = 'incident_lang';

}