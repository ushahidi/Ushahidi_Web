<?php
/**
 * Collector class for javascript and stylesheets
 * Thanks to Zombor @ Argentum
 * 
 * @package    Page
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */

class page_Core {

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
}