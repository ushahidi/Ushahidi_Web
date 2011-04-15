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
	 * @param   string location / address
	 * @return  array (longitude, latitude)
	 */
	function geocode_location ($address = NULL)
	{
		if ($address)
		{
			// Does this installation have a google api key?
			$api_key = Kohana::config('settings.api_google');
			if ($api_key)
			{
				$base_url = "http://" . GEOCODER_GOOGLE . "/maps/geo?output=xml" . "&key=" . api_key;
				
				// Deal with the geocoder timing out during operations
				$geocode_pending = true;
				
				while ($geocode_pending) {
					$request_url = $base_url . "&q=" . urlencode($address);

					//$xml = simplexml_load_file(utf8_encode($request_url)) or die("url not loading");
					$page = file_get_contents($request_url);
					$page = utf8_encode($page);
					$xml = new SimpleXMLElement($page);

					$status = $xml->Response->Status->code;
					if (strcmp($status, "200") == 0)
					{
						// Successful geocode
						$geocode_pending = false;
						$coordinates = $xml->Response->Placemark->Point->coordinates;
						$coordinatesSplit = explode(",", $coordinates);
						// Format: Longitude, Latitude, Altitude
						$lng = $coordinatesSplit[0];
						$lat = $coordinatesSplit[1];
						
						return array($lng, $lat);
						
					}
					else if (strcmp($status, "620") == 0)
					{
						// sent geocodes too fast
						$delay += 100000;
					}
					else
					{
						// failure to geocode
						return false;
					}
					usleep($delay);
				}
				
			}
			// Install doesn't have api key - can't geocode with google
			else
			{
				return false;
			}
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
			
			$georss = file_get_contents($request_url);
			//$georss = utf8_encode($georss);

			return $georss;
			
		}
		else
		{
			return false;
		}
	}
	
}