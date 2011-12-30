<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Badges helper class.
 *
 * @package    Admin
 * @author     Ushahidi Team
 * @copyright  (c) 2011 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class badges_Core {

	/**
	 * Get all of the predefined images in badge packs
	 *
	 * @return array 	ie. Array('packname'=>Array('badge1.jpg','badge2.png','badge3.jpg'))
	 */
	public static function get_packs()
	{	
		$packs = array();

		$allowed_badge_extensions = array('png', 'gif', 'jpg', 'jpeg');

		$bp_path = MEDIAPATH.'img/badge_packs/';

		$directories = scandir($bp_path);

		foreach($directories as $directory)
		{
			// Don't examine anything starting with a dot (ie .DS_Store, .., .)
			if ($directory{0} == '.')
				continue;

			$packname = $directory;
			$pack_dir = $bp_path.$directory;

			// Only try to look in the directory if it's actually a directory
			if (is_dir($pack_dir))
			{
				foreach(scandir($pack_dir) as $filename)
				{
					if ($filename{0} == '.')
						continue;

					// We only want to consider images.
					if ( in_array(pathinfo($filename, PATHINFO_EXTENSION),$allowed_badge_extensions) )
					{
						$packs[$packname][] = $filename;
					}
				}
			}
		}

		return $packs;
	}

}