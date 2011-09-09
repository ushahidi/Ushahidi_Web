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
 */
class reports_Core {
	
	/**
	 * Maintains the list of parameters used for fetching incidents
	 * in the fetch_incidents method
	 * @var array
	 */
	public static $params = array();
	
			
	/**
	 * Validation of form fields
	 *
	 * @param array $post Values to be validated
	 * @param bool $admin_section Whether the validation is for the admin section
	 */
	public static function validate(array & $post, $admin_section = FALSE)
	{
		// Exception handling
		if ( ! isset($post) OR ! is_array($post))
			return FALSE;
		
		// Create validation object
		$post = Validation::factory($post)
				->pre_filter('trim', TRUE);
		
		$post->add_rules('incident_title','required', 'length[3,200]');
		$post->add_rules('incident_description','required');
		$post->add_rules('incident_date','required','date_mmddyyyy');
		$post->add_rules('incident_hour','required','between[1,12]');
		$post->add_rules('incident_minute','required','between[0,59]');
			
		if ($post->incident_ampm != "am" AND $post->incident_ampm != "pm")
		{
			$post->add_error('incident_ampm','values');
		}
			
		// Validate for maximum and minimum latitude values
		$post->add_rules('latitude','required','between[-90,90]');
		
		// Validate for maximum and minimum longitude values		
		$post->add_rules('longitude','required','between[-180,180]');	
		$post->add_rules('location_name','required', 'length[3,200]');

		//XXX: Hack to validate for no checkboxes checked
		if ( ! isset($post->incident_category))
		{
			$post->incident_category = "";
			$post->add_error('incident_category','required');
		}
		else
		{
			$post->add_rules('incident_category.*','required','numeric');
		}

		// Validate only the fields that are filled in
		if ( ! empty($post->incident_news))
		{
			foreach ($post->incident_news as $key => $url) 
			{
				if ( ! empty($url) AND !(bool) filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED))
				{
					$post->add_error('incident_news','url');
				}
			}
		}

		// Validate only the fields that are filled in
		if ( ! empty($post->incident_video))
		{
			foreach ($post->incident_video as $key => $url) 
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
		if ( ! empty($post->person_first))
		{
			$post->add_rules('person_first', 'length[3,100]');
		}

		if ( ! empty($post->person_last))
		{
			$post->add_rules('person_last', 'length[2,100]');
		}

		if ( ! empty($post->person_email))
		{
			$post->add_rules('person_email', 'email', 'length[3,100]');
		}
		
		// Extra validation rules for the admin section
		if ($admin_section)
		{
			$post->add_rules('location_id','numeric');
			$post->add_rules('message_id','numeric');
			$post->add_rules('incident_active','required', 'between[0,1]');
			$post->add_rules('incident_verified','required', 'between[0,1]');
			$post->add_rules('incident_source','numeric', 'length[1,1]');
			$post->add_rules('incident_information','numeric', 'length[1,1]');
			$post->add_rules('incident_zoom', 'numeric');
		}
		
		// Custom form fields validation
		$errors = customforms::validate_custom_form_fields($post);

		// Check if any errors have been returned
		if (count($errors) > 0)
		{
			foreach ($errors as $field_name => $error)
			{
				$post->add_error($field_name, $error);
			}
		}

		//> END custom form fields validation
		
		// Return
		return $post->validate();
	}
	
	/**
	 * Function to save report location
	 * 
	 * @param Validation $post
	 * @param Location_Model $location Instance of the location model
	 */
	public static function save_location($post, $location)
	{
		// Load the country
		$country = isset($post->country_name)
			? Country_Model::get_country_by_name($post->country_name)
			: Country_Model::get_country_by_name(Kohana::config('settings.default_country'));
		
		// Fetch the country id
		$country_id = ($country)? $country->id : 0;
		
		// Assign country_id retrieved
		$post->country_id = $country_id;
		$location->location_name = $post->location_name;
		$location->latitude = $post->latitude;
		$location->longitude = $post->longitude;
		$location->country_id = $country_id;
		$location->location_date = date("Y-m-d H:i:s",time());
		$location->save();
		
		// Garbage collection
		unset ($country, $country_id);
	}
	
