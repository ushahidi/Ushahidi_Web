<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Alerts helper class
 *
 * @package     Ushahidi
 * @category    Helpers
 * @author      Ushahidi Team
 * @copyright   (c) 2008 Ushahidi Team
 * @license     http://www.ushahidi.com/license.html
 */

class alert_Core {

	const MOBILE_ALERT = 1;
	const EMAIL_ALERT = 2;

	public static function _send_mobile_alert($post)
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
			if (isset($_SESSION['auth_user']))
			{
				$alert->user_id = $_SESSION['auth_user']->id;
			}
			$alert->save();

			self::_add_categories($alert, $post);

			return TRUE;
		}

		return FALSE;
    }

	/**
	 * Sends an email alert
	 */
	public static function _send_email_alert($post)
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
		$message = Kohana::lang('alerts.confirm_request').url::site().'alerts/verify?c='.$alert_code."&e=".$alert_email;

		if (email::send($to, $from, $subject, $message, TRUE) == 1)
		{
			$alert = ORM::factory('alert');
			$alert->alert_type = self::EMAIL_ALERT;
			$alert->alert_recipient = $alert_email;
			$alert->alert_code = $alert_code;
			$alert->alert_lon = $alert_lon;
			$alert->alert_lat = $alert_lat;
			$alert->alert_radius = $alert_radius;
			if (isset($_SESSION['auth_user']))
			{
				$alert->user_id = $_SESSION['auth_user']->id;
			}
			$alert->save();

			self::_add_categories($alert, $post);

			return TRUE;
		}

		return FALSE;
	}   


	/**
	 * This handles sms alerts subscription via phone
	 * @params alert,location (city) - required
	 * @params distance, category - optional
	 */
	public function mobile_alerts_register($message_from, $message_description)
	{

		/**
		 * Get the message details (location, category, distance)
		 */
			$message_details = explode(" ",$message_description);
			$message = $message_details[1].",".Kohana::config('settings.default_country');
			$geocoder = map::geocode($message);
			
		/**
		 * Generate alert code
		 */
			$alert_code = text::random('distinct', 8);

		/* POST variable with items to save */
			
			$post = array(
				'alert_type'=> self::MOBILE_ALERT,
				'alert_mobile'=>$message_from,
				'alert_code'=>$alert_code,
				'alert_lon'=>$geocoder['lon'],
				'alert_lat'=>$geocoder['lat'],
				'alert_radius'=>'20'
			);

			//convert the array to object
			$p = (object) $post;
		/** 
		 * Save alert details
		 */

		$register_sms_alerts = self::_send_mobile_alert($p);																			    

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



}
