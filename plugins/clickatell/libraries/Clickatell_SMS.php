<?php defined('SYSPATH') or die('No direct script access.');

/**
 * The clickatell sender
 */
class Clickatell_SMS_Core {
	
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
	        if ($response == "OK")
	        {
				return true;
			}
			else
			{
				// Send the Error Code Back
				return $response;
			}
		}
		
		return "Clickatell Is Not Set Up!";
	}
	
}