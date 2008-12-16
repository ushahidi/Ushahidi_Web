<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller handles requests for SMS/ Email alerts
 */

class Alerts_Controller extends Main_Controller {

    function __construct()
    {
        parent::__construct();
        $this->session = Session::instance();
    }

    public function index()
    {
        // Create new session
        $this->session->create();
		
        $this->template->header->this_page = 'alerts';
        $this->template->content = new View('alerts');
		
		// Display news feeds?
		$this->template->content->allow_feed = Kohana::config('settings.allow_feed');
		
        // Retrieve default country, latitude, longitude
        $default_country = Kohana::config('settings.default_country');
		
        // Retrieve Country Cities
        $this->template->content->cities = $this->_get_cities($default_country);
		
	// setup and initialize form field names
        $form = array (
                'alert_mobile'      => '',
                'alert_mobile_yes'      => '',
                'alert_email'      => '',
                'alert_email_yes'      => '',
                'alert_lat'    => '',
                'alert_lon'  => ''
             );

        // copy the form as errors, so the errors will be stored with keys
        // corresponding to the form field names
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
            if ( !empty($_POST['alert_mobile']) || isset($_POST['alert_mobile_yes']) )
            {
                $post->add_rules('alert_mobile', 'required', 'numeric', 'length[6,20]');
            }
			
            if ( !empty($_POST['alert_email']) || isset($_POST['alert_email_yes']) )
            {
                $post->add_rules('alert_email', 'required', 'email', 'length[3,64]');
            }
			
            if ( empty($_POST['alert_email']) && empty($_POST['alert_mobile']) )
            {
                $post->add_error('alert_mobile','one_required');
                $post->add_error('alert_email','one_required');
            }
			
            $post->add_rules('alert_lat','required','between[-90,90]'); // Validate for maximum and minimum latitude values
            $post->add_rules('alert_lon','required','between[-180,180]'); // Validate for maximum and minimum longitude values
			
            // Add a callback, to validate the mobile phone/email (See the methods below)
            $post->add_callbacks('alert_mobile', array($this, 'mobile_check'));
            $post->add_callbacks('alert_email', array($this, 'email_check'));
			
			
            // Test to see if things passed the rule checks
            if ($post->validate())
            {
                // Yes! everything is valid

                $this->session->set('alert_mobile', $post->alert_mobile);
                $this->session->set('alert_email', $post->alert_email);
				
                url::redirect('alerts/confirm');		            
            }
            // No! We have validation errors, we need to show the form again, with the errors
            else
            {
                // repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

                // populate the error fields, if any
                $errors = arr::overwrite($errors, $post->errors('alerts'));
                $form_error = TRUE;
            }
        }
        else
        {
            $form['alert_lat'] = Kohana::config('settings.default_lat');
            $form['alert_lon'] = Kohana::config('settings.default_lon');
        }
		
        $this->template->content->form = $form;
        $this->template->content->errors = $errors;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
		
        // Javascript Header
        $this->template->header->map_enabled = TRUE;
        $this->template->header->js = new View('alerts_js');
        $this->template->header->js->default_map = Kohana::config('settings.default_map');
        $this->template->header->js->default_zoom = Kohana::config('settings.default_zoom');
        $this->template->header->js->latitude = $form['alert_lat'];
        $this->template->header->js->longitude = $form['alert_lon'];
    }
	
    /**
     * Alerts Confirmation Page
     */
    function confirm ()
    {
        $this->template->header->this_page = 'alerts';
        $this->template->content = new View('alerts_confirm');
		if (isset($_SESSION['alert_mobile']) && isset($_SESSION['alert_email'])) {
			$this->template->content->alert_mobile = $_SESSION['alert_mobile'];
	        $this->template->content->alert_email = $_SESSION['alert_email'];
		}
    }
	
    /*
     * Retrieves Previously Cached Geonames Cities
     */
    private function _get_cities()
    {
        $cities = ORM::factory('city')->orderby('city', 'asc')->find_all();
        $city_select = array('' => Kohana::lang('ui_main.alerts_select_city'));
        foreach ($cities as $city) {
            $city_select[$city->city_lon .  "," . $city->city_lat] = $city->city;
        }
        return $city_select;
    }
	
    /*
     * Checks to see if a previous alert has been set for the mobile phone
     */
    public function mobile_check(Validation $post)
    {
        // If add->rules validation found any errors, get me out of here!
        if (array_key_exists('alert_mobile', $post->errors()) 
            || array_key_exists('alert_lat', $post->errors()) 
            || array_key_exists('alert_lon', $post->errors()))
            return;

        // Now check for similar alert in system
        $mobile_check = ORM::factory('alert')
            ->where('alert_type', '1')
            ->where('alert_recipient', $post->alert_mobile)
            ->where('alert_lat', $post->alert_lat)
            ->where('alert_lon', $post->alert_lon)->find();
        
        if ( $mobile_check->id )
        {
            // Add a validation error, this will cause $post->validate() to return FALSE
            $post->add_error( 'alert_mobile', 'mobile_check');
        }
    }
	
    /*
     * Checks to see if a previous alert has been set for the email address
     */
    public function email_check(Validation $post)
    {
        // If add->rules validation found any errors, get me out of here!
        if (array_key_exists('alert_email', $post->errors()) 
            || array_key_exists('alert_lat', $post->errors()) 
            || array_key_exists('alert_lon', $post->errors()))
            return;

        // Now check for similar alert in system
        $email_check = ORM::factory('alert')
            ->where('alert_type', '2')
            ->where('alert_recipient', $post->alert_email)
            ->where('alert_lat', $post->alert_lat)
            ->where('alert_lon', $post->alert_lon)->find();

        if ( $email_check->id )
        {
            // Add a validation error, this will cause $post->validate() to return FALSE
            $post->add_error( 'alert_email', 'email_check');
        }
    }
}
