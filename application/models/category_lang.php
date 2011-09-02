<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Localization of Categories
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Category_Lang_Model extends ORM
{
	protected $belongs_to = array('category');

	protected $primary_key = 'id';

	// Database table name
	protected $table_name = 'category_lang';

	static function category_langs($category_id=FALSE)
	{
		if($category_id != FALSE)
		{
			$category_langs = ORM::factory('category_lang')->where(array('category_id'=>$category_id))->find_all();
		}else{
			$category_langs = ORM::factory('category_lang')->find_all();
		}

		$cat_langs = array();
		foreach($category_langs as $category_lang) {
			$cat_langs[$category_lang->category_id][$category_lang->locale]['id'] = $category_lang->id;
			$cat_langs[$category_lang->category_id][$category_lang->locale]['category_title'] = $category_lang->category_title;
		}

		return $cat_langs;
	}

	static function category_title($category_id,$locale)
	{
		$category_lang = ORM::factory('category_lang')
							->where(array('category_id'=>$category_id,'locale'=>$locale))
							->find_all();

		foreach($category_lang as $cat){
			if(isset($cat->category_title) AND $cat->category_title != '')
			{
				return $cat->category_title;
			}else{
				return FALSE;
			}
		}
	}

}
