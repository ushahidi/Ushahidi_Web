<?php
/**
 * Plugins helper
 * 
 * @package    Plugin
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class plugin_Core {
	
	/**
	 * Temporarily load a config file.
	 *
	 * @param   string plugin name
	 * @return  array
	 */
	public static function meta($plugin)
	{	
		// Set Default Values
		$plugin_headers = array(
			"plugin_name" => "name",
			"plugin_description" => "description",
			"plugin_uri" => "website",
			"plugin_author" => "author",
			"plugin_version" => "version",
			);
			
		// Determine if readme.txt (Case Insensitive) exists
		$file = PLUGINPATH.$plugin."/readme.txt";
		if ( file::file_exists_i($file) )
		{
			$fp = fopen( $file, 'r' );
			
			// Pull only the first 8kiB of the file in.
			$file_data = fread( $fp, 8192 );
			fclose( $fp );

			foreach ( $plugin_headers as $field => $regex )
			{
				preg_match( '/' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, ${$field});
				if ( ! empty( ${$field} ) )
				{
					${$field} = trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', ${$field}[1] ));
				}
				else
				{
					${$field} = '';
				}
			}

			$file_data = compact( array_keys( $plugin_headers ) );
			
			return $file_data;
		}
		else
		{
			return $plugin_headers;
		}
	}
	
	
	/**
	 * Temporarily load a config file.
	 *
	 * @param   string   config filename, without extension
	 * @param   boolean  is the file required?
	 * @return  array
	 */
	public static function config_load($name, $required = TRUE)
	{
		if (isset(self::$internal_cache['configuration'][$name]))
			return self::$internal_cache['configuration'][$name];

		// Load matching configs
		$configuration = array();
		
		$file = PLUGINPATH.$name.'/config/'.$name.'.php';
		if ( file_exists($file) )
		{
			require $file;
			if (isset($config) AND is_array($config))
			{
				// Merge in configuration
				$configuration = array_merge($configuration, $config);
			}
		}

		if ( ! isset(self::$write_cache['configuration']))
		{
			// Cache has changed
			self::$write_cache['configuration'] = TRUE;
		}

		return self::$internal_cache['configuration'][$name] = $configuration;
	}	
}