	/**
	 * Saves an incident
	 *
	 * @param Validation $post Validation object with the data to be saved
	 * @param Incident_Model $incident Incident_Model instance to be modified
	 * @param Location_Model $location_model Location to be attached to the incident
	 * @param int $id ID no. of the report
	 *
	 */
	public static function save_report($post, $incident, $location_id)
	{
		// Exception handling
		if ( ! $post instanceof Validation_Core AND  ! $incident instanceof Incident_Model)
		{
			// Throw exception
			throw new Kohana_Exception('Invalid parameter types');
		}
		
		// Verify that the location id exists
		if ( ! Location_Model::is_valid_location($location_id))
		{
			throw new Kohana_Exception(sprintf('Invalid location id specified: ', $location_id));
		}
		
		// Is this new or edit?
		if ($incident->loaded)	
		{
			// Edit
			$incident->incident_datemodify = date("Y-m-d H:i:s",time());
		}
		else		
		{
			// New
			$incident->incident_dateadd = date("Y-m-d H:i:s",time());
		}
		
		$incident->location_id = $location_id;
		//$incident->locale = $post->locale;
		if (isset($post->form_id))
		{
			$incident->form_id = $post->form_id;
		}
		
		// Check if the user id has been specified
		if (isset($_SESSION['auth_user']))
		{
			$incident->user_id = $_SESSION['auth_user']->id;
		}
		
		$incident->incident_title = $post->incident_title;
		$incident->incident_description = $post->incident_description;

		$incident_date=explode("/",$post->incident_date);
		// Where the $_POST['date'] is a value posted by form in mm/dd/yyyy format
		$incident_date=$incident_date[2]."-".$incident_date[0]."-".$incident_date[1];

		$incident_time = $post->incident_hour . ":" . $post->incident_minute . ":00 " . $post->incident_ampm;
		$incident->incident_date = date( "Y-m-d H:i:s", strtotime($incident_date . " " . $incident_time) );
				
		
		// Is this an Email, SMS, Twitter submitted report?
		if ( ! empty($post->service_id))
		{
			// SMS
			if ($post->service_id == 1)
			{
				$incident->incident_mode = 2;
			}
			// Email
			elseif($post->service_id == 2)
			{
				$incident->incident_mode = 3;
			}
			// Twitter
			elseif ($post->service_id == 3)
			{
				$incident->incident_mode = 4;
			}
			else
			{
				// Default to Web Form
				$incident->incident_mode = 1;
			}
		}
		
		// Check for incident evaluation info
		if ( ! empty($post->incident_active))
		{
			$incident->incident_active = $post->incident_active;
		}
		
		// Verification status
		if ( ! empty($post->incident_verified))
		{
			$incident->incident_verified = $post->incident_verified;
		}
		
		// Incident source
		if ( ! empty($post->incident_source))
		{
			$incident->incident_source = $post->incident_source;
		}
		
		// Incident information
		if ( ! empty($post->incident_information))
		{
			$incident->incident_information = $post->incident_information;
		}
		
		// Incident zoom
		if ( ! empty($post->incident_zoom))
		{
			$incident->incident_zoom = intval($post->incident_zoom);
		}
		
		// Tag this as a report that needs to be sent out as an alert
		if ($incident->incident_active == 1 AND $incident->incident_alert_status != 2)
		{ 
			// 2 = report that has had an alert sent
			$incident->incident_alert_status = '1';
		}
		
		// Remove alert if report is unactivated and alert hasn't yet been sent
		if ($incident->incident_active == 0 AND $incident->incident_alert_status == 1)
		{
			$incident->incident_alert_status = '0';
		}
		
		// Save the incident
		$incident->save();
	}
	
