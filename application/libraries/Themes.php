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
	public $editor_enabled = false;
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
		$content = Kohana::config("globalcode.head").
			$this->_header_css().
			$this->_header_feeds().
			$this->_header_js();

		// Filter::header_block - Modify Header Block
		Event::run('ushahidi_filter.header_block', $content);

		return $content;
	}

	/**
	* Admin Header Block
	*   The admin header has different requirements so it has a special function
	*/
	public function admin_header_block()
	{
		$content = Kohana::config("globalcode.head");

		// Filter::admin_header_block - Modify Admin Header Block
		Event::run('ushahidi_filter.admin_header_block', $content);

		return $content;
	}

	/**
	 * Css Items
	 */
	private function _header_css()
	{
		$core_css = "";
		$core_css .= html::stylesheet($this->css_url."media/css/jquery-ui-themeroller", "", TRUE);

		foreach (Kohana::config("settings.site_style_css") as $theme_css)
		{
			$core_css .= html::stylesheet($theme_css,"",TRUE);
		}

		$core_css .= "<!--[if lte IE 7]>".html::stylesheet($this->css_url."media/css/iehacks","",TRUE)."<![endif]-->";
		$core_css .= "<!--[if IE 7]>".html::stylesheet($this->css_url."media/css/ie7hacks","",TRUE)."<![endif]-->";
		$core_css .= "<!--[if IE 6]>".html::stylesheet($this->css_url."media/css/ie6hacks","",TRUE)."<![endif]-->";

		if ($this->map_enabled)
		{
			$core_css .= html::stylesheet($this->css_url."media/css/openlayers","",TRUE);
		}

		if ($this->treeview_enabled)
		{
			$core_css .= html::stylesheet($this->css_url."media/css/jquery.treeview","",TRUE);
		}

		if ($this->photoslider_enabled)
		{
			$core_css .= html::stylesheet($this->css_url."media/css/picbox/picbox","",TRUE);
		}

		if ($this->videoslider_enabled)
		{
			$core_css .= html::stylesheet($this->css_url."media/css/videoslider","",TRUE);
		}

		if ($this->colorpicker_enabled)
		{
			$core_css .= html::stylesheet($this->css_url."media/css/colorpicker","",TRUE);
		}

		if ($this->site_style AND $this->site_style != "default")
		{
			$core_css .= html::stylesheet($this->css_url."themes/".$site_style."/style.css");
		}

		$core_css .= html::stylesheet($this->css_url."media/css/global","",TRUE);
		$core_css .= html::stylesheet($this->css_url."media/css/jquery.jqplot.min", "", TRUE);

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
			$core_js .= html::script($this->js_url."media/js/OpenLayers", TRUE);
			$core_js .= "<script type=\"text/javascript\">OpenLayers.ImgPath = '".$this->js_url."media/img/openlayers/"."';</script>";
			$core_js .= html::script($this->js_url."media/js/ushahidi", TRUE);
		}

		$core_js .= html::script($this->js_url."media/js/jquery", TRUE);
		//$core_js .= html::script($this->js_url."media/js/jquery.ui.min", TRUE);
		$core_js .= html::script("https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js", TRUE);
		$core_js .= html::script($this->js_url."media/js/jquery.pngFix.pack", TRUE);
		$core_js .= html::script($this->js_url."media/js/jquery.timeago", TRUE);

		if ($this->map_enabled)
		{

			$core_js .= $this->api_url;

			if ($this->main_page || $this->this_page == "alerts")
			{
				$core_js .= html::script($this->js_url."media/js/selectToUISlider.jQuery", TRUE);
			}

			if ($this->main_page)
			{
				// Notes: E.Kala <emmanuel(at)ushahidi.com>
				// TODO: Only include the jqplot JS when the timeline is enabled
				$core_js .= html::script($this->js_url."media/js/jquery.jqplot.min");
				$core_js .= html::script($this->js_url."media/js/jqplot.dateAxisRenderer.min");

				$core_js .= "<!--[if IE]>".html::script($this->js_url."media/js/excanvas.min", TRUE)."<![endif]-->";
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
			$core_js .= html::script($this->js_url."media/js/picbox", TRUE);
		}

		if ($this->videoslider_enabled)
		{
			$core_js .= html::script($this->js_url."media/js/coda-slider.pack");
		}

		if ($this->colorpicker_enabled)
		{
			$core_js .= html::script($this->js_url."media/js/colorpicker");
		}

		$core_js .= html::script($this->js_url."media/js/global");

		if ($this->editor_enabled)
		{
			$core_js .= html::script($this->js_url."media/js/jwysiwyg/jwysiwyg/jquery.wysiwyg.js");
		}

		// Javascript files from plugins
		$plugin_js = plugin::render('javascript');

		// Javascript files from themes
		foreach (Kohana::config("settings.site_style_js") as $theme_js)
		{
			$core_js .= html::script($theme_js,"",TRUE);
		}

		// Inline Javascript
		$insert_js = trim($this->js);
		$inline_js = <<< INLINEJS
<script type="text/javascript">
//<![CDATA[
\$(function() { $(document).pngFix(); });

{$insert_js}
//]]>
</script>
INLINEJS;

		// Filter::header_js - Modify Header Javascript
		Event::run('ushahidi_filter.header_js', $inline_js);

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

	/**
	 * Footer Block potentially holds tracking codes or other code that needs
	 * to run in the footer
	 */
	public function footer_block()
	{
		$content = Kohana::config("globalcode.foot").
				$this->google_analytics()."\n".
				$this->ushahidi_stats_js()."\n".
				$this->scheduler_js();

		// Filter::footer_block - Modify Footer Block
		Event::run('ushahidi_filter.footer_block', $content);

		return $content;
	}

	public function languages()
	{
		// *** Locales/Languages ***
		// First Get Available Locales

		$locales = ush_locale::get_i18n();

		$languages = "";
		$languages .= "<div class=\"language-box\">";
		$languages .= form::open(NULL, array('method' => 'get'));

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
			if (is_array($value)) continue;
			$languages .= form::hidden($name, $value);
		}

		// Do a case insensitive sort of locales so it comes up in a rough alphabetical order

		natcasesort($locales);

		$languages .= form::dropdown('l', $locales, Kohana::config('locale.language'),
			' onchange="this.form.submit()" ');
		$languages .= form::close();
		$languages .= "</div>";

		return $languages;
	}

	public function search()
	{
		$search = "";
		$search .= "<div class=\"search-form\">";
		$search .= form::open("search", array('method' => 'get', 'id' => 'search'));
		$search .= "<ul>";
		$search .= "<li><input type=\"text\" name=\"k\" value=\"\" class=\"text\" /></li>";
		$search .= "<li><input type=\"submit\" name=\"b\" class=\"searchbtn\" value=\"".Kohana::lang('ui_main.search')."\" /></li>";
		$search .= "</ul>";
		$search .= form::close();
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
	public function google_analytics()
	{
		$html = "";
		if (Kohana::config('settings.google_analytics') == TRUE) {
			$html = "<script type=\"text/javascript\">

			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', '".Kohana::config('settings.google_analytics')."']);
			_gaq.push(['_trackPageview']);

			(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();

			</script>";
		}

		// See if we need to disqualify showing the tag on the admin panel
		if (Kohana::config('config.google_analytics_in_admin') == FALSE
			AND isset(Router::$segments[0])
			AND Router::$segments[0] == 'admin')
		{
			// Site is configured to not use the google analytics tag in the admin panel
			//   and we are in the admin panel. Wipe out the tag.
			$html = '';
		}


		return $html;
	}

	/**
	 * Scheduler JS Call
	 *
	 * @return string
	 */
	public function scheduler_js()
	{
		if (Kohana::config('config.output_scheduler_js'))
		{
			$schedulerPath = url::base() . 'scheduler';
			$schedulerCode = <<< SCHEDULER
				<!-- Task Scheduler -->
				<script type="text/javascript">
				setTimeout(function() {
					var scheduler = document.createElement('img');
					    scheduler.src = "$schedulerPath";
					    scheduler.style.cssText = "width: 1px; height: 1px; opacity: 0.1;";

					document.body.appendChild(scheduler);
				}, 200);
				</script>
				<!-- End Task Scheduler -->
SCHEDULER;
			return $schedulerCode;
		}
		return '';
	}

	/*
	* Ushahidi Stats JS Call
	*    If a deployer is using Ushahidi to track their stats, this is the JS
	*    call for that
	*/
	public function ushahidi_stats_js()
	{
		if (Kohana::config('settings.allow_stat_sharing') == 1)
		{
			return Stats_Model::get_javascript();
		}
		return '';
	}
}
