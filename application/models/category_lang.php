<?php defined('SYSPATH') or die('No direct script access.');

/**
* Category Localization Table Model
*/

class Category_Lang_Model extends ORM
{
	protected $belongs_to = array('category');
	
	// Database table name
	protected $table_name = 'category_lang';

}