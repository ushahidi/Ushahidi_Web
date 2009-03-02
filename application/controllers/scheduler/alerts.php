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

		$db = new Database();
		
		$incidents = $db->query("SELECT distinct incident.id, incident_title, incident_verified, location.latitude, location.longitude 
									FROM location, incident, alert_sent 
									WHERE incident.location_id = location.id AND incident.id != alert_sent.incident_id");

		$settings = null;
		$sms_from = null;
		$clickatell = null;

		foreach ($incidents as $incident)
		{
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

					if ($alert_type == 1) # SMS alertee
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
						
						//XXX: Fix the outgoing message!
						$message = $incident->incident_title." occured near you!";
						echo "$message<br/>";

						if ($clickatell->send ($alertee->alert_alert_recipient, $sms_from, $message) == "OK")
                        {
                            $alert = ORM::factory('alert_sent');
                            $alert->alert_id = $alertee->id;
                            $alert->incident_id = $incident->id;
                            $alert->alert_date = date("Y-m-d H:i:s");
							$alert->save();

                        }
					
					}

					elseif ($alert_type == 2) # Email alertee
					{
						 //XXX: Setup correct 'from' address and message
                    	$to = $alertee->alert_recipient;
                    	$from = 'alert@ushahidi.com';
                    	$subject = 'Ushahidi alert!';
						$message = $incident->incident_title." occured near you!";


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
		
		$radius = " alert_lat >= '" . $proximity->minLat . "' 
            AND alert_lat <= '" . $proximity->maxLat . "' 
            AND alert_lon >= '" . $proximity->minLong . "'
            AND alert_lon <= '" . $proximity->maxLong . "'
			AND alert_confirmed = 1";

		$alertees = ORM::factory('alert')
					->select('id, alert_type, alert_recipient')
					->where($radius)
					->find_all();

		return $alertees;
					
	}
	
}
