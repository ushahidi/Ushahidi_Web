<?php defined('SYSPATH') or die('No direct script access.');

/**
* OpenID Table Model
*/

class Openid_Model extends ORM
{
	protected $belongs_to = array('user');
	
	// Database table name
	protected $table_name = 'openid';
}
