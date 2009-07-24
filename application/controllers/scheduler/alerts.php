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

class Alerts_Controller extends Scheduler_Controller
{
	public function __construct()
    {
        parent::__construct();
	}	
	
	public function index() 
	{
		$config = kohana::config('alerts');
		$settings = kohana::config('settings');
		$site_name = $settings['site_name'];
		$unsubscribe_message = Kohana::lang('alerts.unsubscribe')
								.url::site().'alerts/unsubscribe/';
		$settings = NULL;
		$sms_from = NULL;
		$clickatell = NULL;

		$db = new Database();
		
		$incidents = $db->query("SELECT incident.id, incident_title, 
								 incident_description, incident_verified, 
								 location.latitude, location.longitude, alert_sent.incident_id
								 FROM incident INNER JOIN location ON incident.location_id = location.id
								 LEFT OUTER JOIN alert_sent ON incident.id = alert_sent.incident_id");
		
		foreach ($incidents as $incident)
		{
			if ($incident->incident_id != NULL)
				continue;

			$verified = (int) $incident->incident_verified;
			
			if ($verified)
			{
				$latitude = (double) $incident->latitude;
				$longitude = (double) $incident->longitude;
				$proximity = new Proximity($latitude, $longitude);
				$alertees = $this->_get_alertees($proximity);

				foreach ($alertees as $alertee)
				{
					$alert_type = (int) $alertee->alert_type;

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
									$sms_from = "000";      // User needs to set up an SMS number
							}
						
							$clickatell = new Clickatell();
							$clickatell->api_id = $settings->clickatell_api;
							$clickatell->user = $settings->clickatell_username;
							$clickatell->password = $settings->clickatell_password;
							$clickatell->use_ssl = false;
							$clickatell->sms();
						}	
						
						$message = $incident->incident_description;

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
						$from = $config['alerts_email'];
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
			}
		}
	}

	private function _get_alertees(Proximity $proximity) 
	{
		$radius = " alert_lat >= ".$proximity->minLat." 
            AND alert_lat <= ".$proximity->maxLat." 
            AND alert_lon >= ".$proximity->minLong."
            AND alert_lon <= ".$proximity->maxLong."
			AND alert_confirmed = 1";

		$alertees = ORM::factory('alert')
					->select('id, alert_type, alert_recipient, alert_code')
					->where($radius)
					->find_all();

		return $alertees;
	}
}
