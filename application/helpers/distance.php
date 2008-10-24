<?php
/**
 * Distance Calculator helper
 * Formulas to calculate the exact distance in miles 
 * between two points on a sphere.
 * 
 * @package    Distance
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class distance_Core {
	
	function distance() {
		
	}

	/**
	 * Calculate distances between two points
	 *
	 * @param   string	 point 1 latitude
	 * @param   string   point 1 longitude
	 * @param   string	 point 2 latitude
	 * @param   string   point 2 longitude
	 */
	public static function calculate(
		$dblLat1,
		$dblLong1,
		$dblLat2,
		$dblLong2
	) {
		$EARTH_RADIUS_MILES = 3963;
		$EARTH_RADIUS_KMS = 6377.83027;
		
		$dist = 0;

		//convert degrees to radians
		$dblLat1 = $dblLat1 * M_PI / 180;
		$dblLong1 = $dblLong1 * M_PI / 180;
		$dblLat2 = $dblLat2 * M_PI / 180;
		$dblLong2 = $dblLong2 * M_PI / 180;

		if ($dblLat1 != $dblLat2 || $dblLong1 != $dblLong2) 
		{
			//the two points are not the same
			$dist = 
				sin($dblLat1) * sin($dblLat2)
				+ cos($dblLat1) * cos($dblLat2)
				* cos($dblLong2 - $dblLong1);

			$dist = 
				$EARTH_RADIUS_MILES
				* (-1 * atan($dist / sqrt(1 - $dist * $dist)) + M_PI / 2);
		}
		return $dist;
	}
		
}