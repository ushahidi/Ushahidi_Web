<?php defined('SYSPATH') or die('No direct script access.');

/**
* Reports Controller
*/
class Reports_Controller extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
	
		$this->template->this_page = 'reports';		
	}
	
	
	// Reports Main Listing
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function index($page = 1)
	{
		$this->template->content = new View('admin/reports');
		$this->template->content->title = 'Reports';
		
		
		if (!empty($_GET['status']))
		{
			$status = $_GET['status'];
			
			if (strtolower($status) == 'a')
			{
				$filter = 'incident_active = 1';
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
						$update->incident_active = '1';
						$update->save();
						$form_action = "APPROVED";
					}
				}
				elseif ($post->action == 'u') 	// Unapprove Action
				{
					foreach($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						$update->incident_active = '0';
						$update->save();
						$form_action = "UNAPPROVED";
					}
				}
				elseif ($post->action == 'v')	// Verify Action
				{
					foreach($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						$update->incident_verified = '1';
						$update->verify->user_id = $_SESSION['auth_user']->id;			// Record 'Verified By' Action
						$update->verify->verified_date = date("Y-m-d H:i:s",time());
						$update->verify->verified_status = '1';
						$update->save();
						$form_action = "VERIFIED";
					}
				}
				elseif ($post->action == 'd')	// Delete Action
				{
					foreach($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						ORM::factory('location')->where('id',$update->location_id)->delete_all();	// Delete Location
						ORM::factory('incident_category')->where('incident_id',$update->id)->delete_all();	// Delete Categories
						ORM::factory('media')->where('incident_id',$update->id)->delete_all();				// Delete Media
						ORM::factory('incident_person')->where('incident_id',$update->id)->delete_all();	// Delete Sender
						$update->delete();						
						$form_action = "DELETED";
					}
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

	
	
	
	
	
	// Add & Edit Reports
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function edit( $id = false, $saved = false )
	{
		$this->template->content = new View('admin/reports_edit');
		$this->template->content->title = 'Create A Report';
		
		// setup and initialize form field names
		$form = array
	    (
	        'location_id'      => '',
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
		
		
		// get categories array
		$this->template->content->bind('categories', $categories);
		// Total Categories In system
		$this->template->content->categories_total = ORM::factory('category')->where('category_visible', '1')->count_all();
		
		$categories = array();
		foreach (ORM::factory('category')->where('category_visible', '1')->find_all() as $category)
		{
			// Create a list of all categories
			$categories[$category->id] = array($category->category_title, $category->category_color);
		}
		
		
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
		
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
	        // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			
			// Merge the POST and FILES for validation
			$post = Validation::factory(array_merge($_POST,$_FILES));
			
	         //  Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
			
	        $post->add_rules('location_id','numeric');
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
			$post->add_rules('incident_category.*','required','numeric');
			
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
				$incident->user_id = $_SESSION['auth_user']->id;
				$incident->incident_title = $post->incident_title;
				$incident->incident_description = $post->incident_description;
				
				$incident_date=split("/",$post->incident_date);
				// where the $_POST['date'] is a value posted by form in mm/dd/yyyy format
					$incident_date=$incident_date[2]."-".$incident_date[0]."-".$incident_date[1];
					
				$incident_time = $post->incident_hour . ":" . $post->incident_hour . ":00 " . $post->incident_ampm;
				$incident->incident_date = $incident_date . " " . $incident_time;
				if ($id)
				{
					$incident->incident_datemodify = date("Y-m-d H:i:s",time());
				}
				else
				{
					$incident->incident_dateadd = date("Y-m-d H:i:s",time());
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
				ORM::factory('Media')->where('incident_id',$incident->id)->delete_all();		// Delete Previous Entries
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
					$photo->media_type = 1;		// Images
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
				
				if ($post->save == 1)		// Save and redirect back to same report
				{
					url::redirect(url::base() . 'admin/reports/edit/'. $incident->id .'/saved');
				}
				else 						// Save and close
				{
					url::redirect(url::base() . 'admin/reports/');
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
					url::redirect(url::base() . 'admin/reports/');
				}		
				
			}
			else
			{
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
		$this->template->js = new View('admin/reports_edit_js');
		$this->template->js->default_map = Kohana::config('settings.default_map');
		$this->template->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->template->js->latitude = $form['latitude'];
		$this->template->js->longitude = $form['longitude'];
	}
}