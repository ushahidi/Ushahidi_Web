<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Reports Controller.
 * This controller will take care of adding and editing reports in the Admin section.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Reports Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Reports_Controller extends Admin_Controller
{
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
		$this->template->content = new View('admin/reports');
		$this->template->content->title = 'Reports';
		
		
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
		
		
		// check, has the form been submitted?
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
	    if ($_POST)
	    {
			$post = Validation::factory($_POST);
			
	         //  Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('action','required', 'alpha', 'length[1,1]');
			$post->add_rules('incident_id.*','required','numeric');
			
			if ($post->validate())
	        {
				if ($post->action == 'a')		// Approve Action
				{
					foreach($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						if ($update->loaded == true) {
							$update->incident_active = '1';
							$update->save();
						}
					}
					$form_action = "APPROVED";
				}
				elseif ($post->action == 'u') 	// Unapprove Action
				{
					foreach($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						if ($update->loaded == true) {
							$update->incident_active = '0';
							$update->save();
						}
					}
					$form_action = "UNAPPROVED";
				}
				elseif ($post->action == 'v')	// Verify Action
				{
					foreach($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						if ($update->loaded == true) {
							if ($update->incident_verified == '1') {
								$update->incident_verified = '0';
							}
							else {
								$update->incident_verified = '1';
							}
							$update->verify->user_id = $_SESSION['auth_user']->id;			// Record 'Verified By' Action
							$update->verify->verified_date = date("Y-m-d H:i:s",time());
							$update->verify->verified_status = '1';
							$update->save();
						}
					}
					$form_action = "VERIFIED";
				}
				elseif ($post->action == 'd')	// Delete Action
				{
					foreach($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						if ($update->loaded == true) {
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
							
							// Delete relationship to Twitter message
							$updatemessage = ORM::factory('twitter')->where('incident_id',$incident_id)->find();
							if ($updatemessage->loaded == true) {
								$updatemessage->incident_id = 0;
								$updatemessage->save();
							}
						}					
					}
					$form_action = "DELETED";
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
			'query_string'    => 'page',
			'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
			'total_items'    => ORM::factory('incident')->where($filter)->count_all()
		));

		$incidents = ORM::factory('incident')->where($filter)->orderby('incident_dateadd', 'desc')->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);
		
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
		$this->template->content = new View('admin/reports_edit');
		$this->template->content->title = 'Create A Report';
		
		// setup and initialize form field names
		$form = array
	    (
	        'location_id'      => '',
			'locale'		   => '',
			'incident_title'      => '',
	        'incident_description'    => '',
	        'incident_date'  => '',
	        'incident_hour'      => '',
			'incident_minute'      => '',
			'incident_ampm' => '',
			'latitude' => '',
			'longitude' => '',
			'location_name' => '',
			'country_id' => '',
			'incident_category' => array(),
			'incident_news' => array(),
			'incident_video' => array(),
			'incident_photo' => array(),
			'person_first' => '',
			'person_last' => '',
			'person_email' => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
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
		
        // Create Categories
        $this->template->content->categories = $this->_get_categories();	
		$this->template->content->new_categories_form = $this->_new_categories_form_arr();
		 
		// Time formatting
	    $this->template->content->hour_array = $this->_hour_array();
	    $this->template->content->minute_array = $this->_minute_array();
        $this->template->content->ampm_array = $this->_ampm_array();

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
		
		// Retrieve thumbnail photos (if edit);
		//XXX: fix _get_thumbnails
		$this->template->content->incident = $this->_get_thumbnails($id);
		
		// Are we creating this report from an SMS or Twitter Message?
		// If so retrieve message
		if ((isset($_GET['mid']) && !empty($_GET['mid'])) || (isset($_GET['tid']) && !empty($_GET['tid']))) {
			
			// Check what kind of message this is
			if(isset($_GET['mid'])){
				//Then it's an SMS message
				$messageType = 'sms';
				$mobile_id = $_GET['mid'];
				$dbtable = 'message';
				$col_prefix = 'message';
				$incident_title = 'Mobile Report';
			}elseif(isset($_GET['tid'])){
				//Then it's a Twitter message
				$messageType = 'twitter';
				$mobile_id = $_GET['tid'];
				$dbtable = 'twitter';
				$col_prefix = 'tweet';
				$incident_title = 'Twitter Report';
			}
			
			$message = ORM::factory($dbtable, $mobile_id)->where($col_prefix.'_type','1');
			if ($message->loaded == true) {
				
				// Has a report already been created for this SMS?
				if ($message->incident_id != 0) {
					// Redirect to report
					url::redirect('admin/reports/edit/'. $message->incident_id);
				}
				if($messageType == 'sms'){
					$this->template->content->message = $message->message;
					$this->template->content->message_from = $message->message_from;
					$this->template->content->show_messages = true;
					$form['incident_title'] = $incident_title;
					$form['incident_description'] = $message->message;
					$from_search = $this->template->content->message_from;
				}elseif($messageType == 'twitter'){
					$this->template->content->message = $message->tweet;
					$this->template->content->message_from = $message->tweet_from;
					$this->template->content->show_messages = true;
					$form['incident_title'] = $incident_title;
					$form['incident_description'] = $message->tweet;
					$from_search = $this->template->content->tweet_from;
				}
				
				// Retrieve Last 5 Messages From this Number
				$this->template->content->allmessages = ORM::factory($dbtable)
					->where($col_prefix.'_from', $from_search)
					->where($col_prefix.'_type','1')
					->orderby($col_prefix.'_date', 'desc')
					->limit(5)
					->find_all();
			}else{
				$mobile_id = "";
				$this->template->content->show_messages = false;
			}
		}else{
			$this->template->content->show_messages = false;
		}
	
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
            // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory(array_merge($_POST,$_FILES));

	         //  Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
	        $post->add_rules('locale','required','alpha_dash','length[5]');
			$post->add_rules('location_id','numeric');
			$post->add_rules('mobile_id','numeric');
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
			
			// Test to see if things passed the rule checks
	        if ($post->validate())
	        {
                // Yes! everything is valid
				$location_id = $post->location_id;
				// STEP 1: SAVE LOCATION
				$location = new Location_Model($location_id);
				$location->location_name = $post->location_name;
				$location->country_id = $post->country_id;
				$location->latitude = $post->latitude;
				$location->longitude = $post->longitude;
				$location->location_date = date("Y-m-d H:i:s",time());
				$location->save();
				
				// STEP 2: SAVE INCIDENT
				$incident = new Incident_Model($id);
				$incident->location_id = $location->id;
				$incident->locale = $post->locale;
				$incident->user_id = $_SESSION['auth_user']->id;
				$incident->incident_title = $post->incident_title;
				$incident->incident_description = $post->incident_description;
				
				$incident_date=split("/",$post->incident_date);
				// where the $_POST['date'] is a value posted by form in mm/dd/yyyy format
					$incident_date=$incident_date[2]."-".$incident_date[0]."-".$incident_date[1];
					
				$incident_time = $post->incident_hour . ":" . $post->incident_minute . ":00 " . $post->incident_ampm;
				$incident->incident_date = $incident_date . " " . $incident_time;
				// Is this new or edit?
				if ($id)	// edit
				{
					$incident->incident_datemodify = date("Y-m-d H:i:s",time());
				}
				else 		// new
				{
					$incident->incident_dateadd = date("Y-m-d H:i:s",time());
				}
				// Is this an SMS or Twitter submitted report?
                //XXX: It is possible that 'mobile_id' may not be available through
                //$_POST
				if(isset($messageType) && $messageType != "")
				{
					if($messageType == 'sms'){
						$incident->incident_mode = 2; // SMS - 2
					}elseif($messageType == 'twitter'){
						$incident->incident_mode = 4; // Twitter - 4
					}elseif(isset($mobile_id) && $mobile_id != ""){
						$incident->incident_mode = 2; //Set the default as SMS - 2
					}
				}
				$incident->save();
				
				
				// STEP 3: SAVE CATEGORIES
				ORM::factory('Incident_Category')->where('incident_id',$incident->id)->delete_all();		// Delete Previous Entries
				foreach($post->incident_category as $item)
				{
					$incident_category = new Incident_Category_Model();
					$incident_category->incident_id = $incident->id;
					$incident_category->category_id = $item;
					$incident_category->save();
				}
				
				
				// STEP 4: SAVE MEDIA
				ORM::factory('Media')->where('incident_id',$incident->id)->where('media_type <> 1')->delete_all();		// Delete Previous Entries
				// a. News
				foreach($post->incident_news as $item)
				{
					if(!empty($item))
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
				foreach ($filenames as $filename) {
					$new_filename = $incident->id . "_" . $i . "_" . time();
					
					// Resize original file... make sure its max 408px wide
					Image::factory($filename)->resize(408,248,Image::AUTO)
						->save(Kohana::config('upload.directory', TRUE) . $new_filename . ".jpg");
					
					// Create thumbnail
					Image::factory($filename)->resize(70,41,Image::HEIGHT)
						->save(Kohana::config('upload.directory', TRUE) . $new_filename . "_t.jpg");
					
					// Remove the temporary file
					unlink($filename);
					
					// Save to DB
					$photo = new Media_Model();
					$photo->location_id = $location->id;
					$photo->incident_id = $incident->id;
					$photo->media_type = 1; // Images
					$photo->media_link = $new_filename . ".jpg";
					$photo->media_thumb = $new_filename . "_t.jpg";
					$photo->media_date = date("Y-m-d H:i:s",time());
					$photo->save();
					$i++;
				}				
				
				
				// STEP 5: SAVE PERSONAL INFORMATION
				ORM::factory('Incident_Person')->where('incident_id',$incident->id)->delete_all();		// Delete Previous Entries
	            $person = new Incident_Person_Model();
				$person->location_id = $location->id;
				$person->incident_id = $incident->id;
				$person->person_first = $post->person_first;
				$person->person_last = $post->person_last;
				$person->person_email = $post->person_email;
				$person->person_date = date("Y-m-d H:i:s",time());
				$person->save();
				
				
				// STEP 6: SAVE LINK TO SMS MESSAGE
				if(isset($mobile_id) && $mobile_id != "")
				{
					$savemessage = ORM::factory($dbtable, $mobile_id);
					if ($savemessage->loaded == true) 
					{
						$savemessage->incident_id = $incident->id;
						$savemessage->save();
					}
				}
				
				
				// STEP 7: SAVE AND CLOSE?
				if ($post->save == 1)		// Save but don't close
				{
					url::redirect('admin/reports/edit/'. $incident->id .'/saved');
				}
				else 						// Save and close
				{
					url::redirect('admin/reports/');
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
				$incident = ORM::factory('incident', $id);
				if ($incident != "0")
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
					
					// Combine Everything
					$incident_arr = array
				    (
						'location_id' => $incident->location->id,
						'locale' => $incident->locale,
						'incident_title' => $incident->incident_title,
						'incident_description' => $incident->incident_description,
						'incident_date' => date('m/d/Y', strtotime($incident->incident_date)),
						'incident_hour' => date('h', strtotime($incident->incident_date)),
						'incident_minute' => date('i', strtotime($incident->incident_date)),
						'incident_ampm' => date('A', strtotime($incident->incident_date)),
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
						'person_email' => $incident->incident_person->person_email
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
			else
			{
				$form['locale'] = Kohana::config('locale.language');
				$form['latitude'] = Kohana::config('settings.default_lat');
				$form['longitude'] = Kohana::config('settings.default_lon');
				$form['country_id'] = Kohana::config('settings.default_country');
			}
		}
	
		$this->template->content->form = $form;
	    $this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		
		// Javascript Header
		$this->template->map_enabled = TRUE;
        $this->template->colorpicker_enabled = TRUE;
		$this->template->js = new View('admin/reports_edit_js');
		$this->template->js->default_map = Kohana::config('settings.default_map');
		$this->template->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->template->js->latitude = $form['latitude'];
		$this->template->js->longitude = $form['longitude'];
		
		// Inline Javascript
		$this->template->content->date_picker_js = $this->_date_picker_js();
        $this->template->content->color_picker_js = $this->_color_picker_js();
        $this->template->content->new_category_toggle_js = $this->_new_category_toggle_js();
	}


	/**
	* Download Reports in CSV format
    */
    
	function download()
	{
		$this->template->content = new View('admin/reports_download');
		$this->template->content->title = 'Download Reports';
		
		$form = array(
			'data_point'      => '',
			'data_include'      => '',
			'from_date'    => '',
			'to_date'    => ''
		);
		$errors = $form;
		$form_error = FALSE;
		
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
            // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

	         //  Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
	        $post->add_rules('data_point.*','required','numeric','between[1,4]');
			$post->add_rules('data_include.*','numeric','between[1,3]');
			$post->add_rules('from_date','date_mmddyyyy');
			$post->add_rules('to_date','date_mmddyyyy');
			
			// Validate the report dates, if included in report filter
			if (!empty($_POST['from_date']) || !empty($_POST['to_date']))
			{	
				// Valid FROM Date?
				if (empty($_POST['from_date']) || (strtotime($_POST['from_date']) > strtotime("today"))) {
					$post->add_error('from_date','range');
				}
				
				// Valid TO date?
				if (empty($_POST['to_date']) || (strtotime($_POST['to_date']) > strtotime("today"))) {
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
					if ($item == 1) {
						$filter .= " OR incident_active = 1 ";
					}
					if ($item == 2) {
						$filter .= " OR incident_verified = 1 ";
					}
					if ($item == 3) {
						$filter .= " OR incident_active = 0 ";
					}
					if ($item == 4) {
						$filter .= " OR incident_verified = 0 ";
					}
				}
				$filter .= ") ";
				
				// Report Date Filter
				if (!empty($post->from_date) && !empty($post->to_date)) 
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
				}
				$report_csv .= ",APPROVED,VERIFIED";
				$report_csv .= "\n";
				
				foreach ($incidents as $incident)
				{
					$report_csv .= '"'.$incident->id.'",';
					$report_csv .= '"'.htmlspecialchars($incident->incident_title).'",';
					$report_csv .= '"'.$incident->incident_date.'"';
					
					foreach($post->data_include as $item)
					{
						if ($item == 1) {
							$report_csv .= ',"'.htmlspecialchars($incident->location->location_name).'"';
						}
						if ($item == 2) {
							$report_csv .= ',"'.htmlspecialchars($incident->incident_description).'"';
						}
						if ($item == 3) {
							$report_csv .= ',"';
							foreach($incident->incident_category as $category) 
							{ 
								$report_csv .= htmlspecialchars($category->category->category_title) . ", ";
							}
							$report_csv .= '"';
						}
					}
					if ($incident->incident_active) {
						$report_csv .= ",YES";
					}
					else
					{
						$report_csv .= ",NO";
					}
					if ($incident->incident_verified) {
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
	            // repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

	            // populate the error fields, if any
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
    function upload() {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->template->content = new View('admin/reports_upload');
			$this->template->content->title = 'Upload Reports';
			$this->template->content->form_error = false;
		}
		if($_SERVER['REQUEST_METHOD']=='POST') {
			$errors = array();
			$notices = array();
			if(!$_FILES['csvfile']['error']) {
				if(file_exists($_FILES['csvfile']['tmp_name'])) {
					if($filehandle = fopen($_FILES['csvfile']['tmp_name'], 'r')) {
						$importer = new ReportsImporter;
						if($importer->import($filehandle)) {
							$this->template->content = new View('admin/reports_upload_success');
							$this->template->content->title = 'Upload Reports';		
							$this->template->content->rowcount = $importer->totalrows;
							$this->template->content->imported = $importer->importedrows;
							$this->template->content->notices = $importer->notices;
						}
						else {
							$errors = $importer->errors;
						}
					}
					else {
						$errors[] = 'Could not open file for reading';
					}
				} // file exists?
				else {
					$errors[] = 'Could not find uploaded file';
				}
			} // upload errors?
			else {
				$errors[] = $_FILES['csvfile']['error'];
			}
			if(count($errors)) {
				$this->template->content = new View('admin/reports_upload');
				$this->template->content->title = 'Upload Reports';		
				$this->template->content->errors = $errors;
				$this->template->content->form_error = 1;
			}
		} // _POST
	}

	/**
	* Translate a report
    * @param bool|int $id The id no. of the report
    * @param bool|string $saved
    */
    
	function translate( $id = false, $saved = false )
	{
		$this->template->content = new View('admin/reports_translate');
		$this->template->content->title = 'Translate Report';
		
		// Which incident are we adding this translation for?
		if (isset($_GET['iid']) && !empty($_GET['iid']))
		{
			$incident_id = $_GET['iid'];
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
		
		
		// setup and initialize form field names
		$form = array
	    (
	        'locale'      => '',
			'incident_title'      => '',
			'incident_description'    => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
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
	
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
            // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

	         //  Add some filters
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
				if ($post->save == 1)		// Save but don't close
				{
					url::redirect('admin/reports/translate/'. $incident_l->id .'/saved/?iid=' . $incident_id);
				}
				else 						// Save and close
				{
					url::redirect('admin/reports/');
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
		
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
	        // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);
			
	         //  Add some filters
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
			if (!empty($photo_large))
				unlink(Kohana::config('upload.directory', TRUE) . $photo_large);
			if (!empty($photo_thumb))
				unlink(Kohana::config('upload.directory', TRUE) . $photo_thumb);

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
 	    // get categories array
		//$this->template->content->bind('categories', $categories);
				
        $categories_total = ORM::factory('category')->where('category_visible', '1')->count_all();
        $this->template->content->categories_total = $categories_total;

		$categories = array();
		foreach (ORM::factory('category')->where('category_visible', '1')->find_all() as $category)
		{
			// Create a list of all categories
			$categories[$category->id] = array($category->category_title, $category->category_color);
		}
		
	    return $categories;
		
	}

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
		    $hour_array[sprintf("%02d", $i)] = sprintf("%02d", $i); 	// Add Leading Zero
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
	    return $ampm_array = array('pm'=>'pm','am'=>'am');
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
		
		$iid = $_GET['iid'];
		if (empty($iid)) {
			$iid = 0;
		}
		$translate = ORM::factory('incident_lang')->where('incident_id',$iid)->where('locale',$post->locale)->find();
		if ($translate->loaded == true) {
			$post->add_error( 'locale', 'exists');		
		// Not found
		} else {
			return;
		}
	}
}
