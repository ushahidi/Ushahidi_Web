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

	private $maxLat;
	private $minLat;
	private $maxLong;
	private $minLong;

	public $radius;
	
	const EQUATOR_LAT_MILE = 69.172;
	const EQUATOR_LAT_KM = 111.321543;
	
	/**
	 * Create a  Proximity object
	 *
	 * @param	string	latitude of this point
	 * @param	string	longitude of this point
	 * @param	int		the radius distance in miles/ km
	 * @param	bool	True if the distance is in Kms, otherwise returns Miles
	 */

	function __construct($latitude = 0, $longitude = 0, $distance = 100, $in_kms = TRUE) {
		$equator_lat_dist = $in_kms ? self::EQUATOR_LAT_KM : self::EQUATOR_LAT_MILE;
		
		$this->maxLat = $latitude + $distance / $equator_lat_dist;
		$this->minLat = $latitude - ($this->maxLat - $latitude);
		$this->maxLong = $longitude + $distance / (cos($this->minLat * M_PI / 180) * $equator_lat_dist);
		$this->minLong = $longitude - ($this->maxLong - $longitude);
	}

	public function __get($name) {
            return $this->$name;
    }
}
