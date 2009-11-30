<?php defined('SYSPATH') or die('No direct script access.');

/**
* Feed Items Table Model
*/

class Feed_Item_Model extends ORM
{
	protected $belongs_to = array('feed', 'location');
	
	// Database table name
	protected $table_name = 'feed_item';
}