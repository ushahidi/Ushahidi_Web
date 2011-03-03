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
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Checkin_Api_Object extends Api_Object_Core {

    private $sort; // Sort descriptor ASC or DESC
    private $order_field; // Column name by which to order the records
    private $checkin_photo = FALSE;
    private $abs_upload_url = FALSE;
    
    public function __construct($api_service)
    {
        parent::__construct($api_service);
        $this->abs_upload_url = url::site().Kohana::config('upload.relative_directory', TRUE);
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
            case "all":
            	die("Returning checkins not in yet. Hold yer horses.");	
            break;
            
            // Add a checkin
            case "ci":
            	$this->_do_ci();
            break;
            
            case "get_ci":
            	$this->_do_get_ci();
            break;

            // Error therefore set error message 
            default:
                $this->set_error_message(array(
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
					@$this->request['email']
				);
					
					
		if(count($data) > 0)
		{
			// Data!
			$this->response = array(
					"payload" => array(
						"checkins" => $data,
						"domain" => $this->domain,
						"success" => "true"
					),
					"error" => $this->api_service->get_error_msg(0)
					);
		}else{
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
	
	public function gather_checkins($id,$user_id,$mobileid,$email)
	{
		$data = array();
		
		if($mobileid != '')
		{
			$find_user = ORM::factory('user_devices')->find($mobileid);
			$user_id = $find_user->user_id;
		}
		
		if($user_id == '')
		{
			$where_user_id = array('checkin.user_id !=' => '-1');
		}else{
			$where_user_id = array('checkin.user_id' => $user_id);
		}
		
		if($id == '')
		{
			$where_id = array('checkin.id !=' => '-1');
		}else{
			$where_id = array('checkin.id' => $id);
		}
		
		$checkins = ORM::factory('checkin')
					//->select('DISTINCT checkin.*, media.id as media:media_id, media.media_type as media:media_type, media.media_link as media:media_link, media.media_medium as media:media_medium, media.media_thumb as media:media_thumb')
					->select('DISTINCT checkin.*')
					->where($where_id)
					->where($where_user_id)
					//->join('media', 'checkin.id', 'media.checkin_id','LEFT')
					//->join('users', 'checkin.id', 'users.id','LEFT')
					->with('user')
					->with('location')
					->find_all();
		
		$i = 0;
		foreach($checkins as $checkin)
		{	
			$data[$i] = array(
				"id" => $checkin->id,
				"user_id" => $checkin->user_id,
				"location_id" => $checkin->location_id,
				"checkin_description" => $checkin->checkin_description,
				"checkin_date" => $checkin->checkin_date,
				"latitude" => $checkin->location->latitude,
				"longitude" => $checkin->location->longitude
			);

			foreach ($checkin->media as $media)
			{
			    $data[$i]['media'][$media->id] = array(
			    	"id" => $media->id,
			    	"media_type" => $media->media_type,
			    	"media_link" => $this->abs_upload_url.$media->media_link,
			    	"media_medium" => $this->abs_upload_url.$media->media_medium,
			    	"media_thumb" => $this->abs_upload_url.$media->media_thumb
			    );
			}

			
			/*
			if( isset($checkin->media->id) )
			{
				var_dump($checkin->media);
				echo '<br/><br/><br/><br/>';
			}
			*/
			
			$i++;
		}
		
		return $data;
	}
	
	public function _do_ci()
	{
		// Check if mobileid is set
            	
        if ( ! $this->api_service->verify_array_index($this->request, 'mobileid'))
        {
            $this->set_error_message(array(
                "error" => $this->api_service->get_error_msg(001, 'mobileid')
            ));
            return;
        }
        
        // Check if lat, lon is set
    	
        if ( ! $this->api_service->verify_array_index($this->request, 'lat')
        	AND  ! $this->api_service->verify_array_index($this->request, 'lon'))
        {
            $this->set_error_message(array(
                "error" => $this->api_service->get_error_msg(001, 'lat, lon')
            ));
            return;
        }

		$this->register_checkin(
					$this->request['mobileid'],
					$this->request['lat'],
					$this->request['lon'],
					@$this->request['message'],
					@$this->request['firstname'],
					@$this->request['lastname'],
					@$this->request['email']
				);
		
		$this->response = array(
				"payload" => array(
					"domain" => $this->domain,
					"success" => "true"
				),
				"error" => $this->api_service->get_error_msg(0)
				);
	}
	
	/**
     * This function performs the actual checkin and will register a new user
     *   if the user doesn't exist. Also, if the name and email is passed with
     *   the checkin, the user will be updated.
     *
     *   mobileid, lat and lon are the only required fields.
     *
     * Handles the API task parameters
     */
	public function register_checkin($mobileid,$lat,$lon,$message=FALSE,$firstname=FALSE,$lastname=FALSE,$email=FALSE)
	{
		// Check if this device has been registered yet
		
		if( ! User_Devices_Model::device_registered($mobileid))
		{
			// Device has not been registered yet. Register it!
			
			// TODO: Formalize the user creation process. For now we are creating
			//       a new user for every new device but eventually, we need
			//       to be able to have multiple devices for each user
			if($firstname AND $lastname)
			{
				$user_name = $firstname.' '.$lastname;	
			}else{
				$user_name = 'Not Fully Registered Checkin User';
			}
			
			if($email)
			{
				$user_email = $email;
			}else{
				$user_email = $this->getRandomString();
			}
			
			// Create a new user
			
			$user = ORM::factory('user');
            $user->name = $user_name;
            $user->email = $user_email;
            $user->username = $this->getRandomString();
            $user->password = 'checkinuserpw';
            $user->add(ORM::factory('role', 'login'));
            $user_id = $user->save();
			
			
			
			//   TODO: When we have user registration down, we need to pass a user id here
			//         so we can assign it to a specific user
			User_Devices_Model::register_device($mobileid,$user_id);
		}
		
		// Now we have a fully registered device so lets update our user if we need to
		
		if($firstname AND $lastname AND $email)
		{
			$user_id = User_Devices_Model::device_owner($mobileid);
			$user_name = $firstname.' '.$lastname;
			$user_email = $email;
			
			$user = ORM::factory('user');
            $user->name = $user_name;
            $user->email = $user_email;
            $user_id = $user->save();
		}
		
		// Get our user id if it hasn't already been set by one of the processes above
		
		if( ! isset($user_id))
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
		
		if( ! $message)
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
		
		if( isset($_FILES['photo']))
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

			// Remove the temporary file
			unlink($filename);

			// Save to DB
			$media_photo = new Media_Model();
			$media_photo->location_id = $location_id;
			$media_photo->checkin_id = $checkin_id;
			$media_photo->media_type = 1; // Images
			$media_photo->media_link = $new_filename.$file_type;
			$media_photo->media_medium = $new_filename."_m".$file_type;
			$media_photo->media_thumb = $new_filename."_t".$file_type;
			$media_photo->media_date = date("Y-m-d H:i:s",time());
			$media_photo->save();
		}
		
	}
	
	// This function helps support some random string action for user accounts and filenames
	//   This supports strings up to 32 characters in length due to the md5 hash
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
}
