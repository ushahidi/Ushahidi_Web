<?php
/**
 * Proximity Calculator library.
 * allows for radius-based searches to quickly written, tested, and deployed.
 * It works by calculating the lines of latitude and longitude at
 * exactly X miles(Kms) from the specified center point.
 * This allows for much more efficient database queries to be written by
 * eliminating almost all points that don't fall within the specified radius.
 * 
 * @package    Proximity
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class Proximity_Core {

	/**
	 * Maximum latitude
	 * @var double
	 */
	private $maxLat;
	/**
	 * Minimum latitude
	 * @var double
	 */
	
	private $minLat;
	/**
	 * Maximimum longitude
	 * @var double
	 */
	private $maxLong;
	
	/**
	 * Minimum longitude
	 * @var double
	 */
	private $minLong;
	
	/**
	 * Angular distance
	 * @var float
	 */
	private $angular_distance;

	public $radius;
	
	const EQUATOR_LAT_MILE = 69.172;
	const EQUATOR_LAT_KM = 111.321543;
	
	/**
	 * The earth's radius in KM
	 */
	const EARTH_RADIUS = 6371;
	
	/**
	 * Creates an instance of Proximity
	 *
	 * @param	string	$latitude latitude of this point
	 * @param	string	$longitude longitude of this point
	 * @param	int		$distance the radius distance in miles/ km
	 * @param	bool	$in_kms True if the distance is in Kms, otherwise returns Miles
	 */
	public function __construct($latitude = 0, $longitude = 0, $distance = 100, $in_kms = TRUE)
	{
		if ( ! intval($distance) > 0 )
		{
			throw new Kohana_Exception('The specified distance is invalid');
		}
		
		if ($in_kms == FALSE)
		{
			// Convert the distance to miles and round of to 3 decimal places
			$distance = round((1/0.62 * $distance), 3);
		}
		
		// Calculate the angular distance
		$this->angular_distance = $distance/self::EARTH_RADIUS;
		
		// Convert latitude and longitude to radians
		$latitude = $latitude * M_PI / 180;
		$longitude = $longitude * M_PI / 180;
		
		// Calculate the minimum and maximum latitude
		$this->minLat = $this->_get_destination_latitude($latitude, 180) * 180 / M_PI;
		$this->maxLat = $this->_get_destination_latitude($latitude, 360) * 180 / M_PI;
				
		// Calculate the minimum and maximum longitude
		$temp_lat = $this->_get_destination_latitude($latitude, 270);
		$this->minLong = $this->_get_destination_longitude($longitude, $latitude, $temp_lat, 270) * 180 / M_PI;
		
		$temp_lat2 = $this->_get_destination_latitude($latitude,  90);
		$this->maxLong = $this->_get_destination_longitude($longitude, $latitude, $temp_lat2, 90) * 180 / M_PI;
		
		// Garbage collection
		unset ($temp_lat, $temp_lat2);
	}
	
	public function __get($name)
	{
		return $this->$name;
	}
	
	/**
	 * Calculates the destination latitude given the bearing
	 *
	 * @param float $latitude Latitude of the origin location
	 * @param float $bearing Bearing, clockwise from North of the current location
	 * @return float
	 */
	private function _get_destination_latitude($latitude, $bearing)
	{
		$bearing = $bearing * M_PI / 180;
		// Return the destination latitude
		return asin(sin($latitude) * cos($this->angular_distance) + cos($latitude) * sin($this->angular_distance) * cos($bearing));
	}
	
	/**
	 * Calculates the destination longitude given the bearing, origin latitude and target latitude
	 *
	 * @param float $longitude Longitude of the origin location
	 * @param float $origin_latitude Latitude of the origin location
	 * @param float $destination_latitude Latitude of the destination point
	 * @param float $bearing Bearing, clockwise from North of the current location
	 * @return float
	 */
	private function _get_destination_longitude($longitude, $origin_latitude, $destination_latitude, $bearing)
	{
		$bearing = $bearing * M_PI / 180;
		
		// Get the final latitude
		return $longitude + atan2(sin($bearing) * sin($this->angular_distance) * cos($origin_latitude), 
			cos($this->angular_distance) - sin($origin_latitude) * sin($destination_latitude));
	}
}
