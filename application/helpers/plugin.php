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
	 * Rettuns the list of SMS providers
	 *
	 * @return array
	 */
	public static function get_sms_providers()
	{
		return self::$sms_providers;
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
	 * Adds a the stylesheet/javascript to the header of the view file
	 *
	 * @param string $type
	 */
	public static function get_requirements($type)
	{
		$files = $type.'s';
		
		$output = array();

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
					$output[] = "plugins/".$file;
					break;
				case 'javascript':
					if (substr_compare($file, '.js', -3, 3, FALSE) !== 0)
					{
						// Add the javascript suffix
						$file .= '.js';
					}
					$output[] = "plugins/".$file;
					break;
			}
		}
		
		return $output;
	}
	
	/**
	 * Find plugin install file
	 * Using this function because someone somewhere will name this file wrong!!!
	 * @param string $plugin 
	 */
	public static function find_install($plugin, $type = 'plugin')
	{
		$base = ($type == 'plugin') ? PLUGINPATH : THEMEPATH;
		// Determine if readme.txt (Case Insensitive) exists
		$file = $base."{$plugin}/libraries/{$plugin}_install.php";
		$real_path = null;
		if ( file::file_exists_i($file, $real_path) )
		{
			return $real_path;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Discover Plugin Settings Controller
	 *
	 * @param   string plugin name
	 * @return  mixed Plugin settings page on success, FALSE otherwise
	 */
	public static function find_settings($plugin)
	{
		// Determine if settings controller exists
		$file = "admin/{$plugin}_settings";
		$file2 = "admin/settings/{$plugin}";
		if ($path = Kohana::find_file('controllers', $file))
		{
			return $file;
		}
		elseif ($path = Kohana::find_file('controllers', $file2))
		{
			return $file2;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Resync plugins in codebase with database
	 */
	public static function resync_plugins()
	{
		$plugins = addon::get_addons('plugin', FALSE);

		// Get all plugins from the db
		$plugins_db = ORM::factory('plugin')->select_list('id','plugin_name');

		// Sync the folder with the database
		foreach ($plugins as $dir => $plugin)
		{
			if ( ! in_array($dir, $plugins_db))
			{
				$plugin = ORM::factory('plugin');
				$plugin->plugin_name = $dir;
				$plugin->save();
			}
		}

		// Remove any plugins not found in the plugins folder and not previously installed from the database
		foreach (ORM::factory('plugin')->where('plugin_installed', 0)->find_all() as $plugin)
		{
			if ( ! array_key_exists($plugin->plugin_name, $plugins) )
			{
				$plugin->delete();
			}
		}
	}
}