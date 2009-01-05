<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to import legacy data
 * SOURCE: XML
 * LINK: http://legacy.ushahidi.com/export_data.asp
 * *** USE WITH CAUTION!!!!! ***
 * *** DELETES ALL PREVIOUS ENTRIES ***
 * *** ASSUMES DEFAULT CATEGORIES ARE IN PLACE ***
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Import Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Import_Controller extends Controller
{
	function index()
	{
		$source = 'http://legacy.ushahidi.com/export_data.asp';
		
		ORM::factory('Location')->delete_all();
		ORM::factory('Incident')->delete_all();
		ORM::factory('Media')->delete_all();
		ORM::factory('Incident_Person')->delete_all();
		ORM::factory('Incident_Category')->delete_all();
		ORM::factory('Comment')->delete_all();
		ORM::factory('Rating')->delete_all();
		
		// load as string
		$xmlstr = file_get_contents($source);
		$incidents = new SimpleXMLElement($xmlstr);
		
		foreach($incidents as $post) {		
			// STEP 1: SAVE LOCATION
			$location = new Location_Model();
			$location->location_name = (string)$post->location_name;
			$location->latitude = (string)$post->latitude;
			$location->longitude = (string)$post->longitude;
			$location->country_id = 115;
			$location->location_date = date("Y-m-d H:i:s",time());
			$location->save();
			
			// STEP 2: SAVE INCIDENT
			$incident = new Incident_Model();
			$incident->location_id = $location->id;
			$incident->user_id = 0;
			$incident->incident_title = (string)$post->incident_title;
			$incident->incident_description = (string)$post->incident_description;
			$incident->incident_date = (string)$post->incident_date;
			$incident->incident_active = (string)$post->active;
			$incident->incident_verified = (string)$post->verified;
			$incident->incident_dateadd = date("Y-m-d H:i:s",time());
			$incident->save();
			
			// STEP 3: SAVE CATEGORIES
			$incident_category = split(",", (string)$post->incident_category);
			foreach($incident_category as $item)
			{
				if ($item != "")
				{
					$incident_category = new Incident_Category_Model();
					$incident_category->incident_id = $incident->id;
					$incident_category->category_id = $item;
					$incident_category->save();
				}
			}
			
			// STEP 4: SAVE MEDIA
			// a. News
			$news = new Media_Model();
			$news->location_id = $location->id;
			$news->incident_id = $incident->id;
			$news->media_type = 4;		// News
			$news->media_link = (string)$post->news;
			$news->media_date = date("Y-m-d H:i:s",time());
			$news->save();

			
			// b. Video
			$video = new Media_Model();
			$video->location_id = $location->id;
			$video->incident_id = $incident->id;
			$video->media_type = 2;		// Video
			$video->media_link = (string)$post->video;
			$video->media_date = date("Y-m-d H:i:s",time());
			$video->save();
			
			
			// STEP 5: SAVE PERSONAL INFORMATION
            $person = new Incident_Person_Model();
			$person->location_id = $location->id;
			$person->incident_id = $incident->id;
			$person->person_first = (string)$post->person_first;
			$person->person_phone = (string)$post->person_phone;
			$person->person_email = (string)$post->person_email;
			$person->person_ip = (string)$post->person_ip;
			$person->person_date = date("Y-m-d H:i:s",time());
			$person->save();
		}
		
		echo "******************************************<BR>";
		echo "******************************************<BR>";
		echo "**** IMPORT COMPLETE!!!<BR>";
		echo "******************************************<BR>";
		echo "******************************************<BR>";
	}
}
