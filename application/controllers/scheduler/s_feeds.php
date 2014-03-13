<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Feeds Scheduler Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Scheduler
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class S_Feeds_Controller extends Controller {
	
	public function __construct()
    {
        parent::__construct();
	}
	
	/**
	 * parse feed and send feed items to database
	 */
	public function index()
	{
		// Max number of feeds to keep
		$max_feeds = 100;
		
		// Today's Date
		$today = strtotime('now');
		
		// Get All Feeds From DB
		$feeds = ORM::factory('feed')->find_all();
		foreach ($feeds as $feed)
		{
			$last_update = $feed->feed_update;
			
			// Parse Feed URL using Feed Helper
			$feed_data = feed::simplepie( $feed->feed_url );

			foreach($feed_data->get_items(0,50) as $feed_data_item)
			{
				$title = $feed_data_item->get_title();
				$link = $feed_data_item->get_link();
				$description = $feed_data_item->get_description();
				$date = $feed_data_item->get_date();
				$latitude = $feed_data_item->get_latitude();
				$longitude = $feed_data_item->get_longitude();
				$categories = $feed_data_item->get_categories(); // HT: new code
				$category_ids = new stdClass(); // HT: new code
				
				// Make Sure Title is Set (Atleast)
				if (isset($title) && !empty($title ))
				{	
					// We need to check for duplicates!!!
					// Maybe combination of Title + Date? (Kinda Heavy on the Server :-( )
					$dupe_count = ORM::factory('feed_item')->where('item_title',$title)->where('item_date',date("Y-m-d H:i:s",strtotime($date)))->count_all();

					if ($dupe_count == 0)
					{
						// Does this feed have a location??
						$location_id = 0;
						// STEP 1: SAVE LOCATION
						if ($latitude AND $longitude)
						{
							$location = new Location_Model();
							$location->location_name = "Unknown";
							$location->latitude = $latitude;
							$location->longitude = $longitude;
							$location->location_date = date("Y-m-d H:i:s",time());
							$location->save();
							$location_id = $location->id;
						}
						
						$newitem = new Feed_Item_Model();
						$newitem->feed_id = $feed->id;
						$newitem->location_id = $location_id;
						$newitem->item_title = $title;
						if (isset($description) AND !empty($description))
						{
							$newitem->item_description = $description;
						}
						if (isset($link) AND !empty($link))
						{
							$newitem->item_link = $link;
						}
						if (isset($date) AND !empty($date))
						{
							$newitem->item_date = date("Y-m-d H:i:s",strtotime($date));
						}
						// Set todays date
						else
						{
							$newitem->item_date = date("Y-m-d H:i:s",time());
						}
						// HT: new code
							if(!empty($categories)) {
								foreach($categories as $category) {
									$categoryData = ORM::factory('category')->where('category_title', $category->term)->find();
									if($categoryData->loaded == TRUE) {
										$category_ids->feed_item_category[$categoryData->id] = $categoryData->id;
									}
									elseif (Kohana::config('settings.allow_feed_category'))
									{
										$newcategory = new Category_Model();
										$newcategory->category_title = $category->term;
										$newcategory->parent_id = 0;
										$newcategory->category_description = $category->term;
										$newcategory->category_color = '000000';
										$newcategory->category_visible = 0;
										$newcategory->save();
										$category_ids->feed_item_category[$newcategory->id] = $newcategory->id;
									}
								}	
							}
						// HT: End of new code
						
						$newitem->save();

						// HT: New code
						if(!empty($category_ids->feed_item_category)) {
							feed::save_category($category_ids, $newitem);
						}
						// HT: End of New code
						
						// Action::feed_item_add - Feed Item Received!
						Event::run('ushahidi_action.feed_item_add', $newitem);
					}
				}
			}
			
			// Get Feed Item Count
			$feed_count = ORM::factory('feed_item')->where('feed_id', $feed->id)->count_all();
			if ($feed_count > $max_feeds) 
			{
				// Excess Feeds
				$feed_excess = $feed_count - $max_feeds;
			}

			// Set feed update date
			$feed->feed_update = strtotime('now');
			$feed->save();
		}
	}
}
