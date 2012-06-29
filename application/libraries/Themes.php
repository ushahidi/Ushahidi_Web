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
	public $colorpicker_enabled = false;
	public $datepicker_enabled = false;
	public $editor_enabled = false;
	public $flot_enabled = false;
	public $protochart_enabled = false;
	public $raphael_enabled = false;
	public $tablerowsort_enabled = false;
	public $json2_enabled = false;
	
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
		Requirements::customHeadTags(Kohana::config("globalcode.head"),'globalcode-head');
		
		// These just need to run now
		$this->_header_css();
		$this->_header_feeds();
		$this->_header_js();

		$content = Requirements::render('head');
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
		Requirements::customHeadTags(Kohana::config("globalcode.head"),'globalcode-head');

		$content = '';
		// Filter::admin_header_block - Modify Admin Header Block
		Event::run('ushahidi_filter.admin_header_block', $content);

		return $content;
	}

	/**
	 * Css Items
	 */
	private function _header_css()
	{
		Requirements::clear();
		Requirements::themedCSS("jquery-ui-themeroller");

		Requirements::ieThemedCSS("lte IE 7", "iehacks");
		Requirements::ieThemedCSS("IE 7", "ie7hacks");
		Requirements::ieThemedCSS("IE 6", "ie6hacks");

		if ($this->map_enabled)
		{
			Requirements::themedCSS("openlayers");
		}

		if ($this->treeview_enabled)
		{
			Requirements::css("media/css/jquery.treeview");
		}

		if ($this->photoslider_enabled)
		{
			Requirements::css("media/css/picbox/picbox");
		}

		if ($this->colorpicker_enabled)
		{
			Requirements::css("media/css/colorpicker");
		}

		if (Kohana::config('settings.enable_timeline'))
		{
			Requirements::css("media/css/jquery.jqplot.min");
		}

		Requirements::css("media/css/global");
		
		foreach(Kohana::config('settings.site_style_css') as $css)
		{
			Requirements::css($css);
		}
	}

	/**
	 * Javascript Files and Inline JS
	 */
	private function _header_js()
	{
		Requirements::set_write_js_to_body(FALSE);
		
		if ($this->map_enabled)
		{
			Requirements::js("media/js/OpenLayers");
			Requirements::customJS("OpenLayers.ImgPath = '".url::file_loc('js')."media/img/openlayers/"."';",'openlayers-imgpath');
			Requirements::js("media/js/ushahidi");
		}

		Requirements::js("media/js/jquery");
		//Requirements::js("media/js/jquery.ui.min");
		Requirements::js(Kohana::config('core.site_protocol')."://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js");
		Requirements::js("media/js/jquery.pngFix.pack");
		Requirements::js("media/js/jquery.timeago");

		if ($this->map_enabled)
		{
			Requirements::js($this->api_url);

			if ($this->main_page || $this->this_page == "alerts")
			{
				Requirements::js($this->js_url."media/js/selectToUISlider.jQuery");
			}

			if ($this->main_page)
			{
				if (Kohana::config('settings.enable_timeline'))
				{
				Requirements::js("media/js/jquery.jqplot.min");
				Requirements::js("media/js/jqplot.dateAxisRenderer.min");
				}

				Requirements::customHeadTags("<!--[if IE]>".html::script($this->js_url."media/js/excanvas.min", TRUE)."<![endif]-->");
			}
		}

		if ($this->treeview_enabled)
		{
			Requirements::js("media/js/jquery.treeview");
		}

		if ($this->validator_enabled)
		{
			Requirements::js("media/js/jquery.validate.min");
		}

		if ($this->photoslider_enabled)
		{
			Requirements::js("media/js/picbox");
		}

		if ($this->colorpicker_enabled)
		{
			Requirements::js("media/js/colorpicker");
		}

		Requirements::js("media/js/global");

		if ($this->editor_enabled)
		{
			Requirements::js("media/js/jwysiwyg/jwysiwyg/jquery.wysiwyg.js");
		}
		
		foreach(Kohana::config('settings.site_style_js') as $js)
		{
			Requirements::js($js);
		}

		// Inline Javascript
		Requirements::customJS($this->js,'pagejs');
	}

	/**
	 * RSS/Atom
	 */
	private function _header_feeds()
	{
		if (Kohana::config("settings.allow_feed"))
		{
			Requirements::customHeadTags("<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".url::site('feed')."\" title=\"RSS2\" />",'rss-feed');
		}
	}

	public function admin_requirements()
	{
		Requirements::clear();
		Requirements::set_write_js_to_body(FALSE);
		Requirements::css('media/css/admin/all');
		Requirements::css('media/css/jquery-ui-themeroller');
		Requirements::ieCSS("lt IE 7", 'media/css/admin/ie6');

		// Load OpenLayers
		if ($this->map_enabled)
		{
			Requirements::js('media/js/OpenLayers');
			Requirements::js('media/js/ushahidi');
			Requirements::js($this->api_url);
			Requirements::customJS("OpenLayers.ImgPath = '".url::file_loc('img').'media/img/openlayers/'."';",'openlayers-imgpath');
			Requirements::css('media/css/openlayers');
		}

		// Load jQuery
		Requirements::js('media/js/jquery');
		Requirements::js('media/js/jquery.form');
		Requirements::js('media/js/jquery.validate.min');
		Requirements::js('media/js/jquery.ui.min');
		Requirements::js('media/js/selectToUISlider.jQuery');
		Requirements::js('media/js/jquery.hovertip-1.0');
		Requirements::js('media/js/jquery.base64');
		Requirements::js("media/js/jquery.pngFix.pack");
		
		Requirements::js('media/js/admin');
	
		if ($this->datepicker_enabled) {
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
	
			Requirements::js('media/js/jquery.datePicker');
			Requirements::customHeadTags(
				'<!--[if IE]>'.html::script(url::file_loc('js').'media/js/jquery.bgiframe.min', TRUE).'<![endif]-->','jquery.bgiframe.min');
		}
	
		Requirements::css('media/css/jquery.hovertip-1.0', '');
	
		Requirements::customJS(
			"$(function() {
					if($('.tooltip[title]') != null)
					$('.tooltip[title]').hovertip();
				});",
			'tooltip-js'
		);
	
		// Load Flot
		if ($this->flot_enabled)
		{
			Requirements::js('media/js/jquery.flot');
			Requirements::js('media/js/excanvas.min');
			Requirements::js('media/js/timeline.js');
		}
	
		// Load TreeView
		if ($this->treeview_enabled)
		{
			Requirements::js('media/js/jquery.treeview');
			Requirements::css('media/css/jquery.treeview');
		}
	
		// Load ProtoChart
		if ($this->protochart_enabled)
		{
			Requirements::customJS("jQuery.noConflict()", 'jquery-noconflict');
			Requirements::js('media/js/protochart/prototype');
			Requirements::customHeadTags(
				'<!--[if IE]>'.html::script(url::file_loc('js').'media/js/protochart/excanvas-compressed', TRUE).'<![endif]-->');
			Requirements::js('media/js/protochart/ProtoChart');
		}
	
		// Load Raphael
		if ($this->raphael_enabled)
		{
			// The only reason we include prototype is to keep the div element naming convention consistent
			//Requirements::js('media/js/protochart/prototype');
			Requirements::js('media/js/raphael');
			Requirements::customJS('var impact_json = '.$this->impact_json .';','impact_json');
			Requirements::js('media/js/raphael-ushahidi-impact');
		}
	
		// Load ColorPicker
		if ($this->colorpicker_enabled)
		{
			Requirements::css('media/css/colorpicker');
			Requirements::js('media/js/colorpicker');
		}
	
		// Load jwysiwyg
		if ($this->editor_enabled)
		{
			if (Kohana::config("cdn.cdn_ignore_jwysiwyg") == TRUE) {
				Requirements::js(url::file_loc('ignore').'media/js/jwysiwyg/jwysiwyg/jquery.wysiwyg.js'); // not sure what the hell to do about this
			} else {
				Requirements::js('media/js/jwysiwyg/jwysiwyg/jquery.wysiwyg.js');
			}
		}
	
		// Table Row Sort
		if ($this->tablerowsort_enabled)
		{
			Requirements::js('media/js/jquery.tablednd_0_5');
		}
	
		// JSON2 for IE+
		if ($this->json2_enabled)
		{
			Requirements::js('media/js/json2');
		}
	
		// Turn on picbox
		Requirements::js('media/js/picbox');
		Requirements::css('media/css/picbox/picbox');
	
		//Turn on jwysiwyg
		Requirements::css('media/js/jwysiwyg/jwysiwyg/jquery.wysiwyg.css');
	
		// Header Nav
		Requirements::js('media/js/global');
		Requirements::css('media/css/global');
		
		
		// Inline Javascript
		Requirements::customJS($this->js,'pagejs');
		
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
		
		$content .= Requirements::render('body');

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
