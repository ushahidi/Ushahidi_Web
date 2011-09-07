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
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Alerts_Controller extends Main_Controller {
	
	const MOBILE_ALERT = 1;
	const EMAIL_ALERT = 2;    

	public function __construct()
	{
		parent::__construct();
	}

    public function index()
    {
		$this->template->header->this_page = $this->themes->this_page = 'alerts';
		$this->template->content = new View('alerts');
		
		// Load the alert radius map view
		$alert_radius_view = new View('alert_radius_view');
		$alert_radius_view->show_usage_info = TRUE;
		$alert_radius_view->enable_find_location = TRUE;
		
		$this->template->content->alert_radius_view = $alert_radius_view;

		// Display news feeds?
		$this->template->content->allow_feed = Kohana::config('settings.allow_feed');

		// Display Mobile Option?
		$this->template->content->show_mobile = TRUE;
		$settings = ORM::factory('settings', 1);
		
		if ( ! Kohana::config("settings.sms_provider"))
		{
			// Hide Mobile
			$this->template->content->show_mobile = FALSE;
		}

		// Retrieve default country, latitude, longitude
		$default_country = Kohana::config('settings.default_country');

		// Retrieve Country Cities
		$this->template->content->cities = $this->_get_cities($default_country);

		// Get all active top level categories
		$this->template->content->categories = $this->get_categories('foo');

		// Setup and initialize form field names
		$form = array (
			'alert_mobile' => '',
			'alert_mobile_yes' => '',
			'alert_email' => '',
			'alert_email_yes' => '',
			'alert_lat' => '',
			'alert_lon' => '',
			'alert_radius' => ''
		);
		
		if ($this->user)
		{
			$form['alert_email'] = $this->user->email;
		}
	
		// Copy the form as errors, so the errors will be stored with keys
		// corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
        
		// If there is a post and $_POST is not empty
		if ($post = $this->input->post())
		{
			if ($this->_valid($post))
			{
                // Yes! everything is valid
                // Save alert and send out confirmation code

				if ( ! empty($post->alert_mobile))
				{
					$this->_send_mobile_alert($post);
				}

				if ( ! empty($post->alert_email))
				{
					$this->_send_email_alert($post);
				}

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
			$form['alert_radius'] = 20;
			$form['alert_category'] = array();
        }
        
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;

		// Javascript Header
		$this->themes->map_enabled = TRUE;
		$this->themes->js = new View('alerts_js');
		$this->themes->treeview_enabled = TRUE;
		$this->themes->js->default_map = Kohana::config('settings.default_map');
		$this->themes->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->themes->js->latitude = $form['alert_lat'];
		$this->themes->js->longitude = $form['alert_lon'];

		// Rebuild Header Block
		$this->template->header->header_block = $this->themes->header_block();
    }

    
	/**
	 * Alerts Confirmation Page
	 */
	function confirm()
	{
		$this->template->header->this_page = 'alerts';
		$this->template->content = new View('alerts_confirm');

		$this->template->content->alert_mobile = (isset($_SESSION['alert_mobile']) AND ! empty($_SESSION['alert_mobile'])) 
			? $_SESSION['alert_mobile'] 
			: "";
        
		$this->template->content->alert_email = (isset($_SESSION['alert_email']) AND ! empty($_SESSION['alert_email']))
			? $_SESSION['alert_email'] 
			: "";
            
		// Display Mobile Option?
		$this->template->content->show_mobile = TRUE;
		$settings = ORM::factory('settings', 1);
		
		if ( ! Kohana::config("settings.sms_provider"))
		{
			// Hide Mobile
			$this->template->content->show_mobile = FALSE;
		}

		// Rebuild Header Block
		$this->template->header->header_block = $this->themes->header_block();
	}
    
    
	/**
	 * Verifies a previously sent alert confirmation code
	 */
	public function verify()
	{
		// Define error codes for this view.
		define("ER_CODE_VERIFIED", 0);
		define("ER_CODE_NOT_FOUND", 1);
		define("ER_CODE_ALREADY_VERIFIED", 3);
        
		$code = (isset($_GET['c']) AND !empty($_GET['c'])) ? $_GET['c'] : "";
            
		$email = (isset($_GET['e']) AND !empty($_GET['e'])) ? $_GET['e'] : "";
        
		// INITIALIZE the content's section of the view
		$this->template->content = new View('alerts_verify');
		$this->template->header->this_page = 'alerts';

		$filter = " ";
		$missing_info = FALSE;
		
		if ($_POST AND isset($_POST['alert_code']) AND ! empty($_POST['alert_code']))
		{
			if (isset($_POST['alert_mobile']) AND ! empty($_POST['alert_mobile']))
			{
				$filter = "alert.alert_type=1 AND alert_code='".strtoupper($_POST['alert_code'])."' AND alert_recipient='".$_POST['alert_mobile']."' ";
			}
			elseif (isset($_POST['alert_email']) AND ! empty($_POST['alert_email']))
			{
				$filter = "alert.alert_type=2 AND alert_code='".$_POST['alert_code']."' AND alert_recipient='".$_POST['alert_email']."' ";
			}
			else
			{
				$missing_info = TRUE;
			}
		}
		else
		{
			if (empty($code) OR empty($email))
			{
				$missing_info = TRUE;
			}
			else
			{
				$filter = "alert.alert_type=2 AND alert_code='".$code."' AND alert_recipient='".$email."' ";
			}
		}
        
		if ( ! $missing_info)
		{
			$alert_check = ORM::factory('alert')
								->where($filter)
								->find();

			// IF there was no result
			if ( ! $alert_check->loaded)
			{
				$this->template->content->errno = ER_CODE_NOT_FOUND;
			}
			elseif ($alert_check->alert_confirmed)
			{
				$this->template->content->errno = ER_CODE_ALREADY_VERIFIED;
			}
			else 
			{
				// SET the alert as confirmed, and save it
				$alert_check->set('alert_confirmed', 1)->save();
				$this->template->content->errno = ER_CODE_VERIFIED;
			}
		}
		else
		{
			$this->template->content->errno = ER_CODE_NOT_FOUND;
		}
        
		// Rebuild Header Block
		$this->template->header->header_block = $this->themes->header_block();
	} // END function verify

	/**
	 * Unsubscribes alertee using alertee's confirmation code
	 * 
	 * @param string $code
	 */
	public function unsubscribe($code = NULL)
	{
		$this->template->content = new View('alerts_unsubscribe');
		$this->template->header->this_page = 'alerts';
		$this->template->content->unsubscribed = FALSE;
        
		// XXX Might need to validate $code as well
		if ($code != NULL)
		{
			Alert_Model::unsubscribe($code);
			$this->template->content->unsubscribed = TRUE;
		}
        
		// Rebuild Header Block
		$this->template->header->header_block = $this->themes->header_block();
    }
     
	/**
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

	private function _send_mobile_alert($post)
	{
		// For Mobile Alerts, Confirmation Code
		$alert_mobile = $post->alert_mobile;
		$alert_lon = $post->alert_lon;
		$alert_lat = $post->alert_lat;
		$alert_radius = $post->alert_radius;

		// Should be 6 distinct characters
		$alert_code = text::random('distinct', 8);
          
		$settings = ORM::factory('settings', 1);

		if ( ! $settings->loaded)
			return FALSE;

        // Get SMS Numbers
		if ( ! empty($settings->sms_no3)) 
		{
			$sms_from = $settings->sms_no3;
		}
		elseif ( ! empty($settings->sms_no2)) 
		{
			$sms_from = $settings->sms_no2;
		}
		elseif ( ! empty($settings->sms_no1)) 
		{
			$sms_from = $settings->sms_no1;
		}
		else
		{
			$sms_from = "000";// User needs to set up an SMS number
		}

		$message = Kohana::lang('ui_admin.confirmation_code').$alert_code
			.'.'.Kohana::lang('ui_admin.not_case_sensitive');
		
		if (sms::send($alert_mobile, $sms_from, $message) === true)
		{
			$alert = ORM::factory('alert'); 
			$alert->alert_type = self::MOBILE_ALERT;
			$alert->alert_recipient = $alert_mobile;
			$alert->alert_code = $alert_code;
			$alert->alert_lon = $alert_lon;
			$alert->alert_lat = $alert_lat;
			$alert->alert_radius = $alert_radius;
			if ($this->user)
			{
				$alert->user_id = $this->user->id;
			}
			$alert->save();

			$this->_add_categories($alert, $post);

			return TRUE;
		}

		return FALSE;
    }

	/**
	 * Sends an email alert
	 */
	private function _send_email_alert($post)
	{
		// Email Alerts, Confirmation Code
		$alert_email = $post->alert_email;
		$alert_lon = $post->alert_lon;
		$alert_lat = $post->alert_lat;
		$alert_radius = $post->alert_radius;

		$alert_code = text::random('alnum', 20);

		$settings = kohana::config('settings');

		$to = $alert_email;
		$from = array();
		
		$from[] = ($settings['alerts_email']) 
			? $settings['alerts_email']
			: $settings['site_email'];
		
		$from[] = $settings['site_name'];
		$subject = $settings['site_name']." ".Kohana::lang('alerts.verification_email_subject');
		$message = Kohana::lang('alerts.confirm_request').url::site().'alerts/verify/?c='.$alert_code."&e=".$alert_email;

		if (email::send($to, $from, $subject, $message, TRUE) == 1)
		{
			$alert = ORM::factory('alert');
			$alert->alert_type = self::EMAIL_ALERT;
			$alert->alert_recipient = $alert_email;
			$alert->alert_code = $alert_code;
			$alert->alert_lon = $alert_lon;
			$alert->alert_lat = $alert_lat;
			$alert->alert_radius = $alert_radius;
			if ($this->user)
			{
				$alert->user_id = $this->user->id;
			}
			$alert->save();

			$this->_add_categories($alert, $post);

			return TRUE;
		}

		return FALSE;
	}   


	private function _add_categories(Alert_Model $alert, $post)
	{
		if (isset($post->alert_category))
		{
			foreach ($post->alert_category as $item)
			{
				$category = ORM::factory('category')->find($item);
				
				if($category->loaded)
				{
					$alert_category = new Alert_Category_Model();
					$alert_category->alert_id = $alert->id;
					$alert_category->category_id = $category->id;
					$alert_category->save();
				}
			}
		}
	}

	private function _valid(array & $post)
	{
		// Create a new alert
		$alert = ORM::factory('alert');         
            
		// Test to see if things passed the rule checks
		if ( ! $alert->validate($post))
		{
			return false;
		}

		// Instantiate Validation
		$valid = Validation::factory($_POST);

		// Add some filters
		$valid->pre_filter('trim', TRUE);
		$valid->add_rules('alert_category.*', 'numeric');

		if ( ! isset($_POST['alert_category']))
		{
			// That's OK.
			$valid->alert_category = "";
		}

		if ($valid->validate())
		{
			return true;
		}

		return false;
	}
}