	/**
	 * Function to record the verification/approval actions
	 *
	 * @param mixed $post
	 * @param mixed $verify Instance of the verify model
	 * @param mixed $incident
	 *
	 */
	public static function verify_approve($post, $verify, $incident)
	{
		// @todo Exception handling
		
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
		elseif ($post->incident_active == 1 AND $post->incident_verified == 1)
		{
			$verify->verified_status = '3';
		}
		else
		{
			$verify->verified_status = '0';
		}
		
		// Save
		$verify->save();		
	} 
	
	/**
	 * Function that saves incident geometries
	 *
	 * @param Incident_Model $incident
	 * @param mixed $incident
	 *
	 */
	public static function save_report_geometry($post, $incident)
	{
		// Delete all current geometry
		ORM::factory('geometry')->where('incident_id',$incident->id)->delete_all();
		
		if (isset($post->geometry)) 
		{
			// Database object
			$db = new Database();
			
			// SQL for creating the incident geometry
			$sql = "INSERT INTO ".Kohana::config('database.default.table_prefix')."geometry "
				. "(incident_id, geometry, geometry_label, geometry_comment, geometry_color, geometry_strokewidth) "
				. "VALUES(%d, GeomFromText('%s'), '%s', '%s', '%s', %s)";
				
			foreach($post->geometry as $item)
			{
				if ( ! empty($item))
				{
					//Decode JSON
					$item = json_decode($item);
					//++ TODO - validate geometry
					$geometry = (isset($item->geometry)) ? $db->escape_str($item->geometry) : "";
					$label = (isset($item->label)) ? $db->escape_str(substr($item->label, 0, 150)) : "";
					$comment = (isset($item->comment)) ? $db->escape_str(substr($item->comment, 0, 255)) : "";
					$color = (isset($item->color)) ? $db->escape_str(substr($item->color, 0, 6)) : "";
					$strokewidth = (isset($item->strokewidth) AND (float) $item->strokewidth) ? (float) $item->strokewidth : "2.5";
					if ($geometry)
					{
						// 	Format the SQL string
						$sql = sprintf($sql, $incident->id, $geometry, $label, $comment, $color, $strokewidth);
						
						// Execute the query
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
	public static function save_category($post, $incident)
	{
		// Delete Previous Entries
		ORM::factory('incident_category')->where('incident_id', $incident->id)->delete_all();
		
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
	 * @param mixed $post
	 *
	 */
	public static function save_media($post, $incident)
	{
		// Delete Previous Entries
		ORM::factory('media')->where('incident_id',$incident->id)->where('media_type <> 1')->delete_all();
		
		// a. News
		foreach ($post->incident_news as $item)
		{
			if ( ! empty($item))
			{
				$news = new Media_Model();
				$news->location_id = $incident->location_id;
				$news->incident_id = $incident->id;
				$news->media_type = 4;		// News
				$news->media_link = $item;
				$news->media_date = date("Y-m-d H:i:s",time());
				$news->save();
			}
		}

		// b. Video
		foreach ($post->incident_video as $item)
		{
			if ( ! empty($item))
			{
				$video = new Media_Model();
				$video->location_id = $incident->location_id;
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
			$photo->location_id = $incident->location_id;
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
	public static function save_custom_fields($post, $incident)
	{
		if (isset($post->custom_field))
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
	 * @param mixed $post
	 *
	 */
	public static function save_personal_info($post, $incident)
	{
		// Delete Previous Entries
		ORM::factory('incident_person')->where('incident_id',$incident->id)->delete_all();
		
		$person = new Incident_Person_Model();
		$person->location_id = $incident->location_id;
		$person->incident_id = $incident->id;
		$person->person_first = $post->person_first;
		$person->person_last = $post->person_last;
		$person->person_email = $post->person_email;
		$person->person_date = date("Y-m-d H:i:s",time());
		$person->save();		
	}
	
	/**
	 * Helper function to fetch and optionally paginate the list of 
	 * incidents/reports via the Incident Model using one or all of the 
	 * following URL parameters
	 *	- category
	 *	- location bounds
	 *	- incident mode
	 *	- media
	 *	- location radius
	 *
	 * @param $paginate Optionally paginate the incidents - Default is FALSE
	 * @return Database_Result
	 */
	public static function fetch_incidents($paginate = FALSE)
	{
		// Reset the paramters
		self::$params = array();
		
		// Initialize the category id
		$category_id = 0;
		
		$table_prefix = Kohana::config('database.default.table_prefix');
		
		// Fetch the URL data into a local variable
		$url_data = array_merge($_GET);
		
		// Check if some parameter values are separated by "," except the location bounds
		$exclude_params = array('c' => '', 'v' => '', 'm' => '', 'mode' => '', 'sw'=> '', 'ne'=> '');
		foreach ($url_data as $key => $value)
		{
			if (array_key_exists($key, $exclude_params) AND !is_array($value))
			{
				if (is_array(explode(",", $value)))
				{
					$url_data[$key] = explode(",", $value);
				}
			}
		}
		
		//> BEGIN PARAMETER FETCH
		
		// 
		// Check for the category parameter
		// 
		if ( isset($url_data['c']) AND !is_array($url_data['c']) AND intval($url_data['c']) > 0)
		{
			// Get the category ID
			$category_id = intval($_GET['c']);
			
			// Add category parameter to the parameter list
			array_push(self::$params,
				'(c.id = '.$category_id.' OR c.parent_id = '.$category_id.')',
				'c.category_visible = 1'
			);
		}
		elseif (isset($url_data['c']) AND is_array($url_data['c']))
		{
			// Sanitize each of the category ids
			$category_ids = array();
			foreach ($url_data['c'] as $c_id)
			{
				if (intval($c_id) > 0)
				{
					$category_ids[] = intval($c_id);
				}
			}
			
			// Check if there are any category ids
			if (count($category_ids) > 0)
			{
				$category_ids = implode(",", $category_ids);
			
				array_push(self::$params,
					'(c.id IN ('.$category_ids.') OR c.parent_id IN ('.$category_ids.'))',
					'c.category_visible = 1'
				);
			}
		}
		
		// 
		// Incident modes
		// 
		if (isset($url_data['mode']) AND is_array($url_data['mode']))
		{
			$incident_modes = array();
			
			// Sanitize the modes
			foreach ($url_data['mode'] as $mode)
			{
				if (intval($mode) > 0)
				{
					$incident_modes[] = intval($mode);
				}
			}
			
			// Check if any modes exist and add them to the parameter list
			if (count($incident_modes) > 0)
			{
				array_push(self::$params, 
					'i.incident_mode IN ('.implode(",", $incident_modes).')'
				);
			}
		}
		
		// 
		// Location bounds parameters
		// 
		if (isset($url_data['sw']) AND isset($url_data['ne']))
		{
			$southwest = $url_data['sw'];
			$northeast = $url_data['ne'];
			
			if ( count($southwest) == 2 AND count($northeast) == 2 )
			{
				$lon_min = (float) $southwest[0];
				$lon_max = (float) $northeast[0];
				$lat_min = (float) $southwest[1];
				$lat_max = (float) $northeast[1];
			
				// Add the location conditions to the parameter list
				array_push(self::$params, 
					'l.latitude >= '.$lat_min,
					'l.latitude <= '.$lat_max,
					'l.longitude >= '.$lon_min,
					'l.longitude <= '.$lon_max
				);
			}
		}
		
		// 
		// Location bounds - based on start location and radius
		// 
		if (isset($url_data['radius']) AND isset($url_data['start_loc']))
		{
			//if $url_data['start_loc'] is just comma delimited strings, then make it into an array
			if(!is_array($url_data['start_loc']))
			{
				$url_data['start_loc'] = explode(",", $url_data['start_loc']);
			}
			if (intval($url_data['radius']) > 0 AND is_array($url_data['start_loc']))
			{
				$bounds = $url_data['start_loc'];			
				if (count($bounds) == 2 AND is_numeric($bounds[0]) AND is_numeric($bounds[1]))
				{
					self::$params['radius'] = array(
						'distance' => intval($url_data['radius']),
						'latitude' => $bounds[0],
						'longitude' => $bounds[1]
					);
				}
			}
		}
		
		// 
		// Check for incident date range parameters
		// 
		if (isset($url_data['from']) AND isset($url_data['to']))
		{
			$date_from = date('Y-m-d', strtotime($url_data['from']));
			$date_to = date('Y-m-d', strtotime($url_data['to']));
			
			array_push(self::$params, 
				'i.incident_date >= "'.$date_from.'"',
				'i.incident_date <= "'.$date_to.'"'
			);
		}
		
		/**
		 * ---------------------------
		 * NOTES: E.Kala July 13, 2011
		 * ---------------------------
		 * Additional checks for date parameters specified in timestamp format
		 * This only affects those submitted from the main page
		 */
		
		// Start Date
		if (isset($_GET['s']) AND intval($_GET['s']) > 0)
		{
			$start_date = intval($_GET['s']);
			array_push(self::$params, 
				'i.incident_date >= "'.date("Y-m-d H:i:s", $start_date).'"'
			);
		}

		// End Date
		if (isset($_GET['e']) AND intval($_GET['e']))
		{
			$end_date = intval($_GET['e']);
			array_push(self::$params, 
				'i.incident_date <= "'.date("Y-m-d H:i:s", $end_date).'"'
			);
		}
		
		// 
		// Check for media type parameter
		// 
		if (isset($url_data['m']) AND is_array($url_data['m']))
		{
			// An array of media filters has been specified
			// Validate the media types
			$media_types = array();
			foreach ($url_data['m'] as $media_type)
			{
				if (intval($media_type) > 0)
				{
					$media_types[] = intval($media_type);
				}
			}
			if (count($media_types) > 0)
			{
				array_push(self::$params, 
					'i.id IN (SELECT DISTINCT incident_id FROM '.$table_prefix.'media WHERE media_type IN ('.implode(",", $media_types).'))'
				);
			}
			
		}
		elseif (isset($url_data['m']) AND !is_array($url_data['m']))
		{
			// A single media filter has been specified
			$media_type = $url_data['m'];
			
			// Sanitization
			if (intval($media_type) > 0)
			{
				array_push(self::$params, 
					'i.id IN (SELECT DISTINCT incident_id FROM '.$table_prefix.'media WHERE media_type = '.$media_type.')'
				);
			}
		}
		
		// 
		// Check if the verification status has been specified
		// 
		if (isset($url_data['v']) AND is_array($url_data['v']))
		{
			$verified_status = array();
			foreach ($url_data['v'] as $verified)
			{
				if (intval($verified) >= 0)
				{
					$verified_status[] = intval($verified);
				}
			}
			
			if (count($verified_status) > 0)
			{
				array_push(self::$params, 
					'i.incident_verified IN ('.implode(",", $verified_status).')'
				);
			}
		}
		elseif (isset($url_data['v']) AND !is_array($url_data['v']) AND intval($url_data) >= 0)
		{
			array_push(self::$param, 
				'i.incident_verified = '.intval($url_data['v'])
			);
		}
		
		//> END PARAMETER FETCH
		
		// Fetch all the incidents
		$all_incidents = Incident_Model::get_incidents(self::$params);
		
		if ($paginate)
		{
			// Set up pagination
			// Pagination
			$pagination = new Pagination(array(
					'style' => 'front-end-reports',
					'query_string' => 'page',
					'items_per_page' => (int) Kohana::config('settings.items_per_page'),
					'total_items' => $all_incidents->count()
					));
			
			// Return paginated results
			return Incident_Model::get_incidents(self::$params, $pagination);
		}
		else
		{
			// Return
			return $all_incidents;
		}
	}	
}
?>
