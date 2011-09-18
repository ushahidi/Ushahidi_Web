<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Helper library for the Clickatell API
 *
 * @package     Clickatell SMS 
 * @category    Plugins
 * @author      Ushahidi Team
 * @copyright   (c) 2008-2011 Ushahidi Team
 * @license     http://www.gnu.org/copyleft/lesser.html GNU Less Public General License (LGPL)
 */
class Clickatell_Sms_Provider implements Sms_Provider_Core {
	
	/**
	 * Sends a text message (SMS) using the Clickatell API
	 *
	 * @param string $to
	 * @param string $from
	 * @param string $to
	 */
	public function send($to = NULL, $from = NULL, $message = NULL)
	{
		// Get Current Clickatell Settings
		$clickatell = ORM::factory("clickatell", 1)->find();
		
		if ($clickatell->loaded)
		{
			// Create Clickatell Object
			$new_sms = new Clickatell_API();
			$new_sms->api_id = $clickatell->clickatell_api;
			$new_sms->user = $clickatell->clickatell_username;
			$new_sms->password = $clickatell->clickatell_password;
			$new_sms->use_ssl = false;
			$new_sms->sms();
			$response = $new_sms->send($to, $from, $message);
			
			// Message Went Through??
			return ($response == "OK")? TRUE : $response;
		}
		
		return "Clickatell Is Not Set Up!";
	}
	
}