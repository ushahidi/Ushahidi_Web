<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Reports Controller.
 * This controller will take care of adding and editing reports in the Member section.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Members
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Reports_Controller extends Members_Controller {
	
	function __construct()
	{
		parent::__construct();

		$this->template->this_page = 'reports';
	}


	/**
	* Lists the reports.
	* @param int $page
	*/
	function index($page = 1)
	{
		$this->template->content = new View('members/reports');
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

		// check, has the form been submitted?
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		if ($_POST)
		{
			$post = Validation::factory($_POST);

			 //	 Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('action','required', 'alpha', 'length[1,1]');
			$post->add_rules('incident_id.*','required','numeric');

			if ($post->validate())
			{
				if ($post->action == 'd')	//Delete Action
				{
					foreach($post->incident_id as $item)
					{
						$update = ORM::factory('incident')
							->where('user_id', $this->user->id)
							->find($item);
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

							// Action::report_delete - Deleted a Report
							Event::run('ushahidi_action.report_delete', $update);
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
			'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
			'total_items'	 => ORM::factory('incident')
				->join('location', 'incident.location_id', 'location.id','INNER')
				->where($filter)
				->where('user_id', $this->user->id)
				->count_all()
			));

		$incidents = ORM::factory('incident')
			->join('location', 'incident.location_id', 'location.id','INNER')
			->where($filter)
			->where('user_id', $this->user->id)
			->orderby('incident_dateadd', 'desc')
			->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);

		$location_ids = array();
		foreach ($incidents as $incident)
		{
			$location_ids[] = $incident->location_id;
		}
		
		//check if location_ids is not empty
		if( count($location_ids ) > 0 ) 
		{
			$locations_result = ORM::factory('location')->in('id',implode(',',$location_ids))->find_all();
			$locations = array();
			foreach ($locations_result as $loc)
			{
				$locations[$loc->id] = $loc->location_name;
			}
		}
		else
		{
			$locations = array();
		}

		$this->template->content->locations = $locations;

		//GET countries
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
	function edit( $id = false, $saved = false )
	{
		$db = new Database();

		$this->template->content = new View('members/reports_edit');
		$this->template->content->title = Kohana::lang('ui_admin.create_report');

		// setup and initialize form field names
		$form = array
		(
			'location_id'	   => '',
			'form_id'	   => '',
			'locale'		   => '',
			'incident_title'	  => '',
			'incident_description'	  => '',
			'incident_date'	 => '',
			'incident_hour'		 => '',
			'incident_minute'	   => '',
			'incident_ampm' => '',
			'latitude' => '',
			'longitude' => '',
			'geometry' => array(),
			'location_name' => '',
			'country_id' => '',
			'incident_category' => array(),
			'incident_news' => array(),
			'incident_video' => array(),
			'incident_photo' => array(),
			'person_first' => '',
			'person_last' => '',
			'person_email' => '',
			'custom_field' => array(),
			'incident_source' => '',
			'incident_information' => '',
			'incident_zoom' => ''
		);

		//	copy the form as errors, so the errors will be stored with keys corresponding to the form field names
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

		// Initialize Default Values
		$form['locale'] = Kohana::config('locale.language');
		//$form['latitude'] = Kohana::config('settings.default_lat');
		//$form['longitude'] = Kohana::config('settings.default_lon');
		$form['country_id'] = Kohana::config('settings.default_country');
		$form['incident_date'] = date("m/d/Y",time());
		$form['incident_hour'] = date('h');
		$form['incident_minute'] = date('i');
		$form['incident_ampm'] = date('a');
		// initialize custom field array
		$form['custom_field'] = $this->_get_custom_form_fields($id,'',true);


		// Locale (Language) Array
		$this->template->content->locale_array = Kohana::config('locale.all_languages');

		// Create Categories
		$this->template->content->categories = $this->_get_categories();

		// Time formatting
		$this->template->content->hour_array = $this->_hour_array();
		$this->template->content->minute_array = $this->_minute_array();
		$this->template->content->ampm_array = $this->_ampm_array();
		
		$this->template->content->stroke_width_array = $this->_stroke_width_array();

		// Get Countries
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

		//GET custom forms
		$forms = array();
		foreach (ORM::factory('form')->where('form_active',1)->find_all() as $custom_forms)
		{
			$forms[$custom_forms->id] = $custom_forms->form_title;
		}
		
		$this->template->content->forms = $forms;

		// Retrieve thumbnail photos (if edit);
		//XXX: fix _get_thumbnails
		$this->template->content->incident = $this->_get_thumbnails($id);
		
		
		// Are we creating this report from a Checkin?
		if ( isset($_GET['cid']) && !empty($_GET['cid']) ) {

			$checkin_id = (int) $_GET['cid'];
			$checkin = ORM::factory('checkin', $checkin_id);

			if ($checkin->loaded)
			{
				// Has a report already been created for this Checkin?
				if ( (int) $checkin->incident_id > 0)
				{
					// Redirect to report
					url::redirect('members/reports/edit/'. $checkin->incident_id);
				}

				$incident_description = $checkin->checkin_description;
				$incident_title = text::limit_chars(strip_tags($incident_description), 100, "...", true);
				$form['incident_title'] = $incident_title;
				$form['incident_description'] = $incident_description;
				$form['incident_date'] = date('m/d/Y', strtotime($checkin->checkin_date));
				$form['incident_hour'] = date('h', strtotime($checkin->checkin_date));
				$form['incident_minute'] = date('i', strtotime($checkin->checkin_date));
				$form['incident_ampm'] = date('a', strtotime($checkin->checkin_date));

				// Does the sender of this message have a location?
				if ($checkin->location->loaded)
				{
					$form['location_id'] = $checkin->location_id;
					$form['latitude'] = $checkin->location->latitude;
					$form['longitude'] = $checkin->location->longitude;
					$form['location_name'] = $checkin->location->location_name;
				}
			}
		}
		

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory(array_merge($_POST,$_FILES));

			 //	 Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			// $post->add_rules('locale','required','alpha_dash','length[5]');
			$post->add_rules('location_id','numeric');
			$post->add_rules('message_id','numeric');
			$post->add_rules('incident_title','required', 'length[3,200]');
			$post->add_rules('incident_description','required');
			$post->add_rules('incident_date','required','date_mmddyyyy');
			$post->add_rules('incident_hour','required','between[1,12]');
			$post->add_rules('incident_minute','required','between[0,59]');
			
			if ($_POST['incident_ampm'] != "am" && $_POST['incident_ampm'] != "pm")
			{
				$post->add_error('incident_ampm','values');
			}
			
			$post->add_rules('latitude','required','between[-90,90]');		// Validate for maximum and minimum latitude values
			$post->add_rules('longitude','required','between[-180,180]');	// Validate for maximum and minimum longitude values
			$post->add_rules('location_name','required', 'length[3,200]');

			//XXX: Hack to validate for no checkboxes checked
			if (!isset($_POST['incident_category'])) {
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
				foreach ($_POST['incident_news'] as $key => $url) {
					if (!empty($url) AND !(bool) filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED))
					{
						$post->add_error('incident_news','url');
					}
				}
			}

			// Validate only the fields that are filled in
			if (!empty($_POST['incident_video']))
			{
				foreach ($_POST['incident_video'] as $key => $url) {
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

			// Validate Custom Fields
			if (isset($post->custom_field) && !$this->_validate_custom_form_fields($post->custom_field))
			{
				$post->add_error('custom_field', 'values');
			}

			$post->add_rules('incident_source','numeric', 'length[1,1]');
			$post->add_rules('incident_information','numeric', 'length[1,1]');

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				// STEP 1: SAVE LOCATION
				$location = new Location_Model();
				reports::save_location($post, $location);

				// STEP 2: SAVE INCIDENT
				$incident = new Incident_Model();
				reports::save_report($post, $incident, $location->id);

				// STEP 3: SAVE CATEGORIES
				reports::save_category($post, $incident);

				// STEP 4: SAVE MEDIA
				reports::save_media($post, $incident);

				// STEP 5: SAVE CUSTOM FORM FIELDS
				reports::save_custom_fields($post, $incident);

				// STEP 6: SAVE PERSONAL INFORMATION
				reports::save_personal_info($post, $incident);
				
				// If creating a report from a checkin
				if(isset($checkin_id) AND $checkin_id != "")
				{
					$checkin = ORM::factory('checkin', $checkin_id);
					if ($checkin->loaded)
					{
						$checkin->incident_id = $incident->id;
						$checkin->save();
					
						// Attach all the media items in this checkin to the report
						foreach ($checkin->media as $media)
						{
							$media->incident_id = $incident->id;
							$media->save();
						}
					}
				}

				// Action::report_add / report_submit_members - Added a New Report
				//++ Do we need two events for this? Or will one suffice?
				Event::run('ushahidi_action.report_add', $incident);
				Event::run('ushahidi_action.report_submit_members', $post);


				// SAVE AND CLOSE?
				if ($post->save == 1)		// Save but don't close
				{
					url::redirect('members/reports/edit/'. $incident->id .'/saved');
				}
				else						// Save and close
				{
					url::redirect('members/reports/');
				}
			}

			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('report'));
				$form_error = TRUE;
			}
		}
		else
		{
			if ( $id )
			{
				// Retrieve Current Incident
				$incident = ORM::factory('incident')
					->where('user_id', $this->user->id)
					->find($id);
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
						'custom_field' => $this->_get_custom_form_fields($id,$incident->form_id,true),
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
					url::redirect('members/reports/');
				}

			}
		}

		$this->template->content->id = $id;
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;

		// Retrieve Custom Form Fields Structure
		$disp_custom_fields = $this->_get_custom_form_fields($id,$form['form_id'],false);
		$this->template->content->disp_custom_fields = $disp_custom_fields;

		// Retrieve Previous & Next Records
		$previous = ORM::factory('incident')->where('id < ', $id)->orderby('id','desc')->find();
		$previous_url = ($previous->loaded ?
				url::base().'members/reports/edit/'.$previous->id :
				url::base().'members/reports/');
		$next = ORM::factory('incident')->where('id > ', $id)->orderby('id','desc')->find();
		$next_url = ($next->loaded ?
				url::base().'members/reports/edit/'.$next->id :
				url::base().'members/reports/');
		$this->template->content->previous_url = $previous_url;
		$this->template->content->next_url = $next_url;

		// Javascript Header
		$this->template->map_enabled = TRUE;
		$this->template->colorpicker_enabled = TRUE;
		$this->template->treeview_enabled = TRUE;
		$this->template->json2_enabled = TRUE;
		
		$this->template->js = new View('admin/reports_edit_js');
		$this->template->js->default_map = Kohana::config('settings.default_map');
		$this->template->js->default_zoom = Kohana::config('settings.default_zoom');

		if (!$form['latitude'] || !$form['latitude'])
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
		
		// Pack Javascript
		$myPacker = new javascriptpacker($this->template->js , 'Normal', false, false);
		$this->template->js = $myPacker->pack();
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

	// Return thumbnail photos
	//XXX: This needs to be fixed, it's probably ok to return an empty iterable instead of "0"
	private function _get_thumbnails( $id )
	{
		$incident = ORM::factory('incident', $id);

		if ( $id )
		{
			$incident = ORM::factory('incident', $id);

			return $incident;

		}
		return "0";
	}

	private function _get_categories()
	{
		$categories = ORM::factory('category')
			//->where('category_visible', '1')
			->where('parent_id', '0')
			->orderby('category_title', 'ASC')
			->find_all();

		return $categories;
	}

	// Time functions
	private function _hour_array()
	{
		for ($i=1; $i <= 12 ; $i++)
		{
			$hour_array[sprintf("%02d", $i)] = sprintf("%02d", $i);		// Add Leading Zero
		}
		return $hour_array;
	}

	private function _minute_array()
	{
		for ($j=0; $j <= 59 ; $j++)
		{
			$minute_array[sprintf("%02d", $j)] = sprintf("%02d", $j);	// Add Leading Zero
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
	 * Retrieve Custom Form Fields
	 * @param bool|int $incident_id The unique incident_id of the original report
	 * @param int $form_id The unique form_id. Uses default form (1), if none selected
	 * @param bool $field_names_only Whether or not to include just fields names, or field names + data
	 * @param bool $data_only Whether or not to include just data
	 */
	private function _get_custom_form_fields($incident_id = false, $form_id = 1, $data_only = false)
	{
		$fields_array = array();

		if (!$form_id)
		{
			$form_id = 1;
		}
		$custom_form = ORM::factory('form', $form_id)->orderby('field_position','asc');
		foreach ($custom_form->form_field as $custom_formfield)
		{
			if ($data_only)
			{ // Return Data Only
				$fields_array[$custom_formfield->id] = '';

				foreach ($custom_formfield->form_response as $form_response)
				{
					if ($form_response->incident_id == $incident_id)
					{
						$fields_array[$custom_formfield->id] = $form_response->form_response;
					}
				}
			}
			else
			{ // Return Field Structure
				$fields_array[$custom_formfield->id] = array(
					'field_id' => $custom_formfield->id,
					'field_name' => $custom_formfield->field_name,
					'field_type' => $custom_formfield->field_type,
					'field_required' => $custom_formfield->field_required,
					'field_maxlength' => $custom_formfield->field_maxlength,
					'field_height' => $custom_formfield->field_height,
					'field_width' => $custom_formfield->field_width,
					'field_isdate' => $custom_formfield->field_isdate,
					'field_response' => ''
					);
			}
		}

		return $fields_array;
	}


	/**
	 * Validate Custom Form Fields
	 * @param array $custom_fields Array
	 */
	private function _validate_custom_form_fields($custom_fields = array())
	{
		$custom_fields_error = "";

		foreach ($custom_fields as $field_id => $field_response)
		{
			// Get the parameters for this field
			$field_param = ORM::factory('form_field', $field_id);
			if ($field_param->loaded == true)
			{
				// Validate for required
				if ($field_param->field_required == 1 && $field_response == "")
				{
					return false;
				}

				// Validate for date
				if ($field_param->field_isdate == 1 && $field_response != "")
				{
					$myvalid = new Valid();
					return $myvalid->date_mmddyyyy($field_response);
				}
			}
		}
		return true;
	}


	/**
	 * Ajax call to update Incident Reporting Form
	 */
	public function switch_form()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		isset($_POST['form_id']) ? $form_id = $_POST['form_id'] : $form_id = "1";
		isset($_POST['incident_id']) ? $incident_id = $_POST['incident_id'] : $incident_id = "";

		$html = "";
		$fields_array = array();
		$custom_form = ORM::factory('form', $form_id)->orderby('field_position','asc');

		foreach ($custom_form->form_field as $custom_formfield)
		{
			$fields_array[$custom_formfield->id] = array(
				'field_id' => $custom_formfield->id,
				'field_name' => $custom_formfield->field_name,
				'field_type' => $custom_formfield->field_type,
				'field_required' => $custom_formfield->field_required,
				'field_maxlength' => $custom_formfield->field_maxlength,
				'field_height' => $custom_formfield->field_height,
				'field_width' => $custom_formfield->field_width,
				'field_isdate' => $custom_formfield->field_isdate,
				'field_response' => ''
				);

			// Load Data, if Any
			foreach ($custom_formfield->form_response as $form_response)
			{
				if ($form_response->incident_id = $incident_id)
				{
					$fields_array[$custom_formfield->id]['field_response'] = $form_response->form_response;
				}
			}
		}

		foreach ($fields_array as $field_property)
		{
			$html .= "<div class=\"row\">";
			$html .= "<h4>" . $field_property['field_name'] . "</h4>";
			if ($field_property['field_type'] == 1)
			{ // Text Field
				// Is this a date field?
				if ($field_property['field_isdate'] == 1)
				{
					$html .= form::input('custom_field['.$field_property['field_id'].']', $field_property['field_response'],
						' id="custom_field_'.$field_property['field_id'].'" class="text"');
					$html .= "<script type=\"text/javascript\">
							$(document).ready(function() {
							$(\"#custom_field_".$field_property['field_id']."\").datepicker({
							showOn: \"both\",
							buttonImage: \"" . url::base() . "media/img/icon-calendar.gif\",
							buttonImageOnly: true
							});
							});
						</script>";
				}
				else
				{
					$html .= form::input('custom_field['.$field_property['field_id'].']', $field_property['field_response'],
						' id="custom_field_'.$field_property['field_id'].'" class="text custom_text"');
				}
			}
			elseif ($field_property['field_type'] == 2)
			{ // TextArea Field
				$html .= form::textarea('custom_field['.$field_property['field_id'].']',
					$field_property['field_response'], ' class="custom_text" rows="3"');
			}
			$html .= "</div>";
		}

		echo json_encode(array("status"=>"success", "response"=>$html));
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
