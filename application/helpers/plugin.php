<?php
/**
 * Plugins helper
 * 
 * @package    Ushahidi
 * @subpackage Helpers
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class plugin_Core {
	
	/**
	 * @var array
	 */
	protected static $javascripts = array();
	
	/**
	 * @var array
	 */
	protected static $stylesheets = array();
	
	/**
	 * @var array
	 */
	protected static $sms_providers = array();
	
	/**
	 * Adds an array of javascript items to the list of javascript sources
	 *
	 * @param array $javascripts
	 */
	public static function add_javascript($javascripts = array())
	{
		if ( ! is_array($javascripts))
			$javascripts = array($javascripts);

		foreach ($javascripts as $key => $javascript)
		{
			self::$javascripts[] = $javascript;
		}
	}
	
	/**
	 * Removes a list of javascript items from the list of javascript
	 * sources
	 *
	 * @param array $javascripts
	 */
	public static function remove_javascript($javascripts = array())
	{
		foreach (self::$javascripts as $key => $javascript)
		{
			if (in_array($javascript, $javascripts))
				unset(self::$javascripts[$key]);
		}
	}
	
	/**
	 * Adds a list of stylesheet items to the list of stylesheets for the
	 * plugin
	 *
	 * @param array $stylesheets
	 */
	public static function add_stylesheet($stylesheets = array())
	{
		if ( ! is_array($stylesheets))
			$stylesheets = array($stylesheets);

		foreach ($stylesheets as $key => $stylesheet)
		{
			self::$stylesheets[] = $stylesheet;
		}
	}
	
	/**
	 * Adds an SMS provider to the list of available SMS providers
	 *
	 * @param array $sms_providers
	 */
	public static function add_sms_provider($sms_providers = array())
	{
		if ( ! is_array($sms_providers))
			$sms_providers = array($sms_providers => $sms_providers);

		foreach ($sms_providers as $key => $sms_provider)
		{
			self::$sms_providers[$key] = $sms_provider;
		}
	}
	
	/**
	 * Adds a the stylesheet/javascript to the header of the view file
	 *
	 * @param string $type
	 */
	public static function render($type)
	{
		$files = $type.'s';
		
		$html = '';

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
					$html .= '<link rel="stylesheet" type="text/css" href="'.url::base()."plugins/".$file.'" />';
					break;
				case 'javascript':
					if (substr_compare($file, '.js', -3, 3, FALSE) !== 0)
					{
						// Add the javascript suffix
						$file .= '.js';
					}
					$html .= '<script type="text/javascript" src="'.url::base()."plugins/".$file.'"></script>';
					break;
			}
		}
		
		return $html;
	}
	
	/**
	 * Rettuns the list of SMS providers
	 *
	 * @return array
	 */
	public static function get_sms_providers()
	{
		return self::$sms_providers;
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
	 * @return  mixed Plugin settings page on success, FALSE otherwise
	 */
	public static function settings($plugin = NULL)
	{
		// Determine if readme.txt (Case Insensitive) exists
		$file = PLUGINPATH.$plugin."/controllers/admin/".$plugin."_settings.php";
		if ( file::file_exists_i($file) )
		{
			return $plugin."_settings";
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Delete plugin from Plugin Folder
	 *
	 * @param string  plugin name/folder
	 */
	public static function delete($folder = NULL)
	{
		if ($folder)
		{
			if (is_dir(PLUGINPATH.$folder))
			{ 
				// First Delete Files Recursively
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
							// Log exceptions
							Kohana::log('error', 'Caught exception: '.$e->getMessage());
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
							// Log exceptions
							Kohana::log('error', 'Caught exception: '.$e->getMessage());
						}
					}
				}
			}
		}
	}

	/**
	 * Temporarily load a config file.
	 *
	 * @param string $name config filename, without extension
	 * @param boolean $required is the file required?
	 * @return array
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