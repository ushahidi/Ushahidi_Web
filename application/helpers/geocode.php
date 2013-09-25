<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Geocode helper class
 *
 * Portions of this class credited to: zzolo, phayes, tmcw, brynbellomy, bdragon
 *
 * @package    Geocode
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */


class geocode_Core {

	static public function geocode($address = NULL) {
		$service = Kohana::config('map.geocode');

		if ($address)
		{
			if (! method_exists('geocode_Core', $service)) {
				throw new Kohana_Exception("'" . $service . "' is not a valid geocode service");			
				return FALSE;
			}

			return self::$service($address);
		}
		else
		{
			return FALSE;
		}		
	}

	static public function nominatim($address) {
		$payload = FALSE;
		$result = FALSE;

		$params = array(
				"format"			=> "json",
				"addressdetails"	=> 1,
				"accept-language"	=> "en_US", // force country names to come back as english,
				"q"					=> $address
			);

		$url = "http://nominatim.openstreetmap.org/search/?" . http_build_query($params);

		$url_request = new HttpClient($url);

		if ($result = $url_request->execute()) 
		{
			$payload = json_decode($result);
		}


		if (count($payload) == 0) 
		{
			return FALSE;
		}

		$result = array_pop($payload);

		$country_name = isset($result->address->country) ? $result->address->country : $result->display_name;

		$geocodes = array(
			'country' 		=> $country_name,
			'country_id' 	=> self::getCountryId($country_name),
			'location_name' => $result->display_name,
			'latitude' 		=> $result->lat,
			'longitude' 	=> $result->lon
		);

		return $geocodes;
	}

	static public function google($address) {
		$payload = FALSE;

		$url = Kohana::config('config.external_site_protocol').'://maps.google.com/maps/api/geocode/json?sensor=false&address='.rawurlencode($address);
		$result = FALSE;

		$url_request = new HttpClient($url);

		if ($result = $url_request->execute()) 
		{
			$payload = json_decode($result);
		}

		// Verify that the request succeeded
		if (! isset($payload->status)) return FALSE;
		if ($payload->status != 'OK') return FALSE;

		// Convert the Geocoder's results to an array
		$all_components = json_decode(json_encode($payload->results), TRUE);
		$location = $all_components[0]['geometry']['location'];

		// Find the country
		$address_components = $all_components[0]['address_components'];
		$country_name = NULL;
		foreach ($address_components as $component)
		{
			if (in_array('country', $component['types']))
			{
				$country_name  = $component['long_name'];
				break;
			}
		}

		// If no country has been found, use the formatted address
		if (empty($country_name))
		{
			$country_name = $all_components[0]['formatted_address'];
		}

		$geocodes = array(
			'country' => $country_name,
			'country_id' => self::getCountryId($country_name),
			'location_name' => $all_components[0]['formatted_address'],
			'latitude' => $location['lat'],
			'longitude' => $location['lng']
		);

		return $geocodes;
	}

	static function getCountryId($country_name) {
		// Grab country_id
		$country = Country_Model::get_country_by_name($country_name);
		return ( ! empty($country) AND $country->loaded)? $country->id : 0;
	}


}