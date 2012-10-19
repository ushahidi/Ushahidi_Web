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
 * @category   Helpers
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
	 * Pagination object user in fetch_incidents method
	 * @var Pagination
	 */
	public static $pagination = array();
	
			
	/**
	 * Validation of form fields
	 *
	 * @param array $post Values to be validated
	 */
	public static function validate(array & $post)
	{

		// Exception handling
		if ( ! isset($post) OR ! is_array($post))
			return FALSE;
		
		// Create validation object
		$post = Validation::factory($post)
				->pre_filter('trim', TRUE)
				->add_rules('incident_title','required', 'length[3,200]')
				->add_rules('incident_description','required')
				->add_rules('incident_date','required','date_mmddyyyy')
				->add_rules('incident_hour','required','between[1,12]')
				->add_rules('incident_minute','required','between[0,59]')
				->add_rules('incident_ampm','required');
			
		if (isset($post->incident_ampm) AND $post->incident_ampm != "am" AND $post->incident_ampm != "pm")
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
				if ( ! empty($url) AND ! valid::url($url))
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
				if (!empty($url) AND ! valid::url($url))
				{
					$post->add_error('incident_video','url');
				}
			}
		}
		
		// If deployment is a single country deployment, check that the location mapped is in the default country
		if ( ! Kohana::config('settings.multi_country') AND isset($post->country_name))
		{
			$country = Country_Model::get_country_by_name($post->country_name);
			if ($country AND $country->id != Kohana::config('settings.default_country'))
			{
				$post->add_error('country_name','single_country', array(ORM::factory('country', Kohana::config('settings.default_country'))->country) );
			}
		}
		
		// Validate photo uploads
		$post->add_rules('incident_photo', 'upload::valid', 'upload::type[gif,jpg,png,jpeg]', 'upload::size[2M]');


		// Validate Personal Information
		if ( ! empty($post->person_first))
		{
			$post->add_rules('person_first', 'length[2,100]');
		}
		else
		{
			$post->person_first = '';
		}

		if ( ! empty($post->person_last))
		{
			$post->add_rules('person_last', 'length[2,100]');
		}
		else
		{
			$post->person_last = '';
		}

		if ( ! empty($post->person_email))
		{
			$post->add_rules('person_email', 'email', 'length[3,100]');
		}
		else
		{
			$post->person_email = '';
		}
		
		$post->add_rules('location_id','numeric');
		$post->add_rules('incident_active', 'between[0,1]');
		$post->add_rules('incident_verified', 'between[0,1]');
		$post->add_rules('incident_zoom', 'numeric');
		
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
			: new Country_Model(Kohana::config('settings.default_country'));
			
		// Fetch the country id
		$country_id = ( ! empty($country) AND $country->loaded)? $country->id : 0;
		
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
		if ( ! $incident->loaded AND isset($_SESSION['auth_user']))
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
			elseif ($post->service_id == 2)
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
		
		// Approval Status: Only set if user has permission
		if (isset($post->incident_active) AND Auth::instance()->has_permission('reports_approve'))
		{
			$incident->incident_active = $post->incident_active;
		}
		// Verification status:  Only set if user has permission
		if (isset($post->incident_verified) AND Auth::instance()->has_permission('reports_verify'))
		{
			$incident->incident_verified = $post->incident_verified;
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
	 * @param mixed $incident
	 */
	public static function verify_approve($incident)
	{
		// @todo Exception handling
		
		$verify = new Verify_Model();
		$verify->incident_id = $incident->id;
		
		// Record 'Verified By' Action
		$verify->user_id = $_SESSION['auth_user']->id;
		$verify->verified_date = date("Y-m-d H:i:s",time());
		
		if ($incident->incident_active == 1)
		{
			$verify->verified_status = '1';
		}
		elseif ($incident->incident_verified == 1)
		{
			$verify->verified_status = '2';
		}
		elseif ($incident->incident_active == 1 AND $incident->incident_verified == 1)
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
						$sql = "INSERT INTO ".Kohana::config('database.default.table_prefix')."geometry "
							. "(incident_id, geometry, geometry_label, geometry_comment, geometry_color, geometry_strokewidth)"
							. "VALUES(".$incident->id.", GeomFromText('".$geometry."'), '".$label."', '".$comment."', '".$color."', ".$strokewidth.")";
						Kohana::log('debug', $sql);
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
		if (isset($post->incident_news))
		{
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
		}

		// b. Video
		if (isset($post->incident_video))
		{
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
		}

		// c. Photos
		if ( ! empty($post->incident_photo))
		{
			$filenames = upload::save('incident_photo');
			$i = 1;

			foreach ($filenames as $filename)
			{
				$new_filename = $incident->id.'_'.$i.'_'.time();

				$file_type = strrev(substr(strrev($filename),0,4));
				
				// IMAGE SIZES: 800X600, 400X300, 89X59
				// Catch any errors from corrupt image files
				try
				{
					// Large size
					Image::factory($filename)->resize(800,600,Image::AUTO)
						->save(Kohana::config('upload.directory', TRUE).$new_filename.$file_type);

					// Medium size
					Image::factory($filename)->resize(400,300,Image::HEIGHT)
						->save(Kohana::config('upload.directory', TRUE).$new_filename.'_m'.$file_type);

					// Thumbnail
					Image::factory($filename)->resize(89,59,Image::HEIGHT)
						->save(Kohana::config('upload.directory', TRUE).$new_filename.'_t'.$file_type);
				}
				catch (Kohana_Exception $e)
				{
					// Do nothing. Too late to throw errors
				}
				
				// Name the files for the DB
				$media_link = $new_filename.$file_type;
				$media_medium = $new_filename.'_m'.$file_type;
				$media_thumb = $new_filename.'_t'.$file_type;
					
				// Okay, now we have these three different files on the server, now check to see
				//   if we should be dropping them on the CDN
				
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
				$photo = new Media_Model();
				$photo->location_id = $incident->location_id;
				$photo->incident_id = $incident->id;
				$photo->media_type = 1; // Images
				$photo->media_link = $media_link;
				$photo->media_medium = $media_medium;
				$photo->media_thumb = $media_thumb;
				$photo->media_date = date("Y-m-d H:i:s",time());
				$photo->save();
				$i++;
			}
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
	 * @param bool $paginate Optionally paginate the incidents - Default is FALSE
	 * @param int $items_per_page No. of items to show per page
	 * @return Database_Result
	 */
	public static function fetch_incidents($paginate = FALSE, $items_per_page = 0)
	{
		// Reset the paramters
		self::$params = array();
		
		// Initialize the category id
		$category_id = 0;
		
		$table_prefix = Kohana::config('database.default.table_prefix');
		
		// Fetch the URL data into a local variable
		$url_data = $_GET;
		
		// Split selected parameters on ","
		// For simplicity, always turn them into arrays even theres just one value
		$exclude_params = array('c', 'v', 'm', 'mode', 'sw', 'ne', 'start_loc');
		foreach ($url_data as $key => $value)
		{
			if (in_array($key, $exclude_params) AND ! is_array($value))
			{
				$url_data[$key] = explode(",", $value);
			}
		}
		
		//> BEGIN PARAMETER FETCH
		// 
		// Check for the category parameter
		// 
		if (isset($url_data['c']) AND is_array($url_data['c']))
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
		
		// Additional checks for date parameters specified in timestamp format
		// This only affects those submitted from the main page
		
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
					'i.id IN (SELECT DISTINCT incident_id FROM '
						.$table_prefix.'media WHERE media_type IN ('.implode(",", $media_types).'))'
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
		
		//
		// Check if they're filtering over custom form fields
		//
		if (isset($url_data['cff']) AND is_array($url_data['cff']))
		{
			$where_text = "";
			$i = 0;
			foreach ($url_data['cff'] as $field)
			{			
				$field_id = $field[0];
				if (intval($field_id) < 1)
					continue;

				$field_value = $field[1];
				if (is_array($field_value))
				{
					$field_value = implode(",", $field_value);
				}
								
				$i++;
				if ($i > 1)
				{
					$where_text .= " OR ";
				}
				
				$where_text .= "(form_field_id = ".intval($field_id)
					. " AND form_response = '".Database::instance()->escape_str(trim($field_value))."')";
			}
			
			// Make sure there was some valid input in there
			if ($i > 0)
			{
				// Get the valid IDs - faster in a separate query as opposed
				// to a subquery within the main query
				$db = new Database();

				$rows = $db->query('SELECT DISTINCT incident_id FROM '
				    .$table_prefix.'form_response WHERE '.$where_text);
				
				$incident_ids = '';
				foreach ($rows as $row)
				{
					if ($incident_ids != '')
					{
							$incident_ids .= ',';
					}

					$incident_ids .= $row->incident_id;
				}
				//make sure there are IDs found
				if ($incident_ids != '')
				{
					array_push(self::$params, 'i.id IN ('.$incident_ids.')');
				}
				else
				{
					array_push(self::$params, 'i.id IN (0)');
				}
			}
			
		} // End of handling cff
		
		// In case a plugin or something wants to get in on the parameter fetching fun
		Event::run('ushahidi_filter.fetch_incidents_set_params', self::$params);
		
		//> END PARAMETER FETCH

		// Check for order and sort params
		$order_field = NULL; $sort = NULL;
		$order_options = array(
			'title' => 'i.incident_title',
			'date' => 'i.incident_date',
			'id' => 'i.id'
		);
		if (isset($url_data['order']) AND isset($order_options[$url_data['order']]))
		{
			$order_field = $order_options[$url_data['order']];
		}
		if (isset($url_data['sort']))
		{
			$sort = (strtoupper($url_data['sort']) == 'ASC') ? 'ASC' : 'DESC';
		}
		
		if ($paginate)
		{
			// Fetch incident count
			$incident_count = Incident_Model::get_incidents(self::$params, false, $order_field, $sort, TRUE);
			
			// Set up pagination
			$page_limit = (intval($items_per_page) > 0)
			    ? $items_per_page 
			    : intval(Kohana::config('settings.items_per_page'));
					
			$total_items = $incident_count->current()
					? $incident_count->current()->report_count
					: 0;
			
			$pagination = new Pagination(array(
					'style' => 'front-end-reports',
					'query_string' => 'page',
					'items_per_page' => $page_limit,
					'total_items' => $total_items
				));
			
			Event::run('ushahidi_filter.pagination',$pagination);
			
			self::$pagination = $pagination;
			
			// Return paginated results
			return Incident_Model::get_incidents(self::$params, self::$pagination, $order_field, $sort);
		}
		else
		{
			// Return
			return Incident_Model::get_incidents(self::$params, false, $order_field, $sort);;
		}
	}	
}
?>
