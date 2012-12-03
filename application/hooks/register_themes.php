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
	
	protected $themes = array();
	protected $loaded_themes = array();
	
	protected $theme_js = array();
	protected $theme_css = array();
	
	/**
	 * Adds the register method to load after system.ready
	 */
	public function __construct()
	{
		// Hook into routing
		if (Kohana::config('config.installer_check') == FALSE OR file_exists(DOCROOT."application/config/database.php"))
		{
			Event::add('system.ready', array($this, 'register'));
		}
	}

	/**
	 * Loads ushahidi themes
	 */
	public function register()
	{
		$this->themes = addon::get_addons('theme', FALSE);
		
		$theme = Kohana::config("settings.site_style");
		$theme = empty($theme) ? 'default' : $theme;
		$this->_load_theme($theme);
		
		// Save theme CSS and JS for inclusion later
		Kohana::config_set('settings.site_style_js', $this->theme_js);
		Kohana::config_set('settings.site_style_css', $this->theme_css);
	}
	
	/**
	 * Load theme
	 * Loads theme into modules, includes its hooks and recursively loads parent themes
	 * @param string $theme theme name/directory
	 **/
	private function _load_theme($theme)
	{
		// Record loading this theme, so we can avoid dependency loops
		$this->loaded_themes[] = $theme;
		
		// Get meta data to check the base theme
		$meta = addon::meta_data($theme, 'theme', array('Base Theme' => 'default'));
		
		// If base theme is set, the base theme exists, and we haven't loaded it yet
		// Load the base theme
		if (! empty($meta['Base Theme'])
				AND isset($this->themes[$meta['Base Theme']])
				AND ! in_array($meta['Base Theme'], $this->loaded_themes)
			)
		{
			$this->_load_theme($meta['Base Theme']);
		}
		
		// Add theme to modules
		$theme_base = THEMEPATH . $theme;
		Kohana::config_set('core.modules', array_merge(array($theme_base), Kohana::config("core.modules")));

		// We need to manually include the hook file for each theme
		if (file_exists($theme_base.'/hooks'))
		{
			$d = dir($theme_base.'/hooks'); // Load all the hooks
			while (($entry = $d->read()) !== FALSE)
			{
				if ($entry[0] != '.')
				{
					include $theme_base.'/hooks/'.$entry;
				}
			}
		}
		
		$this->load_theme_css($theme);
		$this->load_theme_js($theme);
	}

	/*
	 * Find theme css and store for inclusion later
	 */
	private function load_theme_css($theme)
	{
		$css_dir = THEMEPATH.$theme.'/css';
		if ( is_dir($css_dir) )
		{
			$css = dir($css_dir); // Load all the themes css files
			while (($css_file = $css->read()) !== FALSE)
				if (preg_match('/\.css/i', $css_file))
				{
					$this->theme_css[str_replace('.css','',$css_file)] = "themes/$theme/css/$css_file";
				}
		}
	}

	/*
	 * Find theme css and store for inclusion later
	 */
	private function load_theme_js($theme)
	{
		if ( is_dir(THEMEPATH.$theme.'/js') )
		{
			$js = dir(THEMEPATH.$theme.'/js'); // Load all the themes js files
			while (($js_file = $js->read()) !== FALSE)
				if (preg_match('/\.js/i', $js_file))
				{
					$this->theme_js[str_replace('.js','',$js_file)] = "themes/$theme/js/$js_file";
				}
		}
	}
}

new register_themes;