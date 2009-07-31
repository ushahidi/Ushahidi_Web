<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Utility class provides reusable functions to use when dealing with alerts.
 * @package    Alert
 * @author	   Ushahidi Team
 * @copyright  (c) 2009 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class Alert
{
	const ER_CODE_VERIFIED = 0;
	const ER_CODE_NOT_FOUND = 1;
	const ER_CODE_ALREADY_VERIFIED = 3;
	
	/**
	 * Verifies alerts request.
	 * 
	 * @param	string	unique code sent out for this location
	 * @return	boolean
	 */
	public static function verify($code)
	{
		// XXX: Send a reply only if the recipient does not exist to save on
		// MTs
		
		$alert_code = ORM::factory('alert')
							->where('alert_code', $code)
							->find();
		// IF there was no result
		if (!$alert_code->loaded)
			return self::ER_CODE_NOT_FOUND;
		elseif ($alert_code->alert_confirmed)
			return  self::ER_CODE_ALREADY_VERIFIED;
		
		// SET the alert as confirmed, and save it
		$alert_code->set('alert_confirmed', 1)->save();
		return self::ER_CODE_VERIFIED;
	}

	/**
	 * Unsubscribes alertee.
	 * 
	 * @param	string	unique code for this location
	 * @return	boolean
	 */
	public static function unsubscribe($code)
	{
		$alert_code = ORM::factory('alert')
							->where('alert_recipient', $code)
							->find();
			
		if (!$alert_code->loaded)
			return FALSE;

		$alert_code->delete();
		return TRUE;
	}
} // End Alert
