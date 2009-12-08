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

	// Session instance
	protected $session;
	
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
		
		//call the feedback form
		$this->_get_feedback_form();
        
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
        $this->template->header->api_url = Kohana::config('settings.api_url');

		// Display Contact Tab?
		$this->template->header->site_contact_page = Kohana::config('settings.site_contact_page');
				
		// Display Help Tab?
		$this->template->header->site_help_page = Kohana::config('settings.site_help_page');
		
		// Get Custom Pages
		$this->template->header->pages = ORM::factory('page')->where('page_active', '1')->find_all();
        
        // Get custom CSS file from settings
        $this->template->header->site_style = Kohana::config('settings.site_style');
		
		// Display News Feed?
		$this->template->header->allow_feed = Kohana::config('settings.allow_feed');
		
		// Javascript Header
		$this->template->header->map_enabled = FALSE;
		$this->template->header->validator_enabled = FALSE;
		$this->template->header->datepicker_enabled = FALSE;
		$this->template->header->photoslider_enabled = FALSE;
		$this->template->header->videoslider_enabled = FALSE;
		$this->template->header->protochart_enabled = FALSE;
		$this->template->header->main_page = FALSE;
		
		$footerjs = new View('footer_form_js');
		
		// Pack the javascript using the javascriptpacker helper
		$myPacker = new javascriptpacker($footerjs , 'Normal', false, false);
		$footerjs = $myPacker->pack();
		$this->template->header->js = $footerjs;
		
		$this->template->header->this_page = "";
		
		// Google Analytics
		$google_analytics = Kohana::config('settings.google_analytics');
		$this->template->footer->google_analytics = $this->_google_analytics($google_analytics);
		
		// *** Locales/Languages ***
		// First Get Available Locales
		$this->template->header->locales_array = $this->cache->get('locales');
		
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
		$this->template->header->l = Kohana::config('locale.language');
		
		//Set up tracking gif
		if($_SERVER['SERVER_NAME'] != 'localhost' && $_SERVER['SERVER_NAME'] != '127.0.0.1'){
			$track_url = $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
		}else{
			$track_url = 'null';
		}
		$this->template->footer->tracker_url = 'http://tracker.ushahidi.com/track.php?url='.urlencode($track_url).'&lang='.$this->template->header->l.'&version='.Kohana::config('version.ushahidi_version');
        // Load profiler
        // $profiler = new Profiler;
        
        // Get tracking javascript for stats
        $this->template->footer->ushahidi_stats = $this->_ushahidi_stats();
    }

    public function index()
    {		
        $this->template->header->this_page = 'home';
        $this->template->content = new View('main');
		
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
				$children[$child->id] = array( 
					$child->category_title, 
					$child->category_color
				);
			}
			
			// Put it all together
            $parent_categories[$category->id] = array( 
				$category->category_title, 
				$category->category_color,
				$children
			);
        }
        $this->template->content->categories = $parent_categories;

		// Get all active Layers (KMZ/KML)
		$layers = array();
		foreach (ORM::factory('layer')
				  ->where('layer_visible', 1)
				  ->find_all() as $layer)
		{
			$layers[$layer->id] = array($layer->layer_name, $layer->layer_color,
				$layer->layer_url, $layer->layer_file);
		}
		$this->template->content->layers = $layers;
		
		// Get all active Shares
		$shares = array();
		foreach (ORM::factory('sharing')
				  ->where('sharing_active', 1)
				  ->where('sharing_type', 1)
				  ->find_all() as $share)
		{
			$shares[$share->id] = array($share->sharing_site_name, $share->sharing_color);
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
			->with('location')
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
		$query = $db->query('SELECT incident_date, count(*) AS incident_count FROM incident WHERE incident_active = 1 GROUP BY DATE_FORMAT(incident_date, \'%Y-%m\') ORDER BY incident_count DESC LIMIT 1');
		foreach ($query as $query_active)
		{
			$active_month = date('n', strtotime($query_active->incident_date));
			$active_year = date('Y', strtotime($query_active->incident_date));
			$active_startDate = strtotime($active_year . "-" . $active_month . "-01");
			$active_endDate = strtotime($active_year . "-" . $active_month . 
				"-" . date('t', mktime(0,0,0,$active_month,1))." 23:59:59");
		}
		
        // Next, Get the Range of Years
        $query = $db->query('SELECT DATE_FORMAT(incident_date, \'%Y\') AS incident_date FROM incident WHERE incident_active = 1 GROUP BY DATE_FORMAT(incident_date, \'%Y\') ORDER BY incident_date');
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
                if ( $active_month && 
						( ( (int) $i == ( $active_month + 1)) )
						 	|| $i == 12)
				{
					$endDate .= " selected=\"selected\" ";
                }
                $endDate .= ">" . date('M', mktime(0,0,0,$i,1)) . " " . $years . "</option>";
            }
            $endDate .= "</optgroup>";			
        }
        $this->template->content->startDate = $startDate;
        $this->template->content->endDate = $endDate;
		
		
		// get graph data
		// could not use DB query builder. It does not support parentheses yet
		$graph_data = array();		
		$all_graphs = Incident_Model::get_incidents_by_interval('month');
		$daily_graphs = Incident_Model::get_incidents_by_interval('day');
		$weekly_graphs = Incident_Model::get_incidents_by_interval('week');
		$hourly_graphs = Incident_Model::get_incidents_by_interval('hour');
		$this->template->content->all_graphs = $all_graphs;
		$this->template->content->daily_graphs = $daily_graphs;
		
		// If we are looking at the standard street map set by user
		if(!isset($_GET['3dmap'])) {
		
			//echo 'STREET MAP';
		
			// Javascript Header
			$this->template->header->map_enabled = 'streetmap';
			$this->template->content->map_enabled = 'streetmap';
			$this->template->content->map_container = 'map';
			$this->template->header->main_page = TRUE;
			$this->template->header->validator_enabled = TRUE;
			
			// Map Settings
			$clustering = Kohana::config('settings.allow_clustering');
			$marker_radius = Kohana::config('map.marker_radius');
			$marker_opacity = Kohana::config('map.marker_opacity');
			$marker_stroke_width = Kohana::config('map.marker_stroke_width');
			$marker_stroke_opacity = Kohana::config('map.marker_stroke_opacity');
			$this->template->header->js = new View('main_cluster_js');
			$this->template->header->js->cluster = ($clustering == 1) ? "true" : "false";
			$this->template->header->js->marker_radius =
				($marker_radius >=1 && $marker_radius <= 10 ) ? $marker_radius : 5;
			$this->template->header->js->marker_opacity =
				($marker_opacity >=1 && $marker_opacity <= 10 ) 
				? $marker_opacity * 0.1  : 0.9;
			$this->template->header->js->marker_stroke_width =
				($marker_stroke_width >=1 && $marker_stroke_width <= 5 ) ? $marker_stroke_width : 2;
			$this->template->header->js->marker_stroke_opacity =
				($marker_stroke_opacity >=1 && $marker_stroke_opacity <= 10 ) 
				? $marker_stroke_opacity * 0.1  : 0.9;	
			
			$this->template->header->js->default_map = Kohana::config('settings.default_map');
			$this->template->header->js->default_zoom = Kohana::config('settings.default_zoom');
			$this->template->header->js->latitude = Kohana::config('settings.default_lat');
			$this->template->header->js->longitude = Kohana::config('settings.default_lon');
			$this->template->header->js->graph_data = $graph_data;
			$this->template->header->js->all_graphs = $all_graphs;
			$this->template->header->js->daily_graphs = $daily_graphs;
			$this->template->header->js->hourly_graphs = $hourly_graphs;
			$this->template->header->js->weekly_graphs = $weekly_graphs;
			$this->template->header->js->default_map_all = Kohana::config('settings.default_map_all');
			
			//
			$this->template->header->js->active_startDate = $active_startDate;
			$this->template->header->js->active_endDate = $active_endDate;
			
		// If we are viewing the 3D map
		}else{
		
			//echo '3D MAP';
			
			// Javascript Header
			$this->template->header->map_enabled = '3dmap';
			$this->template->content->map_enabled = '3dmap';
			$this->template->content->map_container = 'map3d';
			$this->template->header->main_page = FALSE; // Setting to false because we don't want all the external controls that the street map has
			$this->template->header->js = new View('main_3d_js');
			
			$this->template->header->js->default_zoom = Kohana::config('settings.default_zoom');
			$this->template->header->js->latitude = Kohana::config('settings.default_lat');
			$this->template->header->js->longitude = Kohana::config('settings.default_lon');
			
			// Override API URL
			$this->template->header->api_url = '<script src="http://www.google.com/jsapi?key='.Kohana::config('settings.api_google').'"> </script>';
		}
		
		
		$footerjs = new View('footer_form_js');
		
		// Pack the javascript using the javascriptpacker helper		
		$this->template->header->js .= $footerjs;
		
		$myPacker = new javascriptpacker($this->template->header->js , 'Normal', false, false);
		$this->template->header->js = $myPacker->pack();
	}
	
	/*
	* Ushahidi Stats HTML/JavaScript
    * @return mixed  Return ushahidi stats HTML code.
	*/
	private function _ushahidi_stats( )
	{	
		// Make sure cURL is installed
		if (!function_exists('curl_exec')) {
			throw new Kohana_Exception('footer.cURL_not_installed');
			return false;
		}
		
		$settings = ORM::factory('settings', 1);
		$stat_id = $settings->stat_id;
		
		if($stat_id == 0) return ''; 
		$url = 'http://tracker.ushahidi.com/px.php?task=tc&siteid='.$stat_id;
		
		$curl_handle = curl_init();
		curl_setopt($curl_handle,CURLOPT_URL,$url);
		curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,15); // Timeout set to 15 seconds. This is somewhat arbitrary and can be changed.
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1); // Set cURL to store data in variable instead of print
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);
		
		try {
			$tag = @simplexml_load_string($buffer); // This works because the tracking code is only wrapped in one tag
		} catch (Exception $e) {
			// In case the xml was malformed for whatever reason, we will just guess what the tag should be here
			$tag = '<!-- Piwik -->
					<script type="text/javascript">
					var pkBaseURL = (("https:" == document.location.protocol) ? "https://tracker.ushahidi.com/piwik/" : "http://tracker.ushahidi.com/piwik/");
					document.write(unescape("%3Cscript src=\'" + pkBaseURL + "piwik.js\' type=\'text/javascript\'%3E%3C/script%3E"));
					</script><script type="text/javascript">
					try {
					  var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", '.$stat_id.');
					  piwikTracker.trackPageView();
					  piwikTracker.enableLinkTracking();
					} catch( err ) {}
					</script><noscript><p><img src="http://tracker.ushahidi.com/piwik/piwik.php?idsite='.$stat_id.'" style="border:0" alt=""/></p></noscript>
					<!-- End Piwik Tag -->
					';
		}
		
		return $tag;

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
	
	/**
	 * Get the feedback
	 */
	private function _get_feedback_form() {
		//setup and initialize form fields
		$form = array
		(
				'feedback_message' => '',
				'person_email' => '',
				'feedback_captcha' => ''
		);
		
		// Load Akismet API Key (Spam Blocker)
		$api_akismet = Kohana::config('settings.api_akismet');
		
		$captcha = Captcha::factory();
		
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;

		//has form been submitted, if so setup validation
		if($_POST)
		{

			$post = Validation::factory($_POST);

			//Trim whitespaces
			$post->pre_filter('trim', TRUE);

			//Add validation rules
			$post->add_rules('feedback_message','required');
			$post->add_rules('person_email', 'required','email');
			$post->add_rules('feedback_captcha', 'required', 'Captcha::valid');
			if( $post->validate() ) { 
				if($api_akismet != "" ) {
					// Run Akismet Spam Checker
						$akismet = new Akismet();

						// comment data
						$feedback = array(
							'feedback_message' => $post->feedback_message,
							'person_email' => $post->feedback_message,
						);

						$config = array(
							'blog_url' => url::site(),
							'api_key' => $api_akismet,
							'feedback' => $feedback
						);

						$akismet->init($config);

						if($akismet->errors_exist()) 
						{
							if($akismet->is_error('AKISMET_INVALID_KEY'))
							{
								// throw new Kohana_Exception('akismet.api_key');
							}
							elseif($akismet->is_error('AKISMET_RESPONSE_FAILED')) 
							{
								// throw new Kohana_Exception('akismet.server_failed');
							}
							elseif($akismet->is_error('AKISMET_SERVER_NOT_FOUND')) 
							{
								// throw new Kohana_Exception('akismet.server_not_found');
							}
							// If the server is down, we have to post 
							// the comment :(
							// $this->_post_comment($comment);
							$feedback_spam = 0;
						}
						else {
							if($akismet->is_spam()) 
							{
								$feedback_spam = 1;
							}
							else {
								$feedback_spam = 0;
							}
						}
					}
					else
					{ // No API Key!!
						$feedback_spam = 0;
					}
				$this->_dump_feedback($post);


				//send details to admin
				$frm = $post->person_email;
				$subject = Kohana::lang('feedback.feedback_details');;
				$message = $post->feedback_message;
				$email = Kohana::config('settings.site_email');
				$this->_send_feedback( $email, $message, $subject, $frm );

				//send details to ushahidi
				$frm = $post->person_email;
				$subject = Kohana::lang('feedback.feedback_details');;
				$message = $post->feedback_message;
				$message .= "Instance: ".url::base();
				$email = "feedback@ushahidi.com";
				$this->_send_feedback( $email, $message, $subject, $frm );
			}
			else
	        {
				// repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

	            // populate the error fields, if any
	            $errors = arr::overwrite($errors, $post->errors('feedback'));
				$form_error = TRUE;
			}
		}
		$this->template->footer->js = new View('footer_form_js');
		$this->template->footer->form = $form;
		$this->template->footer->captcha = $captcha;
		$this->template->footer->errors = $errors;
		$this->template->footer->form_error = $form_error;
        }

        /**
         * Escape string
         */
        private function _escape_string($str) {
            if( $str != "" ){
                $str = str_replace(array('\''),array('\\\''),$str);
                $str = "'".$str."'";
            }else {
                return "";
            }
            return $str;
        }

	
	/**
	 * puts feedback info into the database.
	 * @param the post object
	 */
	private function _dump_feedback($post) {
		
		$feedback = new Feedback_Model();
		$feedback->feedback_mesg = $post->feedback_message;
		$feedback->feedback_dateadd = date("Y-m-d H:i:s",time());
		$feedback->save();//save feedback info to db
		
		$feedback_person = new Feedback_Person_Model();
		$feedback_person->feedback_id = $feedback->id;
		$feedback_person->person_email = $post->person_email;
		$feedback_person->person_date = date("Y-m-d H:i:s",time());
		$feedback_person->person_ip = $post->person_ip;
		$feedback_person->save(); //save person info to db
	}
	
	/**
	 * Send feedback info as email to admin and Ushahidi
	 */
	public function _send_feedback( $email, $message, $subject, $frm )
	{
		$to = $email;
		$from = $frm;
		$subject = $subject;
		
		$message .= "\n\n";
		//email details
		if( email::send( $to, $from, $subject, $message, FALSE ) == 1 )
		{
			return TRUE;
		}
		else 
		{
			return FALSE;
		}
	}
	
} // End Main
