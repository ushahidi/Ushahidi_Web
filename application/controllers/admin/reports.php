<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Reports Controller.
 * This controller will take care of adding and editing reports in the Admin section.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Admin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Reports_Controller extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->template->this_page = 'reports';
	}


	/**
	* Lists the reports.
	* @param int $page
	*/
	public function index($page = 1)
	{
		// If user doesn't have access, redirect to dashboard
		if ( ! admin::permissions($this->user, "reports_view"))
		{
			url::redirect(url::site().'admin/dashboard');
		}

		$this->template->content = new View('admin/reports');
		$this->template->content->title = Kohana::lang('ui_admin.reports');


		if (!empty($_GET['status']))
		{
			$status = $_GET['status'];

			if (strtolower($status) == 'a')
			{
				$filter = 'incident_active = 0';
			}
			elseif (strtolower($status) == 'v')
			{
				$filter = 'incident_verified = 0';
			}
			else
			{
				$status = "0";
				$filter = '1=1';
			}
		}
		else
		{
			$status = "0";
			$filter = "1=1";
		}

		// Get Search Keywords (If Any)
		if (isset($_GET['k']))
		{
			//	Brute force input sanitization
			
			// Phase 1 - Strip the search string of all non-word characters 
			$keyword_raw = preg_replace('/[^\w+]\w*/', '', $_GET['k']);
			
			// Strip any HTML tags that may have been missed in Phase 1
			$keyword_raw = strip_tags($keyword_raw);
			
			// Phase 3 - Invoke Kohana's XSS cleaning mechanism just incase an outlier wasn't caught
			// in the first 2 steps
			$keyword_raw = $this->input->xss_clean($keyword_raw);
			
			$filter .= " AND (".$this->_get_searchstring($keyword_raw).")";
		}
		else
		{
			$keyword_raw = "";
		}

		// Check, has the form been submitted?
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		if ($_POST)
		{
			$post = Validation::factory($_POST);

			 //	Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('action','required', 'alpha', 'length[1,1]');
			$post->add_rules('incident_id.*','required','numeric');

			if ($post->validate())
			{
				// Approve Action
				if ($post->action == 'a')		
				{
					foreach($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						if ($update->loaded == TRUE) 
						{
							$update->incident_active =($update->incident_active == 0) ? '1' : '0'; 

							// Tag this as a report that needs to be sent out as an alert
							if ($update->incident_alert_status != '2')
							{ 
								// 2 = report that has had an alert sent
								$update->incident_alert_status = '1';
							}

							$update->save();

							$verify = new Verify_Model();
							$verify->incident_id = $item;
							$verify->verified_status = '1';
							
							// Record 'Verified By' Action
							$verify->user_id = $_SESSION['auth_user']->id;			
							$verify->verified_date = date("Y-m-d H:i:s",time());
							$verify->save();

							// Action::report_approve - Approve a Report
							Event::run('ushahidi_action.report_approve', $update);
						}
					}
					$form_action = strtoupper(Kohana::lang('ui_admin.approved'));
				}
				// Unapprove Action
				elseif ($post->action == 'u')	
				{
					foreach ($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						if ($update->loaded == true) {
							$update->incident_active = '0';

							// If Alert hasn't been sent yet, disable it
							if ($update->incident_alert_status == '1')
							{
								$update->incident_alert_status = '0';
							}

							$update->save();

							$verify = new Verify_Model();
							$verify->incident_id = $item;
							$verify->verified_status = '0';
							
							// Record 'Verified By' Action
							$verify->user_id = $_SESSION['auth_user']->id;			
							$verify->verified_date = date("Y-m-d H:i:s",time());
							$verify->save();

							// Action::report_unapprove - Unapprove a Report
							Event::run('ushahidi_action.report_unapprove', $update);
						}
					}
					$form_action = strtoupper(Kohana::lang('ui_admin.unapproved'));
				}
				// Verify Action
				elseif ($post->action == 'v')	
				{
					foreach ($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						$verify = new Verify_Model();
						if ($update->loaded == true) {
							if ($update->incident_verified == '1')
							{
								$update->incident_verified = '0';
								$verify->verified_status = '0';
							}
							else {
								$update->incident_verified = '1';
								$verify->verified_status = '2';
							}
							$update->save();

							$verify->incident_id = $item;
							// Record 'Verified By' Action
							$verify->user_id = $_SESSION['auth_user']->id;			
							$verify->verified_date = date("Y-m-d H:i:s",time());
							$verify->save();
						}
					}
					
					// Set the form action
					$form_action = strtoupper(Kohana::lang('ui_admin.verified_unverified'));
				}
				
				//Delete Action
				elseif ($post->action == 'd')	
				{
					foreach($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						if ($update->loaded == true)
						{
							$incident_id = $update->id;
							$location_id = $update->location_id;
							$update->delete();

							// Delete Location
							ORM::factory('location')->where('id',$location_id)->delete_all();

							// Delete Categories
							ORM::factory('incident_category')->where('incident_id',$incident_id)->delete_all();

							// Delete Translations
							ORM::factory('incident_lang')->where('incident_id',$incident_id)->delete_all();

							// Delete Photos From Directory
							foreach (ORM::factory('media')->where('incident_id',$incident_id)->where('media_type', 1) as $photo) {
								deletePhoto($photo->id);
							}

							// Delete Media
							ORM::factory('media')->where('incident_id',$incident_id)->delete_all();

							// Delete Sender
							ORM::factory('incident_person')->where('incident_id',$incident_id)->delete_all();

							// Delete relationship to SMS message
							$updatemessage = ORM::factory('message')->where('incident_id',$incident_id)->find();
							if ($updatemessage->loaded == true) {
								$updatemessage->incident_id = 0;
								$updatemessage->save();
							}

							// Delete Comments
							ORM::factory('comment')->where('incident_id',$incident_id)->delete_all();
							
							// Delete form responses
							ORM::factory('form_response')->where('incident_id', $incident_id)->delete_all();
							
							// Action::report_delete - Deleted a Report
							Event::run('ushahidi_action.report_delete', $incident_id);
						}
					}
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
				}
				$form_saved = TRUE;
			}
			else
			{
				$form_error = TRUE;
			}

		}

		// Pagination
		$pagination = new Pagination(array(
			'query_string'	 => 'page',
			'items_per_page' => $this->items_per_page,
			'total_items'	 => ORM::factory('incident')
				->join('location', 'incident.location_id', 'location.id','INNER')
				->where($filter)
				->count_all()
			));
			
		$incidents = ORM::factory('incident')
			->join('location', 'incident.location_id', 'location.id','INNER')
			->where($filter)
			->orderby('incident_dateadd', 'desc')
			->find_all($this->items_per_page, $pagination->sql_offset);

		$location_ids = array();
		foreach ($incidents as $incident)
		{
			$location_ids[] = $incident->location_id;
				
		}
		// Check if location_ids is not empty
		if( count($location_ids ) > 0 ) 
		{
			$locations_result = ORM::factory('location')
				->in('id',implode(',',$location_ids))
				->find_all();
			$locations = array();
			$country_ids = array();
			foreach ($locations_result as $loc)
			{
				$locations[$loc->id] = $loc->location_name;
				$country_ids[$loc->id]['country_id'] = $loc->country_id;		 
			}	
		}
		else
		{
			$locations = array();
		}
		$this->template->content->locations = $locations;
		$this->template->content->country_ids = $country_ids;
	
		// GET countries
		$countries = array();
		foreach (ORM::factory('country')->orderby('country')->find_all() as $country)
		{
			// Create a list of all categories
			$this_country = $country->country;
			if (strlen($this_country) > 35)
			{
				$this_country = substr($this_country, 0, 35) . "...";
			}
			$countries[$country->id] = $this_country;
		}

		$this->template->content->countries = $countries;
		$this->template->content->incidents = $incidents;
		$this->template->content->pagination = $pagination;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;

		// Total Reports
		$this->template->content->total_items = $pagination->total_items;

		// Status Tab
		$this->template->content->status = $status;

		// Javascript Header
		$this->template->js = new View('admin/reports_js');
	}
	/**
	 * Edit a report
	 * @param bool|int $id The id no. of the report
	 * @param bool|string $saved
	 */
	public function edit($id = FALSE, $saved = FALSE)
	{
		$db = new Database();
		
		// If user doesn't have access, redirect to dashboard
		if ( ! admin::permissions($this->user, "reports_edit"))
		{
			url::redirect(url::site().'admin/dashboard');
		}

		$this->template->content = new View('admin/reports_edit');
		$this->template->content->title = Kohana::lang('ui_admin.create_report');

		// setup and initialize form field names
		$form = array
		(
			'location_id' => '',
			'form_id' => '',
			'locale' => '',
			'incident_title' => '',
			'incident_description' => '',
			'incident_date' => '',
			'incident_hour' => '',
			'incident_minute' => '',
			'incident_ampm' => '',
			'latitude' => '',
			'longitude' => '',
			'geometry' => array(),
			'location_name' => '',
			'country_id' => '',
			'country_name' =>'',
			'incident_category' => array(),
			'incident_news' => array(),
			'incident_video' => array(),
			'incident_photo' => array(),
			'person_first' => '',
			'person_last' => '',
			'person_email' => '',
			'custom_field' => array(),
			'incident_active' => '',
			'incident_verified' => '',
			'incident_source' => '',
			'incident_information' => '',
			'incident_zoom' => ''
		);

		// Copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = ($saved == 'saved');

		// Initialize Default Values
		$form['locale'] = Kohana::config('locale.language');
		//$form['latitude'] = Kohana::config('settings.default_lat');
		//$form['longitude'] = Kohana::config('settings.default_lon');
		$form['incident_date'] = date("m/d/Y",time());
		$form['incident_hour'] = date('h');
		$form['incident_minute'] = date('i');
		$form['incident_ampm'] = date('a');
		$form['country_id'] = Kohana::config('settings.default_country');
		
		// initialize custom field array
        $form['custom_field'] = customforms::get_custom_form_fields($id,'',true);

		// Locale (Language) Array
		$this->template->content->locale_array = Kohana::config('locale.all_languages');

		// Create Categories
		$this->template->content->categories = Category_Model::get_categories();
		$this->template->content->new_categories_form = $this->_new_categories_form_arr();

		// Time formatting
		$this->template->content->hour_array = $this->_hour_array();
		$this->template->content->minute_array = $this->_minute_array();
		$this->template->content->ampm_array = $this->_ampm_array();
		
		$this->template->content->stroke_width_array = $this->_stroke_width_array();

		// Get Countries
		$countries = array();
		foreach (ORM::factory('country')->orderby('country')->find_all() as $country)
		{
			// Create a list of all countries
			$this_country = $country->country;
			if (strlen($this_country) > 35)
			{
				$this_country = substr($this_country, 0, 35) . "...";
			}
			$countries[$country->id] = $this_country;
		}
		
		// Initialize Default Value for Hidden Field Country Name, just incase Reverse Geo coding yields no result
		$form['country_name'] = $countries[$form['country_id']];
		
		$this->template->content->countries = $countries;

		//GET custom forms
		$forms = array();
		foreach (ORM::factory('form')->where('form_active',1)->find_all() as $custom_forms)
		{
			$forms[$custom_forms->id] = $custom_forms->form_title;
		}
		
		$this->template->content->forms = $forms;
		
		// Get the incident media
		$incident_media =  Incident_Model::is_valid_incident($id)
			? ORM::factory('incident', $id)->media
			: FALSE;
		
		$this->template->content->incident_media = $incident_media;

		// Are we creating this report from SMS/Email/Twitter?
		// If so retrieve message
		if ( isset($_GET['mid']) AND intval($_GET['mid']) > 0 ) {

			$message_id = intval($_GET['mid']);
			$service_id = "";
			$message = ORM::factory('message', $message_id);

			if ($message->loaded AND $message->message_type == 1)
			{
				$service_id = $message->reporter->service_id;

				// Has a report already been created for this Message?
				if ($message->incident_id != 0) {
					
					// Redirect to report
					url::redirect('admin/reports/edit/'. $message->incident_id);
				}

				$this->template->content->show_messages = true;
				$incident_description = $message->message;
				if ( ! empty($message->message_detail))
				{
					$form['incident_title'] = $message->message;
					$incident_description = $message->message_detail;
				}
				
				$form['incident_description'] = $incident_description;
				$form['incident_date'] = date('m/d/Y', strtotime($message->message_date));
				$form['incident_hour'] = date('h', strtotime($message->message_date));
				$form['incident_minute'] = date('i', strtotime($message->message_date));
				$form['incident_ampm'] = date('a', strtotime($message->message_date));
				$form['person_first'] = $message->reporter->reporter_first;
				$form['person_last'] = $message->reporter->reporter_last;

				// Does the sender of this message have a location?
				if ($message->reporter->location->loaded)
				{
					$form['location_id'] = $message->reporter->location->id;
					$form['latitude'] = $message->reporter->location->latitude;
					$form['longitude'] = $message->reporter->location->longitude;
					$form['location_name'] = $message->reporter->location->location_name;
				}

				// Retrieve Last 5 Messages From this account
				$this->template->content->all_messages = ORM::factory('message')
					->where('reporter_id', $message->reporter_id)
					->orderby('message_date', 'desc')
					->limit(5)
					->find_all();
			}
			else
			{
				$message_id = "";
				$this->template->content->show_messages = false;
			}
		}
		else
		{
			$this->template->content->show_messages = false;
		}

		// Are we creating this report from a Newsfeed?
		if ( isset($_GET['fid']) AND intval($_GET['fid']) > 0 )
		{
			$feed_item_id = intval($_GET['fid']);
			$feed_item = ORM::factory('feed_item', $feed_item_id);

			if ($feed_item->loaded)
			{
				// Has a report already been created for this Feed item?
				if ($feed_item->incident_id != 0)
				{
					// Redirect to report
					url::redirect('admin/reports/edit/'. $feed_item->incident_id);
				}

				$form['incident_title'] = $feed_item->item_title;
				$form['incident_description'] = $feed_item->item_description;
				$form['incident_date'] = date('m/d/Y', strtotime($feed_item->item_date));
				$form['incident_hour'] = date('h', strtotime($feed_item->item_date));
				$form['incident_minute'] = date('i', strtotime($feed_item->item_date));
				$form['incident_ampm'] = date('a', strtotime($feed_item->item_date));

				// News Link
				$form['incident_news'][0] = $feed_item->item_link;

				// Does this newsfeed have a geolocation?
				if ($feed_item->location_id)
				{
					$form['location_id'] = $feed_item->location_id;
					$form['latitude'] = $feed_item->location->latitude;
					$form['longitude'] = $feed_item->location->longitude;
					$form['location_name'] = $feed_item->location->location_name;
				}
			}
			else
			{
				$feed_item_id = "";
			}
		}

		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = array_merge($_POST, $_FILES);
			
			// Check if the service id exists
			if (isset($service_id) AND intval($service_id) > 0)
			{
				$post = array_merge($post, array('service_id' => $service_id));
			}
			
			// Check if the incident id is valid an add it to the post data
			if (Incident_Model::is_valid_incident($id))
			{
				$post = array_merge($post, array('incident_id' => $id));
			}
			
			/**
			 * NOTES - E.Kala July 27, 2011
			 *
			 * Previously, the $post parameter for this event was a Validation
			 * object. Now it's an array (i.e. the raw data without any validation rules applied to them). 
			 * As such, all plugins making use of this event shall have to be updated
			 */
			
			// Action::report_submit_admin - Report Posted
			Event::run('ushahidi_action.report_submit_admin', $post);

			// Validate
			if (reports::validate($post, TRUE))
			{
				// Yes! everything is valid
				$location_id = $post->location_id;
				
				// STEP 1: SAVE LOCATION
				$location = new Location_Model($location_id);
				reports::save_location($post, $location);

				// STEP 2: SAVE INCIDENT
				$incident = new Incident_Model($id);
				reports::save_report($post, $incident, $location->id);
				
				// STEP 2b: Record Approval/Verification Action
				$verify = new Verify_Model();
				reports::verify_approve($post, $verify, $incident);
				
				// STEP 2c: SAVE INCIDENT GEOMETRIES
				reports::save_report_geometry($post, $incident);

				// STEP 3: SAVE CATEGORIES
				reports::save_category($post, $incident);

				// STEP 4: SAVE MEDIA
				reports::save_media($post, $incident);

				// STEP 5: SAVE PERSONAL INFORMATION
				reports::save_personal_info($post, $incident);

				// STEP 6a: SAVE LINK TO REPORTER MESSAGE
				// We're creating a report from a message with this option
				if (isset($message_id) AND intval($message_id) > 0)
				{
					$savemessage = ORM::factory('message', $message_id);
					if ($savemessage->loaded)
					{
						$savemessage->incident_id = $incident->id;
						$savemessage->save();

						// Does Message Have Attachments?
						// Add Attachments
						$attachments = ORM::factory("media")
							->where("message_id", $savemessage->id)
							->find_all();
						foreach ($attachments AS $attachment)
						{
							$attachment->incident_id = $incident->id;
							$attachment->save();
						}
					}
				}

				// STEP 6b: SAVE LINK TO NEWS FEED
				// We're creating a report from a newsfeed with this option
				if (isset($feed_item_id) AND intval($feed_item_id) > 0)
				{
					$savefeed = ORM::factory('feed_item', $feed_item_id);
					if ($savefeed->loaded)
					{
						$savefeed->incident_id = $incident->id;
						$savefeed->location_id = $location->id;
						$savefeed->save();
					}
				}

				// STEP 7: SAVE CUSTOM FORM FIELDS
				reports::save_custom_fields($post, $incident);

				// Action::report_edit - Edited a Report
				Event::run('ushahidi_action.report_edit', $incident);


				// SAVE AND CLOSE?
				if ($post->save == 1)		
				{
					// Save but don't close
					url::redirect('admin/reports/edit/'. $incident->id .'/saved');
				}
				else						
				{
					// Save and close
					url::redirect('admin/reports/');
				}
				
			}
			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// Repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// Populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('report'));
				$form_error = TRUE;
			}
		}
		else
		{
			if (Incident_Model::is_valid_incident($id))
			{
				// Retrieve Current Incident
				$incident = ORM::factory('incident', $id);
				if ($incident->loaded == true)
				{
					// Retrieve Categories
					$incident_category = array();
					foreach($incident->incident_category as $category)
					{
						$incident_category[] = $category->category_id;
					}

					// Retrieve Media
					$incident_news = array();
					$incident_video = array();
					$incident_photo = array();
					foreach($incident->media as $media)
					{
						if ($media->media_type == 4)
						{
							$incident_news[] = $media->media_link;
						}
						elseif ($media->media_type == 2)
						{
							$incident_video[] = $media->media_link;
						}
						elseif ($media->media_type == 1)
						{
							$incident_photo[] = $media->media_link;
						}
					}
					
					// Get Geometries via SQL query as ORM can't handle Spatial Data
					$sql = "SELECT AsText(geometry) as geometry, geometry_label, 
						geometry_comment, geometry_color, geometry_strokewidth 
						FROM ".Kohana::config('database.default.table_prefix')."geometry 
						WHERE incident_id=".$id;
					$query = $db->query($sql);
					foreach ( $query as $item )
					{
						$geometry = array(
								"geometry" => $item->geometry,
								"label" => $item->geometry_label,
								"comment" => $item->geometry_comment,
								"color" => $item->geometry_color,
								"strokewidth" => $item->geometry_strokewidth
							);
						$form['geometry'][] = json_encode($geometry);
					}
					
					// Combine Everything
					$incident_arr = array
					(
						'location_id' => $incident->location->id,
						'form_id' => $incident->form_id,
						'locale' => $incident->locale,
						'incident_title' => $incident->incident_title,
						'incident_description' => $incident->incident_description,
						'incident_date' => date('m/d/Y', strtotime($incident->incident_date)),
						'incident_hour' => date('h', strtotime($incident->incident_date)),
						'incident_minute' => date('i', strtotime($incident->incident_date)),
						'incident_ampm' => date('a', strtotime($incident->incident_date)),
						'latitude' => $incident->location->latitude,
						'longitude' => $incident->location->longitude,
						'location_name' => $incident->location->location_name,
						'country_id' => $incident->location->country_id,
						'incident_category' => $incident_category,
						'incident_news' => $incident_news,
						'incident_video' => $incident_video,
						'incident_photo' => $incident_photo,
						'person_first' => $incident->incident_person->person_first,
						'person_last' => $incident->incident_person->person_last,
						'person_email' => $incident->incident_person->person_email,
						'custom_field' => customforms::get_custom_form_fields($id,$incident->form_id,true),
						'incident_active' => $incident->incident_active,
						'incident_verified' => $incident->incident_verified,
						'incident_source' => $incident->incident_source,
						'incident_information' => $incident->incident_information,
						'incident_zoom' => $incident->incident_zoom
					);

					// Merge To Form Array For Display
					$form = arr::overwrite($form, $incident_arr);
				}
				else
				{
					// Redirect
					url::redirect('admin/reports/');
				}

			}
		}
		
		$this->template->content->id = $id;
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		
		// Retrieve Custom Form Fields Structure
		$this->template->content->custom_forms = new View('reports_submit_custom_forms');
		$disp_custom_fields = customforms::get_custom_form_fields($id, $form['form_id'], FALSE, "view");
		$custom_field_mismatch = customforms::get_edit_mismatch($form['form_id']);
        $this->template->content->custom_forms->disp_custom_fields = $disp_custom_fields;
		$this->template->content->custom_forms->custom_field_mismatch = $custom_field_mismatch;
		$this->template->content->custom_forms->form = $form;

		// Retrieve Previous & Next Records
		$previous = ORM::factory('incident')->where('id < ', $id)->orderby('id','desc')->find();
		$previous_url = ($previous->loaded ?
				url::base().'admin/reports/edit/'.$previous->id :
				url::base().'admin/reports/');
		$next = ORM::factory('incident')->where('id > ', $id)->orderby('id','desc')->find();
		$next_url = ($next->loaded ?
				url::base().'admin/reports/edit/'.$next->id :
				url::base().'admin/reports/');
		$this->template->content->previous_url = $previous_url;
		$this->template->content->next_url = $next_url;

		// Javascript Header
		$this->template->map_enabled = TRUE;
		$this->template->colorpicker_enabled = TRUE;
		$this->template->treeview_enabled = TRUE;
		$this->template->json2_enabled = TRUE;
		
		$this->template->js = new View('reports_submit_edit_js');
		$this->template->js->edit_mode = TRUE;
		$this->template->js->default_map = Kohana::config('settings.default_map');
		$this->template->js->default_zoom = Kohana::config('settings.default_zoom');
		
		if ( ! $form['latitude'] OR !$form['latitude'])
		{
			$this->template->js->latitude = Kohana::config('settings.default_lat');
			$this->template->js->longitude = Kohana::config('settings.default_lon');
		}
		else
		{
			$this->template->js->latitude = $form['latitude'];
			$this->template->js->longitude = $form['longitude'];
		}
		
		$this->template->js->incident_zoom = $form['incident_zoom'];
		$this->template->js->geometries = $form['geometry'];
		
		// Inline Javascript
		$this->template->content->date_picker_js = $this->_date_picker_js();
		$this->template->content->color_picker_js = $this->_color_picker_js();
		$this->template->content->new_category_toggle_js = $this->_new_category_toggle_js();
		
		// Pack Javascript
		$myPacker = new javascriptpacker($this->template->js , 'Normal', false, false);
		$this->template->js = $myPacker->pack();
	}


	/**
	 * Download Reports in CSV format
	 */
	function download()
	{
		// If user doesn't have access, redirect to dashboard
		if ( ! admin::permissions($this->user, "reports_download"))
		{
			url::redirect(url::site().'admin/dashboard');
		}

		$this->template->content = new View('admin/reports_download');
		$this->template->content->title = Kohana::lang('ui_admin.download_reports');

		$form = array(
			'data_point'   => '',
			'data_include' => '',
			'from_date'	   => '',
			'to_date'	   => ''
		);
		
		$errors = $form;
		$form_error = FALSE;

		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

			 //	Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('data_point.*','required','numeric','between[1,4]');
			//$post->add_rules('data_include.*','numeric','between[1,5]');
			$post->add_rules('data_include.*','numeric','between[1,6]');
			$post->add_rules('from_date','date_mmddyyyy');
			$post->add_rules('to_date','date_mmddyyyy');

			// Validate the report dates, if included in report filter
			if (!empty($_POST['from_date']) OR !empty($_POST['to_date']))
			{
				// Valid FROM Date?
				if (empty($_POST['from_date']) OR (strtotime($_POST['from_date']) > strtotime("today"))) {
					$post->add_error('from_date','range');
				}

				// Valid TO date?
				if (empty($_POST['to_date']) OR (strtotime($_POST['to_date']) > strtotime("today"))) {
					$post->add_error('to_date','range');
				}

				// TO Date not greater than FROM Date?
				if (strtotime($_POST['from_date']) > strtotime($_POST['to_date'])) {
					$post->add_error('to_date','range_greater');
				}
			}

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				// Add Filters
				$filter = " ( 1=1";
				
				// Report Type Filter
				foreach($post->data_point as $item)
				{
					if ($item == 1)
					{
						$filter .= " OR incident_active = 1 ";
					}
					
					if ($item == 2)
					{
						$filter .= " OR incident_verified = 1 ";
					}
					
					if ($item == 3)
					{
						$filter .= " OR incident_active = 0 ";
					}
					
					if ($item == 4)
					{
						$filter .= " OR incident_verified = 0 ";
					}
				}
				$filter .= ") ";

				// Report Date Filter
				if (!empty($post->from_date) AND !empty($post->to_date))
				{
					$filter .= " AND ( incident_date >= '" . date("Y-m-d H:i:s",strtotime($post->from_date)) . "' AND incident_date <= '" . date("Y-m-d H:i:s",strtotime($post->to_date)) . "' ) ";
				}

				// Retrieve reports
				$incidents = ORM::factory('incident')->where($filter)->orderby('incident_dateadd', 'desc')->find_all();

				// Column Titles
				$report_csv = "#,INCIDENT TITLE,INCIDENT DATE";
				foreach($post->data_include as $item)
				{
					if ($item == 1) {
						$report_csv .= ",LOCATION";
					}
					
					if ($item == 2) {
						$report_csv .= ",DESCRIPTION";
					}
					
					if ($item == 3) {
						$report_csv .= ",CATEGORY";
					}
					
					if ($item == 4) {
						$report_csv .= ",LATITUDE";
					}
					
					if($item == 5) {
						$report_csv .= ",LONGITUDE";
					}
					if($item == 6)
					{
						$custom_titles = ORM::factory('form_field')->orderby('field_position','desc')->find_all();
						foreach($custom_titles as $field_name)
						{

							$report_csv .= ",".$field_name->field_name;
						}	

					}

				}
				
				$report_csv .= ",APPROVED,VERIFIED";
				
				
				$report_csv .= "\n";

				foreach ($incidents as $incident)
				{
					$report_csv .= '"'.$incident->id.'",';
					$report_csv .= '"'.$this->_csv_text($incident->incident_title).'",';
					$report_csv .= '"'.$incident->incident_date.'"';

					foreach($post->data_include as $item)
					{
						switch ($item)
						{
							case 1:
								$report_csv .= ',"'.$this->_csv_text($incident->location->location_name).'"';
							break;

							case 2:
								$report_csv .= ',"'.$this->_csv_text($incident->incident_description).'"';
							break;

							case 3:
								$report_csv .= ',"';
							
								foreach($incident->incident_category as $category)
								{
									if ($category->category->category_title)
									{
										$report_csv .= $this->_csv_text($category->category->category_title) . ", ";
									}
								}
								$report_csv .= '"';
							break;
						
							case 4:
								$report_csv .= ',"'.$this->_csv_text($incident->location->latitude).'"';
							break;
						
							case 5:
								$report_csv .= ',"'.$this->_csv_text($incident->location->longitude).'"';
							break;

							case 6:
								$incident_id = $incident->id;
								$custom_fields = ORM::factory('form_response')->where('incident_id',$incident_id)->orderby('form_field_id','desc')->find_all();
								foreach($custom_fields as $custom_field)
								{
									$report_csv .=',"'.$this->_csv_text($custom_field->form_response).'"';
								}	
								break;

						}
					}
					
					if ($incident->incident_active)
					{
						$report_csv .= ",YES";
					}
					else
					{
						$report_csv .= ",NO";
					}
					
					if ($incident->incident_verified)
					{
						$report_csv .= ",YES";
					}
					else
					{
						$report_csv .= ",NO";
					}
					
					$report_csv .= "\n";
				}

				// Output to browser
				header("Content-type: text/x-csv");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-Disposition: attachment; filename=" . time() . ".csv");
				header("Content-Length: " . strlen($report_csv));
				echo $report_csv;
				exit;

			}
			
			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// Repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// Populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('report'));
				$form_error = TRUE;
			}
		}

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;

		// Javascript Header
		$this->template->js = new View('admin/reports_download_js');
		$this->template->js->calendar_img = url::base() . "media/img/icon-calendar.gif";
	}

	public function upload()
	{
		// If user doesn't have access, redirect to dashboard
		if ( ! admin::permissions($this->user, "reports_upload"))
		{
			url::redirect(url::site().'admin/dashboard');
		}

		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->template->content = new View('admin/reports_upload');
			$this->template->content->title = 'Upload Reports';
			$this->template->content->form_error = false;
		}
		
		if ($_SERVER['REQUEST_METHOD']=='POST')
		{
			$errors = array();
			$notices = array();
			
			if (!$_FILES['csvfile']['error']) 
			{
				if (file_exists($_FILES['csvfile']['tmp_name']))
				{
					if($filehandle = fopen($_FILES['csvfile']['tmp_name'], 'r'))
					{
						$importer = new ReportsImporter;
						
						if ($importer->import($filehandle))
						{
							$this->template->content = new View('admin/reports_upload_success');
							$this->template->content->title = 'Upload Reports';
							$this->template->content->rowcount = $importer->totalrows;
							$this->template->content->imported = $importer->importedrows;
							$this->template->content->notices = $importer->notices;
						}
						else
						{
							$errors = $importer->errors;
						}
					}
					else
					{
						$errors[] = Kohana::lang('ui_admin.file_open_error');
					}
				} 
				
				// File exists?
				else
				{
					$errors[] = Kohana::lang('ui_admin.file_not_found_upload');
				}
			} 
			
			// Upload errors?
			else
			{
				$errors[] = $_FILES['csvfile']['error'];
			}

			if(count($errors))
			{
				$this->template->content = new View('admin/reports_upload');
				$this->template->content->title = Kohana::lang('ui_admin.upload_reports');
				$this->template->content->errors = $errors;
				$this->template->content->form_error = 1;
			}
		}
	}

	/**
	* Translate a report
	* @param bool|int $id The id no. of the report
	* @param bool|string $saved
	*/

	function translate( $id = false, $saved = false )
	{
		$this->template->content = new View('admin/reports_translate');
		$this->template->content->title = Kohana::lang('ui_admin.translate_reports');

		// Which incident are we adding this translation for?
		if (isset($_GET['iid']) && !empty($_GET['iid']))
		{
			$incident_id = (int) $_GET['iid'];
			$incident = ORM::factory('incident', $incident_id);
			
			if ($incident->loaded == true)
			{
				$orig_locale = $incident->locale;
				$this->template->content->orig_title = $incident->incident_title;
				$this->template->content->orig_description = $incident->incident_description;
			}
			else
			{
				// Redirect
				url::redirect('admin/reports/');
			}
		}
		else
		{
			// Redirect
			url::redirect('admin/reports/');
		}


		// Setup and initialize form field names
		$form = array
		(
			'locale'	  => '',
			'incident_title'	  => '',
			'incident_description'	  => ''
		);
		// Copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		if ($saved == 'saved')
		{
			$form_saved = TRUE;
		}
		else
		{
			$form_saved = FALSE;
		}

		// Locale (Language) Array
		$this->template->content->locale_array = Kohana::config('locale.all_languages');

		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

			 //	Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('locale','required','alpha_dash','length[5]');
			$post->add_rules('incident_title','required', 'length[3,200]');
			$post->add_rules('incident_description','required');
			$post->add_callbacks('locale', array($this,'translate_exists_chk'));

			if ($orig_locale == $_POST['locale'])
			{
				// The original report and the translation are the same language!
				$post->add_error('locale','locale');
			}

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				// SAVE INCIDENT TRANSLATION
				$incident_l = new Incident_Lang_Model($id);
				$incident_l->incident_id = $incident_id;
				$incident_l->locale = $post->locale;
				$incident_l->incident_title = $post->incident_title;
				$incident_l->incident_description = $post->incident_description;
				$incident_l->save();


				// SAVE AND CLOSE?
				// Save but don't close
				if ($post->save == 1)		
				{
					url::redirect('admin/reports/translate/'. $incident_l->id .'/saved/?iid=' . $incident_id);
				}
				
				// Save and close
				else						
				{
					url::redirect('admin/reports/');
				}
			}

			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// Repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// Populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('report'));
				$form_error = TRUE;
			}
		}
		else
		{
			if ( $id )
			{
				// Retrieve Current Incident
				$incident_l = ORM::factory('incident_lang', $id)->where('incident_id', $incident_id)->find();
				if ($incident_l->loaded == true)
				{
					$form['locale'] = $incident_l->locale;
					$form['incident_title'] = $incident_l->incident_title;
					$form['incident_description'] = $incident_l->incident_description;
				}
				else
				{
					// Redirect
					url::redirect('admin/reports/');
				}

			}
		}

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;

		// Javascript Header
		$this->template->js = new View('admin/reports_translate_js');
	}




	/**
	* Save newly added dynamic categories
	*/
	function save_category()
	{
		$this->auto_render = FALSE;
		$this->template = "";

		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

			 //	Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('category_title','required', 'length[3,200]');
			$post->add_rules('category_description','required');
			$post->add_rules('category_color','required', 'length[6,6]');


			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				// SAVE Category
				$category = new Category_Model();
				$category->category_title = $post->category_title;
				$category->category_description = $post->category_description;
				$category->category_color = $post->category_color;
				$category->save();
				$form_saved = TRUE;

				echo json_encode(array("status"=>"saved", "id"=>$category->id));
			}

			else

			{
				echo json_encode(array("status"=>"error"));
			}
		}
		else
		{
			echo json_encode(array("status"=>"error"));
		}
	}

	/**
	* Delete Photo
	* @param int $id The unique id of the photo to be deleted
	*/
	function deletePhoto ( $id )
	{
		$this->auto_render = FALSE;
		$this->template = "";

		if ( $id )
		{
			$photo = ORM::factory('media', $id);
			$photo_large = $photo->media_link;
			$photo_thumb = $photo->media_thumb;

			// Delete Files from Directory
			if ( ! empty($photo_large))
			{
				unlink(Kohana::config('upload.directory', TRUE) . $photo_large);
			}
			
			if ( ! empty($photo_thumb))
			{
				unlink(Kohana::config('upload.directory', TRUE) . $photo_thumb);
			}

			// Finally Remove from DB
			$photo->delete();
		}
	}

	/* private functions */

	// Dynamic categories form fields
	private function _new_categories_form_arr()
	{
		return array
		(
			'category_name' => '',
			'category_description' => '',
			'category_color' => '',
		);
	}

	// Time functions
	private function _hour_array()
	{
		for ($i=1; $i <= 12 ; $i++)
		{
			// Add Leading Zero
			$hour_array[sprintf("%02d", $i)] = sprintf("%02d", $i);		
		}
		return $hour_array;
	}

	private function _minute_array()
	{
		for ($j=0; $j <= 59 ; $j++)
		{
			// Add Leading Zero
			$minute_array[sprintf("%02d", $j)] = sprintf("%02d", $j);	
		}

		return $minute_array;
	}

	private function _ampm_array()
	{
		return $ampm_array = array('pm'=>Kohana::lang('ui_admin.pm'),'am'=>Kohana::lang('ui_admin.am'));
	}
	
	private function _stroke_width_array()
	{
		for ($i = 0.5; $i <= 8 ; $i += 0.5)
		{
			$stroke_width_array["$i"] = $i;
		}
		
		return $stroke_width_array;
	}

	// Javascript functions
	 private function _color_picker_js()
	{
	 return "<script type=\"text/javascript\">
				$(document).ready(function() {
				$('#category_color').ColorPicker({
						onSubmit: function(hsb, hex, rgb) {
							$('#category_color').val(hex);
						},
						onChange: function(hsb, hex, rgb) {
							$('#category_color').val(hex);
						},
						onBeforeShow: function () {
							$(this).ColorPickerSetColor(this.value);
						}
					})
				.bind('keyup', function(){
					$(this).ColorPickerSetColor(this.value);
				});
				});
			</script>";
	}

	private function _date_picker_js()
	{
		return "<script type=\"text/javascript\">
				$(document).ready(function() {
				$(\"#incident_date\").datepicker({
				showOn: \"both\",
				buttonImage: \"" . url::base() . "media/img/icon-calendar.gif\",
				buttonImageOnly: true
				});
				});
			</script>";
	}


	private function _new_category_toggle_js()
	{
		return "<script type=\"text/javascript\">
				$(document).ready(function() {
				$('a#category_toggle').click(function() {
				$('#category_add').toggle(400);
				return false;
				});
				});
			</script>";
	}


	/**
	 * Checks if translation for this report & locale exists
	 * @param Validation $post $_POST variable with validation rules
	 * @param int $iid The unique incident_id of the original report
	 */
	public function translate_exists_chk(Validation $post)
	{
		// If add->rules validation found any errors, get me out of here!
		if (array_key_exists('locale', $post->errors()))
			return;

		$iid = (isset($_GET['iid']) AND intval($_GTE['iid'] > 0))? intval($_GET['iid']) : 0;
		
		// Load translation
		$translate = ORM::factory('incident_lang')
						->where('incident_id',$iid)
						->where('locale',$post->locale)
						->find();
		
		if ($translate->loaded)
		{
			$post->add_error( 'locale', 'exists');
		}
		else
		{
			// Not found
			return;
		}
	}

	/**
	 * Creates a SQL string from search keywords
	 */
	private function _get_searchstring($keyword_raw)
	{
		$or = '';
		$where_string = '';


		// Stop words that we won't search for
		// Add words as needed!!
		$stop_words = array('the', 'and', 'a', 'to', 'of', 'in', 'i', 'is', 'that', 'it',
		'on', 'you', 'this', 'for', 'but', 'with', 'are', 'have', 'be',
		'at', 'or', 'as', 'was', 'so', 'if', 'out', 'not');

		$keywords = explode(' ', $keyword_raw);
		
		if (is_array($keywords) && !empty($keywords))
		{
			array_change_key_case($keywords, CASE_LOWER);
			$i = 0;
			
			foreach($keywords as $value)
			{
				if (!in_array($value,$stop_words) && !empty($value))
				{
					$chunk = mysql_real_escape_string($value);
					if ($i > 0) {
						$or = ' OR ';
					}
					$where_string = $where_string.$or."incident_title LIKE '%$chunk%' OR incident_description LIKE '%$chunk%'  OR location_name LIKE '%$chunk%'";
					$i++;
				}
			}
		}

		if ($where_string)
		{
			return $where_string;
		}
		else
		{
			return "1=1";
		}
	}

	private function _csv_text($text)
	{
		$text = stripslashes(htmlspecialchars($text));
		return $text;
	}
}
