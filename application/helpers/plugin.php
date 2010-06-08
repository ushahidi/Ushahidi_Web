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
	
	protected static $javascripts = array();
	protected static $stylesheets = array();

	public static function add_javascript($javascripts = array())
	{
		if ( ! is_array($javascripts))
			$javascripts = array($javascripts);

		foreach ($javascripts as $key => $javascript)
		{
			self::$javascripts[] = $javascript;
		}
	}

	public static function remove_javascript($javascripts = array())
	{
		foreach (self::$javascripts as $key => $javascript)
		{
			if (in_array($javascript, $javascripts))
				unset(self::$javascripts[$key]);
		}
	}

	public static function add_stylesheet($stylesheets = array())
	{
		if ( ! is_array($stylesheets))
			$stylesheets = array($stylesheets);

		foreach ($stylesheets as $key => $stylesheet)
		{
			self::$stylesheets[] = $stylesheet;
		}
	}

	public static function render($type)
	{
		$files = $type.'s';

		foreach (self::$$files as $key => $file)
		{
			switch ($type)
			{
				case 'stylesheet':
					if (substr_compare($file, '.css', -3, 3, FALSE) !== 0)
					{
						// Add the javascript suffix
						$file .= '.css';
					}
					echo '<link rel="stylesheet" type="text/css" href="'.url::site()."plugins/".$file.'" />';
					break;
				case 'javascript':
					if (substr_compare($file, '.js', -3, 3, FALSE) !== 0)
					{
						// Add the javascript suffix
						$file .= '.js';
					}
					echo '<script type="text/javascript" src="'.url::base()."plugins/".$file.'"></script>';
					break;
			}
		}
	}	
	
	/**
	 * Load Plugin Information from readme.txt file
	 *
	 * @param   string plugin name
	 * @return  array
	 */
	public static function meta($plugin = NULL)
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
	 * Discover Plugin Settings Controller
	 *
	 * @param   string plugin name
	 * @return  string plugin settings page
	 */
	public static function settings($plugin = NULL)
	{
		// Determine if readme.txt (Case Insensitive) exists
		$file = PLUGINPATH.$plugin."/controllers/admin/".$plugin."_admin.php";
		if ( file::file_exists_i($file) )
		{
			return $plugin."_admin";
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Delete plugin from Plugin Folder
	 *
	 * @param   string   plugin name/folder
	 */
	public static function delete($folder = NULL)
	{
		if ($folder)
		{
			if (is_dir(PLUGINPATH.$folder))
			{ // First Delete Files Recursively
				$files = scandir(PLUGINPATH.$folder);
				array_shift($files);    // remove '.' from array
				array_shift($files);    // remove '..' from array

				foreach ($files as $file)
				{
					$file = PLUGINPATH.$folder."/".$file;
					if (is_dir($file))
					{
						plugin::delete($file);
						try
						{
							rmdir($file);
						}
						catch (Kohana_Database_Exception $e)
						{
							echo 'Caught exception: ',  $e->getMessage(), "\n";
						}
					}
					else
					{
						try
						{
							unlink($file);
						}
						catch (Kohana_Database_Exception $e)
						{
							echo 'Caught exception: ',  $e->getMessage(), "\n";
						}
					}
				}
			}
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