<?php
/**
 * Proximity Calculator library.
 * allows for radius-based searches to quickly written, tested, and deployed.
 * It works by calculating the lines of latitude and longitude at
 * exactly X miles(Kms) from the specified center point.
 * This allows for much more efficient database queries to be written by
 * eliminating almost all points that don't fall within the specified radius.
 * password hashing.
 * 
 * @package    Proximity
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class Proximity_Core {

	var $maxLat;
	var $minLat;
	var $maxLong;
	var $minLong;
	
	
	/**
	 * Retrieve Proximity Values
	 *
	 * @param   string	 latitude of this point
	 * @param   string   longitude of this point
	 * @param   string   the radius distance in miles
	 */
	public function Proximity($Latitude = 0, $Longitude = 0, $Miles = 100) {
		global $maxLat,$minLat,$maxLong,$minLong;
		$EQUATOR_LAT_MILE = 69.172;
		$EQUATOR_LAT_KM = 111.321543;
		$maxLat = $Latitude + $Miles / $EQUATOR_LAT_MILE;
		$minLat = $Latitude - ($maxLat - $Latitude);
		$maxLong = $Longitude + $Miles / (cos($minLat * M_PI / 180) * $EQUATOR_LAT_MILE);
		$minLong = $Longitude - ($maxLong - $Longitude);
	}

	public function MaxLatitude() {
		return $GLOBALS["maxLat"];
	}
	public function MinLatitude() {
		return $GLOBALS["minLat"];
	}
	public function MaxLongitude() {
		return $GLOBALS["maxLong"];
	}
	public function MinLongitude() {
		return $GLOBALS["minLong"];
	}
	
}