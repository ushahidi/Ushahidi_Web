<?php defined('SYSPATH') or die('No direct script access.');
/**
 * EMAIL Scheduler Controller (IMAP/POP3)
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Email Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class Email_Controller extends Controller
{
	public function __construct()
    {
        parent::__construct();
	}	
	
	public function index() 
	{
		$check_email = new Imap;
		
		$messages = $check_email->get_messages();
		
		// Close Connection
		$check_email->close();
		
		// Add Messages
        $this->add_email($messages);
    }


	/**
	* Adds email to the database and saves the sender as a new
	* Reporter if they don't already exist
    * @param string $messages
    */
	private function add_email($messages)
	{
		$services = new Service_Model();	
    	$service = $services->where('service_name', 'Email')->find();
	   	if (!$service) {
 		    return;
	    }
		if (empty($messages) || !is_array($messages)) {
			return;
		}
		
		foreach($messages as $message) {		
			$reporter = ORM::factory('reporter')
				->where('service_id', $service->id)
				->where('service_account', $message['email'])
				->find();
			
			if (!$reporter->loaded == true)
			{
				// Add new reporter
	    		$names = explode(' ', $message['from'], 2);
	    		$last_name = '';
	    		if (count($names) == 2) {
	    			$last_name = $names[1]; 
	    		}
	    		
	    		// get default reporter level (Untrusted)
				$level = ORM::factory('level')
					->where('level_weight', 0)
					->find();
		    	
	    		$reporter->service_id       = $service->id;
				$reporter->level_id	        = $level->id;
	    		$reporter->service_userid   = null;
	    		$reporter->service_account  = $message['email']; 
	    		$reporter->reporter_first   = $names[0];
	    		$reporter->reporter_last    = $last_name;
	    		$reporter->reporter_email   = $message['email'];
	    		$reporter->reporter_phone   = null;
	    		$reporter->reporter_ip      = null;
	    		$reporter->reporter_date    = date('Y-m-d');
	    		$reporter->save();
			}
			
			if ($reporter->level_id > 1 && 
				count(ORM::factory('message')
					->where('service_messageid', $message['message_id'])
					->find_all()) == 0 )
			{	
				// Save Email as Message
				$email = new Message_Model();
				$email->parent_id = 0;
				$email->incident_id = 0;
				$email->user_id = 0;
				$email->reporter_id = $reporter->id;
				$email->message_from = $message['from'];
				$email->message_to = null;
				$email->message = $message['subject'];
				$email->message_detail = $message['body'];
				$email->message_type = 1; // Inbox
				$email->message_date = $message['date'];
				$email->service_messageid = $message['message_id'];
				$email->save();
				
				// Notify Admin Of New Email Message
				$send = notifications::notify_admins(
					"[".Kohana::config('settings.site_name')."] ".
						Kohana::lang('notifications.admin_new_email.subject'),
					Kohana::lang('notifications.admin_new_email.message')
					);
			}
		}
	}
}
