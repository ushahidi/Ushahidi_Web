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

class Reports_Controller extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->template->this_page = 'reports';
		$this->params = array('all_reports' => TRUE);
	}


	/**
	 * Lists the reports.
	 *
	 * @param int $page
	 */
	public function index($page = 1)
	{
		// If user doesn't have access, redirect to dashboard
		if ( ! $this->auth->has_permission("reports_view"))
		{
			url::redirect(url::site().'admin/dashboard');
		}

		$this->template->content = new View('admin/reports/main');
		$this->template->content->title = Kohana::lang('ui_admin.reports');

		// Database table prefix
		$table_prefix = Kohana::config('database.default.table_prefix');

		// Hook into the event for the reports::fetch_incidents() method
		Event::add('ushahidi_filter.fetch_incidents_set_params', array($this,'_add_incident_filters'));


		$status = "0";

		if ( !empty($_GET['status']))
		{
			$status = $_GET['status'];

			if (strtolower($status) == 'a')
			{
				array_push($this->params, 'i.incident_active = 0');
			}
			elseif (strtolower($status) == 'v')
			{
				array_push($this->params, 'i.incident_verified = 0');
			}
			elseif (strtolower($status) == 'o')
			{
				array_push($this->params, '(ic.category_id IS NULL)');
			}
			else
			{
				$status = "0";
			}
		}

		// Get Search Keywords (If Any)
		if (isset($_GET['k']))
		{
			//	Brute force input sanitization
			// Phase 1 - Strip the search string of all non-word characters
			$keyword_raw = (isset($_GET['k']))? preg_replace('#/\w+/#', '', $_GET['k']) : "";

			// Strip any HTML tags that may have been missed in Phase 1
			$keyword_raw = strip_tags($keyword_raw);

			// Phase 3 - Invoke Kohana's XSS cleaning mechanism just incase an outlier wasn't caught
			// in the first 2 steps
			$keyword_raw = $this->input->xss_clean($keyword_raw);

			$filter = " (".$this->_get_searchstring($keyword_raw).")";

			array_push($this->params, $filter);
		}
		else
		{
			$keyword_raw = "";
		}
		
		$order_field = 'date'; $sort = 'DESC';
		if (isset($_GET['order']))
		{
			$order_field = htmlentities($_GET['order']);
		}
		if (isset($_GET['sort']))
		{
			$sort = (strtoupper($_GET['sort']) == 'ASC') ? 'ASC' : 'DESC';
		}

		// Check, has the form been submitted?
		$form_error = FALSE;
		$errors = array();
		$form_saved = FALSE;
		$form_action = "";

		if ($_POST)
		{
			$post = Validation::factory($_POST);

			 //	Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks,
			// carried out in order
			$post->add_rules('action','required', 'alpha', 'length[1,1]');
			$post->add_rules('incident_id.*','required','numeric');
			
			if (in_array($post->action, array('a','u')) AND ! Auth::instance()->has_permission('reports_approve'))
			{
				$post->add_error('action','permission');
			}
			
			if ($post->action == 'v' AND ! Auth::instance()->has_permission('reports_verify'))
			{
				$post->add_error('action','permission');
			}
			
			if ($post->action == 'd' AND ! Auth::instance()->has_permission('reports_edit'))
			{
				$post->add_error('action','permission');
			}
			
			if ($post->action == 'a')
			{
				// sanitize the incident_ids
				$post->incident_id = array_map('intval', $post->incident_id);
				
				// Query to check if this report is uncategorized i.e categoryless
				$query = "SELECT i.* FROM ".$table_prefix."incident i "
				    . "LEFT JOIN ".$table_prefix."incident_category ic ON i.id=ic.incident_id "
				    . "LEFT JOIN ".$table_prefix."category c ON c.id = ic.category_id "
				    . "WHERE c.id IS NULL "
				    . "AND i.id IN :incidentids";

				$result = Database::instance()->query($query, array(':incidentids' => $post->incident_id));

				// We enly approve the report IF it's categorized
				// throw an error if any incidents aren't categorized
				foreach ($result as $incident)
				{
					$post->add_error('incident_id', 'categories_required', $incident->incident_title);
				}
			}

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
							$update->incident_active = '1';

							// Tag this as a report that needs to be sent out as an alert
							if ($update->incident_alert_status != '2')
							{
								// 2 = report that has had an alert sent
								$update->incident_alert_status = '1';
							}
							$update->save();

							// Record 'Verified By' Action
							reports::verify_approve($update);

							// Action::report_approve - Approve a Report
							Event::run('ushahidi_action.report_approve', $update);
						}
						$form_action = utf8::strtoupper(Kohana::lang('ui_admin.approved'));
					}

				}
				
				// Unapprove Action
				elseif ($post->action == 'u')
				{
					foreach ($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						if ($update->loaded == TRUE)
						{
							$update->incident_active = '0';

							// If Alert hasn't been sent yet, disable it
							if ($update->incident_alert_status == '1')
							{
								$update->incident_alert_status = '0';
							}

							$update->save();

							// Record 'Verified By' Action
							reports::verify_approve($update);

							// Action::report_unapprove - Unapprove a Report
							Event::run('ushahidi_action.report_unapprove', $update);
						}
					}
					$form_action = utf8::strtoupper(Kohana::lang('ui_admin.unapproved'));
				}
				
				// Verify Action
				elseif ($post->action == 'v')
				{
					foreach ($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						$verify = new Verify_Model();
						if ($update->loaded == TRUE)
						{
							if ($update->incident_verified == '1')
							{
								$update->incident_verified = '0';
								$verify->verified_status = '0';
							}
							else
							{
								$update->incident_verified = '1';
								$verify->verified_status = '2';
							}
							$update->save();

							// Record 'Verified By' Action
							reports::verify_approve($update);
						}
					}

					// Set the form action
					$form_action = utf8::strtoupper(Kohana::lang('ui_admin.verified_unverified'));
				}

				// Delete Action
				elseif ($post->action == 'd')
				{
					foreach ($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						if ($update->loaded)
						{
							$update->delete();
						}
					}
					$form_action = utf8::strtoupper(Kohana::lang('ui_admin.deleted'));
				}
				$form_saved = TRUE;
			}
			else
			{
				// Repopulate the form fields
				//$form = arr::overwrite($form, $post->as_array());

				// Populate the error fields, if any
				$errors = $post->errors('reports');
				$form_error = TRUE;
			}
		}

		// Fetch all incidents
		$incidents = reports::fetch_incidents(TRUE, Kohana::config('settings.items_per_page_admin'));

		Event::run('ushahidi_filter.filter_incidents',$incidents);

		$this->template->content->countries = Country_Model::get_countries_list();
		$this->template->content->incidents = $incidents;
		$this->template->content->pagination = reports::$pagination;
		$this->template->content->form_error = $form_error;
		$this->template->content->errors = $errors;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;

		// Total Reports
		$this->template->content->total_items = reports::$pagination->total_items;

		// Status Tab
		$this->template->content->status = $status;
		$this->template->content->order_field = $order_field;
		$this->template->content->sort = $sort;
		

		// Javascript Header
		$this->template->js = new View('admin/reports/reports_js');
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
		if ( ! $this->auth->has_permission("reports_edit"))
		{
			url::redirect('admin/dashboard');
		}

		$this->template->content = new View('admin/reports/edit');
		$this->template->content->title = Kohana::lang('ui_admin.create_report');

		// Setup and initialize form field names
		$form = array(
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
			'incident_zoom' => ''
		);

		// Copy the form as errors, so the errors will be stored with keys
		// corresponding to the form field names
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

		// get the form ID if relevant, kind of a hack
		// to just hit the database like this for one
		// tiny bit of info then throw away the DB model object,
		// but seems to be what everyone else does, so
		// why should I care. Just know that when your Ush system crashes
		// because you have 1000 concurrent users you'll need to do this
		// correctly. Etherton.
		$form['form_id'] = 1;
		$form_id = $form['form_id'];
		if ($id AND Incident_Model::is_valid_incident($id, FALSE))
		{
			$form_id = ORM::factory('incident', $id)->form_id;
		}
		
		// Initialize custom field array
		$form['custom_field'] = customforms::get_custom_form_fields($id,$form_id,true);

		// Locale (Language) Array
		$this->template->content->locale_array = Kohana::config('locale.all_languages');

		// Create Categories
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

		// Initialize Default Value for Hidden Field Country Name, 
		// just incase Reverse Geo coding yields no result
		$form['country_name'] = $countries[$form['country_id']];
		$this->template->content->countries = $countries;

		// GET custom forms
		$forms = array();
		foreach (customforms::get_custom_forms(FALSE) as $custom_forms)
		{
			$forms[$custom_forms->id] = $custom_forms->form_title;
		}
		$this->template->content->forms = $forms;

		// Get the incident media
		$incident_media =  Incident_Model::is_valid_incident($id, FALSE)
			? ORM::factory('incident', $id)->media
			: FALSE;

		$this->template->content->incident_media = $incident_media;

		// Are we creating this report from SMS/Email/Twitter?
		// If so retrieve message
		if (isset($_GET['mid']) AND intval($_GET['mid']) > 0)
		{
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

				// Does the message itself have a location?
				if ($message->latitude != NULL AND $message->longitude != NULL)
				{
					$form['latitude'] = $message->latitude;
					$form['longitude'] = $message->longitude;
				}
				
				// As a fallback, does the sender of this message have a location?
				elseif ($message->reporter->location->loaded)
				{
					$form['location_id'] = $message->reporter->location->id;
					$form['latitude'] = $message->reporter->location->latitude;
					$form['longitude'] = $message->reporter->location->longitude;
					$form['location_name'] = $message->reporter->location->location_name;
				}

				// Events to manipulate an already known location
				Event::run('ushahidi_action.location_from',$message_from = $message->message_from);
				
				// Filter location name
				Event::run('ushahidi_filter.location_name',$form['location_name']);
				
				// Filter //location find
				Event::run('ushahidi_filter.location_find',$form['location_find']);


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
				$this->template->content->show_messages = FALSE;
			}
		}
		else
		{
			$this->template->content->show_messages = FALSE;
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
			// Instantiate Validation, use $post, so we don't overwrite 
			// $_POST fields with our own things
			$post = array_merge($_POST, $_FILES);

			// Check if the service id exists
			if (isset($service_id) AND intval($service_id) > 0)
			{
				$post = array_merge($post, array('service_id' => $service_id));
			}

			// Check if the incident id is valid an add it to the post data
			if (Incident_Model::is_valid_incident($id, FALSE))
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
			if (reports::validate($post))
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
				reports::verify_approve($incident);

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
				switch ($post->save)
				{
					case 1:
					case 'dontclose':
						// Save but don't close
						url::redirect('admin/reports/edit/'. $incident->id .'/saved');
						break;
					case 'addnew':
						// Save and add new
						url::redirect('admin/reports/edit/0/saved');
						break;
					default:
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
				$errors = arr::merge($errors, $post->errors('report'));
				$form_error = TRUE;
			}
		}
		else
		{
			if (Incident_Model::is_valid_incident($id, FALSE))
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
						WHERE incident_id = ?";
					$query = $db->query($sql, $id);
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
					$incident_arr = array(
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
		$this->template->content->custom_forms = new View('reports/submit_custom_forms');
		$disp_custom_fields = customforms::get_custom_form_fields($id, $form['form_id'], FALSE, "view");
		$custom_field_mismatch = customforms::get_edit_mismatch($form['form_id']);
        $this->template->content->custom_forms->disp_custom_fields = $disp_custom_fields;
		$this->template->content->custom_forms->custom_field_mismatch = $custom_field_mismatch;
		$this->template->content->custom_forms->form = $form;

		// Retrieve Previous & Next Records
		$previous = ORM::factory('incident')->where('id < ', $id)->orderby('id','desc')->find();
		$previous_url = $previous->loaded
		    ? url::base().'admin/reports/edit/'.$previous->id
		    : url::base().'admin/reports/';
		$next = ORM::factory('incident')->where('id > ', $id)->orderby('id','desc')->find();
		$next_url = $next->loaded
		    ? url::base().'admin/reports/edit/'.$next->id
		    : url::base().'admin/reports/';
		$this->template->content->previous_url = $previous_url;
		$this->template->content->next_url = $next_url;

		// Javascript Header
		$this->template->map_enabled = TRUE;
		$this->template->colorpicker_enabled = TRUE;
		$this->template->treeview_enabled = TRUE;
		$this->template->json2_enabled = TRUE;

		$this->template->js = new View('reports/submit_edit_js');
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
	public function download()
	{
		// If user doesn't have access, redirect to dashboard
		if ( ! $this->auth->has_permission("reports_download"))
		{
			url::redirect(url::site().'admin/dashboard');
		}

		$this->template->content = new View('admin/reports/download');
		$this->template->content->title = Kohana::lang('ui_admin.download_reports');

		$form = array(
			'data_active'   => '',
			'data_verified'   => '',
			'data_include' => '',
			'from_date'	   => '',
			'to_date'	   => '',
			'form_auth_token'=> ''
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
			$post->add_rules('data_active.*','required','numeric','between[0,1]');
			$post->add_rules('data_verified.*','required','numeric','between[0,1]');
			//$post->add_rules('data_include.*','numeric','between[1,5]');
			$post->add_rules('data_include.*','numeric','between[1,7]');
			$post->add_rules('from_date','date_mmddyyyy');
			$post->add_rules('to_date','date_mmddyyyy');

			// Validate the report dates, if included in report filter
			if (!empty($_POST['from_date']) OR !empty($_POST['to_date']))
			{
				// Valid FROM Date?
				if (empty($_POST['from_date']) OR (strtotime($_POST['from_date']) > strtotime("today")))
				{
					$post->add_error('from_date','range');
				}

				// Valid TO date?
				if (empty($_POST['to_date']) OR (strtotime($_POST['to_date']) > strtotime("today")))
				{
					$post->add_error('to_date','range');
				}

				// TO Date not greater than FROM Date?
				if (strtotime($_POST['from_date']) > strtotime($_POST['to_date']))
				{
					$post->add_error('to_date','range_greater');
				}
			}

			// Test to see if things passed the rule checks
			if ($post->validate(TRUE))
			{
				// Set filter
				$filter = '( ';
				
				// Report Type Filter
				$show_active = false;
				$show_inactive = false;
				$show_verified = false;
				$show_not_verified = false;
				
				if (in_array(1, $post->data_active))
				{
					$show_active = true;
				}

				if (in_array(0, $post->data_active))
				{
					$show_inactive = true;
				}

				if (in_array(1, $post->data_verified))
				{
					$show_verified = true;
				}

				if (in_array(0, $post->data_verified))
				{
					$show_not_verified = true;
				}
				
				// Handle active or not active
				if ($show_active && !$show_inactive)
				{
					$filter .= ' incident_active = 1 ';
				}
				elseif (!$show_active && $show_inactive)
				{
					$filter .= '  incident_active = 0 ';
				}
				elseif ($show_active && $show_inactive)
				{
					$filter .= ' (incident_active = 1 OR incident_active = 0) ';
				}
				
				// Neither active nor inactive selected: select nothing
				elseif (!$show_active && !$show_inactive)
				{
					// Equivalent to 1 = 0
					$filter .= ' (incident_active = 0 AND incident_active = 1) ';
				}
				
				$filter .= ' AND ';
				
				// Handle verified
				if($show_verified && !$show_not_verified)
				{				
					$filter .= ' incident_verified = 1 ';
				}
				elseif (!$show_verified && $show_not_verified)
				{				
					$filter .= ' incident_verified = 0 ';
				}
				elseif ($show_verified && $show_not_verified)
				{				
					$filter .= ' (incident_verified = 0 OR incident_verified = 1) ';
				}
				elseif (!$show_verified && !$show_not_verified)
				{				
					$filter .= ' (incident_verified = 0 AND incident_verified = 1) ';
				}
				
				$filter .= ') ';

				// Report Date Filter
				if ( ! empty($post->from_date) AND !empty($post->to_date))
				{
					$filter .= " AND ( incident_date >= '" . date("Y-m-d H:i:s",strtotime($post->from_date))
							. "' AND incident_date <= '" . date("Y-m-d H:i:s",strtotime($post->to_date)) . "' ) ";
				}

				// Retrieve reports
				$incidents = ORM::factory('incident')->where($filter)->orderby('incident_dateadd', 'desc')->find_all();

				// Column Titles
				ob_start();
				echo "#,INCIDENT TITLE,INCIDENT DATE";
				foreach($post->data_include as $item)
				{
					if ($item == 1) {
						echo ",LOCATION";
					}

					if ($item == 2) {
						echo ",DESCRIPTION";
					}

					if ($item == 3) {
						echo ",CATEGORY";
					}

					if ($item == 4) {
						echo ",LATITUDE";
					}

					if($item == 5) {
						echo ",LONGITUDE";
					}
					if($item == 6)
					{
						$custom_titles = customforms::get_custom_form_fields('','',false);
						foreach($custom_titles as $field_name)
						{

							echo ",".$field_name['field_name'];
						}

					}
					if($item == 7)
					{
						echo ",FIRST NAME,LAST NAME,EMAIL";
					}

				}

				echo ",APPROVED,VERIFIED";

				// Incase a plugin would like to add some custom fields
				$custom_headers = "";
				Event::run('ushahidi_filter.report_download_csv_header', $custom_headers);
				echo $custom_headers;

				echo "\n";

				foreach ($incidents as $incident)
				{
					echo '"'.$incident->id.'",';
					echo '"'.$this->_csv_text($incident->incident_title).'",';
					echo '"'.$incident->incident_date.'"';

					foreach($post->data_include as $item)
					{
						switch ($item)
						{
							case 1:
								echo ',"'.$this->_csv_text($incident->location->location_name).'"';
							break;

							case 2:
								echo ',"'.$this->_csv_text($incident->incident_description).'"';
							break;

							case 3:
								echo ',"';

								foreach($incident->incident_category as $category)
								{
									if ($category->category->category_title)
									{
										echo $this->_csv_text($category->category->category_title) . ", ";
									}
								}
								echo '"';
							break;

							case 4:
								echo ',"'.$this->_csv_text($incident->location->latitude).'"';
							break;

							case 5:
								echo ',"'.$this->_csv_text($incident->location->longitude).'"';
							break;

							case 6:
								$incident_id = $incident->id;
								$custom_fields = customforms::get_custom_form_fields($incident_id,'',false);
								if ( ! empty($custom_fields))
								{
									foreach($custom_fields as $custom_field)
									{
										echo',"'.$this->_csv_text($custom_field['field_response']).'"';
									}
								}
								else
								{
									$custom_field = customforms::get_custom_form_fields('','',false);
									foreach ($custom_field as $custom)
									{
										echo',"'.$this->_csv_text("").'"';
									}
								}
							break;
							
							case 7:
								$incident_orm = ORM::factory('incident', $incident->id);
								$incident_person = $incident_orm->incident_person;
								if($incident_person->loaded)
								{
									echo',"'.$this->_csv_text($incident_person->person_first).'"'.',"'.$this->_csv_text($incident_person->person_last).'"'.
									',"'.$this->_csv_text($incident_person->person_email).'"';
								}
								else
								{
									echo',"'.$this->_csv_text("").'"'.',"'.$this->_csv_text("").'"'.',"'.$this->_csv_text("").'"';
								}
							break;
						}
					}

					if ($incident->incident_active)
					{
						echo ",YES";
					}
					else
					{
						echo ",NO";
					}

					if ($incident->incident_verified)
					{
						echo ",YES";
					}
					else
					{
						echo ",NO";
					}

					// Incase a plugin would like to add some custom data for an incident
					$event_data = array("report_csv" => "", "incident" => $incident);
					Event::run('ushahidi_filter.report_download_csv_incident', $event_data);
					echo $event_data['report_csv'];
					echo "\n";
				}
				$report_csv = ob_get_clean();

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
				$errors = arr::merge($errors, $post->errors('report'));
				$form_error = TRUE;
			}
		}

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;

		// Javascript Header
		$this->template->js = new View('admin/reports/download_js');
		$this->template->js->calendar_img = url::base() . "media/img/icon-calendar.gif";
	}

	public function upload()
	{
		// If user doesn't have access, redirect to dashboard
		if ( ! $this->auth->has_permission("reports_upload"))
		{
			url::redirect(url::site().'admin/dashboard');
		}

		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->template->content = new View('admin/reports/upload');
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
					// Get contents of CSV file
					$data = file_get_contents($_FILES['csvfile']['tmp_name']);
					
					// Replace carriage return character
					$replacedata = preg_replace("/\r/","\n",$data);
					
					// Replace file content
					file_put_contents($_FILES['csvfile']['tmp_name'], $replacedata);
					
					if($filehandle = fopen($_FILES['csvfile']['tmp_name'], 'r'))
					{
						$importer = new ReportsImporter;

						if ($importer->import($filehandle))
						{
							$this->template->content = new View('admin/reports/upload_success');
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

			if (count($errors))
			{
				$this->template->content = new View('admin/reports/upload');
				$this->template->content->title = Kohana::lang('ui_admin.upload_reports');
				$this->template->content->errors = $errors;
				$this->template->content->form_error = 1;
			}
		}
	}

	/**
	* Save newly added dynamic categories
	*/
	public function save_category()
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

	/* private functions */

	// Dynamic categories form fields
	private function _new_categories_form_arr()
	{
		return array(
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

		/**
		 * NOTES: 2011-11-17 - John Etherton <john@ethertontech.com> I'm pretty sure this needs to be
		 * internationalized, seems rather biased towards English.
		 * */
		// Stop words that we won't search for
		// Add words as needed!!
		$stop_words = array('the', 'and', 'a', 'to', 'of', 'in', 'i', 'is', 'that', 'it',
		'on', 'you', 'this', 'for', 'but', 'with', 'are', 'have', 'be',
		'at', 'or', 'as', 'was', 'so', 'if', 'out', 'not');

		$keywords = explode(' ', $keyword_raw);

		if (is_array($keywords) AND !empty($keywords))
		{
			array_change_key_case($keywords, CASE_LOWER);
			$i = 0;

			foreach ($keywords as $value)
			{
				if (!in_array($value,$stop_words) AND !empty($value))
				{
					$chunk = $this->db->escape_str($value);
					if ($i > 0)
					{
						$or = ' OR ';
					}
					$where_string = $where_string
									.$or
									."incident_title LIKE '%$chunk%' OR incident_description LIKE '%$chunk%'  OR location_name LIKE '%$chunk%'";
					$i++;
				}
			}
		}

		// Return
		return (!empty($where_string)) ? $where_string :  "1=1";
	}

	private function _csv_text($text)
	{
		$text = stripslashes(htmlspecialchars($text));
		return $text;
	}

	/**
	 * Adds extra filter parameters to the reports::fetch_incidents()
	 * method. This way we can add 'all_reports=>true and other filters
	 * that don't come standard since we are on the backend.
	 * Works by simply adding in SQL conditions to the params
	 * array of the reports::fetch_incidents() method
	 * @return none
	 */
	public function _add_incident_filters()
	{
		$params = Event::$data;
		$params = array_merge($params, $this->params);
		Event::$data = $params;
	}
	

	/**
	* Delete Photo
	* @param int $id The unique id of the photo to be deleted
	*/
	public function deletePhoto ($id)
	{
		$this->auto_render = FALSE;
		$this->template = "";

		if ($id)
		{
			Media_Model::delete_photo($id);
		}
	}

}
