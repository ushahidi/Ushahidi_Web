<?php defined('SYSPATH') or die('No direct script access.');

/**
* Settings Controller
*/
class Settings_Controller extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'settings';		
	}
	
	function index()
	{
		// Display all maps
		$this->template->api_url = Kohana::config('settings.api_url_all');
		
		$this->template->content = new View('admin/settings');
		$this->template->content->title = 'Settings';
		
		// setup and initialize form field names
		$form = array
	    (
	        'site_name' => '',
			'default_map' => '',
			'api_google' => '',
			'api_yahoo' => '',
			'default_country' => '',
			'default_lat' => '',
			'default_lon' => '',
			'default_zoom' => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		
		
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
	                // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
	        $post = new Validation($_POST);

	         //  Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
			
	        $post->add_rules('site_name','required', 'length[3,200]');
			$post->add_rules('default_country', 'required', 'numeric', 'length[1,3]');
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
				$settings->site_name = $post->site_name;
				$settings->default_country = $post->default_country;
				$settings->default_map = $post->default_map;
				$settings->api_google = $post->api_google;
				$settings->api_yahoo = $post->api_yahoo;
				$settings->default_zoom = $post->default_zoom;
				$settings->default_lat = $post->default_lat;
				$settings->default_lon = $post->default_lon;
				$settings->save();
				
				// Everything is A-Okay!
				$form_saved = TRUE;
				
				// repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());
					            
	        }
	                    // No! We have validation errors, we need to show the form again, with the errors
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
				'default_map' => $settings->default_map,
				'api_google' => $settings->api_google,
				'api_yahoo' => $settings->api_yahoo,
				'default_country' => $settings->default_country,
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
				
		// Javascript Header
		$this->template->map_enabled = TRUE;
		$this->template->js = new View('admin/settings_js');
		$this->template->js->default_map = $form['default_map'];
		$this->template->js->default_zoom = $form['default_zoom'];
		$this->template->js->default_lat = Kohana::config('settings.default_lat');
		$this->template->js->default_lon = Kohana::config('settings.default_lon');
	}

	function sms()
	{
		$this->template->content = new View('admin/sms');
		$this->template->content->title = 'Settings';
	}

	function sharing()
	{
		$this->template->content = new View('admin/sharing');
		$this->template->content->title = 'Settings';
	}

}