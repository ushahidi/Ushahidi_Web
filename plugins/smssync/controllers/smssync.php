<?php defined('SYSPATH') or die('No direct script access.');
/**
 * SMSSync HTTP Post Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   SMSSync Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class Smssync_Controller extends Controller {
	
	private $request = array();
	
	public function __construct()
	{
		$this->request = ($_SERVER['REQUEST_METHOD'] == 'POST')
			? $_POST
			: $_GET;
	}
	
	function index()
	{
		$task = (isset($this->request['task'])) ? $this->request['task'] : "";
		
		switch ($task) {
			// Send
			case "send":
				$this->_send();
				break;
			
			// Receive
			default:
				$this->_receive();
				break;
		}
	}
	
	private function _receive()
	{
		$secret = "";
		$success = "false";
		
		if (isset($this->request['secret']))
		{
			$secret = $this->request['secret'];
		}
		
		if (isset($this->request['from']))
		{
			$message_from = $this->request['from'];
			// Remove non-numeric characters from string
			$message_from = preg_replace("#[^0-9]#", "", $message_from);
		}
		
		if (isset($this->request['message']))
		{
			$message_description = $this->request['message'];
		}
		
		if ( ! empty($message_from) AND ! empty($message_description))
		{
			$secret_match = TRUE;
			
			// Is this a valid Secret?
			$smssync = ORM::factory('smssync_settings')
				->find(1);
			
			if ($smssync->loaded)
			{
				$smssync_secret = $smssync->smssync_secret;
				if ($smssync_secret AND $secret != $smssync_secret)
				{ // A Secret has been set and they don't match
					$secret_match = FALSE;
				}
			}
			else
			{ // No Secret Set
				$secret_match = TRUE;
			}
			
			if ($secret_match)
			{
				sms::add($message_from, $message_description);
				$success = "true";
			}
		}
		
		echo json_encode(array("payload"=>array("success"=>$success)));
	}
	
	private function _send()
	{
		$all_messages =  array();
		
		// Find all unsent messages
		$messages = ORM::factory("smssync_message")
			->where("smssync_sent", 0)
			->find_all();
			
		foreach ($messages as $message)
		{
			$all_messages[] = array(
				"to"=>$message->smssync_to,
				"message"=>$message->smssync_message
			);
			
			$message->smssync_sent = 1;
			$message->smssync_sent_date = date("Y-m-d H:i:s",time());
			$message->save();
		}
		
		echo json_encode(array("payload"=>array("task"=>"send","messages"=>$all_messages)));
	}
}
