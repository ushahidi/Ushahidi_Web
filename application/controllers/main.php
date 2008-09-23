<?php defined('SYSPATH') or die('No direct script access.');
/**
* MAIN SITE CONTROLLER
*/

class Main_Controller extends Template_Controller {

	public $auto_render = TRUE;
	
	// Main template
	public $template = 'layout';
	
	// Cache instance
	protected $cache;

	public function __construct()
	{
		parent::__construct();	

		// Load cache
		$this->cache = new Cache;

		// Load session
		$this->session = new Session;
		
		// Load Header & Footer
		$this->template->header  = new View('header');
		$this->template->footer  = new View('footer');
		
		// Retrieve Default Settings
		$this->template->header->site_name = Kohana::config('settings.site_name');
		$this->template->header->api_url = Kohana::config('settings.api_url');
		
		// Javascript Header
		$this->template->header->map_enabled = FALSE;
		$this->template->header->js = '';
		
		// Load profiler
		// $profiler = new Profiler;		
		
	}

	public function index()
	{		
		$this->template->header->this_page = 'home';
		$this->template->content = new View('main');
		
		// Get all active categories
		$categories = array();
		foreach (ORM::factory('category')->where('category_visible', '1')->find_all() as $category)
		{
			// Create a list of all categories
			$categories[$category->id] = array($category->category_title, $category->category_color);
		}
		$this->template->content->categories = $categories;
		
		
		// Get Reports
		$this->template->content->total_items = ORM::factory('incident')->where('incident_active', '1')->limit('8')->count_all();
		$this->template->content->incidents = ORM::factory('incident')->where('incident_active', '1')->orderby('incident_dateadd', 'desc')->find_all();		
		
		// Get Slider Dates By Year
		$startDate = "";
		$endDate = "";
		$db = new Database();	// We need to use the DB builder for a custom query
		$query = $db->query('SELECT DATE_FORMAT(incident_date, \'%Y\') AS incident_date FROM incident WHERE incident_active = 1 GROUP BY DATE_FORMAT(incident_date, \'%Y\') ORDER BY incident_date');
		foreach ( $query as $slider_date )
        {
			$startDate .= "<optgroup label=\"" . $slider_date->incident_date . "\">";
			for ( $i=1; $i <= 12; $i++ ) {
				if ( $i < 10 )
				{
					$i = "0" . $i;
				}
				$startDate .= "<option value=\"" . $slider_date->incident_date . "-" . $i . "-01\">" . date('M', mktime(0,0,0,$i,1)) . " " . $slider_date->incident_date . "</option>";
			}
			$startDate .= "</optgroup>";
			
			$endDate .= "<optgroup label=\"" . $slider_date->incident_date . "\">";
			for ( $i=1; $i <= 12; $i++ ) {
				if ( $i < 10 )
				{
					$i = "0" . $i;
				}
				$endDate .= "<option value=\"" . $slider_date->incident_date . "-" . $i . "-" . date('t', mktime(0,0,0,$i,1)) . "\"";
				if ( $i == 12 )
				{
					$endDate .= " selected=\"selected\" ";
				}
				$endDate .= ">" . date('M', mktime(0,0,0,$i,1)) . " " . $slider_date->incident_date . "</option>";
			}
			$endDate .= "</optgroup>";			
        }
		$this->template->content->startDate = $startDate;
		$this->template->content->endDate = $endDate;
		
		// Javascript Header
		$this->template->header->map_enabled = TRUE;
		$this->template->header->js = new View('main_js');
		$this->template->header->js->default_map = Kohana::config('settings.default_map');
		$this->template->header->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->template->header->js->latitude = Kohana::config('settings.default_lat');
		$this->template->header->js->longitude = Kohana::config('settings.default_lon');
	}

} // End Main