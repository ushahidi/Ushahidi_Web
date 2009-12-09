<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to manage user settings
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Settings Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Settings_Controller extends Admin_Controller
{
	protected $cache;
	
	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'settings';

		// If this is not a super-user account, redirect to dashboard
		if(!$this->auth->logged_in('admin') && !$this->auth->logged_in('superadmin'))
        {
             url::redirect('admin/dashboard');
		}
		
		$this->cache = Cache::instance();
	}

	/**
	* Site Settings
    */
	function site()
	{
		$this->template->content = new View('admin/site');
		$this->template->content->title = 'Settings';

		// setup and initialize form field names
		$form = array
	    (
			'site_name' => '',
			'site_tagline' => '',
			'site_email' => '',
			'alerts_email' =>  '',
			'site_language' => '',
			'site_help_page' => '',
			'site_contact_page' => '',
			'items_per_page' => '',
			'items_per_page_admin' => '',
			'allow_reports' => '',
			'allow_comments' => '',
			'allow_feed' => '',
			'allow_stat_sharing' => '',
			'allow_clustering' => '',
			'default_map_all' => '',
			'google_analytics' => '',
			'twitter_hashtags' => '',
			'twitter_username' => '',
			'twitter_password' => '',
			'laconica_username' => '',
			'laconica_password' => '',
			'laconica_site' => '',
			'api_akismet' => ''
	    );
        //  Copy the form as errors, so the errors will be stored with keys
        //  corresponding to the form field names
        $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;

		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
            // Instantiate Validation, use $post, so we don't overwrite $_POST
            // fields with our own things
            $post = new Validation($_POST);

	        // Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order

			$post->add_rules('site_name', 'required', 'length[3,50]');
			$post->add_rules('site_tagline', 'length[3,100]');
			$post->add_rules('site_email', 'email', 'length[4,100]');
			$post->add_rules('alerts_email', 'email', 'length[4,100]');
			$post->add_rules('site_language','required', 'length[5, 5]');
			$post->add_rules('site_help_page','required','between[0,1]');
			$post->add_rules('site_contact_page','required','between[0,1]');
			$post->add_rules('items_per_page','required','between[10,50]');
			$post->add_rules('items_per_page_admin','required','between[10,50]');
			$post->add_rules('allow_reports','required','between[0,1]');
			$post->add_rules('allow_comments','required','between[0,1]');
			$post->add_rules('allow_feed','required','between[0,1]');
			$post->add_rules('allow_stat_sharing','required','between[0,1]');
			$post->add_rules('allow_clustering','required','between[0,1]');
			$post->add_rules('default_map_all','required', 'alpha_numeric', 'length[6,6]');
			$post->add_rules('google_analytics','length[0,20]');
			$post->add_rules('twitter_hashtags','length[0,500]');
			$post->add_rules('twitter_username','length[0,50]');
			$post->add_rules('twitter_password','length[0,50]');
			$post->add_rules('laconica_username','length[0,50]');
			$post->add_rules('laconica_password','length[0,50]');
			$post->add_rules('laconica_site','length[0,30]');
			$post->add_rules('api_akismet','length[0,100]', 'alpha_numeric');

			// Test to see if things passed the rule checks
	        if ($post->validate())
	        {
	            // Yes! everything is valid
				$settings = new Settings_Model(1);
				$settings->site_name = $post->site_name;
				$settings->site_tagline = $post->site_tagline;
				$settings->site_email = $post->site_email;
				$settings->alerts_email = $post->alerts_email;
				$settings->site_language = $post->site_language;
				$settings->site_help_page = $post->site_help_page;
				$settings->site_contact_page = $post->site_contact_page;
				$settings->items_per_page = $post->items_per_page;
				$settings->items_per_page_admin = $post->items_per_page_admin;
				$settings->allow_reports = $post->allow_reports;
				$settings->allow_comments = $post->allow_comments;
				$settings->allow_feed = $post->allow_feed;
				$settings->allow_stat_sharing = $post->allow_stat_sharing;
				$settings->allow_clustering = $post->allow_clustering;
				$settings->default_map_all = $post->default_map_all;
				$settings->google_analytics = $post->google_analytics;
				$settings->twitter_hashtags = $post->twitter_hashtags;
				$settings->twitter_username = $post->twitter_username;
				$settings->twitter_password = $post->twitter_password;
				$settings->laconica_username = $post->laconica_username;
				$settings->laconica_password = $post->laconica_password;
				$settings->laconica_site = $post->laconica_site;
				$settings->api_akismet = $post->api_akismet;
				$settings->date_modify = date("Y-m-d H:i:s",time());
				$settings->save();
				
				// Delete Settings Cache
				$this->cache->delete('settings');
				$this->cache->delete_tag('settings');

				// Everything is A-Okay!
				$form_saved = TRUE;
					
				// repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

	        }

            // No! We have validation errors, we need to show the form again,
            // with the errors
            else
	        {
	            // repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

	            // populate the error fields, if any
	            $errors = arr::overwrite($errors, $post->errors('settings'));
				$form_error = TRUE;
	        }
	    }
		else
		{
			// Retrieve Current Settings
			$settings = ORM::factory('settings', 1);

			$form = array
		    (
		        'site_name' => $settings->site_name,
				'site_tagline' => $settings->site_tagline,
				'site_email' => $settings->site_email,
				'alerts_email' => $settings->alerts_email,
				'site_language' => $settings->site_language,
				'site_help_page' => $settings->site_help_page,
				'site_contact_page' => $settings->site_contact_page,
				'items_per_page' => $settings->items_per_page,
				'items_per_page_admin' => $settings->items_per_page_admin,
				'allow_reports' => $settings->allow_reports,
				'allow_comments' => $settings->allow_comments,
				'allow_feed' => $settings->allow_feed,
				'allow_stat_sharing' => $settings->allow_stat_sharing,
				'allow_clustering' => $settings->allow_clustering,
				'default_map_all' => $settings->default_map_all,
				'google_analytics' => $settings->google_analytics,
				'twitter_hashtags' => $settings->twitter_hashtags,
				'twitter_username' => $settings->twitter_username,
				'twitter_password' => $settings->twitter_password,
				'laconica_username' => $settings->laconica_username,
				'laconica_password' => $settings->laconica_password,
				'laconica_site' => $settings->laconica_site,
				'api_akismet' => $settings->api_akismet
		    );
		}
		
		$this->template->colorpicker_enabled = TRUE;
		$this->template->content->form = $form;
	    $this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->items_per_page_array = array('10'=>'10 Items','20'=>'20 Items','30'=>'30 Items','50'=>'50 Items');
		$this->template->content->yesno_array = array('1'=>'YES','0'=>'NO');
		
		// Generate Available Locales
		$locales = $this->_get_i18n();
		$this->template->content->locales_array = $locales;
		$this->cache->set('locales', $locales, array('locales'), 0);
	}

	/**
	* Map Settings
    */
	function index($saved = false)
	{
		// Display all maps
		$this->template->api_url = Kohana::config('settings.api_url_all');

		// Current Default Country
		$current_country = Kohana::config('settings.default_country');

		$this->template->content = new View('admin/settings');
		$this->template->content->title = 'Settings';

		// setup and initialize form field names
		$form = array
	    (
			'default_map' => '',
			'api_google' => '',
			'api_yahoo' => '',
			'default_country' => '',
			'multi_country' => '',
			'default_lat' => '',
			'default_lon' => '',
			'default_zoom' => ''
	    );
        //  Copy the form as errors, so the errors will be stored with keys
        //  corresponding to the form field names
        $errors = $form;
		$form_error = FALSE;
		if ($saved == 'saved')
		{
			$form_saved = TRUE;
		}
		else
		{
			$form_saved = FALSE;
		}

		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
            // Instantiate Validation, use $post, so we don't overwrite $_POST
            // fields with our own things
            $post = new Validation($_POST);

	        // Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order

			$post->add_rules('default_country', 'required', 'numeric', 'length[1,4]');
			$post->add_rules('multi_country', 'numeric', 'length[1,1]');
			$post->add_rules('default_map', 'required', 'between[1,4]');
			$post->add_rules('api_google','required', 'length[0,200]');
			$post->add_rules('api_yahoo','required', 'length[0,200]');
			$post->add_rules('default_zoom','required','between[0,16]');		// Validate for maximum and minimum zoom values
			$post->add_rules('default_lat','required','between[-90,90]');		// Validate for maximum and minimum latitude values
			$post->add_rules('default_lon','required','between[-180,180]');		// Validate for maximum and minimum longitude values

			// Test to see if things passed the rule checks
	        if ($post->validate())
	        {
	            // Yes! everything is valid
				$settings = new Settings_Model(1);
				$settings->default_country = $post->default_country;
				$settings->multi_country = $post->multi_country;
				$settings->default_map = $post->default_map;
				$settings->api_google = $post->api_google;
				$settings->api_yahoo = $post->api_yahoo;
				$settings->default_zoom = $post->default_zoom;
				$settings->default_lat = $post->default_lat;
				$settings->default_lon = $post->default_lon;
				$settings->date_modify = date("Y-m-d H:i:s",time());
				$settings->save();
				
				// Delete Settings Cache
				$this->cache->delete('settings');
				$this->cache->delete_tag('settings');

				// Everything is A-Okay!
				$form_saved = TRUE;

				// Redirect to reload everything over again
	            url::redirect('admin/settings/index/saved');

	        }

            // No! We have validation errors, we need to show the form again,
            // with the errors
            else
	        {
	            // repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

	            // populate the error fields, if any
	            $errors = arr::overwrite($errors, $post->errors('settings'));
				$form_error = TRUE;
	        }
	    }
		else
		{
			// Retrieve Current Settings
			$settings = ORM::factory('settings', 1);

			$form = array
		    (
				'default_map' => $settings->default_map,
				'api_google' => $settings->api_google,
				'api_yahoo' => $settings->api_yahoo,
				'default_country' => $settings->default_country,
				'multi_country' => $settings->multi_country,
				'default_lat' => $settings->default_lat,
				'default_lon' => $settings->default_lon,
				'default_zoom' => $settings->default_zoom
		    );
		}


		$this->template->content->form = $form;
	    $this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;

		// Get Countries
		$countries = array();
		foreach (ORM::factory('country')->orderby('country')->find_all() as $country)
		{
			// Create a list of all categories
			$this_country = $country->country;
			if (strlen($this_country) > 35)
			{
				$this_country = substr($this_country, 0, 35) . "...";
			}
			$countries[$country->id] = $this_country;
		}
		$this->template->content->countries = $countries;
		
		// Zoom Array for Slider
		$default_zoom_array = array();
		for ($i=0; $i<16 ; $i++)
		{ 
			$default_zoom_array[$i] = $i;
		}
		$this->template->content->default_zoom_array = $default_zoom_array;

		// Javascript Header
		$this->template->map_enabled = TRUE;
		$this->template->js = new View('admin/settings_js');
		$this->template->js->default_map = $form['default_map'];
		$this->template->js->default_zoom = $form['default_zoom'];
		$this->template->js->default_lat = $form['default_lat'];
		$this->template->js->default_lon = $form['default_lon'];
	}


    /**
     * Handles settings for FrontlineSMS
     */
	function sms()
	{
		$this->template->content = new View('admin/sms');
		$this->template->content->title = 'Settings';

		// setup and initialize form field names
		$form = array
	    (
			'sms_no1' => '',
			'sms_no2' => '',
			'sms_no3' => ''
	    );
        //  Copy the form as errors, so the errors will be stored with keys
        //  corresponding to the form field names
        $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;

		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
            // Instantiate Validation, use $post, so we don't overwrite $_POST
            // fields with our own things
            $post = new Validation($_POST);

	        // Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order

			$post->add_rules('sms_no1', 'numeric', 'length[1,30]');
			$post->add_rules('sms_no2', 'numeric', 'length[1,30]');
			$post->add_rules('sms_no3', 'numeric', 'length[1,30]');

			// Test to see if things passed the rule checks
	        if ($post->validate())
	        {
	            // Yes! everything is valid
				$settings = new Settings_Model(1);
				$settings->sms_no1 = $post->sms_no1;
				$settings->sms_no2 = $post->sms_no2;
				$settings->sms_no3 = $post->sms_no3;
				$settings->date_modify = date("Y-m-d H:i:s",time());
				$settings->save();

				// Everything is A-Okay!
				$form_saved = TRUE;

				// repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

	        }

            // No! We have validation errors, we need to show the form again,
            // with the errors
            else
	        {
	            // repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

	            // populate the error fields, if any
	            $errors = arr::overwrite($errors, $post->errors('settings'));
				$form_error = TRUE;
	        }
	    }
		else
		{
			// Retrieve Current Settings
			$settings = ORM::factory('settings', 1);

			$form = array
		    (
		        'sms_no1' => $settings->sms_no1,
				'sms_no2' => $settings->sms_no2,
				'sms_no3' => $settings->sms_no3
		    );
		}

		// Do we have a frontlineSMS Key? If not create and save one on the fly
		$settings = ORM::factory('settings', 1);
		$frontlinesms_key = $settings->frontlinesms_key;
		if (!$frontlinesms_key)
		{
			$frontlinesms_key = strtoupper(text::random('alnum',8));
			$settings->frontlinesms_key = $frontlinesms_key;
			$settings->save();
		}

		$this->template->content->form = $form;
	    $this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->frontlinesms_key = $frontlinesms_key;
		$this->template->content->frontlinesms_link = url::base(). "frontlinesms/?key=" . $frontlinesms_key . "&s=\${sender_number}&m=\${message_content}";
	}



    /**
     * Handles settings for Global SMS Providers - Clickatell In This Case
     */
	function smsglobal()
	{
		$this->template->content = new View('admin/smsglobal');
		$this->template->content->title = 'Settings';

		// setup and initialize form field names
		$form = array
	    (
			'clickatell_api' => '',
			'clickatell_username' => '',
			'clickatell_password' => ''
	    );
        //  Copy the form as errors, so the errors will be stored with keys
        //  corresponding to the form field names
        $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;

		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
            // Instantiate Validation, use $post, so we don't overwrite $_POST
            // fields with our own things
            $post = new Validation($_POST);

	        // Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order

			$post->add_rules('clickatell_api','required', 'length[4,20]');
			$post->add_rules('clickatell_username', 'required', 'length[3,50]');
			$post->add_rules('clickatell_password', 'required', 'length[5,50]');

			// Test to see if things passed the rule checks
	        if ($post->validate())
	        {
	            // Yes! everything is valid
				$settings = new Settings_Model(1);
				$settings->clickatell_api = $post->clickatell_api;
				$settings->clickatell_username = $post->clickatell_username;
				$settings->clickatell_password = $post->clickatell_password;
				$settings->date_modify = date("Y-m-d H:i:s",time());
				$settings->save();

				// Everything is A-Okay!
				$form_saved = TRUE;

				// repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

	        }

            // No! We have validation errors, we need to show the form again,
            // with the errors
            else
	        {
	            // repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

	            // populate the error fields, if any
	            $errors = arr::overwrite($errors, $post->errors('settings'));
				$form_error = TRUE;
	        }
	    }
		else
		{
			// Retrieve Current Settings
			$settings = ORM::factory('settings', 1);

			$form = array
		    (
		        'clickatell_api' => $settings->clickatell_api,
				'clickatell_username' => $settings->clickatell_username,
				'clickatell_password' => $settings->clickatell_password
		    );
		}

		$this->template->content->form = $form;
	    $this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;

		// Javascript Header
		$this->template->js = new View('admin/smsglobal_js');
	}


	/**
     * Retrieves Clickatell Balance using Clickatell Library
     */
	function smsbalance()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		$settings = new Settings_Model(1);
		if ($settings->loaded == true) {
			$clickatell_api = $settings->clickatell_api;
			$clickatell_username = $settings->clickatell_username;
			$clickatell_password = $settings->clickatell_password;

			$mysms = new Clickatell();
			$mysms->api_id = $clickatell_api;
			$mysms->user = $clickatell_username;
			$mysms->password = $clickatell_password;
			$mysms->use_ssl = false;
			$mysms->sms();
		 	// echo $mysms->session;
		 	echo $mysms->getbalance();
		}
	}
	
	/**
	* Email Settings
    */
	function email()
	{
		$this->template->content = new View('admin/email');
		$this->template->content->title = 'Settings';

		// setup and initialize form field names
		$form = array
	    (
			'email_username' => '',
			'email_password' => '',
			'email_port' => '',
			'email_host' => '',
			'email_servertype' => '',
			'email_ssl' => ''
	    );
        //  Copy the form as errors, so the errors will be stored with keys
        //  corresponding to the form field names
        $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;

		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
            // Instantiate Validation, use $post, so we don't overwrite $_POST
            // fields with our own things
            $post = new Validation($_POST);

	        // Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order

			$post->add_rules('email_username', 'required', 'length[3,50]');
			$post->add_rules('email_password', 'length[3,100]');
			$post->add_rules('email_port', 'numeric[1,100]','length[1,20]');
			$post->add_rules('email_host','required', 'length[3,100]');
			$post->add_rules('email_servertype','required','length[3,100]');
			
			// Test to see if things passed the rule checks
	        if ($post->validate())
	        {
	            // Yes! everything is valid
				$settings = new Settings_Model(1);
				$settings->email_username = $post->email_username;
				$settings->email_password = $post->email_password;
				$settings->email_port = $post->email_port;
				$settings->email_host = $post->email_host;
				$settings->email_servertype = $post->email_servertype;
				$settings->email_ssl = $post->email_ssl;
				$settings->save();
				
				//add details to application/config/email.php
				//$this->_add_email_settings($settings);
				
				// Delete Settings Cache
				$this->cache->delete('settings');
				$this->cache->delete_tag('settings');
				
				
				// Everything is A-Okay!
				$form_saved = TRUE;

				// repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

	        }

            // No! We have validation errors, we need to show the form again,
            // with the errors
            else
	        {
	            // repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());
				
	            // populate the error fields, if any
	            $errors = arr::overwrite($errors, $post->errors('settings'));
				$form_error = TRUE;
	        }
	    }
		else
		{
			// Retrieve Current Settings
			$settings = ORM::factory('settings', 1);

			$form = array
		    (
		        'email_username' => $settings->email_username,
				'email_password' => $settings->email_password,
				'email_port' => $settings->email_port,
				'email_host' => $settings->email_host,
				'email_servertype' => $settings->email_servertype,
				'email_ssl' => $settings->email_ssl
		    );
		}
		
		$this->template->colorpicker_enabled = TRUE;
		$this->template->content->form = $form;
	    $this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->email_ssl_array = array('1'=>'YES','0'=>'NO');
	}


	/**
	 * Retrieves cities listing using GeoNames Service
	 * @param int $cid The id of the country to retrieve cities for
	 * Returns a JSON response
	 */
	function updateCities($cid = 0)
	{
		$this->template = "";
		$this->auto_render = FALSE;

		$cities = 0;

		// Get country ISO code from DB
		$country = ORM::factory('country', (int)$cid);

		if ($country->loaded==true)
		{
			$iso = $country->iso;

			// GeoNames WebService URL + Country ISO Code
			$geonames_url = "http://ws.geonames.org/search?country="
                            .$iso."&featureCode=PPL&featureCode=PPLA&featureCode=PPLC";

			// Grabbing GeoNames requires cURL so we will check for that here.
			if (!function_exists('curl_exec'))
			{
				throw new Kohana_Exception('settings.updateCities.cURL_not_installed');
				return false;
			}

			// Use Curl
			$ch = curl_init();
			$timeout = 20;
			curl_setopt ($ch, CURLOPT_URL, $geonames_url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$xmlstr = curl_exec($ch);
			$err = curl_errno( $ch );
			curl_close($ch);

			// $xmlstr = file_get_contents($geonames_url);

			// No Timeout Error, so proceed
			if ($err == 0) {
				// Reset All Countries City Counts to Zero
				$countries = ORM::factory('country')->find_all();
				foreach ($countries as $country)
				{
					$country->cities = 0;
					$country->save();
				}

				// Delete currently loaded cities
				ORM::factory('city')->delete_all();

				$sitemap = new SimpleXMLElement($xmlstr);
				foreach($sitemap as $city)
	            {
					if ($city->name && $city->lng && $city->lat)
					{
						$newcity = new City_Model();
						$newcity->country_id = $cid;
						$newcity->city = mysql_real_escape_string($city->name);
						$newcity->city_lat = mysql_real_escape_string($city->lat);
						$newcity->city_lon = mysql_real_escape_string($city->lng);
						$newcity->save();

						$cities++;
					}
				}
				// Update Country With City Count
				$country = ORM::factory('country', $cid);
				$country->cities = $cities;
				$country->save();

				echo json_encode(array("status"=>"success", "response"=>"$cities Cities Loaded!"));
			}
			else {
				echo json_encode(array("status"=>"error", "response"=>"0 Cities Loaded. Geonames Timeout Error!"));
			}
		}
		else
		{
			echo json_encode(array("status"=>"error", "response"=>"0 Cities Loaded. Country Not Found!"));
		}
	}
	
	/**
	 * adds the email settings to the application/config/email.php file
	 */
	private function _add_email_settings( $settings )
	{
	    $email_file = @file('application/config/email.template.php');
        $handle = @fopen('application/config/email.php', 'w');

	    foreach( $email_file as $number_line => $line )
	    {
	        
	    	switch( $line ) {
	        	case strpos($line,"\$config['username']"):
	            	fwrite($handle,  str_replace("\$config['username'] = \"\"","\$config['username'] = ".'"'.$settings->email_username.'"',$line ));
	           		break;
				
				case strpos($line,"\$config['password']"):
		            fwrite($handle,  str_replace("\$config['password'] = \"\"","\$config['password'] = ".'"'.$settings->email_password.'"',$line ));
		           	break;	
				
				case strpos($line,"\$config['port']"):
		            fwrite($handle,  str_replace("\$config['port'] = 25","\$config['port'] = ".'"'.$settings->email_port.'"',$line ));
		           	break;
				
				case strpos($line,"\$config['server']"):
		            fwrite($handle,  str_replace("\$config['server'] = \"\"","\$config['server'] = ".'"'.$settings->email_host.'"',$line ));
		          	break;
		
				case strpos($line,"\$config['servertype']"):
		            fwrite($handle,  str_replace("\$config['servertype'] = \"pop3\"","\$config['servertype'] = ".'"'.$settings->email_servertype.'"',$line ));
		           	break;
		
				case strpos($line,"\$config['ssl']"):
					$enable = $settings->email_ssl == 0? 'false':'true';
			        fwrite($handle,  str_replace("\$config['ssl'] = false","\$config['ssl'] = ".$enable,$line ));
			        break;
					
	            default:
	            	fwrite($handle, $line );
	        }
	    }

	}
	
	/**
	 * checks the i18n folder to see what folders we have available
	 */
	private function _get_i18n()
	{
		$locales = array();
		
		// i18n path
		$i18n_path = APPPATH.'i18n/';
		
		// i18n folder
		$i18n_folder = @ opendir($i18n_path);
		
		if ( !$i18n_folder )
			return false;
		
		while ( ($i18n_dir = readdir($i18n_folder)) !== false )
		{			
			if ( is_dir($i18n_path.$i18n_dir) && is_readable($i18n_path.$i18n_dir) )
			{				
				// Strip out .  and .. and any other stuff
				if ( $i18n_dir{0} == '.' || $i18n_dir == '..'
				 	|| $i18n_dir ==  '.DS_Store' || $i18n_dir == '.git')
					continue;
				
				$locale = explode("_", $i18n_dir);
				if ( count($locale) < 2 )
					continue;
					
				$directories[$i18n_dir] = locale::language($locale[0])." (".$locale[1].")";
			}
		}
		
		if ( is_dir( $i18n_dir ) )
			@closedir( $i18n_dir );
		
		return $directories;
	}
}
