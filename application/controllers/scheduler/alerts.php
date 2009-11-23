<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alerts Scheduler Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Alerts Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class Alerts_Controller extends Controller
{
	public function __construct()
    {
        parent::__construct();
	}	
	
	public function index() 
	{
		$settings = kohana::config('settings');
		$site_name = $settings['site_name'];
		$alerts_email = $settings['alerts_email'];
		$unsubscribe_message = Kohana::lang('alerts.unsubscribe')
								.url::site().'alerts/unsubscribe/';
		$settings = NULL;
		$sms_from = NULL;
		$clickatell = NULL;

		$db = new Database();
		
		/* Find All Alerts with the following parameters
		- incident_active = 1 -- An approved incident
		- incident_alert_status = 1 -- Incident has been tagged for sending
		
		Incident Alert Statuses
		  - 0, Incident has not been tagged for sending. Ensures old incidents are not sent out as alerts
		  - 1, Incident has been tagged for sending by updating it with 'approved' or 'verified'
		  - 2, Incident has been tagged as sent. No need to resend again
		*/
		$incidents = $db->query("SELECT incident.id, incident_title, 
								 incident_description, incident_verified, 
								 location.latitude, location.longitude, alert_sent.alert_id, alert_sent.incident_id
								 FROM incident INNER JOIN location ON incident.location_id = location.id
								 LEFT OUTER JOIN alert_sent ON incident.id = alert_sent.incident_id WHERE
								 incident.incident_active=1 AND incident.incident_alert_status = 1 ");
		
		foreach ($incidents as $incident)
		{
			
			$latitude = (double) $incident->latitude;
			$longitude = (double) $incident->longitude;
			
			// Get all alertees
			$alertees = ORM::factory('alert')
				->where('alert_confirmed','1')
				->find_all();
			
			foreach ($alertees as $alertee)
			{
				// Has this alert been sent to this alertee?
				if ($alertee->id == $incident->alert_id)
					continue;
				
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
						if ($settings == null)
						{
							$settings = ORM::factory('settings', 1);
							if ($settings->loaded == true)
							{
								// Get SMS Numbers
								if (!empty($settings->sms_no3))
									$sms_from = $settings->sms_no3;
								elseif (!empty($settings->sms_no2))
									$sms_from = $settings->sms_no2;
								elseif (!empty($settings->sms_no1))
									$sms_from = $settings->sms_no1;
								else
									$sms_from = "000";      // Admin needs to set up an SMS number
							}

							$clickatell = new Clickatell();
							$clickatell->api_id = $settings->clickatell_api;
							$clickatell->user = $settings->clickatell_username;
							$clickatell->password = $settings->clickatell_password;
							$clickatell->use_ssl = false;
							$clickatell->sms();
						}	

						$message = $incident->incident_description;
						
						// If Clickatell Is Set Up
						if ($clickatell->send($alertee->alert_recipient, $sms_from, $message) == "OK")
						{
							$alert = ORM::factory('alert_sent');
							$alert->alert_id = $alertee->id;
							$alert->incident_id = $incident->id;
							$alert->alert_date = date("Y-m-d H:i:s");
							$alert->save();
						}
					}

					elseif ($alert_type == 2) // Email alertee
					{
						$to = $alertee->alert_recipient;
						$from = $alerts_email;
						$subject = "[$site_name] ".$incident->incident_title;
						$message = $incident->incident_description
									."<p>".$unsubscribe_message
									.$alertee->alert_code."</p>";

						if (email::send($to, $from, $subject, $message, TRUE) == 1)
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
}
