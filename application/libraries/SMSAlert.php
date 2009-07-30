<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Utility class provides reusable functions to use when dealing with SMS alerts.
 * @package    SMSAlert
 * @author	   Ushahidi Team
 * @copyright  (c) 2009 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class SMSAlert
{
	/**
	 * Verifies alerts request.
	 * 
	 * @param	string	originator of the SMS message (Should be an MSISDN)
	 * @return	boolean
	 */
	public static function verify($originator)
	{
		// XXX: Send a reply only if the recipient does not exist to save on
		// MTs
		
		$alert_recipient = ORM::factory('alert')
							->where('alert_recipient', $code)
							->find();
		if (!$alert_recipient->loaded)
			return FALSE;
		
		$alert_recipient->set('alert_confirmed', 1)->save();
		return TRUE;
	}

	/**
	 * Unsubscribes alertee.
	 * 
	 * @param	string	originator of the SMS message (Should be an MSISDN)
	 * @return	boolean
	 */
	public static function unsubscribe($originator)
	{
		$alert_recipient = ORM::factory('alert')
							->where('alert_recipient', $code)
							->find();
			
		if (!$alert_recipient->loaded)
			return FALSE;

		$alert_recipient->delete();
		return TRUE;
	}

	/**
	 * Sends SMS message.
	 * 
	 * @param	string	destination of the SMS message (Should be an MSISDN)
	 * @throws	Kohana_Exception	If settings could not be loaded or if an
	 *								error occured while trying to send a
	 *								message
	 * @return	void
	 */
	public static function send($destination, $message)
	{
		$settings = ORM::factory('settings', 1);

		if (!$settings->loaded)
			throw new Kohana_Exception('alerts.settings_error');

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
			throw new Kohana_Exception('alerts.settings_error');

		$sms = new Clickatell();
		$sms->api_id = $settings->clickatell_api;
		$sms->user = $settings->clickatell_username;
		$sms->password = $settings->clickatell_password;
		$sms->use_ssl = false;
		$sms->sms();
	
		if ($sms->send($destination, $sms_from, $message) != "OK")
			throw new Kohana_Exception('alerts.settings_error');
	}
} // End SMSAlert
