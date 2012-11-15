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

	/**
	 * Sends an alert to a mobile phone
	 *
	 * @param Validation_Core $post
	 * @param Alert_Model $alert
	 * @return bool
	 */
	public static function _send_mobile_alert($post, $alert)
	{
		if ( ! $post instanceof Validation_Core AND !$alert instanceof Alert_Model)
		{
			throw new Kohana_Exception('Invalid parameter types');
		}

		// Should be 8 distinct characters
		$alert_code = text::random('distinct', 8);

		$sms_from = self::_sms_from();

		$message = Kohana::lang('ui_admin.confirmation_code').$alert_code
			.'.'.Kohana::lang('ui_admin.not_case_sensitive');
	
		if (sms::send($post->alert_mobile, $sms_from, $message) === true)
		{
			$alert->alert_type = self::MOBILE_ALERT;
			$alert->alert_recipient = $post->alert_mobile;
			$alert->alert_code = $alert_code;
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
	 *
	 * @param Validation_Core $post
	 * @param Alert_Model $alert
	 * @return bool 
	 */
	public static function _send_email_alert($post, $alert)
	{
		if ( ! $post instanceof Validation_Core AND !$alert instanceof Alert_Model)
		{
			throw new Kohana_Exception('Invalid parameter types');
		}

		// Email Alerts, Confirmation Code
		$alert_email = $post->alert_email;
		$alert_code = text::random('alnum', 20);

		$settings = kohana::config('settings');

		$to = $alert_email;
		$from = array();
		
		$from[] = ($settings['alerts_email']) 
			? $settings['alerts_email']
			: $settings['site_email'];
		
		$from[] = $settings['site_name'];
		$subject = $settings['site_name']." ".Kohana::lang('alerts.verification_email_subject');
		

		$message = Kohana::lang('ui_admin.confirmation_code').$alert_code."<br><br>";
		if(!empty($post->alert_category))
		{
			$message .= Kohana::lang('alerts.alerts_subscribed')."\n";
			foreach ($post->alert_category as $item)
			{
				$category = ORM::factory('category')
								->where('id',$item)
								->find();

				if($category->loaded)
				{

					$message .= "<ul><li>".$category->category_title ."</li></ul>";
				}
			}
			
		}

		$message .= Kohana::lang('alerts.confirm_request').url::site().'alerts/verify?c='.$alert_code."&e=".$alert_email;

		if (email::send($to, $from, $subject, $message, TRUE) == 1)
		{
			$alert->alert_type = self::EMAIL_ALERT;
			$alert->alert_recipient = $alert_email;
			$alert->alert_code = $alert_code;
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
	 *
	 * @param string $message_from Subscriber MSISDN (mobile phone number)
	 * @param string $message_description Message content
	 * @return bool
	 */
	public static function mobile_alerts_register($message_from, $message_description)
	{
		// Preliminary validation
		if (empty($message_from) OR empty($message_description))
		{
			// Log the error
			Kohana::log('info', 'Insufficient data to proceed with subscription via mobile phone');
			
			// Return
			return FALSE;
		}

		//Get the message details (location, category, distance)
		$message_details = explode(" ",$message_description);
		$message = $message_details[1].",".Kohana::config('settings.default_country');
		$geocoder = map::geocode($message);
			
		// Generate alert code
		$alert_code = text::random('distinct', 8);

		// POST variable with items to save
		$post = array(
			'alert_type'=> self::MOBILE_ALERT,
			'alert_mobile'=>$message_from,
			'alert_code'=>$alert_code,
			'alert_lon'=> isset($geocoder['lon']) ? $geocoder['lon'] : FALSE,
			'alert_lat'=> isset($geocoder['lat']) ? $geocoder['lat'] : FALSE,
			'alert_radius'=>'20',
			'alert_confirmed'=>'1'
		);

		// Create ORM object for the alert and validate
		$alert_orm = new Alert_Model();
		if ($alert_orm->validate($post))
		{
			return self::_send_mobile_alert($post, $alert_orm);
		}

		return FALSE;

	}

	/**
	 * This handles unsubscription from alerts via the mobile phone
	 * 
	 * @param string $message_from Phone number of subscriber
	 * @param string $message_description Message content
	 * @return bool
	 */
	public static function mobile_alerts_unsubscribe($message_from, $message_description)
	{
		// Validate parameters
		
		if (empty($message_from) OR empty($message_description))
		{
			// Log the error
			Kohana::log('info', 'Cannot unsubscribe from alerts via the mobile phone - insufficient data');
			
			// Return
			return FALSE;
		}

		$sms_from = self::_sms_from();

		$site_name = $settings->site_name;
		$message = Kohana::lang('ui_admin.unsubscribe_message').' ' .$site_name;

		if (sms::send($message_from, $sms_from, $message) === true)
		{
			// Fetch all alerts with the specified code
			$alerts = ORM::factory('alert')
					->where('alert_recipient', $message_from)
					->find_all();
		
			foreach ($alerts as $alert)
			{
				// Delete all alert categories with the specified phone number
				ORM::factory('alert_category')
					->where('alert_id', $alert->id)
					->delete_all();

				$alert->delete();
			}
			return TRUE;
		}
		return FALSE;	
	}


	/* This handles saving alert categories that a subscriber has subscribed for 
	 *
	 * @param $Alert_Model $alert 
	*/

	private static function _add_categories(Alert_Model $alert, $post)
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
	
	private static function _sms_from()
	{
		
		$settings = Kohana::config('settings');
		
		// Get SMS Numbers
		if ( ! empty($settings['sms_no3'])) 
		{
			$sms_from = $settings['sms_no3'];
		}
		elseif ( ! empty($settings['sms_no2'])) 
		{
			$sms_from = $settings['sms_no2'];
		}
		elseif ( ! empty($settings['sms_no1'])) 
		{
			$sms_from = $settings['sms_no1'];
		}
		else
		{
			$sms_from = "000";// User needs to set up an SMS number
		}
		
		return $sms_from;
	}

}
