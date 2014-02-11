<?php defined('SYSPATH') or die('No direct script access.');
// HT: New model
/**
* Model for Categories for each Feed Item
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

class Feed_Item_Category_Model extends ORM
{
	protected $belongs_to = array('feed_item', 'category');
	
	// Database table name
	protected $table_name = 'feed_item_category';
	
	/**
	 * Assigns a category id to an feed item if it hasn't already been assigned
	 * @param int $feed_id feed to assign the category to
	 * @param int $category_id category id of the category you want to assign to the feed
	 * @return array
	 */
	public static function assign_category_to_feed($feed_id,$category_id)
	{
		
		// Check to see if it is already added to that category
		//    If it's not, add it.
		
		$feed_item_category = ORM::factory('feed_item_category')->where(array('feed_item_id'=>$feed_id,'category_id'=>$category_id))->find_all();
		
		if( ! $feed_item_category->count() )
		{
			$new_feed_item_category = ORM::factory('feed_item_category');
			$new_feed_item_category->category_id = $category_id;
			$new_feed_item_category->feed_item_id = $feed_id;
			$new_feed_item_category->save();
		}
		
		return true;
	}
}
