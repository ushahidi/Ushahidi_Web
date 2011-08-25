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
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Main_Controller extends Template_Controller {
	/**
	 * Automatically render the views loaded in this controller
	 * @var bool
	 */
	public $auto_render = TRUE;
	
	/**
	 * Name of the template view
	 * @var string
	 */
	public $template = 'layout';
	
	/**
	 * Cache object - to be used for caching content
	 * @var Cache
	 */
	protected $cache;
	
	/**
	 * Whether the current controller is cacheable - defaults to FALSE
	 * @var bool
	 */
	public $is_cachable = FALSE;
	
	/**
	 * Session object
	 * @var Session
	 */
	protected $session;
	
	/**
	 * Prefix for the database tables
	 * @var string
	 */
	protected $table_prefix;
	
	/**
	 * Themes helper library object
	 * @var Themes
	 */
	protected $themes;
	
	// User Object
	protected $user;

	public function __construct()
	{
		parent::__construct();
		
		$this->auth = new Auth();
		$this->auth->auto_login();
		
		// Load Session
		$this->session = Session::instance();
		
		if(Kohana::config('settings.private_deployment'))
		{
			if ( ! $this->auth->logged_in('login'))
			{
				url::redirect('login/front');
			}
		}
		
        // Load cache
		$this->cache = new Cache;

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
		
		// Get banner image and pass to the header
		if(Kohana::config('settings.site_banner_id') != NULL){
			$banner = ORM::factory('media')->find(Kohana::config('settings.site_banner_id'));
			$this->template->header->banner = $banner->media_link;
		}else{
			$this->template->header->banner = NULL;
		}
		
		// Prevent Site Name From Breaking up if its too long
		// by reducing the size of the font
		$site_name_style = (strlen($site_name) > 20) ? " style=\"font-size:21px;\"" : "";
			
		$this->template->header->private_deployment = Kohana::config('settings.private_deployment');
		$this->template->header->loggedin_username = FALSE;
		$this->template->header->loggedin_userid = FALSE;
		
		if ( isset(Auth::instance()->get_user()->username) AND isset(Auth::instance()->get_user()->id) )
		{
			// Load User
			$this->user = Auth::instance()->get_user();
			$this->template->header->loggedin_username = html::specialchars(Auth::instance()->get_user()->username);
			$this->template->header->loggedin_userid = Auth::instance()->get_user()->id;
			$this->template->header->loggedin_role = ( Auth::instance()->logged_in('member') ) ? "members" : "admin";
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
		$this->template->footer->ushahidi_stats = (Kohana::config('settings.allow_stat_sharing') == 1)
			? Stats_Model::get_javascript()
			: '';
		
		// add copyright info
		$this->template->footer->site_copyright_statement = '';
		$site_copyright_statement = trim(Kohana::config('settings.site_copyright_statement'));
		if($site_copyright_statement != '')
		{
			$this->template->footer->site_copyright_statement = $site_copyright_statement;
		}
		
	}

	/**
	 * Retrieves Categories
	 */
	protected function get_categories($selected_categories)
	{
	  $categories = ORM::factory('category')
	    ->where('category_visible', '1')
	    ->where('parent_id', '0')
	    ->where('category_trusted != 1')
	    ->orderby('category_title', 'ASC')
	    ->find_all();

	  return $categories;
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
				->orderby('category_position', 'asc')
				->find_all() as $category)
		{
			// Get The Children
			$children = array();
			foreach ($category->orderby('category_position', 'asc')->children as $child)
			{
				// Check for localization of child category

				$translated_title = Category_Lang_Model::category_title($child->id,$l);

				$display_title = ($translated_title)? $translated_title : $child->category_title;

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

			$display_title  = ($translated_title)? $translated_title : $category->category_title;

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
		}
		else
		{
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
		if ( ! empty($sms_no1))
		{
			$phone_array[] = $sms_no1;
		}
		if ( ! empty($sms_no2))
		{
			$phone_array[] = $sms_no2;
		}
		if ( ! empty($sms_no3))
		{
			$phone_array[] = $sms_no3;
		}
		$this->template->content->phone_array = $phone_array;

        // Get The START, END and Incident Dates
        $startDate = "";
		$endDate = "";
		$display_startDate = 0;
		$display_endDate = 0;

		$db = new Database();
        // Next, Get the Range of Years
		$query = $db->query('SELECT DATE_FORMAT(incident_date, \'%Y-%c\') AS dates FROM '.$this->table_prefix.'incident WHERE incident_active = 1 GROUP BY DATE_FORMAT(incident_date, \'%Y-%c\') ORDER BY incident_date');

		$first_year = date('Y');
		$last_year = date('Y');
		$first_month = 1;
		$last_month = 12;
		$i = 0;

		foreach ($query as $data)
		{
			$date = explode('-',$data->dates);

			$year = $date[0];
			$month = $date[1];

			// Set first year
			if($i == 0)
			{
				$first_year = $year;
				$first_month = $month;
			}

			// Set last dates
			$last_year = $year;
			$last_month = $month;

			$i++;
		}

		$show_year = $first_year;
		$selected_start_flag = TRUE;
		while($show_year <= $last_year)
		{
			$startDate .= "<optgroup label=\"".$show_year."\">";

			$s_m = 1;
			if($show_year == $first_year)
			{
				// If we are showing the first year, the starting month may not be January
				$s_m = $first_month;
			}

			$l_m = 12;
			if($show_year == $last_year)
			{
				// If we are showing the last year, the ending month may not be December
				$l_m = $last_month;
			}

			for ( $i=$s_m; $i <= $l_m; $i++ )
			{
				if ( $i < 10 )
				{
					// All months need to be two digits
					$i = "0".$i;
				}
				$startDate .= "<option value=\"".strtotime($show_year."-".$i."-01")."\"";
				if($selected_start_flag == TRUE)
				{
					$display_startDate = strtotime($show_year."-".$i."-01");
					$startDate .= " selected=\"selected\" ";
					$selected_start_flag = FALSE;
				}
				$startDate .= ">".date('M', mktime(0,0,0,$i,1))." ".$show_year."</option>";
			}
			$startDate .= "</optgroup>";

			$endDate .= "<optgroup label=\"".$show_year."\">";
			for ( $i=$s_m; $i <= $l_m; $i++ )
			{
				if ( $i < 10 )
				{
					// All months need to be two digits
					$i = "0".$i;
				}
				$endDate .= "<option value=\"".strtotime($show_year."-".$i."-".date('t', mktime(0,0,0,$i,1))." 23:59:59")."\"";

                if($i == $l_m AND $show_year == $last_year)
				{
					$display_endDate = strtotime($show_year."-".$i."-".date('t', mktime(0,0,0,$i,1))." 23:59:59");
					$endDate .= " selected=\"selected\" ";
				}
				$endDate .= ">".date('M', mktime(0,0,0,$i,1))." ".$show_year."</option>";
			}
			$endDate .= "</optgroup>";

			// Show next year
			$show_year++;
		}

		Event::run('ushahidi_filter.active_startDate', $display_startDate);
		Event::run('ushahidi_filter.active_endDate', $display_endDate);
		Event::run('ushahidi_filter.startDate', $startDate);
		Event::run('ushahidi_filter.endDate', $endDate);
		
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

		$this->themes->js->active_startDate = $display_startDate;
		$this->themes->js->active_endDate = $display_endDate;
		
		$this->themes->js->blocks_per_row = Kohana::config('settings.blocks_per_row');

		//$myPacker = new javascriptpacker($js , 'Normal', false, false);
		//$js = $myPacker->pack();

		// Rebuild Header Block
		$this->template->header->header_block = $this->themes->header_block();
	}

} // End Main
