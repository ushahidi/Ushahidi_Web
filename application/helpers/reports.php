<?php 
/**
 * Reports Helper class.
 *
 * This class holds functions used for new report submission from both the backend and frontend.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 *
 */

class reports_Core {
			
	/**
	 * Validation of form fields
	 *
	 * @param mixed $post Variable that holds all that is submitted
	 */
	public static function validate($post)
	{
		$post->add_rules('incident_title','required', 'length[3,200]');
		$post->add_rules('incident_description','required');
		$post->add_rules('incident_date','required','date_mmddyyyy');
		$post->add_rules('incident_hour','required','between[1,12]');
		$post->add_rules('incident_minute','required','between[0,59]');
			
		if ($_POST['incident_ampm'] != "am" && $_POST['incident_ampm'] != "pm")
		{
			$post->add_error('incident_ampm','values');
		}
			
		// Validate for maximum and minimum latitude values
		$post->add_rules('latitude','required','between[-90,90]');
		// Validate for maximum and minimum longitude values		
		$post->add_rules('longitude','required','between[-180,180]');	
		$post->add_rules('location_name','required', 'length[3,200]');

		//XXX: Hack to validate for no checkboxes checked
		if (!isset($_POST['incident_category']))
		{
			$post->incident_category = "";
			$post->add_error('incident_category','required');
		}
		else
		{
			$post->add_rules('incident_category.*','required','numeric');
		}

		// Validate only the fields that are filled in
		if (!empty($_POST['incident_news']))
		{
			foreach ($_POST['incident_news'] as $key => $url) 
			{
				if (!empty($url) AND !(bool) filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED))
				{
					$post->add_error('incident_news','url');
				}
			}
		}

