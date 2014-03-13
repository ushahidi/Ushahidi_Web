<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Feed helper class.
 * Common functions for handling news feeds
 *
 * $Id: valid.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Ushahidi
 * @category   Helpers
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class feed_Core {

	public static function simplepie( $feed_url = NULL )
	{
		if ( ! $feed_url)
			return false;

		$data = new SimplePie();

		//*******************************
		// Convert To GeoRSS feed
		// To Disable Uncomment these 3 lines
		//*******************************

		$geocoder = new Geocoder();
		$georss_feed = $geocoder->geocode_feed($feed_url);

		if ($georss_feed == false OR empty($georss_feed))
		{
			// Our RSS feed pull failed, so let's grab the original RSS feed
			$data->set_feed_url($feed_url);
		}else{
			// Converting our feed to GeoRSS was successful, use that data
			$data->set_raw_data( $georss_feed );
		}

		// Uncomment Below to disable geocoding
		//$data->set_feed_url( $feed_url );
		//*******************************

		$data->enable_cache(false);
		$data->enable_order_by_date(true);
		$data->init();
		$data->handle_content_type();

		return $data;
	}
	
	// HT: New function to save category of feed
	public static function save_category($post, $feed_item)
	{
		// Delete Previous Entries
		ORM::factory('feed_item_category')->where('feed_item_id', $feed_item->id)->delete_all();
	
		foreach ($post->feed_item_category as $item)
		{
			$feed_item_category = new Feed_Item_Category_Model();
			$feed_item_category->feed_item_id = $feed_item->id;
			$feed_item_category->category_id = $item;
			$feed_item_category->save();
		}
	}
	
}
