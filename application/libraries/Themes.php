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

	public $frontend = false;
	public $admin = false;
	
	public $map_enabled = false;
	public $api_url = null;
	public $this_page = false;
	public $treeview_enabled = false;
	public $validator_enabled = false;
	public $photoslider_enabled = false;
	public $colorpicker_enabled = false;
	public $datepicker_enabled = false;
	public $editor_enabled = false;
	public $protochart_enabled = false;
	public $raphael_enabled = false;
	public $tablerowsort_enabled = false;
	public $json2_enabled = false;
	public $hovertip_enabled = false;
	public $slider_enabled = false;
	public $timeline_enabled = false;
	
	// Custom JS to be added
	public $js = null;

	public $css_url = null;
	public $js_url = null;

	public function __construct()
	{
		// Load cache
		$this->cache = new Cache;

		// Load Session
		$this->session = Session::instance();
	}

	/**
	 * Header Block Contains CSS, JS and Feeds
	 * Css is loaded before JS
	 */
	public function header_block()
	{
		$content = '';
		// For backward compatibility render Requirements here rather than in the view
		if (Kohana::config('requirements.write_js_to_body'))
		{
			$content .= Requirements::render('css');
			$content .= Requirements::render('headtags');
		}
		else
		{
			$content .= Requirements::render();
		}
		
		// Filter::header_block - Modify Header Block
		if ($this->admin)
		{
			Event::run('ushahidi_filter.admin_header_block', $content);
		}
		elseif ($this->frontend)
		{
		Event::run('ushahidi_filter.header_block', $content);
		}

		return $content;
	}

	/**
	* Admin Header Block
	*   The admin header has different requirements so it has a special function
	*/
	public function admin_header_block()
	{
		$this->header_block();
	}

	/**
	 * CSS/JS requirements
	 */
	public function requirements()
	{
		Requirements::customHeadTags(Kohana::config("globalcode.head"),'globalcode-head');
		
		Requirements::js("media/js/jquery.js");
		Requirements::js("media/js/jquery.ui.min.js");
		//Requirements::js(Kohana::config('core.site_protocol')."://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js");
		Requirements::js("media/js/jquery.pngFix.pack.js");
		Requirements::js("media/js/jquery.timeago.js");
		
		Requirements::css("media/css/jquery-ui-themeroller.css");
		
		Requirements::js('media/js/global.js');
		Requirements::css('media/css/global.css');

		if ($this->map_enabled)
		{
			Requirements::js("media/js/OpenLayers.js");
			Requirements::js("media/js/ushahidi.js");
			if ($this->api_url)
			{
				Requirements::js($this->api_url);
			}
			Requirements::customJS("OpenLayers.ImgPath = '".url::file_loc('js')."media/img/openlayers/"."';",'openlayers-imgpath');
			
			Requirements::css("media/css/openlayers.css");
		}
		
		if ($this->hovertip_enabled)
		{
			Requirements::js('media/js/jquery.hovertip-1.0.js');
			Requirements::css('media/css/jquery.hovertip-1.0.css', '');
			Requirements::customJS(
				"$(function() {
						if($('.tooltip[title]') != null)
						$('.tooltip[title]').hovertip();
					});",
				'tooltip-js'
			);
		}
		
		if ($this->slider_enabled)
		{
			Requirements::js('media/js/selectToUISlider.jQuery.js');
		}

		if ($this->timeline_enabled)
		{
			Requirements::js("media/js/jquery.jqplot.min.js");
			Requirements::css("media/css/jquery.jqplot.min.css");
			Requirements::js("media/js/jqplot.dateAxisRenderer.min.js");
			Requirements::js("media/js/jqplot.barRenderer.min.js"); // HT: added for bar graph
			Requirements::js("media/js/jqplot.pointLabels.min.js"); // HT: added for showing point label
		}

		if ($this->treeview_enabled)
		{
			Requirements::css("media/css/jquery.treeview.css");
			Requirements::js("media/js/jquery.treeview.js");
		}
	
		// Load ProtoChart
		if ($this->protochart_enabled)
		{
			Requirements::customJS("jQuery.noConflict()", 'jquery-noconflict');
			Requirements::js('media/js/protochart/prototype.js');
			Requirements::customHeadTags(
				'<!--[if IE]>'.html::script(url::file_loc('js').'media/js/protochart/excanvas-compressed', TRUE).'<![endif]-->', 
				'ie-excanvas-compressed');
			Requirements::js('media/js/protochart/ProtoChart.js');
		}
	
		// Load Raphael
		if ($this->raphael_enabled)
		{
			// The only reason we include prototype is to keep the div element naming convention consistent
			//Requirements::js('media/js/protochart/prototype.js');
			Requirements::js('media/js/raphael.js');
			Requirements::customJS('var impact_json = '.$this->impact_json .';','impact_json');
			Requirements::js('media/js/raphael-ushahidi-impact.js');
		}

		if ($this->validator_enabled)
		{
			Requirements::js("media/js/jquery.validate.min.js");
		}

		if ($this->photoslider_enabled)
		{
			Requirements::css("media/css/picbox/picbox.css");
			Requirements::js("media/js/picbox.js");
		}

		if ($this->colorpicker_enabled)
		{
			Requirements::css("media/css/colorpicker.css");
			Requirements::js("media/js/colorpicker.js");
		}

		// Load jwysiwyg
		if ($this->editor_enabled)
		{
			Requirements::css('media/js/jwysiwyg/jquery.wysiwyg.css');
			Requirements::css('media/js/jwysiwyg/plugins/fileManager/wysiwyg.fileManager.css');
			if (Kohana::config("cdn.cdn_ignore_jwysiwyg") == TRUE)
			{
				Requirements::js(url::file_loc('ignore').'media/js/jwysiwyg/jquery.wysiwyg.js'); // not sure what the hell to do about this
				Requirements::js(url::file_loc('ignore').'media/js/jwysiwyg/controls/wysiwyg.link.js');
				Requirements::js(url::file_loc('ignore').'media/js/jwysiwyg/controls/wysiwyg.image.js');
				Requirements::js(url::file_loc('ignore').'media/js/jwysiwyg/controls/wysiwyg.table.js');
				Requirements::js(url::file_loc('ignore').'media/js/jwysiwyg/plugins/wysiwyg.fullscreen.js');
				Requirements::js(url::file_loc('ignore').'media/js/jwysiwyg/plugins/wysiwyg.rmFormat.js');
				Requirements::js(url::file_loc('ignore').'media/js/jwysiwyg/plugins/wysiwyg.fileManager.js');
			}
			else
			{
				Requirements::js('media/js/jwysiwyg/jquery.wysiwyg.js');
				Requirements::js('media/js/jwysiwyg/controls/wysiwyg.link.js');
				Requirements::js('media/js/jwysiwyg/controls/wysiwyg.image.js');
				Requirements::js('media/js/jwysiwyg/controls/wysiwyg.table.js');
				Requirements::js('media/js/jwysiwyg/plugins/wysiwyg.fullscreen.js');
				Requirements::js('media/js/jwysiwyg/plugins/wysiwyg.rmFormat.js');
				Requirements::js('media/js/jwysiwyg/plugins/wysiwyg.fileManager.js');
			}
		}
	
		// Table Row Sort
		if ($this->tablerowsort_enabled)
		{
			Requirements::js('media/js/jquery.tablednd_0_5.js');
		}
	
		// JSON2 for IE+
		if ($this->json2_enabled)
		{
			Requirements::js('media/js/json2.js');
		}
		
		if ($this->datepicker_enabled)
		{
			Requirements::customJS("
				Date.dayNames = [
				    '". Kohana::lang('datetime.sunday.full') ."',
				    '". Kohana::lang('datetime.monday.full') ."',
				    '". Kohana::lang('datetime.tuesday.full') ."',
				    '". Kohana::lang('datetime.wednesday.full') ."',
				    '". Kohana::lang('datetime.thursday.full') ."',
				    '". Kohana::lang('datetime.friday.full') ."',
				    '". Kohana::lang('datetime.saturday.full') ."'
				];
				Date.abbrDayNames = [
				    '". Kohana::lang('datetime.sunday.abbv') ."',
				    '". Kohana::lang('datetime.monday.abbv') ."',
				    '". Kohana::lang('datetime.tuesday.abbv') ."',
				    '". Kohana::lang('datetime.wednesday.abbv') ."',
				    '". Kohana::lang('datetime.thursday.abbv') ."',
				    '". Kohana::lang('datetime.friday.abbv') ."',
				    '". Kohana::lang('datetime.saturday.abbv') ."'
				];
				Date.monthNames = [
				    '". Kohana::lang('datetime.january.full') ."',
				    '". Kohana::lang('datetime.february.full') ."',
				    '". Kohana::lang('datetime.march.full') ."',
				    '". Kohana::lang('datetime.april.full') ."',
				    '". Kohana::lang('datetime.may.full') ."',
				    '". Kohana::lang('datetime.june.full') ."',
				    '". Kohana::lang('datetime.july.full') ."',
				    '". Kohana::lang('datetime.august.full') ."',
				    '". Kohana::lang('datetime.september.full') ."',
				    '". Kohana::lang('datetime.october.full') ."',
				    '". Kohana::lang('datetime.november.full') ."',
				    '". Kohana::lang('datetime.december.full') ."'
				];
				Date.abbrMonthNames = [
				    '". Kohana::lang('datetime.january.abbv') ."',
				    '". Kohana::lang('datetime.february.abbv') ."',
				    '". Kohana::lang('datetime.march.abbv') ."',
				    '". Kohana::lang('datetime.april.abbv') ."',
				    '". Kohana::lang('datetime.may.abbv') ."',
				    '". Kohana::lang('datetime.june.abbv') ."',
				    '". Kohana::lang('datetime.july.abbv') ."',
				    '". Kohana::lang('datetime.august.abbv') ."',
				    '". Kohana::lang('datetime.september.abbv') ."',
				    '". Kohana::lang('datetime.october.abbv') ."',
				    '". Kohana::lang('datetime.november.abbv') ."',
				    '". Kohana::lang('datetime.december.abbv') ."'
				];
				Date.firstDayOfWeek = 1;
				Date.format = 'mm/dd/yyyy';
			",'locale-dates');
	
			Requirements::js('media/js/jquery.datePicker.js');
			Requirements::customHeadTags(
				'<!--[if IE]>'.html::script(url::file_loc('js').'media/js/jquery.bgiframe.min', TRUE).'<![endif]-->','jquery.bgiframe.min');
		}
		
		// JS base combines
		$base_js = array(
			'media/js/jquery.js',
			'media/js/jquery.ui.min.js',
			'media/js/jquery.pngFix.pack.js',
			'media/js/jquery.timeago.js',
			'media/js/global.js',
			//'media/js/OpenLayers.js',
			//'media/js/ushahidi.js',
			'media/js/jquery.treeview.js',
			'media/js/selectToUISlider.jQuery.js',
			'media/js/jquery.validate.min.js',
			'media/js/colorpicker.js',
		);
		if ($this->timeline_enabled)
		{
			$base_js[] = 'media/js/jquery.jqplot.min.js';
			$base_js[] = 'media/js/jqplot.dateAxisRenderer.min.js';
			$base_js[] = 'media/js/jqplot.barRenderer.min.js';  // HT: added for bar graph
			$base_js[] = 'media/js/jqplot.pointLabels.min.js';  // HT: added for showing point label
		}
		Requirements::combine_files('0_base.js', $base_js);
		
		// CSS base combines
		$base_css = array(
			'media/css/jquery-ui-themeroller.css',
			'media/css/global.css',
			'media/css/openlayers.css',
			'media/css/jquery.treeview.css',
			'media/css/colorpicker.css',
		);
		if ($this->timeline_enabled)
		{
			$base_css[] = 'media/css/jquery.jqplot.min.css';
		}
		Requirements::combine_files('0_base.css', $base_css);
		
		
		Event::run('ushahidi_action.themes_add_requirements_pre_theme', $this);
		
		if ($this->admin)
		{
			$this->admin_requirements();
		}
		
		if ($this->frontend)
		{
			$this->frontend_requirements();
		}
		
		// Inline Javascript
		if (!empty($this->js))
		{
			//@todo add deprecated warning
			Requirements::customJS($this->js,'pagejs');
		}
		
		Event::run('ushahidi_action.themes_add_requirements', $this);
	}

	public function admin_requirements()
	{
		Requirements::js('media/js/jquery.form.js');
		Requirements::js('media/js/jquery.validate.min.js');
		Requirements::js('media/js/jquery.base64.js');
		Requirements::js('media/js/admin.js');
		Requirements::css('media/css/admin.css');
		Requirements::ieCSS("lt IE 7", 'media/css/ie6.css');
		
		// JS admin combies
		Requirements::combine_files('1_admin.js', array(
			'media/js/jquery.form.js',
			'media/js/jquery.base64.js',
			'media/js/admin.js',
			'media/js/jquery.hovertip-1.0.js',
		));
		
		// CSS admin combines
		Requirements::combine_files('1_admin.css', array(
			'media/css/jquery.hovertip-1.0.css',
			'media/css/admin.css'
		));
	}
	
	public function frontend_requirements()
	{
		// Add RSS feed if enabled
		if (Kohana::config("settings.allow_feed"))
		{
			Requirements::customHeadTags("<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".url::site('feed')."\" title=\"RSS2\" />",'rss-feed');
		}
		
		// Theme CSS
		foreach(self::$theme_css as $css)
		{
			Requirements::css($css);
		}
		
		Requirements::combine_files("theme_".Kohana::config("settings.site_style").".css", self::$theme_css);
		
		// Theme JS
		foreach(self::$theme_js as $js)
		{
			Requirements::js($js);
		}
		
		Requirements::combine_files("theme_".Kohana::config("settings.site_style").".js", self::$theme_js);
		
		Requirements::ieThemedCSS("lte IE 7", "iehacks.css");
		Requirements::ieThemedCSS("IE 7", "ie7hacks.css");
		Requirements::ieThemedCSS("IE 6", "ie6hacks.css");
	}

	/**
	 *  Add plugin css and js
	 */
	public function plugin_requirements()
	{
		foreach (plugin::get_requirements('javascript') as $js)
		{
			Requirements::js($js);
		}
		foreach (plugin::get_requirements('stylesheet') as $css)
		{
			Requirements::css($css);
		}
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
		
		if (Kohana::config('requirements.write_js_to_body'))
		{
			$content .= Requirements::render('js');
		}

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
			$schedulerPath = url::site('scheduler');
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
	
	/* Static functions for theme loading */
	
	private static $themes = array();
	private static $loaded_themes = array();
	private static $theme_js = array();
	private static $theme_css = array();
	

	/**
	 * Loads ushahidi themes
	 */
	public static function register_theme()
	{
		self::$themes = addon::get_addons('theme', TRUE);
		
		$theme = Kohana::config("settings.site_style");
		$theme = empty($theme) ? 'default' : $theme;
		self::load_theme($theme);
}
	
	/**
	 * Load theme
	 * Loads theme into modules, includes its hooks and recursively loads parent themes
	 * @param string $theme theme name/directory
	 **/
	public static function load_theme($theme)
	{
		// Record loading this theme, so we can avoid dependency loops
		self::$loaded_themes[] = $theme;
		
		// Get meta data to check the base theme
		$meta = self::$themes[$theme];
		
		// If base theme is set, the base theme exists, and we haven't loaded it yet
		// Load the base theme
		if (! empty($meta['Base Theme'])
				AND isset(self::$themes[$meta['Base Theme']])
				AND ! in_array($meta['Base Theme'], self::$loaded_themes)
			)
		{
			self::load_theme($meta['Base Theme']);
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
		
		self::load_theme_css($theme);
		self::load_theme_js($theme);
	}

	/*
	 * Find theme css and store for inclusion later
	 */
	private static function load_theme_css($theme)
	{
		$meta = self::$themes[$theme];
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
				self::$theme_css[$css] = "themes/$theme/css/$css.css";
		}
		
		// Check for overrides of already added stylesheets
		foreach (self::$theme_css as $css => $path)
		{
			if (file_exists(THEMEPATH."$theme/css/$css.css"))
				self::$theme_css[$css] = "themes/$theme/css/$css.css";
		}
	}

	/*
	 * Find theme css and store for inclusion later
	 */
	private static function load_theme_js($theme)
	{
		$meta = self::$themes[$theme];
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
				self::$theme_js[$js] = "themes/$theme/js/$js.js";
		}
		
		// Check for overrides of already added js
		foreach (self::$theme_css as $js => $path)
		{
			if (file_exists(THEMEPATH."$theme/js/$js.js"))
				self::$theme_js[$js] = "themes/$theme/js/$js.js";
		}
	}
	
	public static function loaded_themes()
	{
		return self::$loaded_themes;
	}
}
