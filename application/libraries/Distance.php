<?php
/**
 * Distance Calculator library.
 *
 * Calculates the distance between two points given latitude/longitude
 * co-ordinates of both. Returns KMs or Miles
 * 
 * @package    Proximity
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class Distance_Core
{
	private $dist;
	
	/**
	 * Create a  Distance object
	 *
	 * @param	string	latitude of first point
	 * @param	string	longitude of first point
	 * @param	string	latitude2 of second point
	 * @param	string	longitude2 of second point
	 * @param	bool	True if the distance is in Kms, otherwise returns Miles
	 */
	function __construct(
		$latitude = 0,
		$longitude = 0,
		$latitude2 = 0,
		$longitude2 = 0,
		$in_kms = TRUE
	) {
		$EARTH_RADIUS_MILES = 3963;	// Miles
		$miles2kms = 1.609;
		$dist = 0;

		//convert degrees to radians
		$latitude = $latitude * M_PI / 180;
		$longitude = $longitude * M_PI / 180;
		$latitude2 = $latitude2 * M_PI / 180;
		$longitude2 = $longitude2 * M_PI / 180;

		if ($latitude != $latitude2 || $longitude != $longitude2) 
		{
			//the two points are not the same
			$dist = 
				sin($latitude) * sin($latitude2)
				+ cos($latitude) * cos($latitude2)
				* cos($longitude2 - $longitude);

			$dist = 
				$EARTH_RADIUS_MILES
				* (-1 * atan($dist / sqrt(1 - $dist * $dist)) + M_PI / 2);
		}
		
		if ($in_kms)
		{
			$dist = $dist * $miles2kms;
		}
		
		$this->dist = round($dist,2);
	}
	
	
	public function __get($name)
	{
		return $this->$name;
    }

	public function __toString()
	{
		return (string) $this->dist;
	}

}