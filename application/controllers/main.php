<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This is the controller for the main site.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Main Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Main_Controller extends Template_Controller {

	public $auto_render = TRUE;

    // Main template
	public $template = 'layout';

    // Cache instance
	protected $cache;

	// Cacheable Controller
	public $is_cachable = FALSE;

	// Session instance
	protected $session;

	// Table Prefix
	protected $table_prefix;

	// Themes Helper
	protected $themes;

	public function __construct()
	{
		parent::__construct();

        // Load cache
		$this->cache = new Cache;

		// Load Session
		$this->session = Session::instance();

        // Load Header & Footer
		$this->template->header  = new View('header');
		$this->template->footer  = new View('footer');

		// Themes Helper
		$this->themes = new Themes();
		$this->themes->api_url = Kohana::config('settings.api_url');
		$this->template->header->submit_btn = $this->themes->submit_btn();
		$this->template->header->languages = $this->themes->languages();
		$this->template->header->search = $this->themes->search();

		// Set Table Prefix
		$this->table_prefix = Kohana::config('database.default.table_prefix');

		// Retrieve Default Settings
		$site_name = Kohana::config('settings.site_name');
			// Prevent Site Name From Breaking up if its too long
			// by reducing the size of the font
			if (strlen($site_name) > 20)
			{
				$site_name_style = " style=\"font-size:21px;\"";
			}
			else
			{
				$site_name_style = "";
			}
		$this->template->header->site_name = $site_name;
		$this->template->header->site_name_style = $site_name_style;
		$this->template->header->site_tagline = Kohana::config('settings.site_tagline');

		$this->template->header->this_page = "";

		// Google Analytics
		$google_analytics = Kohana::config('settings.google_analytics');
		$this->template->footer->google_analytics = $this->themes->google_analytics($google_analytics);

        // Load profiler
        // $profiler = new Profiler;

		// Get tracking javascript for stats
		if(Kohana::config('settings.allow_stat_sharing') == 1){
			$this->template->footer->ushahidi_stats = Stats_Model::get_javascript();
		}else{
			$this->template->footer->ushahidi_stats = '';
		}
		
		// add copyright info
		$this->template->footer->site_copyright_statement = '';
		$site_copyright_statement = trim(Kohana::config('settings.site_copyright_statement'));
		if($site_copyright_statement != '')
		{
			$this->template->footer->site_copyright_statement = $site_copyright_statement;
		}
		
	}

    public function index()
    {
        $this->template->header->this_page = 'home';
        $this->template->content = new View('main');

		// Cacheable Main Controller
		$this->is_cachable = TRUE;

		// Map and Slider Blocks
		$div_map = new View('main_map');
		$div_timeline = new View('main_timeline');
			// Filter::map_main - Modify Main Map Block
			Event::run('ushahidi_filter.map_main', $div_map);
			// Filter::map_timeline - Modify Main Map Block
			Event::run('ushahidi_filter.map_timeline', $div_timeline);
		$this->template->content->div_map = $div_map;
		$this->template->content->div_timeline = $div_timeline;

		// Check if there is a site message
		$this->template->content->site_message = '';
		$site_message = trim(Kohana::config('settings.site_message'));
		if($site_message != '')
		{
			$this->template->content->site_message = $site_message;
		}

		// Get locale
		$l = Kohana::config('locale.language.0');

        // Get all active top level categories
		$parent_categories = array();
		foreach (ORM::factory('category')
				->where('category_visible', '1')
				->where('parent_id', '0')
				->find_all() as $category)
		{
			// Get The Children
			$children = array();
			foreach ($category->children as $child)
			{
				// Check for localization of child category

				$translated_title = Category_Lang_Model::category_title($child->id,$l);

				if($translated_title)
				{
					$display_title = $translated_title;
				}
				else
				{
					$display_title = $child->category_title;
				}

				$children[$child->id] = array(
					$display_title,
					$child->category_color,
					$child->category_image
				);

				if ($child->category_trusted)
				{ // Get Trusted Category Count
					$trusted = ORM::factory("incident")
						->join("incident_category","incident.id","incident_category.incident_id")
						->where("category_id",$child->id);
					if ( ! $trusted->count_all())
					{
						unset($children[$child->id]);
					}
				}
			}

			// Check for localization of parent category

			$translated_title = Category_Lang_Model::category_title($category->id,$l);

			if($translated_title)
			{
				$display_title = $translated_title;
			}else{
				$display_title = $category->category_title;
			}

			// Put it all together
			$parent_categories[$category->id] = array(
				$display_title,
				$category->category_color,
				$category->category_image,
				$children
			);

			if ($category->category_trusted)
			{ // Get Trusted Category Count
				$trusted = ORM::factory("incident")
					->join("incident_category","incident.id","incident_category.incident_id")
					->where("category_id",$category->id);
				if ( ! $trusted->count_all())
				{
					unset($parent_categories[$category->id]);
				}
			}
		}
		$this->template->content->categories = $parent_categories;

		// Get all active Layers (KMZ/KML)
		$layers = array();
		$config_layers = Kohana::config('map.layers'); // use config/map layers if set
		if ($config_layers == $layers) {
			foreach (ORM::factory('layer')
					  ->where('layer_visible', 1)
					  ->find_all() as $layer)
			{
				$layers[$layer->id] = array($layer->layer_name, $layer->layer_color,
					$layer->layer_url, $layer->layer_file);
			}
		} else {
			$layers = $config_layers;
		}
		$this->template->content->layers = $layers;

		// Get all active Shares
		$shares = array();
		foreach (ORM::factory('sharing')
				  ->where('sharing_active', 1)
				  ->find_all() as $share)
		{
			$shares[$share->id] = array($share->sharing_name, $share->sharing_color);
		}
		$this->template->content->shares = $shares;

        // Get Reports
        // XXX: Might need to replace magic no. 8 with a constant
		$this->template->content->total_items = ORM::factory('incident')
			->where('incident_active', '1')
			->limit('8')->count_all();
		$this->template->content->incidents = ORM::factory('incident')
			->where('incident_active', '1')
			->limit('10')
			->orderby('incident_date', 'desc')
			->find_all();

		// Get Default Color
		$this->template->content->default_map_all = Kohana::config('settings.default_map_all');

		// Get Twitter Hashtags
		$this->template->content->twitter_hashtag_array = array_filter(array_map('trim',
			explode(',', Kohana::config('settings.twitter_hashtags'))));

		// Get Report-To-Email
		$this->template->content->report_email = Kohana::config('settings.site_email');

		// Get SMS Numbers
		$phone_array = array();
		$sms_no1 = Kohana::config('settings.sms_no1');
		$sms_no2 = Kohana::config('settings.sms_no2');
		$sms_no3 = Kohana::config('settings.sms_no3');
		if (!empty($sms_no1)) {
			$phone_array[] = $sms_no1;
		}
		if (!empty($sms_no2)) {
			$phone_array[] = $sms_no2;
		}
		if (!empty($sms_no3)) {
			$phone_array[] = $sms_no3;
		}
		$this->template->content->phone_array = $phone_array;

		// Get RSS News Feeds
		$this->template->content->feeds = ORM::factory('feed_item')
			->limit('10')
			->orderby('item_date', 'desc')
			->find_all();

        // Get The START, END and most ACTIVE Incident Dates
		$startDate = "";
		$endDate = "";
		$active_month = 0;
		$active_startDate = 0;
		$active_endDate = 0;

		$db = new Database();
		// First Get The Most Active Month
		$query = $db->query('SELECT incident_date, count(*) AS incident_count FROM '.$this->table_prefix.'incident WHERE incident_active = 1 GROUP BY DATE_FORMAT(incident_date, \'%Y-%m\') ORDER BY incident_count DESC LIMIT 1');
		foreach ($query as $query_active)
		{
			$active_month = date('n', strtotime($query_active->incident_date));
			$active_year = date('Y', strtotime($query_active->incident_date));
			$active_startDate = strtotime($active_year . "-" . $active_month . "-01");
			$active_endDate = strtotime($active_year . "-" . $active_month .
				"-" . date('t', mktime(0,0,0,$active_month,1))." 23:59:59");
		}

        // Next, Get the Range of Years
		$query = $db->query('SELECT DATE_FORMAT(incident_date, \'%Y\') AS incident_date FROM '.$this->table_prefix.'incident WHERE incident_active = 1 GROUP BY DATE_FORMAT(incident_date, \'%Y\') ORDER BY incident_date');
		foreach ($query as $slider_date)
		{
			$years = $slider_date->incident_date;
			$startDate .= "<optgroup label=\"" . $years . "\">";
			for ( $i=1; $i <= 12; $i++ ) {
				if ( $i < 10 )
				{
					$i = "0" . $i;
				}
				$startDate .= "<option value=\"" . strtotime($years . "-" . $i . "-01") . "\"";
				if ( $active_month &&
						( (int) $i == ( $active_month - 1)) )
				{
					$startDate .= " selected=\"selected\" ";
				}
				$startDate .= ">" . date('M', mktime(0,0,0,$i,1)) . " " . $years . "</option>";
			}
			$startDate .= "</optgroup>";

			$endDate .= "<optgroup label=\"" . $years . "\">";
			for ( $i=1; $i <= 12; $i++ )
			{
				if ( $i < 10 )
				{
					$i = "0" . $i;
				}
				$endDate .= "<option value=\"" . strtotime($years . "-" . $i . "-" . date('t', mktime(0,0,0,$i,1))." 23:59:59") . "\"";
                // Focus on the most active month or set December as month of endDate
				if ( $active_month &&
						( ( (int) $i == ( $active_month + 1)) )
						 	|| ($i == 12 && preg_match('/selected/', $endDate) == 0))
				{
					$endDate .= " selected=\"selected\" ";
				}
				$endDate .= ">" . date('M', mktime(0,0,0,$i,1)) . " " . $years . "</option>";
			}
			$endDate .= "</optgroup>";
		}

		$this->template->content->div_timeline->startDate = $startDate;
		$this->template->content->div_timeline->endDate = $endDate;

		// Javascript Header
		$this->themes->map_enabled = TRUE;
		$this->themes->main_page = TRUE;

		// Map Settings
		$clustering = Kohana::config('settings.allow_clustering');
		$marker_radius = Kohana::config('map.marker_radius');
		$marker_opacity = Kohana::config('map.marker_opacity');
		$marker_stroke_width = Kohana::config('map.marker_stroke_width');
		$marker_stroke_opacity = Kohana::config('map.marker_stroke_opacity');

        // pdestefanis - allows to restrict the number of zoomlevels available
		$numZoomLevels = Kohana::config('map.numZoomLevels');
		$minZoomLevel = Kohana::config('map.minZoomLevel');
	   	$maxZoomLevel = Kohana::config('map.maxZoomLevel');

		// pdestefanis - allows to limit the extents of the map
		$lonFrom = Kohana::config('map.lonFrom');
		$latFrom = Kohana::config('map.latFrom');
		$lonTo = Kohana::config('map.lonTo');
		$latTo = Kohana::config('map.latTo');

		$this->themes->js = new View('main_js');
		$this->themes->js->json_url = ($clustering == 1) ?
			"json/cluster" : "json";
		$this->themes->js->marker_radius =
			($marker_radius >=1 && $marker_radius <= 10 ) ? $marker_radius : 5;
		$this->themes->js->marker_opacity =
			($marker_opacity >=1 && $marker_opacity <= 10 )
			? $marker_opacity * 0.1  : 0.9;
		$this->themes->js->marker_stroke_width =
			($marker_stroke_width >=1 && $marker_stroke_width <= 5 ) ? $marker_stroke_width : 2;
		$this->themes->js->marker_stroke_opacity =
			($marker_stroke_opacity >=1 && $marker_stroke_opacity <= 10 )
			? $marker_stroke_opacity * 0.1  : 0.9;

		// pdestefanis - allows to restrict the number of zoomlevels available
		$this->themes->js->numZoomLevels = $numZoomLevels;
		$this->themes->js->minZoomLevel = $minZoomLevel;
		$this->themes->js->maxZoomLevel = $maxZoomLevel;

		// pdestefanis - allows to limit the extents of the map
		$this->themes->js->lonFrom = $lonFrom;
		$this->themes->js->latFrom = $latFrom;
		$this->themes->js->lonTo = $lonTo;
		$this->themes->js->latTo = $latTo;

		$this->themes->js->default_map = Kohana::config('settings.default_map');
		$this->themes->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->themes->js->latitude = Kohana::config('settings.default_lat');
		$this->themes->js->longitude = Kohana::config('settings.default_lon');
		$this->themes->js->default_map_all = Kohana::config('settings.default_map_all');
		//
		$this->themes->js->active_startDate = $active_startDate;
		$this->themes->js->active_endDate = $active_endDate;

		//$myPacker = new javascriptpacker($js , 'Normal', false, false);
		//$js = $myPacker->pack();

		// Rebuild Header Block
		$this->template->header->header_block = $this->themes->header_block();
	}

} // End Main
