<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller handles requests for SMS/ Email alerts
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Alerts Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Alerts_Controller extends Main_Controller 
{
    const MOBILE_ALERT = 1;
	const EMAIL_ALERT = 2;
	
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
		
		// Setup and initialize form field names
        $form = array (
                'alert_mobile' => '',
                'alert_mobile_yes' => '',
                'alert_email' => '',
                'alert_email_yes' => '',
                'alert_lat' => '',
                'alert_lon' => ''
        	);

        // Copy the form as errors, so the errors will be stored with keys
        // corresponding to the form field names
        $errors = $form;
        $form_error = FALSE;
        $form_saved = FALSE;
		
        // check, has the form been submitted, if so, setup validation
        if ($_POST)
        {
            // Instantiate Validation, use $post, so we don't overwrite 
			// $_POST fields with our own things
            $post = new Validation($_POST);

            //  Add some filters
            $post->pre_filter('trim', TRUE);
			
            // Add some rules, the input field, followed by a list of checks, 
			// carried out in order
            if (!empty($_POST['alert_mobile']) || isset($_POST['alert_mobile_yes']))
            {
                $post->add_rules('alert_mobile', 'required', 'numeric', 'length[6,20]');
            }
			
            if (!empty($_POST['alert_email']) || isset($_POST['alert_email_yes']))
            {
                $post->add_rules('alert_email', 'required', 'email', 'length[3,64]');
            }
			
            if (empty($_POST['alert_email']) && empty($_POST['alert_mobile']))
            {
                $post->add_error('alert_mobile', 'one_required');
                $post->add_error('alert_email', 'one_required');
            }
			
			// Validate for maximum and minimum latitude values
            $post->add_rules('alert_lat', 'required', 'between[-90,90]'); 
            
			// Validate for maximum and minimum longitude values
			$post->add_rules('alert_lon', 'required', 'between[-180,180]'); 
			
            // Add a callback, to validate the mobile phone/email (See the methods below)
            $post->add_callbacks('alert_mobile', array($this, 'mobile_check'));
            $post->add_callbacks('alert_email', array($this, 'email_check'));
			
			
            // Test to see if things passed the rule checks
            if ($post->validate())
            {
                // Yes! everything is valid
				// Save alert and send out confirmation code
                $email_confirmation_saved = FALSE;
                $sms_confirmation_saved = FALSE;

				if (!empty($post->alert_mobile))
				{
        			$sms_confirmation_saved =
						$this->_send_mobile_alert($post->alert_mobile);
				}

				if (!empty($post->alert_email))
				{
					$email_confirmation_saved =
						$this->_send_email_alert($post->alert_email);			
				}

                $this->session->set('alert_mobile', $post->alert_mobile);
                $this->session->set('alert_email', $post->alert_email);
				$this->session->set('sms_confirmation_saved',
									$sms_confirmation_saved);
                $this->session->set('email_confirmation_saved',
									$email_confirmation_saved);
                
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
        $this->template->content = new View('alerts_confirm');

		if (isset($_SESSION['alert_mobile']) && isset($_SESSION['alert_email']))
		{
			$this->template->content->alert_mobile = $_SESSION['alert_mobile'];
			$this->template->content->alert_email = $_SESSION['alert_email'];
		}

		$this->template->content->email_confirmation_saved =
			isset($_SESSION['email_confirmation_saved']) 
			    ? $_SESSION['email_confirmation_saved'] : FALSE;
		$this->template->content->sms_confirmation_saved =
			isset($_SESSION['sms_confirmation_saved']) 
			    ? $_SESSION['sms_confirmation_saved'] : FALSE;
    }
    
    
    /**
     * Verifies a previously sent alert confirmation code
     * 
     * @param string $code
     */
    public function verify ( )
    {   // INITIALIZE the content's section of the view
        $this->template->content = new View('alerts_verify');
        $this->template->header->this_page = 'alerts';
        
        // IF data has been posted to the request ...
        if ($post = $this->input->post())
        {   // TRY to update the model and save it
            try
            {   // CREATE a local variable representing the alert identified with _POST
                $Code = ORM::factory('alert', $post['alert_code']);
                
                // IF the code couldn't be loaded ...
                if (! $Code->loaded)
                {   // THROW an exception
                    throw new Kohana_Exception('Code not found');
                }    
                
                if ($Code->alert_confirmed)
                {   // SET the errno message to previously confirmed
                    $this->template->content->errno = Alert_Model::ER_CODE_ALREADY_VERIFIED;
                }
                    
                // SET the alert as confirmed, and save it
                $Code->set('alert_confirmed', 1)->save();
            }
            // CATCH any exceptions that might occur ...
            catch (Exception $e)
            {   // SET the errno var to not found, indicating the likley error
                $this->template->content->errno = Alert_Model::ER_CODE_NOT_FOUND;
            }
        }
        // ELSE, no data was posted to the request ...
        else
        {   // SET the errno var to not found, indicating the likley error
            $this->template->content->errno = Alert_Model::ER_CODE_NOT_FOUND;
        }
        
    } // END function ccverify
    
	
    /*
     * Retrieves Previously Cached Geonames Cities
     */
    private function _get_cities()
    {
        $cities = ORM::factory('city')->orderby('city', 'asc')->find_all();
        $city_select = array('' => Kohana::lang('ui_main.alerts_select_city'));
        foreach ($cities as $city) 
		{
            $city_select[$city->city_lon.",".$city->city_lat] = $city->city;
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
            				->where('alert_type', self::MOBILE_ALERT)
            				->where('alert_recipient', $post->alert_mobile)
            				->where('alert_lat', $post->alert_lat)
            				->where('alert_lon', $post->alert_lon)
							->find();
        
        if ($mobile_check->id)
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
            			->where('alert_type', self::EMAIL_ALERT)
            			->where('alert_recipient', $post->alert_email)
            			->where('alert_lat', $post->alert_lat)
            			->where('alert_lon', $post->alert_lon)
						->find();

        if ($email_check->id)
        {
            // Add a validation error, this will cause $post->validate() to return FALSE
            $post->add_error( 'alert_email', 'email_check');
        }
    }

	/**
	 * Creates a confirmation code for use with email or sms verification 
	 */
	private function _mk_code()
	{
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$max = strlen($chars)-1;
		$code_length = 6;
		$code = NULL;

        // Generate unique codes only
		while(1)
		{
			for($i = 0; $i < $code_length; $i++) 
			{
				$code.=$chars[mt_rand(0, $max)];
			}

			$code_check = ORM::factory('alert')
							->where('alert_code', $code)->find();

			if (!$code_check->id)
				break;
		}

		return $code;
	}

	private function _send_mobile_alert($alert_mobile)
	{
        // Instantiate Validation, use $post, so we don't overwrite 
        // $_POST fields with our own things
        $post = new Validation($_POST);
        
		$alert_code = $this->_mk_code();
					
		$settings = ORM::factory('settings', 1);

		if (!$settings->loaded)
			return FALSE;

		// Get SMS Numbers
		if (!empty($settings->sms_no3)) 
		{
			$sms_from = $settings->sms_no3;
		}
		elseif (!empty($settings->sms_no2)) 
		{
			$sms_from = $settings->sms_no2;
		}
		elseif (!empty($settings->sms_no1)) 
		{
			$sms_from = $settings->sms_no1;
		}
		else
		{
			$sms_from = "000";// User needs to set up an SMS number
		}

		$sms = new Clickatell();
		$sms->api_id = $settings->clickatell_api;
		$sms->user = $settings->clickatell_username;
		$sms->password = $settings->clickatell_password;
		$sms->use_ssl = false;
		$sms->sms();
		$message = "Your alerts confirmation code
				is: ".$alert_code." This code is NOT case sensitive";
	
		if ($sms->send($alert_mobile, $sms_from, $message) == "OK")
		{
            // Instantiate Validation, use $post, so we don't overwrite 
            // $_POST fields with our own things
            $post = new Validation($_POST);
            
            
			$alert = ORM::factory('alert');
			$alert->alert_type = self::MOBILE_ALERT;
			$alert->alert_recipient = $alert_mobile;
			$alert->alert_code = $alert_code;
			$alert->alert_lon = $post->alert_lon;
			$alert->alert_lat = $post->alert_lat;
			$alert->save();

			return TRUE;
		}

		return FALSE;
	}

	private function _send_email_alert($alert_email)
	{
        // Instantiate Validation, use $post, so we don't overwrite 
        // $_POST fields with our own things
        $post = new Validation($_POST);
        
		$alert_code = $this->_mk_code();
		
		$config = kohana::config('alerts');
		$settings = kohana::config('settings');
		
		$to = $alert_email;
		$from = $config['alerts_email'];
		$subject = $settings['site_name'].' alerts - verification';
		$message = 'Please follow '.url::site().'alerts/verify/'.$alert_code.
				   ' to confirm your alert request';

		if (email::send($to, $from, $subject, $message, TRUE) == 1)
		{
            // Instantiate Validation, use $post, so we don't overwrite 
            // $_POST fields with our own things
            $post = new Validation($_POST);
            
            
			$alert = ORM::factory('alert');
			$alert->alert_type = self::EMAIL_ALERT;
			$alert->alert_recipient = $alert_email;
			$alert->alert_code = $alert_code;
			$alert->alert_lon = $post->alert_lon;
			$alert->alert_lat = $post->alert_lat;
			$alert->save();
			
			return TRUE;
		}

		return FALSE;
	}	
}
