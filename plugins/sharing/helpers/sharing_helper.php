<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Sharing helper class.
 *
 * @package	   Sharing
 * @author	   Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license	   http://www.ushahidi.com/license.html
 */
class sharing_helper_Core {
	/**
    * Clean Urls
		* We want to standardize urls to prevent duplication
    */
	public static function clean_url($url)
	{
		// Assume http if not included (but don't remove http since it could be https)
		if (stripos($url, 'http') === FALSE)
		{
			$url = 'http://'.$url;
		}

		// Remove trailing slash/s
		$url = preg_replace('{/$}', '', $url);

		return $url;
	}
}
	