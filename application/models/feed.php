<?php defined('SYSPATH') or die('No direct script access.');

/**
* Feeds Table Model
*/

class Feed_Model extends ORM
{
	protected $has_many = array('feed_item');
	
	// Database table name
	protected $table_name = 'feed';
}