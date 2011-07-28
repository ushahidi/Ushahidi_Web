<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Themes Library
 * These are regularly used templating functions
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   Themes Library
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Themes_Core {
	
	public $map_enabled = false;
	public $api_url = null;
	public $main_page = false;
	public $this_page = false;
	public $treeview_enabled = false;
	public $validator_enabled = false;
	public $photoslider_enabled = false;
	public $videoslider_enabled = false;
	public $colorpicker_enabled = false;
	public $site_style = false;
	public $js = null;
	
	public $css_url = null;
	public $js_url = null;
	
	public function __construct()
	{
		// Load cache
		$this->cache = new Cache;

		// Load Session
		$this->session = Session::instance();
		
		// Grab the proper URL for the css and js files
		$this->css_url = url::file_loc('css');
		$this->js_url = url::file_loc('js');
	}
	
	/**
	 * Header Block Contains CSS, JS and Feeds
	 * Css is loaded before JS
	 */
	public function header_block()
	{
		return $this->_header_css().
			$this->_header_feeds().
			$this->_header_js();
	}
	
	/**
	 * Css Items
	 */
	private function _header_css()
	{
		$core_css = "";
		$core_css .= html::stylesheet($this->css_url."media/css/jquery-ui-themeroller", "", true);
		
		foreach (Kohana::config("settings.site_style_css") as $theme_css)
		{
			$core_css .= html::stylesheet($theme_css,"",true);
		}
		
		$core_css .= "<!--[if lte IE 7]>".html::stylesheet($this->css_url."media/css/iehacks","",true)."<![endif]-->";
		$core_css .= "<!--[if IE 7]>".html::stylesheet($this->css_url."media/css/ie7hacks","",true)."<![endif]-->";
		$core_css .= "<!--[if IE 6]>".html::stylesheet($this->css_url."media/css/ie6hacks","",true)."<![endif]-->";
			
		if ($this->map_enabled)
		{
			$core_css .= html::stylesheet($this->css_url."media/css/openlayers","",true);
		}
		
		if ($this->treeview_enabled)
		{
			$core_css .= html::stylesheet($this->css_url."media/css/jquery.treeview","",true);
		}
		
		if ($this->photoslider_enabled)
		{
			$core_css .= html::stylesheet($this->css_url."media/css/picbox/picbox","",true);
		}
		
		if ($this->videoslider_enabled)
		{
			$core_css .= html::stylesheet($this->css_url."media/css/videoslider","",true);
		}
		
		if ($this->colorpicker_enabled)
		{
			$core_css .= html::stylesheet($this->css_url."media/css/colorpicker","",true);
		}
		
		if ($this->site_style AND $this->site_style != "default")
		{
			$core_css .= html::stylesheet($this->css_url."themes/".$site_style."/style.css");
		}
		
		// Render CSS
		$plugin_css = plugin::render('stylesheet');
		
		return $core_css.$plugin_css;
	}
	
	/**
	 * Javascript Files and Inline JS
	 */
	private function _header_js()
	{
		$core_js = "";
		if ($this->map_enabled)
		{
			$core_js .= html::script($this->js_url."media/js/OpenLayers", true);
			$core_js .= "<script type=\"text/javascript\">OpenLayers.ImgPath = '".$this->js_url."media/img/openlayers/"."';</script>";
		}
		
		$core_js .= html::script($this->js_url."media/js/jquery", true);
		//$core_js .= html::script($this->js_url."media/js/jquery.ui.min", true);
		$core_js .= html::script("https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js", true);
		$core_js .= html::script($this->js_url."media/js/jquery.pngFix.pack", true);
		
		if ($this->map_enabled)
		{
			$core_js .= $this->api_url;

			if ($this->main_page || $this->this_page == "alerts")
			{
				$core_js .= html::script($this->js_url."media/js/selectToUISlider.jQuery", true);
			}

			if ($this->main_page)
			{
				$core_js .= html::script($this->js_url."media/js/jquery.flot", true);
				$core_js .= html::script($this->js_url."media/js/timeline", true);
				$core_js .= "<!--[if IE]>".html::script($this->js_url."media/js/excanvas.min", true)."<![endif]-->";
			}
		}

		if ($this->treeview_enabled)
		{
			$core_js .= html::script($this->js_url."media/js/jquery.treeview");
		}

		if ($this->validator_enabled)
		{
			$core_js .= html::script($this->js_url."media/js/jquery.validate.min");
		}

		if ($this->photoslider_enabled)
		{
			$core_js .= html::script($this->js_url."media/js/picbox", true);
		}

		if($this->videoslider_enabled )
		{
			$core_js .= html::script($this->js_url."media/js/coda-slider.pack");
		}
		
		if ($this->colorpicker_enabled)
		{
			$core_js .= html::script($this->js_url."media/js/colorpicker");
		}
		
		// Javascript files from plugins
		$plugin_js = plugin::render('javascript');
		
		// Inline Javascript
		$inline_js = "<script type=\"text/javascript\">
                        <!--//
function runScheduler(img){img.onload = null;img.src = '".url::site().'scheduler'."';}
			".'$(document).ready(function(){$(document).pngFix();});'.$this->js.
                        "//-->
                        </script>";
		
		return $core_js.$plugin_js.$inline_js;
	}
	
	/**
	 * RSS/Atom
	 */
	private function _header_feeds()
	{
		$feeds = "";
		if (Kohana::config("settings.allow_feed"))
		{
			$feeds .= "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".url::site()."feed/\" title=\"RSS2\" />";
		}
		
		return $feeds;
	}
	
	public function languages()
	{
		// *** Locales/Languages ***
		// First Get Available Locales

		$locales = $this->cache->get('locales');

		// If we didn't find any languages, we need to look them up and set the cache
		if( ! $locales)
		{
			$locales = locale::get_i18n();
			$this->cache->set('locales', $locales, array('locales'), 604800);
		}
		
		// Locale form submitted?
		if (isset($_GET['l']) && !empty($_GET['l']))
		{
			$this->session->set('locale', $_GET['l']);
		}
		// Has a locale session been set?
		if ($this->session->get('locale',FALSE))
		{
			// Change current locale
			Kohana::config_set('locale.language', $_SESSION['locale']);
		}
		
		$languages = "";
		$languages .= "<div class=\"language-box\">";
		$languages .= "<form action=\"\">";
		
		/**
		 * E.Kala - 05/01/2011
		 *
		 * Fix to ensure to ensure that a change in language loads the page with the same data
		 * 
		 * Only fetch the $_GET data to prevent double submission of data already submitted via $_POST
		 * and create hidden form fields for each variable so that these are submitted along with the selected language
		 *
		 * The assumption is that previously submitted data had already been sanitized!
		 */
		foreach ($_GET as $name => $value)
		{
		    $languages .= form::hidden($name, $value);
		}
		
		// Do a case insensitive sort of locales so it comes up in a rough alphabetical order

		natcasesort($locales);

		$languages .= form::dropdown('l', $locales, Kohana::config('locale.language'),
			' onchange="this.form.submit()" ');
		$languages .= "</form>";
		$languages .= "</div>";
		
		return $languages;
	}
	
	public function search()
	{
		$search = "";
		$search .= "<div class=\"search-form\">";
		$search .= "<form method=\"get\" id=\"search\" action=\"".url::site()."search/\">";
		$search .= "<ul>";
		$search .= "<li><input type=\"text\" name=\"k\" value=\"\" class=\"text\" /></li>";
		$search .= "<li><input type=\"submit\" name=\"b\" class=\"searchbtn\" value=\"search\" /></li>";
		$search .= "</ul>";
		$search .= "</form>";
		$search .= "</div>";
		
		return $search;
	}

	public function submit_btn()
	{
		$btn = "";
		
		// Action::pre_nav_submit - Add items before the submit button
		$btn .= Event::run('ushahidi_action.pre_nav_submit');
		
		if (Kohana::config('settings.allow_reports'))
		{
			$btn .= "<div class=\"submit-incident clearingfix\">";
			$btn .= "<a href=\"".url::site()."reports/submit"."\">".Kohana::lang('ui_main.submit')."</a>";
			$btn .= "</div>";
		}
		
		// Action::post_nav_submit - Add items after the submit button
		$btn .= Event::run('ushahidi_action.post_nav_submit');
		
		return $btn;
	}
	
	/*
	* Google Analytics
	* @param text mixed	 Input google analytics web property ID.
	* @return mixed	 Return google analytics HTML code.
	*/
	public function google_analytics($google_analytics = false)
	{
		$html = "";
		if (!empty($google_analytics)) {
			$html = "<script type=\"text/javascript\">
				var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");
				document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));
				</script>
				<script type=\"text/javascript\">
				var pageTracker = _gat._getTracker(\"" . $google_analytics . "\");
				pageTracker._trackPageview();
				</script>";
		}
		return $html;
	}
}