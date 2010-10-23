<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * SMS helper class
 * 
 *
 * @package    SMS
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */

class sms_Core 
{
	/**
	 * Send The SMS Message Using Default Provider
	 * @param to mixed  The destination address.
     * @param from mixed  The source/sender address
     * @param text mixed  The text content of the message
	 *
	 * @return mixed/bool (returns TRUE if sent FALSE or other text for fail)
	 */
	public function send($to, $from, $message)	
	{
		// 1. Do we have an SMS Provider?
		$provider = Kohana::config("settings.sms_provider");
		if ($provider)
		{
			// 2. Does the plugin exist, and if so, is it active?
			$plugin = ORM::factory("plugin")
				->where("plugin_name", $provider)
				->where("plugin_active", 1)
				->find();
			if ($plugin->loaded)
			{ // Plugin exists and is active
				
				// 3. Does this plugin have the SMS Library in place?
				$class = ucfirst($provider).'_SMS';
				if (Kohana::find_file('libraries', $provider.'_SMS'))
				{ // File Exists
					
					$sender = new $class;
					// 4. Does the send method exist in this class?
					if (method_exists($sender, 'send'))
					{
						$response = $sender->send($to, $from, $message);
						
						return $response;
					}
				}
			}
		}
		
		return "No SMS Sending Provider In System";
	}
}