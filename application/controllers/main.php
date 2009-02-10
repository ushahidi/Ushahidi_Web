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
		$site_name = Kohana::config('settings.site_name');
			// Prevent Site Name From Breaking up if its too long
			// by reducing the size of the font
			if (strlen($site_name) > 20) {
				$site_name_style = " style=\"font-size:21px;\"";
			}
			else
			{
				$site_name_style = "";
			}
        $this->template->header->site_name = $site_name;
		$this->template->header->site_name_style = $site_name_style;
		$this->template->header->site_tagline = Kohana::config('settings.site_tagline');
        $this->template->header->api_url = Kohana::config('settings.api_url');
		
		// Display News Feed?
		$this->template->header->allow_feed = Kohana::config('settings.allow_feed');
		
		// Javascript Header
		$this->template->header->map_enabled = FALSE;
		$this->template->header->validator_enabled = FALSE;
		$this->template->header->datepicker_enabled = FALSE;
		$this->template->header->photoslider_enabled = FALSE;
		$this->template->header->videoslider_enabled = FALSE;
		$this->template->header->main_page = FALSE;
		$this->template->header->js = '';
		
		$this->template->header->this_page = "";
		
		// Google Analytics
		$google_analytics = Kohana::config('settings.google_analytics');
		$this->template->footer->google_analytics = $this->_google_analytics($google_analytics);
		
		// Create Language Session
		if (isset($_GET['lang']) && !empty($_GET['lang'])) {
			$_SESSION['lang'] = $_GET['lang'];
		}
		if (isset($_SESSION['lang']) && !empty($_SESSION['lang'])){
			Kohana::config_set('locale.language', $_SESSION['lang']);
		}
		$this->template->header->site_language = Kohana::config('locale.language');
		
		//Set up tracking gif
		if($_SERVER['SERVER_NAME'] != 'localhost' && $_SERVER['SERVER_NAME'] != '127.0.0.1'){
			$track_url = $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
		}else{
			$track_url = 'null';
		}
		$this->template->footer->tracker_url = 'http://tracker.ushahidi.com/track.php?url='.urlencode($track_url).'&lang='.$this->template->header->site_language.'';
		
        // Load profiler
        // $profiler = new Profiler;		
		
    }

    public function index()
    {		
        $this->template->header->this_page = 'home';
        $this->template->content = new View('main');
		
        // Get all active categories
        $categories = array();
        foreach (ORM::factory('category')
                 ->where('category_visible', '1')
                 ->find_all() as $category)
        {
            // Create a list of all categories
            $categories[$category->id] = array($category->category_title, $category->category_color);
        }
        $this->template->content->categories = $categories;
		
		
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
		
		
        // Get Slider Dates By Year
        $startDate = "";
        $endDate = "";


        // We need to use the DB builder for a custom query
        $db = new Database();	
        $query = $db->query('SELECT DATE_FORMAT(incident_date, \'%Y\') AS incident_date FROM incident WHERE incident_active = 1 GROUP BY DATE_FORMAT(incident_date, \'%Y\') ORDER BY incident_date');
        foreach ($query as $slider_date)
        {
            $startDate .= "<optgroup label=\"" . $slider_date->incident_date . "\">";
            for ( $i=1; $i <= 12; $i++ ) {
                if ( $i < 10 )
                {
                    $i = "0" . $i;
                }
                $startDate .= "<option value=\"" . strtotime($slider_date->incident_date . "-" . $i . "-01") . "\">" . date('M', mktime(0,0,0,$i,1)) . " " . $slider_date->incident_date . "</option>";
            }
            $startDate .= "</optgroup>";
			
            $endDate .= "<optgroup label=\"" . $slider_date->incident_date . "\">";
            for ( $i=1; $i <= 12; $i++ ) 
            {
                if ( $i < 10 )
                {
                    $i = "0" . $i;
                }
                $endDate .= "<option value=\"" . strtotime($slider_date->incident_date . "-" . $i . "-" . date('t', mktime(0,0,0,$i,1))) . "\"";
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
		
		
        // get graph data
       // could not use DB query builder. It does not support parentheses yet
        $graph_data = array();		
        $all_graphs = Incident_Model::get_incidents_by_interval('month');	
		$this->template->content->all_graphs = $all_graphs;
		
		// Javascript Header
		$this->template->header->map_enabled = TRUE;
		$this->template->header->main_page = TRUE;
		// Cluster Reports on Map?
		$clustering = Kohana::config('settings.allow_clustering');
		if ($clustering == 1) 
		{
			$this->template->header->js = new View('main_cluster_js');
		}
		else
		{
			$this->template->header->js = new View('main_js');
		}
		$this->template->header->js->default_map = Kohana::config('settings.default_map');
		$this->template->header->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->template->header->js->latitude = Kohana::config('settings.default_lat');
		$this->template->header->js->longitude = Kohana::config('settings.default_lon');
		$this->template->header->js->graph_data = $graph_data;
		$this->template->header->js->all_graphs = $all_graphs;
		$this->template->header->js->categories = $categories;
		
		// Pack the javascript using the javascriptpacker helper
		$myPacker = new javascriptpacker($this->template->header->js , 'Normal', false, false);
		$this->template->header->js = $myPacker->pack();
	}
	
	
	/*
	* Google Analytics
	* @param text mixed  Input google analytics web property ID.
    * @return mixed  Return google analytics HTML code.
	*/
	private function _google_analytics($google_analytics = false)
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

} // End Main
