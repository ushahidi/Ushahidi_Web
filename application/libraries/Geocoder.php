<?php
/**
 * GeoCoder Library
 * Uses a variety of methods to geocode locations and feeds
 *
 * @package    GeoCoder
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */

define("GEOCODER_GOOGLE", "maps.google.com");
define("GEOCODER_GEONAMES", "ws.geonames.org");

class Geocoder_Core {

	/**
	 * Google Location GeoCoding
	 * 
	 * Reuses map::geocode() rather than reimplementing.
	 * Only really keeping this for backwards compat
	 *
	 * @param   string location / address
	 * @return  array (longitude, latitude)
	 */
	function geocode_location ($address = NULL)
	{
		$result = map::geocode($address);
		if ($result)
		{
			return array($result['longitude'], $result['latitude'], $result['country_id']);
		}
		else
		{
			return false;
		}
	}


	/**
	 * Geonames Feeds GeoCoding (RSS to GEORSS)
	 * Due to limitations, this returns only 20 items
	 *
	 * @param   string location / address
	 * @return  string raw georss data
	 */
	function geocode_feed ($feed_url = NULL)
	{
		$base_url = "http://" . GEOCODER_GEONAMES . "/rssToGeoRSS?";


		if ($feed_url)
		{
			// First check to make sure geonames webservice is running
			$geonames_status = @remote::status( $base_url );

			if ($geonames_status == "200")
			{ // Successful
				$request_url = $base_url . "&feedUrl=" . urlencode($feed_url);
			}
			else
			{ // Down perhaps?? Use direct feed
				$request_url = $feed_url;
			}


			$request = new HttpClient($request_url);

			if ( ! ($georss = $request->execute($request_url)))
			{
				// If the request failed, something may be wrong with the GEOCODER_GEONAMES service
				return false;
			}
			//$georss = utf8_encode($georss);


			return trim($georss);

		}
		else
		{
			return false;
		}
	}

}