		// Validate only the fields that are filled in
		if (!empty($_POST['incident_video']))
		{
			foreach ($_POST['incident_video'] as $key => $url) 
			{
				if (!empty($url) AND !(bool) filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED))
				{
					$post->add_error('incident_video','url');
				}
			}
		}

		// Validate photo uploads
		$post->add_rules('incident_photo', 'upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[2M]');


		// Validate Personal Information
		if (!empty($_POST['person_first']))
		{
			$post->add_rules('person_first', 'length[3,100]');
		}

		if (!empty($_POST['person_last']))
		{
			$post->add_rules('person_last', 'length[3,100]');
		}

		if (!empty($_POST['person_email']))
		{
			$post->add_rules('person_email', 'email', 'length[3,100]');
		}
		
	}
	
	/**
	 * Function to save report location
	 * 
	 * @param mixed $location_model Instance of the location model
	 * @param mixed $post
	 */
	public static function save_location($location_model, $post)
	{
		
		$location= $location_model;
		$location->location_name = $post->location_name;
		$location->latitude = $post->latitude;
		$location->longitude = $post->longitude;
		$location->location_date = date("Y-m-d H:i:s",time());
		$location->save();
	}
	
	/**
	 * Initiates the Incident Saving process
	 *
	 * @param mixed $incident_model
	 * @param mixed $location_model
	 * @param mixed $post
	 * @param int $id ID no. of the report
	 *
	 */
	public static function check_incident($incident_model, $location_model, $post, $id= NULL)
	{
		
		$incident= $incident_model;
		$location= $location_model;
		$incident->location_id = $location->id;
		//$incident->locale = $post->locale;
		$incident->form_id = $post->form_id;
		$incident->user_id = $_SESSION['auth_user']->id;
		$incident->incident_title = $post->incident_title;
		$incident->incident_description = $post->incident_description;

		$incident_date=explode("/",$post->incident_date);
		// Where the $_POST['date'] is a value posted by form in mm/dd/yyyy format
		$incident_date=$incident_date[2]."-".$incident_date[0]."-".$incident_date[1];

		$incident_time = $post->incident_hour . ":" . $post->incident_minute . ":00 " . $post->incident_ampm;
		$incident->incident_date = date( "Y-m-d H:i:s", strtotime($incident_date . " " . $incident_time) );
				
		// Is this new or edit?
		if ($id)	
		{
			// edit
			$incident->incident_datemodify = date("Y-m-d H:i:s",time());
		}
		else		
		{
			// new
			$incident->incident_dateadd = date("Y-m-d H:i:s",time());
		}
		
	}
	
	/**
	 * Function to record the verification/approval actions
	 *
	 * @param mixed $verify_model Instance of the verify model
	 * @param mixed $incident_model
	 * @param mixed $post
	 *
	 */
	public static function verify_approve($verify_model, $incident_model, $post)
	{
		$verify= $verify_model;
		$incident= $incident_model;
		
		$verify->incident_id = $incident->id;
		// Record 'Verified By' Action
		$verify->user_id = $_SESSION['auth_user']->id;			
		$verify->verified_date = date("Y-m-d H:i:s",time());
				
		if ($post->incident_active == 1)
		{
			$verify->verified_status = '1';
		}
		elseif ($post->incident_verified == 1)
		{
			$verify->verified_status = '2';
		}
		elseif ($post->incident_active == 1 && $post->incident_verified == 1)
		{
			$verify->verified_status = '3';
		}
		else
		{
			$verify->verified_status = '0';
		}
		$verify->save();		
	} 
	
	/**
	 * Function that saves incident geometries
	 *
	 * @param mixed $incident_model
	 *
	 */
	public static function save_inc_geometry($incident_model)
	{
		$incident= $incident_model;
		ORM::factory('geometry')->where('incident_id',$incident->id)->delete_all();
		if (isset($post->geometry)) 
		{
			foreach($post->geometry as $item)
			{
				if(!empty($item))
				{
					//Decode JSON
					$item = json_decode($item);
					//++ TODO - validate geometry
					$geometry = (isset($item->geometry)) ? mysql_escape_string($item->geometry) : "";
					$label = (isset($item->label)) ? mysql_escape_string(substr($item->label, 0, 150)) : "";
					$comment = (isset($item->comment)) ? mysql_escape_string(substr($item->comment, 0, 255)) : "";
					$color = (isset($item->color)) ? mysql_escape_string(substr($item->color, 0, 6)) : "";
					$strokewidth = (isset($item->strokewidth) AND (float) $item->strokewidth) ? 
					(float) $item->strokewidth : "2.5";
					if ($geometry)
					{
						//++ Can't Use ORM for this
						$sql = "INSERT INTO ".Kohana::config('database.default.table_prefix')."geometry (
								incident_id, geometry, geometry_label, geometry_comment, geometry_color, geometry_strokewidth ) 
								VALUES( ".$incident->id.",
								GeomFromText( '".$geometry."' ),'".$label."','".$comment."','".$color."','".$strokewidth."')";
						$db->query($sql);
					}
				}
			}
		}
	}
	
	/**
	 * Function to save incident categories
	 *
	 * @param mixed $post
	 * @param mixed $incident_model
	 *
	 */
	public static function save_category($post, $incident_model)
	{
		$incident= $incident_model;
		foreach ($post->incident_category as $item)
		{
			$incident_category = new Incident_Category_Model();
			$incident_category->incident_id = $incident->id;
			$incident_category->category_id = $item;
			$incident_category->save();
		}
	}
	
	/**
	 * Function to save news, photos and videos
	 *
	 * @param mixed $location_model
	 * @param mixed $incident_model
	 * @param mixed $post
	 *
	 */
	public static function save_media($location_model, $incident_model, $post)
	{
		$location= $location_model;
		$incident= $incident_model;
		// a. News
		foreach($post->incident_news as $item)
		{
			if (!empty($item))
			{
				$news = new Media_Model();
				$news->location_id = $location->id;
				$news->incident_id = $incident->id;
				$news->media_type = 4;		// News
				$news->media_link = $item;
				$news->media_date = date("Y-m-d H:i:s",time());
				$news->save();
			}
		}

		// b. Video
		foreach($post->incident_video as $item)
		{
			if(!empty($item))
			{
				$video = new Media_Model();
				$video->location_id = $location->id;
				$video->incident_id = $incident->id;
				$video->media_type = 2;		// Video
				$video->media_link = $item;
				$video->media_date = date("Y-m-d H:i:s",time());
				$video->save();
			}
		}

		// c. Photos
		$filenames = upload::save('incident_photo');
		$i = 1;
		foreach ($filenames as $filename)
		{
			$new_filename = $incident->id . "_" . $i . "_" . time();

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
			$photo = new Media_Model();
			$photo->location_id = $location->id;
			$photo->incident_id = $incident->id;
			$photo->media_type = 1; // Images
			$photo->media_link = $new_filename.$file_type;
			$photo->media_medium = $new_filename."_m".$file_type;
			$photo->media_thumb = $new_filename."_t".$file_type;
			$photo->media_date = date("Y-m-d H:i:s",time());
			$photo->save();
			$i++;
		}
	}
	
	/**
	 * Function to save custom field values
	 *
	 * @param mixed $incident_model
	 * @param mixed $post
	 *
	 */
	public static function save_custom_fields($incident_model, $post)
	{
		$incident=$incident_model;
		if(isset($post->custom_field))
		{
			foreach($post->custom_field as $key => $value)
			{
				$form_response = ORM::factory('form_response')
								->where('form_field_id', $key)
								->where('incident_id', $incident->id)
								->find();
													 
				if ($form_response->loaded == true)
				{
					$form_response->form_field_id = $key;
					$form_response->form_response = $value;
					$form_response->save();
				}
				else
				{
					$form_response = new Form_Response_Model();
					$form_response->form_field_id = $key;
					$form_response->incident_id = $incident->id;
					$form_response->form_response = $value;
					$form_response->save();
				}
			}
		}	
	}
	
	/**
	 * Function to save personal information
	 *
	 * @param mixed $incident_model
	 * @param mixed $location_model
	 * @param mixed $post
	 *
	 */
	public static function save_personal_info($incident_model, $location_model, $post)
	{
		$incident = $incident_model;
		$location = $location_model;
		$person = new Incident_Person_Model();
		$person->location_id = $location->id;
		$person->incident_id = $incident->id;
		$person->person_first = $post->person_first;
		$person->person_last = $post->person_last;
		$person->person_email = $post->person_email;
		$person->person_date = date("Y-m-d H:i:s",time());
		$person->save();		
	}
}
?>