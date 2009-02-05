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

	const EQUATOR_LAT_MILE = 69.172;
	const EQUATOR_LAT_KM = 111.321543;

	/**
	 * Create a proximity object
	 *
	 * @param   int	 latitude of this point
	 * @param   int  longitude of this point
	 * @param   int  the radius distance in miles
	 * @param	bool indicates whether the distance is measured in Kms
	 */
	function __construct($latitude = 0, $longitude = 0, $distance = 100, $is_kms = FALSE)
	{
		$equator_lat_dist = $is_kms ? self::EQUATOR_LAT_KM : self::EQUATOR_LAT_MILE;

		$this->maxLat = $latitude + $distance / $equator_lat_dist;
		$this->minLat = $latitude - ($maxLat - $latitude);
		$this->maxLong = $longitude + $distance / (cos($minLat * M_PI / 180) * $equator_lat_dist);
		$this->minLong = $longitude - ($maxLong - $longitude);
	}
		
	public function __get($name) 
	{
		return $this->$name;
	}
}