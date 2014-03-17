<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * SMS helper class
 *
 * @package     Ushahidi
 * @category    Helpers
 * @author      Ushahidi Team
 * @copyright   (c) 2008 Ushahidi Team
 * @license     http://www.ushahidi.com/license.html
 */

class sms_Core {

	/**
	 * Send The SMS Message Using Default Provider
	 *
	 * @param to mixed	The destination address.
	 * @param from mixed  The source/sender address
	 * @param text mixed  The text content of the message
	 * @return mixed/bool (returns TRUE if sent FALSE or other text for fail)
	 */
	public static function send($to = NULL, $from = NULL, $message = NULL)	
	{
		if ( ! $to OR ! $message)
			return "Missing Recipients and/or Message";
		
		// 1. Do we have an SMS Provider?
		$provider = Kohana::config("settings.sms_provider");
		if ($provider)
		{
			// 2. Does the plugin exist, and if so, is it active?
			$plugin = ORM::factory("plugin")
				->where("plugin_name", $provider)
				->where("plugin_active", 1)
				->find();
			
			// Plugin loaded
			if ($plugin->loaded)
			{
				// 3. Does this plugin have the SMS Library in place?
				// SMS libaries should be suffixed with "_Sms_Provider"
				$class = ucfirst($provider).'_Sms_Provider';
				
				if (Kohana::find_file('libraries', $class))
				{
					$provider  = new $class;
					
					// Sanity check - Ensure all SMS providers are sub-classes of Sms_Provider_Core
					if ( ! $provider instanceof Sms_Provider_Core)
					{
						throw new Kohana_Exception('All SMS Provider libraries must be be sub-classes of Sms_Provider_Core');
					}
					
					// Proceed
					$response = $provider->send($to, $from, $message);
					
					// Return
					return $response;
				}
			}
		}
		
		return "No SMS Sending Provider In System";
	}
	
	/**
	 * Send The SMS Message Using Default Provider
	 * @param from mixed  The source/sender address
	 * @param message mixed  The text content of the message
	 * @param to mixed  Optional... 'which number the message was sent to'
	 */
	public static function add($from = NULL, $message = NULL, $to = NULL)
	{
		$from = preg_replace("#[^0-9]#", "", $from);
		$to = preg_replace("#[^0-9]#", "", $to);
		
		if ( ! $from OR ! $message)
			return "Missing Sender and/or Message";
		
		//Filters to allow modification of the values from the SMS gateway
		Event::run('ushahidi_filter.message_sms_from',$from);
		Event::run('ushahidi_filter.message_sms', $message);

		$services = new Service_Model();
		$service = $services->where('service_name', 'SMS')->find();

		if ( ! $service) 
			return FALSE;

		$reporter = ORM::factory('reporter')
		    ->where('service_id', $service->id)
		    ->where('service_account', $from)
		    ->find();

		if ( ! $reporter->loaded == TRUE)
		{
			// get default reporter level (Untrusted)
			$level = ORM::factory('level')
			    ->where('level_weight', 0)
			    ->find();
			
			$reporter->service_id = $service->id;
			$reporter->level_id = $level->id;
			$reporter->service_account = $from;
			$reporter->reporter_first = NULL;
			$reporter->reporter_last = NULL;
			$reporter->reporter_email = NULL;
			$reporter->reporter_phone = NULL;
			$reporter->reporter_ip = NULL;
			$reporter->reporter_date = date('Y-m-d');
			$reporter->save();
		}
		
		// Save Message
		$sms = new Message_Model();
		$sms->parent_id = 0;
		$sms->incident_id = 0;
		$sms->user_id = 0;
		$sms->reporter_id = $reporter->id;
		$sms->message_from = $from;
		$sms->message_to = $to;
		$sms->message = $message;
		$sms->message_type = 1; // Inbox
		$sms->message_date = date("Y-m-d H:i:s",time());
		$sms->service_messageid = NULL;
		$sms->save();
		
		// Notify Admin Of New Email Message
		$send = notifications::notify_admins(
			"[".Kohana::config('settings.site_name')."] ".
				Kohana::lang('notifications.admin_new_sms.subject'),
			Kohana::lang('notifications.admin_new_sms.message')
			);

		// Action::message_sms_add - SMS Received!
		Event::run('ushahidi_action.message_sms_add', $sms);
		
		// Auto-Create A Report if Reporter is Trusted
		$reporter_weight = $reporter->level->level_weight;
		$reporter_location = $reporter->location;
		if ($reporter_weight > 0 AND $reporter_location)
		{
			$incident_title = text::limit_chars($message, 50, "...", false);
			// Create Incident
			$incident = new Incident_Model();
			$incident->location_id = $reporter_location->id;
			$incident->incident_title = $incident_title;
			$incident->incident_description = $message;
			$incident->incident_date = $sms->message_date;
			$incident->incident_dateadd = date("Y-m-d H:i:s",time());
			$incident->incident_active = 1;
			if ($reporter_weight == 2)
			{
				$incident->incident_verified = 1;
			}
			$incident->save();
			
			// Update Message with Incident ID
			$sms->incident_id = $incident->id;
			$sms->save();
			
			// Save Incident Category
			$trusted_categories = ORM::factory("category")
				->where("category_trusted", 1)
				->find();
			if ($trusted_categories->loaded)
			{
				$incident_category = new Incident_Category_Model();
				$incident_category->incident_id = $incident->id;
				$incident_category->category_id = $trusted_categories->id;
				$incident_category->save();
			}
		}
		
		return TRUE;
	}
}
