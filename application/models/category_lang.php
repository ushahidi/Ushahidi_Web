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
	
	// Array of all category_langs
	protected static $category_langs;

	/**
	 * Get array of category lang entries
	 * @param int category id
	 */
	static function category_langs($category_id = FALSE)
	{
		// If we haven't already, grab all category_lang entries at once
		// We often list all categories at once so its more efficient to just get them all.
		if (! isset(self::$category_langs))
		{
			$category_langs = ORM::factory('category_lang')->find_all();
			self::$category_langs = array();
			
			foreach($category_langs as $category_lang) {
				self::$category_langs[$category_lang->category_id][$category_lang->locale]['id'] = $category_lang->id;
				self::$category_langs[$category_lang->category_id][$category_lang->locale]['category_title'] = $category_lang->category_title;
				self::$category_langs[$category_lang->category_id][$category_lang->locale]['category_description'] = $category_lang->category_description;
			}
		}

		
		// Not sure we need to bother with this
		if ($category_id AND isset(self::$category_langs[$category_id]))
		{
			return array($category_id => self::$category_langs[$category_id]);
		}

		return self::$category_langs;
	}

	/**
	 * Return category title in specified language
	 * If not locale specified return default
	 * @param int category id
	 * @param string Locale string
	 */
	static function category_title($category_id, $locale = FALSE)
	{
		// Use default locale from settings if not specified
		if (! $locale)
		{
			$locale = Kohana::config('locale.language.0');
		}
		
		// Use self::category_langs() to grab all category_lang entries
		// This function is often in a loop so only query once
		$cat_langs = self::category_langs();
		
		// Return translated title if its not blank
		if (isset($cat_langs[$category_id][$locale]) AND ! empty($cat_langs[$category_id][$locale]['category_title']))
		{
			return $cat_langs[$category_id][$locale]['category_title'];
		}
		
		// If we didn't find one, grab the default title
		$categories = Category_Model::categories();
		if (isset($categories[$category_id]['category_title']))
		{
			return $categories[$category_id]['category_title'];
		}
		
		return FALSE;
	}

	/**
	 * Return category description in specified language
	 * If not locale specified return default
	 * @param int category id
	 * @param string Locale string
	 */
	static function category_description($category_id, $locale = FALSE)
	{
		// Use default locale from settings if not specified
		if (! $locale)
		{
			$locale = Kohana::config('locale.language.0');
		}
		
		// Use self::category_langs() to grab all category_lang entries
		// This function is often in a loop so only query once
		$cat_langs = self::category_langs();
		
		// Return translated title if its not blank
		if (isset($cat_langs[$category_id][$locale]) AND ! empty($cat_langs[$category_id][$locale]['category_description']))
		{
			return $cat_langs[$category_id][$locale]['category_description'];
		}
		
		// If we didn't find one, grab the default title
		$categories = Category_Model::categories();
		if (isset($categories[$category_id]['category_description']))
		{
			return $categories[$category_id]['category_description'];
		}
		
		return FALSE;
	}

}
