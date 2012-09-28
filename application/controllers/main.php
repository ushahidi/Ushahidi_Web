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

	public function __construct()
	{
		parent::__construct();

		// Load Session
		$this->session = Session::instance();

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
		$this->template->header->header_block = $this->themes->header_block();
		$this->template->footer->footer_block = $this->themes->footer_block();

		// Set Table Prefix
		$this->table_prefix = Kohana::config('database.default.table_prefix');

		// Retrieve Default Settings
		$site_name = Kohana::config('settings.site_name');

		// Get banner image and pass to the header
		if (Kohana::config('settings.site_banner_id') != NULL)
		{
			$banner = ORM::factory('media')->find(Kohana::config('settings.site_banner_id'));
			$this->template->header->banner = url::convert_uploaded_to_abs($banner->media_link);
		}
		else
		{
			$this->template->header->banner = NULL;
		}

		// Prevent Site Name From Breaking up if its too long
		// by reducing the size of the font
		$site_name_style = (strlen($site_name) > 20) ? " style=\"font-size:21px;\"" : "";

		$this->template->header->private_deployment = Kohana::config('settings.private_deployment');

		$this->template->header->site_name = $site_name;
		$this->template->header->site_name_style = $site_name_style;
		$this->template->header->site_tagline = Kohana::config('settings.site_tagline');

		// page_title is a special variable that will be overridden by other controllers to
		//    change the title bar contents
		$this->template->header->page_title = '';

		//pass the URI to the header so we can dynamically add css classes to the "body" tag
		$this->template->header->uri_segments = Router::$segments;

		$this->template->header->this_page = "";

		// add copyright info
		$this->template->footer->site_copyright_statement = '';
		$site_copyright_statement = trim(Kohana::config('settings.site_copyright_statement'));
		if ($site_copyright_statement != '')
		{
			$this->template->footer->site_copyright_statement = $site_copyright_statement;
		}

		// Display news feeds?
		$this->template->header->allow_feed = Kohana::config('settings.allow_feed');

		// Header Nav
		$header_nav = new View('header_nav');
		$this->template->header->header_nav = $header_nav;
		$this->template->header->header_nav->loggedin_user = FALSE;
		if ( isset(Auth::instance()->get_user()->id) )
		{
			// Load User
			$this->template->header->header_nav->loggedin_role = Auth::instance()->get_user()->dashboard();
			$this->template->header->header_nav->loggedin_user = Auth::instance()->get_user();
		}
		$this->template->header->header_nav->site_name = Kohana::config('settings.site_name');
	}

	/**
	 * Get Trusted Category Count
	 */
	public function get_trusted_category_count($id)
	{
		$trusted = ORM::factory("incident")
						->join("incident_category","incident.id","incident_category.incident_id")
						->where("category_id",$id);
		return $trusted;
	}

    public function index()
    {
        $this->template->header->this_page = 'home';
        $this->template->content = new View('main/layout');

		// Cacheable Main Controller
		$this->is_cachable = TRUE;

		// Map and Slider Blocks
		$div_map = new View('main/map');
		$div_timeline = new View('main/timeline');

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
			// Send the site message to both the header and the main content body
			//   so a theme can utilize it in either spot.
			$this->template->content->site_message = $site_message;
			$this->template->header->site_message = $site_message;
		}

		// Get locale
		$l = Kohana::config('locale.language.0');

        // Get all active top level categories
		$parent_categories = array();
		$all_parents = ORM::factory('category')
		    ->where('category_visible', '1')
		    ->where('parent_id', '0')
		    ->find_all();

		foreach ($all_parents as $category)
		{
			// Get The Children
			$children = array();
			foreach ($category->children as $child)
			{
				$child_visible = $child->category_visible;
				if ($child_visible)
				{
					// Check for localization of child category
					$display_title = Category_Lang_Model::category_title($child->id,$l);

					$ca_img = ($child->category_image != NULL)
					    ? url::convert_uploaded_to_abs($child->category_image)
					    : NULL;
					
					$children[$child->id] = array(
						$display_title,
						$child->category_color,
						$ca_img
					);
				}
			}

			// Check for localization of parent category
			$display_title = Category_Lang_Model::category_title($category->id,$l);

			// Put it all together
			$ca_img = ($category->category_image != NULL)
			    ? url::convert_uploaded_to_abs($category->category_image)
			    : NULL;

			$parent_categories[$category->id] = array(
				$display_title,
				$category->category_color,
				$ca_img,
				$children
			);
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

		// Get Default Color
		$this->template->content->default_map_all = Kohana::config('settings.default_map_all');

		// Get default icon
		$this->template->content->default_map_all_icon = '';
		if (Kohana::config('settings.default_map_all_icon_id'))
		{
			$icon_object = ORM::factory('media')->find(Kohana::config('settings.default_map_all_icon_id'));
			$this->template->content->default_map_all_icon = Kohana::config('upload.relative_directory')."/".$icon_object->media_medium;
		}

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

		// Get external apps
		$external_apps = array();
		// Catch errors, in case we have an old db
		try {
			$external_apps = ORM::factory('externalapp')->find_all();
		}
		catch(Exception $e) {}
		$this->template->content->external_apps = $external_apps;

        // Get The START, END and Incident Dates
        $startDate = "";
		$endDate = "";
		$display_startDate = 0;
		$display_endDate = 0;

		$db = new Database();
		
        // Next, Get the Range of Years
		$query = $db->query('SELECT DATE_FORMAT(incident_date, \'%Y-%c\') AS dates '
		    . 'FROM '.$this->table_prefix.'incident '
		    . 'WHERE incident_active = 1 '
		    . 'GROUP BY DATE_FORMAT(incident_date, \'%Y-%c\') '
		    . 'ORDER BY incident_date');

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
			if ($i == 0)
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

		while ($show_year <= $last_year)
		{
			$startDate .= "<optgroup label=\"".$show_year."\">";

			$s_m = 1;
			if ($show_year == $first_year)
			{
				// If we are showing the first year, the starting month may not be January
				$s_m = $first_month;
			}

			$l_m = 12;
			if ($show_year == $last_year)
			{
				// If we are showing the last year, the ending month may not be December
				$l_m = $last_month;
			}

			for ( $i=$s_m; $i <= $l_m; $i++ )
			{
				if ($i < 10 )
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
		$marker_radius = Kohana::config('map.marker_radius');
		$marker_opacity = Kohana::config('map.marker_opacity');
		$marker_stroke_width = Kohana::config('map.marker_stroke_width');
		$marker_stroke_opacity = Kohana::config('map.marker_stroke_opacity');

		$this->themes->js = new View('main/main_js');

		$this->themes->js->marker_radius = ($marker_radius >=1 AND $marker_radius <= 10 )
		    ? $marker_radius
		    : 5;

		$this->themes->js->marker_opacity = ($marker_opacity >=1 AND $marker_opacity <= 10 )
		    ? $marker_opacity * 0.1
		    : 0.9;

		$this->themes->js->marker_stroke_width = ($marker_stroke_width >=1 AND $marker_stroke_width <= 5)
		    ? $marker_stroke_width
		    : 2;

		$this->themes->js->marker_stroke_opacity = ($marker_stroke_opacity >=1 AND $marker_stroke_opacity <= 10)
		    ? $marker_stroke_opacity * 0.1
		    : 0.9;


		$this->themes->js->active_startDate = $display_startDate;
		$this->themes->js->active_endDate = $display_endDate;

		$this->themes->js->blocks_per_row = Kohana::config('settings.blocks_per_row');

		// Build Header and Footer Blocks
		$this->template->header->header_block = $this->themes->header_block();
		$this->template->footer->footer_block = $this->themes->footer_block();
	}

} // End Main
