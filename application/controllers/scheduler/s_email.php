<?php defined('SYSPATH') or die('No direct script access.');
/**
 * EMAIL Scheduler Controller (IMAP/POP3)
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Scheduler
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class S_Email_Controller extends Controller {
	
	public function __construct()
	{
		parent::__construct();
	}	
	
	public function index() 
	{
		$modules = new Modulecheck;
		if ($modules->isLoaded('imap'))
		{
			$email_username = Kohana::config('settings.email_username');
			$email_password = Kohana::config('settings.email_password');
			$email_host = Kohana::config('settings.email_host');
			$email_port = Kohana::config('settings.email_port');
			$email_servertype = Kohana::config('settings.email_servertype');
			
			if ( ! empty($email_username)
			AND ! empty($email_password)
			AND ! empty($email_host)
			AND ! empty($email_port)
			AND ! empty($email_servertype) )
			{
				$check_email = new Imap;

				$messages = $check_email->get_messages();

				// Close Connection
				$check_email->close();

				// Add Messages
				$this->add_email($messages);
			}
			else
			{
				echo "Email is not configured.<BR /><BR />";
			}
		}
		else
		{
			echo "You Do Not Have the IMAP PHP Library installed. Email will not be retrieved.<BR/ ><BR/ >";
		}
	}


	/**
	* Adds email to the database and saves the sender as a new
	* Reporter if they don't already exist
	* @param string $messages
	*/
	private function add_email($messages)
	{
		$service = ORM::factory('service')
			->where('service_name', 'Email')
			->find();
		
		if ( ! $service->loaded)
		{
			return;
		}
	
		if (empty($messages) OR ! is_array($messages))
		{
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
				
				$reporter->service_id		= $service->id;
				$reporter->level_id			= $level->id;
				$reporter->service_account	= $message['email']; 
				$reporter->reporter_first	= $names[0];
				$reporter->reporter_last	= $last_name;
				$reporter->reporter_email	= $message['email'];
				$reporter->reporter_phone	= null;
				$reporter->reporter_ip		= null;
				$reporter->reporter_date	= date('Y-m-d');
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
				
				// Attachments?			
				foreach ($message['attachments'] as $attachments)
				{
					foreach ($attachments as $attachment)
					{
						$media = new Media_Model();
						$media->location_id = 0;
						$media->incident_id = 0;
						$media->message_id = $email->id;
						$media->media_type = 1; // Images
						$media->media_link = $attachment[0];
						$media->media_medium = $attachment[1];
						$media->media_thumb = $attachment[2];
						$media->media_date = date("Y-m-d H:i:s",time());
						$media->save();
					}
				}
				
				
				// Auto-Create A Report if Reporter is Trusted
				$reporter_weight = $reporter->level->level_weight;
				$reporter_location = $reporter->location;
				if ($reporter_weight > 0 AND $reporter_location)
				{
					// Create Incident
					$incident = new Incident_Model();
					$incident->location_id = $reporter_location->id;
					$incident->incident_title = $message['subject'];
					$incident->incident_description = $message['body'];
					$incident->incident_date = $message['date'];
					$incident->incident_dateadd = date("Y-m-d H:i:s",time());
					$incident->incident_active = 1;
					if ($reporter_weight == 2)
					{
						$incident->incident_verified = 1;
					}
					$incident->save();

					// Update Message with Incident ID
					$email->incident_id = $incident->id;
					$email->save();

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

					// Add Attachments
					$attachments = ORM::factory("media")
						->where("message_id", $email->id)
						->find_all();
					foreach ($attachments AS $attachment)
					{
						$attachment->incident_id = $incident->id;
						$attachment->save();
					}
				}
				
				
				// Notify Admin Of New Email Message
				$send = notifications::notify_admins(
					"[".Kohana::config('settings.site_name')."] ".
						Kohana::lang('notifications.admin_new_email.subject'),
					Kohana::lang('notifications.admin_new_email.message')
					);
					
				// Action::message_email_add - Email Received!
				Event::run('ushahidi_action.message_email_add', $email);
			}
		}
	}
}
