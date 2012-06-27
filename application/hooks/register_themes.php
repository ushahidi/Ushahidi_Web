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
		$this->themes = addon::get_addons('theme', TRUE);
		
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
		$meta = $this->themes[$theme];
		
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
		$meta = $this->themes[$theme];
		// Add special cases for old themes
		if (empty($meta['CSS']))
		{
			$meta['CSS'] = array();
			$meta['CSS'][] = 'base';
			$meta['CSS'][] = 'style';
			$meta['CSS'][] = '_default';
			$meta['CSS'][] = $theme;
		}
		else
		{
			$meta['CSS'] = explode(',', $meta['CSS']);
			$meta['CSS'] = array_map('trim',$meta['CSS']);
		}
		
		// Add specified theme stylesheets
		foreach ($meta['CSS'] as $css)
		{
			if (file_exists(THEMEPATH."$theme/css/$css.css"))
				$this->theme_css[$css] = "themes/$theme/css/$css";
		}
		
		// Check for overrides of already added stylesheets
		foreach ($this->theme_css as $css => $path)
		{
			if (file_exists(THEMEPATH."$theme/css/$css.css"))
				$this->theme_css[$css] = "themes/$theme/css/$css";
		}
	}

	/*
	 * Find theme css and store for inclusion later
	 */
	private function load_theme_js($theme)
	{
		$meta = $this->themes[$theme];
		// Add special cases for old themes
		if (empty($meta['JS']))
		{
			$meta['JS'] = array();
		}
		else
		{
			$meta['JS'] = explode(',', $meta['JS']);
			$meta['JS'] = array_map('trim',$meta['JS']);
		}
		
		// Add specified theme js
		foreach ($meta['JS'] as $js)
		{
			if (file_exists(THEMEPATH."$theme/js/$js.js"))
				$this->theme_js[$js] = "themes/$theme/js/$js";
		}
		
		// Check for overrides of already added js
		foreach ($this->theme_css as $js => $path)
		{
			if (file_exists(THEMEPATH."$theme/js/$js.js"))
				$this->theme_js[$js] = "themes/$theme/js/$js";
		}
	}
}

new register_themes;