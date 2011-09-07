<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alerts Scheduler Controller
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

class S_Alerts_Controller extends Controller {
	
	public $table_prefix = '';
	
	// Cache instance
	protected $cache;
	
	function __construct()
	{
		parent::__construct();

		// Load cache
		$this->cache = new Cache;
		
		// *************************************
		// ** SAFEGUARD DUPLICATE SEND-OUTS **
		// Create A 15 Minute SEND LOCK
		// This lock is released at the end of execution
		// Or expires automatically
		$alerts_lock = $this->cache->get(Kohana::config('settings.subdomain')."_alerts_lock");
		if ( ! $alerts_lock)
		{
			// Lock doesn't exist
			$timestamp = time();
			$this->cache->set(Kohana::config('settings.subdomain')."_alerts_lock", $timestamp, array("alerts"), 900);
		}
		else
		{
			// Lock Exists - End
			exit("Other process is running - waiting 15 minutes!");
		}
		// *************************************
	}
	
	function __destruct()
	{
		$this->cache->delete(Kohana::config('settings.subdomain')."_alerts_lock");
	}
	
	public function index() 
	{
		$settings = kohana::config('settings');
		$site_name = $settings['site_name'];
		$alerts_email = ($settings['alerts_email']) ? $settings['alerts_email']
			: $settings['site_email'];
		$unsubscribe_message = Kohana::lang('alerts.unsubscribe')
								.url::site().'alerts/unsubscribe/';

				$database_settings = kohana::config('database'); //around line 33
				$this->table_prefix = $database_settings['default']['table_prefix']; //around line 34

		$settings = NULL;
		$sms_from = NULL;

		$db = new Database();
		
		/* Find All Alerts with the following parameters
		- incident_active = 1 -- An approved incident
		- incident_alert_status = 1 -- Incident has been tagged for sending
		
		Incident Alert Statuses
		  - 0, Incident has not been tagged for sending. Ensures old incidents are not sent out as alerts
		  - 1, Incident has been tagged for sending by updating it with 'approved' or 'verified'
		  - 2, Incident has been tagged as sent. No need to resend again
		*/
		$incidents = $db->query("SELECT i.id, incident_title, 
			incident_description, incident_verified, 
			l.latitude, l.longitude, a.alert_id, a.incident_id
			FROM ".$this->table_prefix."incident AS i INNER JOIN ".$this->table_prefix."location AS l ON i.location_id = l.id
			LEFT OUTER JOIN ".$this->table_prefix."alert_sent AS a ON i.id = a.incident_id WHERE
			i.incident_active=1 AND i.incident_alert_status = 1 ");
		
		foreach ($incidents as $incident)
		{
			// ** Pre-Formatting Message ** //
			// Convert HTML to Text
			$incident_description = $incident->incident_description;
			$html2text = new Html2Text($incident_description);
			$incident_description = $html2text->get_text();

			// EMAIL MESSAGE
			$email_message = $incident_description;

			// SMS MESSAGE
			$sms_message = $incident_description;
			// Remove line breaks
			$sms_message = str_replace("\n", " ", $sms_message);
			// Shorten to text message size
			$sms_message = text::limit_chars($sms_message, 150, "...");
			
			
			
			$latitude = (double) $incident->latitude;
			$longitude = (double) $incident->longitude;
			
			// Find all the catecories including parents
			$category_ids = $this->_find_categories($incident->id);

			// Get all alertees
			$alertees = ORM::factory('alert')
				->where('alert_confirmed','1')
				->find_all();
			
			foreach ($alertees as $alertee)
			{
				// Has this alert been sent to this alertee?
				if ($alertee->id == $incident->alert_id)
					continue;
				
				// Check the categories
				if (!$this->_check_categories($alertee, $category_ids)) {
				  continue;
				}

				$alert_radius = (int) $alertee->alert_radius;
				$alert_type = (int) $alertee->alert_type;
				$latitude2 = (double) $alertee->alert_lat;
				$longitude2 = (double) $alertee->alert_lon;
				
				$distance = (string) new Distance($latitude, $longitude, $latitude2, $longitude2);
				
				// If the calculated distance between the incident and the alert fits...
				if ($distance <= $alert_radius)
				{
					if ($alert_type == 1) // SMS alertee
					{
						// Get SMS Numbers
						if (Kohana::config("settings.sms_no3"))
							$sms_from = Kohana::config("settings.sms_no3");
						elseif (Kohana::config("settings.sms_no2"))
							$sms_from = Kohana::config("settings.sms_no2");
						elseif (Kohana::config("settings.sms_no1"))
							$sms_from = Kohana::config("settings.sms_no1");
						else
							$sms_from = "12053705050";		// Admin needs to set up an SMS number	
						
						
						
						if ($response = sms::send($alertee->alert_recipient, $sms_from, $sms_message) === true)
						{
							$alert = ORM::factory('alert_sent');
							$alert->alert_id = $alertee->id;
							$alert->incident_id = $incident->id;
							$alert->alert_date = date("Y-m-d H:i:s");
							$alert->save();
						}
						else
						{
							// The gateway couldn't send for some reason
							// in future we'll keep a record of this
						}
					}

					elseif ($alert_type == 2) // Email alertee
					{
						$to = $alertee->alert_recipient;
						$from = array();
							$from[] = $alerts_email;
							$from[] = $site_name;
						$subject = "[$site_name] ".$incident->incident_title;
						$message = $email_message
									."\n\n".$unsubscribe_message
									.$alertee->alert_code."\n";

						if (email::send($to, $from, $subject, $message, FALSE) == 1)
						{
							$alert = ORM::factory('alert_sent');
							$alert->alert_id = $alertee->id;
							$alert->incident_id = $incident->id;
							$alert->alert_date = date("Y-m-d H:i:s");
							$alert->save();
						}
					}
				}
			} // End For Each Loop
			
			
			// Update Incident - All Alerts Have Been Sent!
			$update_incident = ORM::factory('incident', $incident->id);
			if ($update_incident->loaded)
			{
				$update_incident->incident_alert_status = 2;
				$update_incident->save();
			}
		}
	}

	private function _find_categories($incident_id) {
	  $ret = array();
	  $incident_categories = ORM::factory('incident_category')
	    ->where('incident_id', $incident_id)
	    ->find_all();

	  foreach ($incident_categories as $ic) {
	    $category = ORM::factory('category')
	      ->where('id', $ic->category_id)
	      ->find();
	    $this->_add_category($ret, $category);
	  }

	  return $ret;
	}

	private function _add_category(array & $ids, Category_Model $category) {
	  if ($category == null) {
	    return;
	  }

	  $id = (string)$category->id;

	  if (!array_key_exists($id, $ids)) {
	    $ids[$id] = 1;
	  }

	  if ($category->parent_id != 0) {
	    $parent = ORM::factory('category')
	      ->where('id', $category->parent_id)
	      ->find();

	    $this->_add_category($ids, $parent);
	  }
	}

	private function _check_categories(Alert_Model $alertee, array $category_ids) {
	  $ret = false;

	  $alert_categories = ORM::factory('alert_category')
	    ->where('alert_id', $alertee->id)
	    ->find_all();

	  if (count($alert_categories) == 0) {
	    $ret = true;
	  }
	  else {
	    foreach ($alert_categories as $ac) {
	      if (array_key_exists((string)$ac->category_id, $category_ids)) {
		$ret = true;
	      }
	    }
	  }

	  return $ret;
	}
}
