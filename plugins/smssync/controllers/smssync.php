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
		
		//Sometimes user send blank SMSs or GSM operators will
		//send promotional SMSs with no phone number, so this way
		//these messages will always end up on the backend and not float around
		//on the phones forever.
		$message_description = Kohana::lang("ui_main.empty");		
		$message_from = "00000000";
		$non_numeric_source = false;
		
		
		if (isset($this->request['secret']))
		{
			$secret = $this->request['secret'];
		}
		
		if(isset($this->request['from']) &&  strlen($this->request['from']) > 0)
		{
			$message_from = $this->request['from'];
			$original_from = $message_from;
			$message_from = preg_replace("#[^0-9]#", "", $message_from);

			if(strlen($message_from) == 0)
			{
				$message_from = "00000000";
				$non_numeric_source = true;
			}
		}
		
		if (isset($this->request['message']) && strlen($this->request['message']) > 0)
		{
			$message_description = $this->request['message'];
		}
		
		if($non_numeric_source)
		{
			$message_description = '<div style="color:red;">'.Kohana::lang("ui_main.message_non_numeric_source")." \"".$original_from."\" </div>".$message_description;			
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
				if(stristr($message_description,"alert"))
				{
					alert::mobile_alerts_register($message_from, $message_description);
					$success = "true";
				}
				elseif(stristr($message_description,"off"))
				{
					alert::mobile_alerts_unsubscribe($message_from, $message_description);
					$success = "true";
				}
				else
				{
					sms::add($message_from, $message_description);
					$success = "true";
				}
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
        
        //get the secret key
		$smssync = ORM::factory('smssync_settings')->find(1);
			
		if ($smssync->loaded)
		{
		    $smssync_secret = $smssync->smssync_secret;
		} 
        else 
        {
            //set to empty, because secret key wasn't set.
            $smssync_secret = "";
        }
		
        echo json_encode(array("payload"=>array("task"=>"send", 
            "secret"=>$smssync_secret,"messages"=>$all_messages)));
	}
}
