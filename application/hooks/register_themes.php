<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Register Themes Hook
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Register Themes Hook
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class register_themes {
	/**
	 * Adds the register method to load after system.ready
	 */
	public function __construct()
	{
		// Hook into routing
		if (file_exists(DOCROOT."application/config/database.php"))
		{
			Event::add('system.ready', array($this, 'register'));
		}
	}

	/**
	 * Loads ushahidi themes
	 */
	public function register()
	{
		// Array to hold all the CSS files
		$theme_css = array();
		
		// 1. Load the default theme
		Kohana::config_set('core.modules', array_merge(array(THEMEPATH."default"), 
			Kohana::config("core.modules")));
			
		$css_url = (Kohana::config("cache.cdn_css")) ? 
			Kohana::config("cache.cdn_css") : url::base();
		$theme_css[] = $css_url."themes/default/css/style.css";
		
		// 2. Extend the default theme
		if ( Kohana::config("settings.site_style") != "default" )
		{
			$theme = THEMEPATH.Kohana::config("settings.site_style");
			Kohana::config_set('core.modules', array_merge(array($theme),
				Kohana::config("core.modules")));
				
			if ( is_dir($theme.'/css') )
			{				
				$css = dir($theme.'/css'); // Load all the themes css files
				while (($css_file = $css->read()) !== FALSE)
					if (preg_match('/\.css/i', $css_file))
					{
						$theme_css[] = url::base()."themes/".Kohana::config("settings.site_style")."/css/".$css_file;
					}
			}
		}
		
		Kohana::config_set('settings.site_style_css',$theme_css);
	}
}

new register_themes;