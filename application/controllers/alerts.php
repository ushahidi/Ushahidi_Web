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

	public function __construct()
	{
		parent::__construct();
	}

    public function index()
    {

		// First, are we allowed to subscribe for alerts via web?
		if ( ! Kohana::config('settings.allow_alerts'))
		{
			url::redirect(url::site().'main');
		}

		$this->template->header->this_page = $this->themes->this_page = 'alerts';
		$this->template->content = new View('alerts/main');

		// Load the alert radius map view
		$alert_radius_view = new View('alerts/radius');
		$alert_radius_view->show_usage_info = TRUE;
		$alert_radius_view->enable_find_location = TRUE;

		$this->template->content->alert_radius_view = $alert_radius_view;


		// Display Mobile Option?
		$this->template->content->show_mobile = TRUE;

		if ( ! Kohana::config("settings.sms_provider"))
		{
			// Hide Mobile
			$this->template->content->show_mobile = FALSE;
		}

		// Retrieve default country, latitude, longitude
		$default_country = Kohana::config('settings.default_country');

		// Retrieve Country Cities
		$this->template->content->cities = $this->_get_cities($default_country);
		
		// Populate this for backwards compat
		$this->template->content->categories = array();

		// Setup and initialize form field names
		$form = array (
			'alert_mobile' => '',
			'alert_mobile_yes' => '',
			'alert_email' => '',
			'alert_email_yes' => '',
			'alert_lat' => '',
			'alert_lon' => '',
			'alert_radius' => '',
			'alert_country' => '',
			'alert_confirmed' => ''
		);

		if ($this->user)
		{
			$form['alert_email'] = $this->user->email;
		}

		// Get Countries
		$countries = array();
		foreach (ORM::factory('country')->orderby('country')->find_all() as $country)
		{
			// Create a list of all countries
			$this_country = $country->country;
			if (strlen($this_country) > 35)
			{
				$this_country = substr($this_country, 0, 35) . "...";
			}
			$countries[$country->id] = $this_country;
		}

		//Initialize default value for Alert confirmed hidden value

		$this->template->content->countries = $countries;

		// Copy the form as errors, so the errors will be stored with keys
		// corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;

		// If there is a post and $_POST is not empty
		if ($post = $this->input->post())
		{
			$alert_orm = new Alert_Model();
			if ($alert_orm->validate($post))
			{
				// Yes! everything is valid
				// Save alert and send out confirmation code

				if ( ! empty($post->alert_mobile))
				{
					alert::_send_mobile_alert($post, $alert_orm);
					$this->session->set('alert_mobile', $post->alert_mobile);
				}

				if ( ! empty($post->alert_email))
				{
					alert::_send_email_alert($post, $alert_orm);
					$this->session->set('alert_email', $post->alert_email);
				}

				url::redirect('alerts/confirm');
            }
            // No! We have validation errors, we need to show the form again, with the errors
            else
            {
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('alerts'));
				
				if (array_key_exists('alert_recipient', $post->errors('alerts')))
				{
					$errors = array_merge($errors, $post->errors('alerts'));
				}
				
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

		$this->template->content->form_error = $form_error;
		// Initialize Default Value for Hidden Field Country Name, just incase Reverse Geo coding yields no result
		$form['alert_country'] = $countries[$default_country];
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_saved = $form_saved;


		// Javascript Header
		$this->themes->map_enabled = TRUE;
		$this->themes->js = new View('alerts/alerts_js');
		$this->themes->treeview_enabled = TRUE;
		$this->themes->js->latitude = $form['alert_lat'];
		$this->themes->js->longitude = $form['alert_lon'];

		// Rebuild Header Block
		$this->template->header->header_block = $this->themes->header_block();
		$this->template->footer->footer_block = $this->themes->footer_block();
    }


	/**
	 * Alerts Confirmation Page
	 */
	public function confirm()
	{
		$this->template->header->this_page = 'alerts';
		$this->template->content = new View('alerts/confirm');

		$this->template->content->alert_mobile = (isset($_SESSION['alert_mobile']) AND ! empty($_SESSION['alert_mobile']))
			? $_SESSION['alert_mobile']
			: "";

		$this->template->content->alert_email = (isset($_SESSION['alert_email']) AND ! empty($_SESSION['alert_email']))
			? $_SESSION['alert_email']
			: "";

		// Display Mobile Option?
		$this->template->content->show_mobile = TRUE;

		if (empty($_SESSION['alert_mobile']))
		{
			// Hide Mobile
			$this->template->content->show_mobile = FALSE;
		}

		// Rebuild Header Block
		$this->template->header->header_block = $this->themes->header_block();
		$this->template->footer->footer_block = $this->themes->footer_block();
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
		$this->template->content = new View('alerts/verify');
		$this->template->header->this_page = 'alerts';

		$filter = " ";
		$missing_info = FALSE;

		if ($_POST AND isset($_POST['alert_code']) AND ! empty($_POST['alert_code']))
		{
			if (isset($_POST['alert_mobile']) AND ! empty($_POST['alert_mobile']))
			{
				$filter = "alert.alert_type=1 AND alert_code='".Database::instance()->escape_str(utf8::strtoupper($_POST['alert_code']))."' AND alert_recipient='".Database::instance()->escape_str($_POST['alert_mobile'])."' ";
			}
			elseif (isset($_POST['alert_email']) AND ! empty($_POST['alert_email']))
			{
				$filter = "alert.alert_type=2 AND alert_code='".Database::instance()->escape_str($_POST['alert_code'])."' AND alert_recipient='".Database::instance()->escape_str($_POST['alert_email'])."' ";
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
				$filter = "alert.alert_type=2 AND alert_code='".Database::instance()->escape_str($code)."' AND alert_recipient='".Database::instance()->escape_str($email)."' ";
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
		$this->template->footer->footer_block = $this->themes->footer_block();
	} // END function verify


	/**
	 * Unsubscribes alertee using alertee's confirmation code
	 *
	 * @param string $code
	 */
	public function unsubscribe($code = NULL)
	{
		$this->template->content = new View('alerts/unsubscribe');
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
		$this->template->footer->footer_block = $this->themes->footer_block();
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

}
