<?php defined('SYSPATH') or die('No direct script access.');
/**
 * GEORSS PARSER (4636.ushahidi.com)
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     GeoRSS Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
*/

class Georss_Controller extends Controller
{
	var $items = 50;	// Size of RSS Feed
	
	
	
	public function __construct()
    {
        parent::__construct();
		set_time_limit(60);
		
		//$profiler = New Profiler;
	}
	
	public function index()
	{
		$birth = 1263427200; // 2010-01-14 - We'll move in 3 hour increments from here
		$cache = Cache::instance();
		
		$last_message_date = $cache->get('georss_parser');
		if ($last_message_date == NULL)
		{
			$last_message_date = $birth;
			$cache->set('georss_parser', $birth, array('georss'), 0);
		}
		
		//echo $last_message_date;
		$settings = ORM::factory('settings', 1);		
		$sms_rss = $settings->georss_feed."&only_phone=1&limit=50,".$this->items;	//."&uptots=".$last_message_date;
		$curl_handle = curl_init();
		curl_setopt($curl_handle,CURLOPT_URL,$sms_rss);
		curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2); // Timeout
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1); // Set curl to store data in variable instead of print
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);
		
		// Parse Feed URL using SimplePIE
		$feed_data = $this->_simplepie( $buffer );
		
		if (count($feed_data->get_items(0,$this->items)) == 0)
		{
			$cache->set('georss_parser', ($last_message_date + 3600), array('georss'), 0);
			//exit;
		}
		
		// Cycle through feed data
		$i = 0;
		foreach($feed_data->get_items(0,$this->items) as $feed_data_item)
		{
			$service_messageid = $feed_data_item->get_item_tags('http://www.w3.org/2005/Atom', 'id');
				$service_messageid = str_replace("http://4636.ushahidi.com/person.php?id=","",
					trim($service_messageid[0]['data']));
			$date = $feed_data_item->get_item_tags('http://www.w3.org/2005/Atom', 'updated');
				$date = date("Y-m-d H:i:s",strtotime(trim($date[0]['data'])));
			$phone = $feed_data_item->get_item_tags('http://www.w3.org/2005/Atom', 'phone');
				$phone = intval($phone[0]['data']);
			$category = $feed_data_item->get_item_tags('http://www.w3.org/2005/Atom', 'categorization');
				$category = trim($category[0]['data']);
			$message_sms = $feed_data_item->get_item_tags('http://www.w3.org/2005/Atom', 'sms');
				$message_sms = trim($message_sms[0]['data']);	
			$message_notes = $feed_data_item->get_item_tags('http://www.w3.org/2005/Atom', 'notes');
				$message_notes = trim($message_notes[0]['data']);
			$message_detail = $message_notes."\n~~~~~~~~~~~~~~~~~\n";
			$message_detail .= "Category: ".$category;
			$latitude = $feed_data_item->get_latitude();
			$longitude = $feed_data_item->get_longitude();
			$location_name = $feed_data_item->get_item_tags('http://www.w3.org/2005/Atom', 'city');
				$location_name = trim($location_name[0]['data']);
			
			// Okay now we have everything we need
			
			// Step 1. Does this message have a phone number?
			if ($phone)
			{
				// Step 2. Has this particular message been saved before??
				$exists = ORM::factory('message')
					->where('service_messageid', $service_messageid)
					->where('message_from', $phone)
					->find();
					
				if(!$exists->loaded)
				{
					
					$parent_id = 0;
				
					// Step 3. Make sure this phone number is not in our database
					$reporter = ORM::factory('reporter')
						->where('service_id', 1)	// 1 - SMS (See Service Table)
						->where('service_account', $phone)
						->find();
				
					if (!$reporter->loaded)
					{
						$reporter->service_id = 1;	// 1 - SMS (See Service Table)
						$reporter->level_id = 3;	// 3 - Untrusted (See Level Table)
						$reporter->service_account = $phone;
						$reporter->reporter_date = $date;
						$reporter->save();
					}
					// Number is in our database
					else
					{
						// Find previous message and use it as parent
						$parent = ORM::factory('message')
							->where('reporter_id', $reporter->id)
							->where('message_type', '1')
							->where('parent_id', '0')
							->where('message_trash', '0')
							->orderby('message_date', 'desc')
							->find();
						if ($parent->loaded)
						{
							$parent_id = $parent->id;
							$parent->message_reply = 1;
							$parent->save($parent->id);
						}
					}
				
				
					// Step 4. If this message has a location, save it!
					$location_id = 0;
					if ($latitude && $longitude)
					{
						$location = ORM::factory('location')
							->where('latitude', $latitude)	// 1 - SMS (See Service Table)
							->where('longitude', $longitude)
							->find();

						if (!$location->loaded)
						{
							$location = new Location_Model();
							if ($location_name)
							{
								$location->location_name = $location_name;
							}
							else
							{
								$location->location_name = "Unknown";
							}
							$location->latitude = $latitude;
							$location->longitude = $longitude;
							$location->location_date = date("Y-m-d H:i:s",time());
							$location->save();
							$location_id = $location->id;
						}
					}

					// Save Message
					$message = new Message_Model();
					$message->parent_id = $parent_id;
					$message->incident_id = 0;
					$message->location_id = $location_id;
					$message->user_id = 0;
					$message->reporter_id = $reporter->id;
					$message->message_from = $phone;
					$message->message_to = null;
					$message->message = $message_sms;
					$message->message_detail = $message_detail;
					$message->message_type = 1; // Inbox
					$message->message_date = $date;
					$message->service_messageid = $service_messageid;
					$message->save();
					
					$i++;
				}
			}			
		}
		
		if ($i == 0)
		{
			$cache->set('georss_parser', ($last_message_date + 3600), array('georss'), 0);
		}
	}


	/**
	 * setup simplepie
	 */
	private function _simplepie( $feed_data )
	{
		$data = new SimplePie();

		$data->set_raw_data( $feed_data );
		$data->enable_cache(false);
		$data->enable_order_by_date(false);
		$data->init();
		$data->handle_content_type();

		return $data;
	}
}