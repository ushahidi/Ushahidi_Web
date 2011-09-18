<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Interface that must be implemented by SMS libraries
 *
 * @package     Ushahidi
 * @category    Libraries
 * @author      Ushahidi Team
 * @copyright   (c) 2008-2011 Ushahidi Team 
 * @license     http://www.gnu.org/copyleft/lesser.html GNU Less Public General License (LGPL)
 */
interface Sms_Provider_Core {

	/**
	 * Sends an SMS - All sub-classes must implement this method
	 *
	 * @param string $to MSISDN of the recipent
	 * @param string $from MSISDN of the sender
	 * @param string $message Message to be transmitted to the recipient
	 */
	public function send($to = NULL, $from = NULL, $message = NULL);

}
?>