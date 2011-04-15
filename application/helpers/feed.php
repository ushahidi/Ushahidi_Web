<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Feed helper class.
 * Common functions for handling news feeds
 *
 * $Id: valid.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Distance
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
	
		$data->set_raw_data( $georss_feed );
		// Uncomment Below to disable geocoding
		//$data->set_feed_url( $feed_url );
		//*******************************
		
		$data->enable_cache(false);
		$data->enable_order_by_date(true);
		$data->init();
		$data->handle_content_type();

		return $data;
	}
}