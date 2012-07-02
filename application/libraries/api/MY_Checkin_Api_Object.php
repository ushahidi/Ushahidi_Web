<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Checkin_Api_Object
 *
 * This class handles reports activities via the API.
 *
 * @version 1 - Brian Herbert (not sure what this version is for, though)
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Checkin_Api_Object extends Api_Object_Core {

	/**
	 * Sort descriptor ASC or DESC
	 * @var string
	 */
	private $sort;

	/**
	 * Column name by which to order the records
	 * @var string
	 */
	private $order_field;

	/**
	 * @var bool
	 */
	private $checkin_photo = FALSE;

	/**
	 * Absolute path for the upload location for media
	 * @var string
	 */
	private $abs_upload_url = FALSE;
	
	public function __construct($api_service)
	{
		parent::__construct($api_service);
		$this->abs_upload_url = url::site().Kohana::config('upload.relative_directory', TRUE);
		
		// If Checkins aren't enabled, we want to essentially shut off this API library
		
		if (Kohana::config('settings.checkins') != 1)
		{
			// Say what is going on
			$this->set_ci_error_message(array(
				"error" => $this->api_service->get_error_msg(010)
			));

			$this->show_response();
			
		}
	}
	
	/**
	 * Implementation of abstract method in parent
	 *
	 * Handles the API task parameters
	 */
	public function perform_task()
	{	
		// Check if the 'by' parameter has been specified
		if ( ! $this->api_service->verify_array_index($this->request, 'action'))
		{
			// Set "all" as the default method for fetching incidents
			$this->action = 'all';
		}
		else
		{
			$this->action = $this->request['action'];
		}
		
		// Begin task switching
		switch ($this->action)
		{
			// Add a checkin
			case "ci":
				$this->_do_ci();
			break;
			
			case "get_ci":
				$this->_do_get_ci();
			break;

			// Error therefore set error message 
			default:
				$this->set_ci_error_message(array(
					"error" => $this->api_service->get_error_msg(002)
				));
		}
		
		$this->show_response();
	}
	
	public function _do_get_ci()
	{
		$data = $this->gather_checkins(
					@$this->request['id'],
					@$this->request['userid'],
					@$this->request['mobileid'],
					@$this->request['mapdata']
				);
					
					
		if (count($data) > 0)
		{
			// Data!
			$this->response = array(
				"payload" => array(
					"checkins" => $data["checkins"],
					"domain" => $this->domain,
					"success" => "true"
				),
				"error" => $this->api_service->get_error_msg(0)
			);
		}
		else
		{
			// No data
			$this->response = array(
				"payload" => array(
					"domain" => $this->domain,
					"success" => "false"
				),
				"error" => $this->api_service->get_error_msg(007)
			);
		}		
		
	}
	
	public function gather_checkins($id, $user_id, $mobileid, $mapdata)
	{
		$data = array();
		
		if ($mobileid != '')
		{
			$find_user = ORM::factory('user_devices')->find($mobileid);
			$user_id = $find_user->user_id;
		}
		
		if ($user_id == '')
		{
			$where_user_id = array('checkin.user_id !=' => '-1');
		}
		else
		{
			$where_user_id = array('checkin.user_id' => $user_id);
		}
		
		if ($id == '')
		{
			$where_id = array('checkin.id !=' => '-1');
		}
		else
		{
			$where_id = array('checkin.id' => $id);
		}
		
		$orderby = 'checkin.id';
		if (isset($this->request['orderby']))
		{
			$orderby = $this->request['orderby'];
		}
		
		$sort = 'ASC';
		if (isset($this->request['sort']))
		{
			$sort = $this->request['sort'];
		}
		
		
		$since_id = 0;
		if (isset($this->request['sinceid']))
		{
			$since_id = $this->request['sinceid'];
		}
		
		//echo $this->request['sqllimit'];
		$limit = 20;
		if (isset($this->request['sqllimit']))
		{
			$limit = $this->request['sqllimit'];
		}
		
		$offset = 0;
		if (isset($this->request['sqloffset']))
		{
			$offset = $this->request['sqloffset'];
		}
		
		$checkins = ORM::factory('checkin')
					->select('DISTINCT checkin.*')
					->where($where_id)
					->where($where_user_id)
					->where('checkin.id >=',$since_id)
					->with('user')
					->with('location')
					->orderby($orderby,$sort)
					->find_all($limit,$offset);
		
		$seen_latest_ci = array();
		$users_names = array();
		$i = 0;
		foreach($checkins as $checkin)
		{
			$data["checkins"][$i] = array(
				"id" => $checkin->id,
				"user" => array(
					"id" => $checkin->user_id,
					"username" => $checkin->user->username,
					"name" => $checkin->user->name,
					"color" => $checkin->user->color,
				),
				"loc" => $checkin->location_id,
				"msg" => $checkin->checkin_description,
				"date" => $checkin->checkin_date,
				"lat" => $checkin->location->latitude,
				"lon" => $checkin->location->longitude
			);
						
			$j = 0;
			foreach ($checkin->media as $media)
			{
				$data["checkins"][$i]['media'][(int)$j] = array(
					"id" => $media->id,
					"type" => $media->media_type,
					"link" => url::convert_uploaded_to_abs($media->media_link),
					"medium" => url::convert_uploaded_to_abs($media->media_medium),
					"thumb" => url::convert_uploaded_to_abs($media->media_thumb)
				);
				$j++;
			}
			
			$j = 0;
			foreach ($checkin->comment as $comment)
			{			
				if ($comment->user_id != 0 )
				{
					$author = $comment->user->name;
					$email = $comment->user->email;
					$username = $comment->user->username;
				}
				else
				{
					$author = $comment->comment_author;
					$email = $comment->comment_email;
					$username = '';
				}
				
				$data["checkins"][$i]['comments'][(int)$j] = array(
					"id" => $comment->id,
					"user_id" => $comment->user_id,
					"author" => $author,
					"email" => $email,
					"username" => $username,
					"description" => $comment->comment_description,
					"date" => $comment->comment_date
				);
				$j++;
			}
			
			// If we are displaying some extra map data...
			
			if ($mapdata != '')
			{
				
				if ( ! isset($seen_latest_ci[$checkin->user_id]))
				{
					$opacity = 1;
				}
				else
				{
					$opacity = .5;
				}
				
				$seen_latest_ci[$checkin->user_id] = $checkin->user_id;
				$data["checkins"][$i]['opacity'] = $opacity;
				
			}
			
			$i++;
		}
		
		// foreach ($users_names as $user_data)
		// {
		// 	$data["users"][] = $user_data;
		// }
		
		return $data;
	}
	
	public function _do_ci()
	{
		// Check if mobileid is set
		
		if ( ! $this->api_service->verify_array_index($this->request, 'mobileid'))
		{
			$this->set_ci_error_message(array(
				"error" => $this->api_service->get_error_msg(001, 'mobileid')
			));
			return;
		}
		
		// Check if lat, lon is set
		
		if ( ! $this->api_service->verify_array_index($this->request, 'lat')
			AND	 ! $this->api_service->verify_array_index($this->request, 'lon'))
		{
			$this->set_ci_error_message(array(
				"error" => $this->api_service->get_error_msg(001, 'lat, lon')
			));
			return;
		}

		$checkedin = $this->register_checkin(
					$this->request['mobileid'],
					$this->request['lat'],
					$this->request['lon'],
					@$this->request['message'],
					@$this->request['firstname'],
					@$this->request['lastname'],
					@$this->request['email'],
					@$this->request['color']
				);

		$this->response = array(
			"payload" => array(
				"checkin_id" => $checkedin['checkin_id'],
				"user_id" => $checkedin['user_id'],
				"domain" => $this->domain,
				"success" => "true"
			),
			"error" => $this->api_service->get_error_msg(0)
		);
	}
	
	/**
	 * This function performs the actual checkin and will register a new user
	 *	 if the user doesn't exist. Also, if the name and email is passed with
	 *	 the checkin, the user will be updated.
	 *
	 *	 mobileid, lat and lon are the only required fields.
	 *
	 * Handles the API task parameters
	 */
	public function register_checkin($mobileid,$lat,$lon,$message=FALSE,$firstname=FALSE,$lastname=FALSE,$email=FALSE,$color=FALSE)
	{
		// Check if this device has been registered yet
		
		if ( ! User_Devices_Model::device_registered($mobileid))
		{
			// Device has not been registered yet. Register it!
			
			// TODO: Formalize the user creation process. For now we are creating
			//		 a new user for every new device but eventually, we need
			//		 to be able to have multiple devices for each user
			
			// Name of the user
			$user_name = ($firstname AND $lastname)
			    ? $firstname.' '.$lastname
			    : '';
			
			// Email address
			$user_email = ($email) ? $email : $this->getRandomString();
			
			// Color
			$user_color = ($color) ? $color : $this->random_color();
			
			// Check if email exists
			
			$query = 'SELECT id FROM `'.$this->table_prefix.'users` WHERE `email` = ? LIMIT 1;';
			$usercheck = $this->db->query($query, $user_email);
			
			if ( isset($usercheck[0]->id) )
			{
				$user_id = $usercheck[0]->id;
			}
			else
			{
				// Create a new user
				$user = ORM::factory('user');
				$user->name = $user_name;
				$user->email = $user_email;
				$user->username = $this->getRandomString();
				$user->password = 'checkinuserpw';
				$user->color = $user_color;
				$user->add(ORM::factory('role', 'login'));
				$user_id = $user->save();

			}

			//	 TODO: When we have user registration down, we need to pass a user id here
			//		   so we can assign it to a specific user
			User_Devices_Model::register_device($mobileid,$user_id);
		}
		
		// Now we have a fully registered device so lets update our user if we need to
		
		if ($firstname AND $lastname AND $email)
		{
			$user_id = User_Devices_Model::device_owner($mobileid);
			$user_name = $firstname.' '.$lastname;
			$user_email = $email;
			
			$user = ORM::factory('user',$user_id);
			$user->name = $user_name;
			$user->email = $user_email;
			
			if ($color)
			{
				$user->color = $color;
			}
			
			$user_id = $user->save();
			$user_id = $user_id->id;
		}
		
		// Get our user id if it hasn't already been set by one of the processes above
		
		if ( ! isset($user_id))
		{
			$user_id = User_Devices_Model::device_owner($mobileid);
		}
		
		// Whew, now that all that is out of the way, do the flippin checkin!
		
		// FIRST, save the location
		
		$location = new Location_Model();
		$location->location_name = $lat.','.$lon;
		$location->latitude = $lat;
		$location->longitude = $lon;
		$location->location_date = date("Y-m-d H:i:s",time());
		$location_id = $location->save();
		
		// SECOND, save the checkin
		if ( ! $message)
		{
			$message = '';
		}
		
		$checkin = ORM::factory('checkin');
		$checkin->user_id = $user_id;
		$checkin->location_id = $location_id;
		$checkin->checkin_description = $message;
		$checkin->checkin_date = date("Y-m-d H:i:s",time());
		$checkin_id = $checkin->save();
		
		// THIRD, save the photo, if there is a photo
		
		if (isset($_FILES['photo']))
		{
			$filename = upload::save('photo');
			
			$new_filename = 'ci_'.$user_id.'_'.time().'_'.$this->getRandomString(4);
			$file_type = strrev(substr(strrev($filename),0,4));
			
			// IMAGE SIZES: 800X600, 400X300, 89X59
			
			// Large size
			Image::factory($filename)->resize(800,600,Image::AUTO)
				->save(Kohana::config('upload.directory', TRUE).$new_filename.$file_type);

			// Medium size
			Image::factory($filename)->resize(400,300,Image::HEIGHT)
				->save(Kohana::config('upload.directory', TRUE).$new_filename."_m".$file_type);

			// Thumbnail
			Image::factory($filename)->resize(89,59,Image::HEIGHT)
				->save(Kohana::config('upload.directory', TRUE).$new_filename."_t".$file_type);
			
			// Name the files for the DB
			$media_link = $new_filename.$file_type;
			$media_medium = $new_filename.'_m'.$file_type;
			$media_thumb = $new_filename.'_t'.$file_type;
			
			// Okay, now we have these three different files on the server, now check to see
			//	 if we should be dropping them on the CDN
			
			if (Kohana::config("cdn.cdn_store_dynamic_content"))
			{
				$media_link = cdn::upload($media_link);
				$media_medium = cdn::upload($media_medium);
				$media_thumb = cdn::upload($media_thumb);
				
				// We no longer need the files we created on the server. Remove them.
				$local_directory = rtrim(Kohana::config('upload.directory', TRUE), '/').'/';
				unlink($local_directory.$new_filename.$file_type);
				unlink($local_directory.$new_filename.'_m'.$file_type);
				unlink($local_directory.$new_filename.'_t'.$file_type);
			}

			// Remove the temporary file
			unlink($filename);

			// Save to DB
			$media_photo = new Media_Model();
			$media_photo->location_id = $location_id;
			$media_photo->checkin_id = $checkin_id;
			$media_photo->media_type = 1; // Images
			$media_photo->media_link = $media_link;
			$media_photo->media_medium = $media_medium;
			$media_photo->media_thumb = $media_thumb;
			$media_photo->media_date = date("Y-m-d H:i:s",time());
			$media_photo->save();
		}
		
		$return = array("checkin_id" => $checkin_id->id, "user_id" => $user_id);
		
		// Hook on successful checkin
		Event::run('ushahidi_action.checkin_recorded', $checkin);
		
		return $return;
		
	}
	
	// This function helps support some random string action for user accounts and filenames
	//	 This supports strings up to 32 characters in length due to the md5 hash
	private function getRandomString($length = 31)
	{
		return substr(md5(uniqid(rand(), true)), 0, $length);
	}
	
	public function show_response()
	{
		if ($this->response_type == 'json')
		{
			echo json_encode($this->response);
		} 
		else 
		{
			echo $this->array_as_xml($this->response, array());
		}
	}
	
	public function random_color()
	{
		$hex = array("00","33","66","99","CC","FF");
		$r1 = array_rand($hex);
		$r2 = array_rand($hex);
		$r3 = array_rand($hex);
		return $hex[$r1].$hex[$r2].$hex[$r3];
	}
	
	public function set_ci_error_message($resp)
	{
		$this->response = $resp;
	}
}
