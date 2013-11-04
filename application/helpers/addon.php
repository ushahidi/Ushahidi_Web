<?php
/**
 * Addons helper
 * 
 * @package    Ushahidi
 * @subpackage Helpers
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class addon_Core {
	
	/**
	 * Find addons in theme/plugin dir
	 * Optionally return the meta data too
	 * 
	 * @param   string addon type
	 * @param   boolean include meta data
	 * @return  array
	 */
	public static function get_addons($type, $include_meta = TRUE)
	{
		$addons = array();

		$base_path = ($type == 'plugin') ? PLUGINPATH : THEMEPATH;

		//var_dump(glob($base_path.'*', GLOB_ONLYDIR));
		// Set default values for addon_type
		if ($type == 'plugin')
		{
			$defaults = array(
				"name" => "",
				"description" => "",
				"website" => "",
				"author" => "",
				"version" => "",
			);
		}
		else
		{
			$defaults = array(
				'Theme Name' => "",
				'Description' => "",
				'Demo' => "",
				'Version' => "",
				'Author' => "",
				'Author Email' => "",
				'Template_Dir' => "",
				'Base Theme' => 'default',
				'CSS' => '',
				'JS' => '',
			);
		}

		// Files ushahidi/themes directory and one subdir down
		$pattern = $base_path . '*/readme.txt';
		foreach (glob($pattern) as $file)
		{
			$addon = str_replace(array('/readme.txt',$base_path),'',$file);
			
			$addons[$addon] = array();
			if ($include_meta)
			{
				$addons[$addon] = self::meta_data($addon, $type, $defaults);
			}
		}

		ksort($addons);

		return $addons;
	}
	

	/**
	 * Load addon Information from readme.txt file
	 *
	 * @param   string addon name
	 * @param   string addon type
	 * @param   array  default meta data
	 * @return  array
	 */
	public static function meta_data($addon, $type, $defaults = array())
	{
		$base = ($type == 'plugin') ? PLUGINPATH : THEMEPATH;
		// Determine if readme.txt (Case Insensitive) exists
		$file = $base.$addon."/readme.txt";
		if ( file::file_exists_i($file, $file) )
		{
			$fp = fopen( $file, 'r' );
			
			// Pull only the first 8kiB of the file in.
			$file_data = fread( $fp, 8192 );
			fclose( $fp );

			preg_match_all( '/^(.*):(.*)$/mU', $file_data, $matches, PREG_PATTERN_ORDER);
			$meta_data = array_combine(array_map('trim',$matches[1]), array_map('trim',$matches[2]));

			foreach (array('png', 'gif', 'jpg', 'jpeg') as $ext)
			{
				if (file_exists($base . $addon . "/screenshot.$ext"))
				{
					$meta_data['Screenshot'] = "screenshot.$ext";
					break;
				}
			}
			
			return arr::merge($defaults, $meta_data);
		}
		return false;
	}
}