<?php defined('SYSPATH') or die('No direct script access.');

/**
* Feeds Parser Controller (RSS2)
* Refreshes Feed Items using the feed helper
*/
class Feeds_Controller extends Controller
{
	function index()
	{
		// Get All Feeds From DB
		$feeds = ORM::factory('feed')->find_all();
		foreach ($feeds as $feed)
		{
			// Parse Feed URL using Feed Helper
			//$feed_data = feed::parse($feed->feed_url);
			//don't function anymore
			$feed_data = array();
			foreach($feed_data as $feed_data_item)
			{
				// Make Sure Title is Set (Atleast)
				if (isset($feed_data_item['title']) && !empty($feed_data_item['title']))
				{
					// We need to check for duplicates!!!
					// Maybe combination of Title + Date? (Kinda Heavy on the Server :-( )
					$dupe_count = ORM::factory('feed_item')->where('item_title',$feed_data_item['title'])->where('item_date',date("Y-m-d H:i:s",strtotime($feed_data_item['pubDate'])))->count_all();
					
					if ($dupe_count == 0) {
						$newitem = new Feed_Item_Model();
						$newitem->feed_id = $feed->id;
						$newitem->item_title = $feed_data_item['title'];
						if (isset($feed_data_item['description']) && !empty($feed_data_item['description']))
						{
							$newitem->item_description = $feed_data_item['description'];
						}
						if (isset($feed_data_item['link']) && !empty($feed_data_item['link']))
						{
							$newitem->item_link = $feed_data_item['link'];
						}
						if (isset($feed_data_item['pubDate']) && !empty($feed_data_item['pubDate']))
						{
							$newitem->item_date = date("Y-m-d H:i:s",strtotime($feed_data_item['pubDate']));
						}
						// Set todays date
						else
						{
							$newitem->item_date = date("Y-m-d H:i:s",time());
						}
						$newitem->save();
					}
				}
			}
		}
	}
}