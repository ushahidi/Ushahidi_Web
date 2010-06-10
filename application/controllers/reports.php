<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This controller is used to list/ view and edit reports
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Reports Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Reports_Controller extends Main_Controller {

	var $logged_in;

	function __construct()
	{
		parent::__construct();

		// Javascript Header

		$this->template->header->validator_enabled = TRUE;

		$this->template->header->js = '';

		// Is the Admin Logged In?

		$this->logged_in = Auth::instance()->logged_in()
			 ? TRUE
			 : FALSE;
	}

	/**
	 * Displays all reports.
	 */
	public function index($cluster_id = 0)
	{
		$this->template->header->this_page = 'reports';
		$this->template->content = new View('reports');
		
		$db = new Database;
		
		$filter = ( isset($_GET['c']) && !empty($_GET['c']) && $_GET['c']!=0 )
			? " AND ( c.id='".$_GET['c']."' OR 
				c.parent_id='".$_GET['c']."' )  "
			: " AND 1 = 1";

		if ( isset($_GET['sw']) && !empty($_GET['sw']) && 
				count($southwest = explode(",",$_GET['sw'])) > 1 &&
			isset($_GET['ne']) && !empty($_GET['ne']) && 
				count($northeast = explode(",",$_GET['ne'])) > 1
			)
 		{
			list($longitude_min, $latitude_min) = $southwest;
			list($longitude_max, $latitude_max) = $northeast;

			$filter .= " AND l.latitude >=".$latitude_min.
				" AND l.latitude <=".$latitude_max;
			$filter .= " AND l.longitude >=".$longitude_min.
				" AND l.longitude <=".$longitude_max;
		}
		
		// Pagination
		$pagination = new Pagination(array(
				'query_string' => 'page',
				'items_per_page' => (int) Kohana::config('settings.items_per_page'),
				'total_items' => $db->query("SELECT DISTINCT i.* FROM `".$this->table_prefix."incident` AS i JOIN `".$this->table_prefix."incident_category` AS ic ON (i.`id` = ic.`incident_id`) JOIN `".$this->table_prefix."category` AS c ON (c.`id` = ic.`category_id`) JOIN `".$this->table_prefix."location` AS l ON (i.`location_id` = l.`id`) WHERE `incident_active` = '1' $filter")->count()
				));

		$incidents = $db->query("SELECT DISTINCT i.*, l.`location_name` FROM `".$this->table_prefix."incident` AS i JOIN `".$this->table_prefix."incident_category` AS ic ON (i.`id` = ic.`incident_id`) JOIN `".$this->table_prefix."category` AS c ON (c.`id` = ic.`category_id`) JOIN `".$this->table_prefix."location` AS l ON (i.`location_id` = l.`id`) WHERE `incident_active` = '1' $filter ORDER BY incident_date DESC LIMIT ". (int) Kohana::config('settings.items_per_page') . " OFFSET ".$pagination->sql_offset);
			
		$this->template->content->incidents = $incidents;

		//Set default as not showing pagination. Will change below if necessary.
		$this->template->content->pagination = '';

		// Pagination and Total Num of Report Stats
		if ($pagination->total_items == 1) {
			$plural = '';
		} else {
			$plural = 's';
		}
		if ($pagination->total_items > 0) {
			$current_page = ($pagination->sql_offset/ (int) Kohana::config('settings.items_per_page')) + 1;
			$total_pages = ceil($pagination->total_items/ (int) Kohana::config('settings.items_per_page'));

			if ($total_pages > 1) { // If we want to show pagination
				$this->template->content->pagination_stats = Kohana::lang('ui_admin.showing_page').' '.$current_page.' '.Kohana::lang('ui_admin.of').' '.$total_pages.' '.Kohana::lang('ui_admin.pages');

				$this->template->content->pagination = $pagination;
			} else { // If we don't want to show pagination
				$this->template->content->pagination_stats = $pagination->total_items.' '.Kohana::lang('ui_admin.reports');
			}
		} else {
			$this->template->content->pagination_stats = '('.$pagination->total_items.' report'.$plural.')';
		}

		$icon_html = array();
		$icon_html[1] = "<img src=\"".url::base()."media/img/image.png\">"; //image
		$icon_html[2] = "<img src=\"".url::base()."media/img/video.png\">"; //video
		$icon_html[3] = ""; //audio
		$icon_html[4] = ""; //news
		$icon_html[5] = ""; //podcast

		//Populate media icon array
		$this->template->content->media_icons = array();
		foreach($incidents as $incident) {
			$incident_id = $incident->id;
			if (ORM::factory('media')
				->where('incident_id', $incident_id)->count_all() > 0) {
				$medias = ORM::factory('media')
												->where('incident_id', $incident_id)->find_all();

				//Modifying a tmp var prevents Kohona from throwing an error
				$tmp = $this->template->content->media_icons;
				$tmp[$incident_id] = '';

				foreach($medias as $media) {
					$tmp[$incident_id] .= $icon_html[$media->media_type];
					$this->template->content->media_icons = $tmp;
				}
			}
		}

		// Category Title, if Category ID available
		$category_id = ( isset($_GET['c']) && !empty($_GET['c']) )
			? $_GET['c'] : "0";
		$category = ORM::factory('category')
			->find($category_id);
		$this->template->content->category_title = ( $category->loaded ) ?
			$category->category_title : "";


		// Collect report stats

		// Total Reports

		$total_reports = Incident_Model::get_total_reports(TRUE);

		// Average Reports Per Day

		$oldest_timestamp = Incident_Model::get_oldest_report_timestamp();

		// Round the number of days up to the nearest full day

		$days_since = ceil((time() - $oldest_timestamp) / 86400);
		if($days_since < 1) {
			$avg_reports_per_day = $total_reports;
		}else{
			$avg_reports_per_day = round(($total_reports / $days_since),2);
		}

		// Percent Verified

		$total_verified = Incident_Model::get_total_reports_by_verified(true);
		$percent_verified = ($total_reports == 0) ? '-' : round((($total_verified / $total_reports) * 100),2).'%';

		$this->template->content->total_reports = $total_reports;
		$this->template->content->avg_reports_per_day = $avg_reports_per_day;
		$this->template->content->percent_verified = $percent_verified;
	}

	/**
	 * Submits a new report.
	 */
	public function submit($id = false, $saved = false)
	{
		// First, are we allowed to submit new reports?
		if ( ! Kohana::config('settings.allow_reports'))
		{
			url::redirect(url::site().'main');
		}
		
		$this->template->header->this_page = 'reports_submit';
		$this->template->content = new View('reports_submit');

		// setup and initialize form field names
		$form = array
		(
			'incident_title' => '',
			'incident_description' => '',
			'incident_date' => '',
			'incident_hour' => '',
			'incident_minute' => '',
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
			'person_email' => '',
			'form_id'	  => '',
			'custom_field' => array()
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
		$form['incident_date'] = date("m/d/Y",time());
		$form['incident_hour'] = "12";
		$form['incident_minute'] = "00";
		$form['incident_ampm'] = "pm";
		// initialize custom field array
		$form['custom_field'] = $this->_get_custom_form_fields($id,'',true);
		//GET custom forms
		$forms = array();
		foreach (ORM::factory('form')->find_all() as $custom_forms)
		{
			$forms[$custom_forms->id] = $custom_forms->form_title;
		}
		$this->template->content->forms = $forms;


		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory(array_merge($_POST,$_FILES));

			 //  Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('incident_title', 'required', 'length[3,200]');
			$post->add_rules('incident_description', 'required');
			$post->add_rules('incident_date', 'required', 'date_mmddyyyy');
			$post->add_rules('incident_hour', 'required', 'between[1,12]');
			$post->add_rules('incident_minute', 'required', 'between[0,59]');

			if ($_POST['incident_ampm'] != "am" && $_POST['incident_ampm'] != "pm")
			{
				$post->add_error('incident_ampm','values');
			}

			// Validate for maximum and minimum latitude values
			$post->add_rules('latitude', 'required', 'between[-90,90]');
			$post->add_rules('longitude', 'required', 'between[-180,180]');
			$post->add_rules('location_name', 'required', 'length[3,200]');

			//XXX: Hack to validate for no checkboxes checked
			if (!isset($_POST['incident_category'])) {
				$post->incident_category = "";
				$post->add_error('incident_category', 'required');
			}
			else
			{
				$post->add_rules('incident_category.*', 'required', 'numeric');
			}

			// Validate only the fields that are filled in
			if (!empty($_POST['incident_news']))
			{
				foreach ($_POST['incident_news'] as $key => $url)
				{
					if (!empty($url) AND
					!(bool) filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED))
					{
						$post->add_error('incident_news', 'url');
					}
				}
			}

			// Validate only the fields that are filled in
			if (!empty($_POST['incident_video']))
			{
				foreach ($_POST['incident_video'] as $key => $url)
				{
					if (!empty($url) AND
						 	!(bool) filter_var($url, FILTER_VALIDATE_URL,
																			 FILTER_FLAG_HOST_REQUIRED))
					{
						$post->add_error('incident_video', 'url');
					}
				}
			}

			// Validate photo uploads
			$post->add_rules('incident_photo', 'upload::valid',
											 'upload::type[gif,jpg,png]', 'upload::size[2M]');


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
				// STEP 1: SAVE LOCATION
				$location = new Location_Model();
				$location->location_name = $post->location_name;
				$location->latitude = $post->latitude;
				$location->longitude = $post->longitude;
				$location->location_date = date("Y-m-d H:i:s",time());
				$location->save();

				// STEP 2: SAVE INCIDENT
				$incident = new Incident_Model();
				$incident->location_id = $location->id;
				$incident->form_id = $post->form_id;
				$incident->user_id = 0;
				$incident->incident_title = $post->incident_title;
				$incident->incident_description = $post->incident_description;

				$incident_date=explode("/",$post->incident_date);

				// The $_POST['date'] is a value posted by form in mm/dd/yyyy format
				$incident_date=$incident_date[2]."-".$incident_date[0]."-".$incident_date[1];
				$incident_time = $post->incident_hour
					.":".$post->incident_minute
					.":00 ".$post->incident_ampm;
				$incident->incident_date = date( "Y-m-d H:i:s", strtotime($incident_date . " " . $incident_time) );				
				$incident->incident_dateadd = date("Y-m-d H:i:s",time());
				$incident->save();

				// STEP 3: SAVE CATEGORIES
				foreach($post->incident_category as $item)
				{
					$incident_category = new Incident_Category_Model();
					$incident_category->incident_id = $incident->id;
					$incident_category->category_id = $item;
					$incident_category->save();
				}

				// STEP 4: SAVE MEDIA
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
					if (!empty($item))
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
					$new_filename = $incident->id."_".$i."_".time();

					// Resize original file... make sure its max 408px wide
					Image::factory($filename)->resize(408,248,Image::AUTO)
						->save(Kohana::config('upload.directory', TRUE).$new_filename.".jpg");

					// Create thumbnail
					Image::factory($filename)->resize(70,41,Image::HEIGHT)
						->save(Kohana::config('upload.directory', TRUE).$new_filename."_t.jpg");

					// Remove the temporary file
					unlink($filename);

					// Save to DB
					$photo = new Media_Model();
					$photo->location_id = $location->id;
					$photo->incident_id = $incident->id;
					$photo->media_type = 1; // Images
					$photo->media_link = $new_filename.".jpg";
					$photo->media_thumb = $new_filename."_t.jpg";
					$photo->media_date = date("Y-m-d H:i:s",time());
					$photo->save();
					$i++;
				}


				// STEP 7: SAVE CUSTOM FORM FIELDS
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

				// STEP 5: SAVE PERSONAL INFORMATION
				$person = new Incident_Person_Model();
				$person->location_id = $location->id;
				$person->incident_id = $incident->id;
				$person->person_first = $post->person_first;
				$person->person_last = $post->person_last;
				$person->person_email = $post->person_email;
				$person->person_date = date("Y-m-d H:i:s",time());
				$person->save();

				url::redirect('reports/thanks');
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


		// Retrieve Country Cities
		$default_country = Kohana::config('settings.default_country');
		$this->template->content->cities = $this->_get_cities($default_country);
		$this->template->content->multi_country = Kohana::config('settings.multi_country');

		$this->template->content->id = $id;
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->categories = $this->_get_categories($form['incident_category']);

		// Retrieve Custom Form Fields Structure
		$disp_custom_fields = $this->_get_custom_form_fields($id,$form['form_id'],false);
		$this->template->content->disp_custom_fields = $disp_custom_fields;


		// Javascript Header
		$this->template->header->map_enabled = TRUE;
		$this->template->header->datepicker_enabled = TRUE;
		$this->template->header->treeview_enabled = TRUE;
		$this->template->header->js = new View('reports_submit_js');
		$this->template->header->js->default_map = Kohana::config('settings.default_map');
		$this->template->header->js->default_zoom = Kohana::config('settings.default_zoom');
		if (!$form['latitude'] || !$form['latitude'])
		{
			$this->template->header->js->latitude = Kohana::config('settings.default_lat');
			$this->template->header->js->longitude = Kohana::config('settings.default_lon');
		}else{
			$this->template->header->js->latitude = $form['latitude'];
			$this->template->header->js->longitude = $form['longitude'];
		}
	}

	 /**
	 * Displays a report.
	 * @param boolean $id If id is supplied, a report with that id will be
	 * retrieved.
	 */
	public function view($id = false)
	{
		$this->template->header->this_page = 'reports';
		$this->template->content = new View('reports_view');

		// Load Akismet API Key (Spam Blocker)

		$api_akismet = Kohana::config('settings.api_akismet');

		if ( !$id )
		{

			url::redirect('main');

		}else{
			$incident = ORM::factory('incident', $id);

			if ( $incident->id == 0 )	// Not Found
			{
				url::redirect('main');
			}

			// Comment Post?
			// Setup and initialize form field names

			$form = array
			(
				'comment_author' => '',
				'comment_description' => '',
				'comment_email' => '',
				'comment_ip' => '',
				'captcha' => ''
			);

			$captcha = Captcha::factory();
			$errors = $form;
			$form_error = FALSE;

			// Check, has the form been submitted, if so, setup validation

			if ($_POST)
			{
				// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things

				$post = Validation::factory($_POST);

				// Add some filters

				$post->pre_filter('trim', TRUE);

				// Add some rules, the input field, followed by a list of checks, carried out in order

				$post->add_rules('comment_author', 'required', 'length[3,100]');
				$post->add_rules('comment_description', 'required');
				$post->add_rules('comment_email', 'required','email', 'length[4,100]');
				$post->add_rules('captcha', 'required', 'Captcha::valid');

				// Test to see if things passed the rule checks

				if ($post->validate())
				{
					// Yes! everything is valid

					if ($api_akismet != "")
					{
						// Run Akismet Spam Checker

						$akismet = new Akismet();

						// Comment data

						$comment = array(
							'author' => $post->comment_author,
							'email' => $post->comment_email,
							'website' => "",
							'body' => $post->comment_description,
							'user_ip' => $_SERVER['REMOTE_ADDR']
						);

						$config = array(
							'blog_url' => url::site(),
							'api_key' => $api_akismet,
							'comment' => $comment
						);

						$akismet->init($config);

						if ($akismet->errors_exist())
						{
							if ($akismet->is_error('AKISMET_INVALID_KEY'))
							{
								// throw new Kohana_Exception('akismet.api_key');

							}elseif ($akismet->is_error('AKISMET_RESPONSE_FAILED')){

								// throw new Kohana_Exception('akismet.server_failed');

							}elseif ($akismet->is_error('AKISMET_SERVER_NOT_FOUND')){

								// throw new Kohana_Exception('akismet.server_not_found');

							}

							// If the server is down, we have to post
							// the comment :(
							// $this->_post_comment($comment);

							$comment_spam = 0;
						}else{

							if ($akismet->is_spam())
							{
								$comment_spam = 1;
							}else{
								$comment_spam = 0;
							}
						}
					}else{

						// No API Key!!

						$comment_spam = 0;
					}


					$comment = new Comment_Model();
					$comment->incident_id = $id;
					$comment->comment_author = strip_tags($post->comment_author);
					$comment->comment_description = strip_tags($post->comment_description);
					$comment->comment_email = strip_tags($post->comment_email);
					$comment->comment_ip = $_SERVER['REMOTE_ADDR'];
					$comment->comment_date = date("Y-m-d H:i:s",time());

					// Activate comment for now

					if ($comment_spam == 1)
					{
						$comment->comment_spam = 1;
						$comment->comment_active = 0;
					}else{
						$comment->comment_spam = 0;
						$comment->comment_active = 1;
					}
					$comment->save();

					// Notify Admin Of New Comment

					$send = notifications::notify_admins(
						"[".Kohana::config('settings.site_name')."] ".
							Kohana::lang('notifications.admin_new_comment.subject'),
							Kohana::lang('notifications.admin_new_comment.message')
							."\n\n'".strtoupper($incident->incident_title)."'"
							."\n".url::base().'reports/view/'.$id
						);

					// Redirect

					url::redirect('reports/view/'.$id);

				}else{

					// No! We have validation errors, we need to show the form again, with the errors

					// Repopulate the form fields

					$form = arr::overwrite($form, $post->as_array());

					// Populate the error fields, if any

					$errors = arr::overwrite($errors, $post->errors('comments'));
					$form_error = TRUE;
				}
			}

			$this->template->content->incident_id = $incident->id;
			$this->template->content->incident_title = $incident->incident_title;
			$this->template->content->incident_description = nl2br($incident->incident_description);
			$this->template->content->incident_location = $incident->location->location_name;
			$this->template->content->incident_latitude = $incident->location->latitude;
			$this->template->content->incident_longitude = $incident->location->longitude;
			$this->template->content->incident_date = date('M j Y', strtotime($incident->incident_date));
			$this->template->content->incident_time = date('H:i', strtotime($incident->incident_date));
			$this->template->content->incident_category = $incident->incident_category;

			if ($incident->incident_rating == '')
			{
				$this->template->content->incident_rating = 0;
			}else{
				$this->template->content->incident_rating = $incident->incident_rating;
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

			$this->template->content->incident_verified = $incident->incident_verified;

			// Retrieve Comments (Additional Information)
			$this->template->content->comments = "";
			if (Kohana::config('settings.allow_comments'))
			{
				$this->template->content->comments = new View('reports_comments');
				$incident_comments = array();
				if ($id)
				{
					$incident_comments = ORM::factory('comment')
																	->where('incident_id',$id)
																	->where('comment_active','1')
																	->where('comment_spam','0')
																	->orderby('comment_date', 'asc')
																	->find_all();
				}
				$this->template->content->comments->incident_comments = $incident_comments;
			}
		}

		// Add Neighbors

		$this->template->content->incident_neighbors = $this->_get_neighbors($incident->location->latitude,
																								 	 $incident->location->longitude);

		// Get RSS News Feeds

		$this->template->content->feeds = ORM::factory('feed_item')
										->limit('5')
										->orderby('item_date', 'desc')
										->find_all();

		// Video links

		$this->template->content->incident_videos = $incident_video;

		// Images

		$this->template->content->incident_photos = $incident_photo;

		// Create object of the video embed class

		$video_embed = new VideoEmbed();
		$this->template->content->videos_embed = $video_embed;

		// Javascript Header

		$this->template->header->map_enabled = TRUE;
		$this->template->header->photoslider_enabled = TRUE;
		$this->template->header->videoslider_enabled = TRUE;
		$this->template->header->js = new View('reports_view_js');
		$this->template->header->js->incident_id = $incident->id;
		$this->template->header->js->default_map = Kohana::config('settings.default_map');
		$this->template->header->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->template->header->js->latitude = $incident->location->latitude;
		$this->template->header->js->longitude = $incident->location->longitude;
		$this->template->header->js->incident_photos = $incident_photo;

		// Initialize custom field array

		$form_field_names = $this->_get_custom_form_fields($id,$incident->form_id,false);

		// Retrieve Custom Form Fields Structure

		$disp_custom_fields = $this->_get_custom_form_fields($id,$incident->form_id,true);
		$this->template->content->disp_custom_fields = $disp_custom_fields;
		
		// Are we allowed to submit comments?
		$this->template->content->comments_form = "";
		if (Kohana::config('settings.allow_comments'))
		{
			$this->template->content->comments_form = new View('reports_comments_form');
			$this->template->content->comments_form->form = $form;
			$this->template->content->comments_form->form_field_names = $form_field_names;
			$this->template->content->comments_form->captcha = $captcha;
			$this->template->content->comments_form->errors = $errors;
			$this->template->content->comments_form->form_error = $form_error;
		}

		// If the Admin is Logged in - Allow for an edit link
		$this->template->content->logged_in = $this->logged_in;
	}

	/**
	 * Report Thanks Page
	 */
	function thanks()
	{
		$this->template->header->this_page = 'reports_submit';
		$this->template->content = new View('reports_submit_thanks');
	}

	/**
	 * Report Rating.
	 * @param boolean $id If id is supplied, a rating will be applied to selected report
	 */
	public function rating($id = false)
	{
		$this->template = "";
		$this->auto_render = FALSE;

		if (!$id)
		{
			echo json_encode(array("status"=>"error", "message"=>"ERROR!"));
		}
		else
		{
			if (!empty($_POST['action']) AND !empty($_POST['type']))
			{
				$action = $_POST['action'];
				$type = $_POST['type'];

				// Is this an ADD(+1) or SUBTRACT(-1)?
				if ($action == 'add')
				{
					$action = 1;
				}
				elseif ($action == 'subtract')
				{
					$action = -1;
				}
				else
				{
					$action = 0;
				}

				if (!empty($action) AND ($type == 'original' OR $type == 'comment'))
				{
					// Has this IP Address rated this post before?
					if ($type == 'original')
					{
						$previous = ORM::factory('rating')
												->where('incident_id',$id)
												->where('rating_ip',$_SERVER['REMOTE_ADDR'])
												->find();
					}
					elseif ($type == 'comment')
					{
						$previous = ORM::factory('rating')
						->where('comment_id',$id)
						->where('rating_ip',$_SERVER['REMOTE_ADDR'])
						->find();
					}

					// If previous exits... update previous vote
					$rating = new Rating_Model($previous->id);

					// Are we rating the original post or the comments?
					if ($type == 'original')
					{
						$rating->incident_id = $id;
					}
					elseif ($type == 'comment')
					{
						$rating->comment_id = $id;
					}

					$rating->rating = $action;
					$rating->rating_ip = $_SERVER['REMOTE_ADDR'];
					$rating->rating_date = date("Y-m-d H:i:s",time());
					$rating->save();

					// Get total rating and send back to json
					$total_rating = $this->_get_rating($id, $type);

					echo json_encode(array("status"=>"saved", "message"=>"SAVED!", "rating"=>$total_rating));
				}
				else
				{
					echo json_encode(array("status"=>"error1", "message"=>"ERROR!"));
				}
			}
			else
			{
				echo json_encode(array("status"=>"error2", "message"=>"ERROR!"));
			}
		}
	}

	/*
	 * Retrieves Cities
	 */
	private function _get_cities()
	{
		$cities = ORM::factory('city')->orderby('city', 'asc')->find_all();
		$city_select = array('' => Kohana::lang('ui_main.reports_select_city'));

		foreach ($cities as $city)
		{
			$city_select[$city->city_lon.",".$city->city_lat] = $city->city;
		}

		return $city_select;
	}

	/*
	 * Retrieves Categories
	 */
	private function _get_categories($selected_categories)
	{
		$categories = ORM::factory('category')
			->where('category_visible', '1')
			->where('parent_id', '0')
			->orderby('category_title', 'ASC')
			->find_all();

		return $categories;
	}

	/*
	 * Retrieves Total Rating For Specific Post
	 * Also Updates The Incident & Comment Tables (Ratings Column)
	 */
	private function _get_rating($id = false, $type = NULL)
	{
		if (!empty($id) AND ($type == 'original' OR $type == 'comment'))
		{
			if ($type == 'original')
			{
				$which_count = 'incident_id';
			}
			elseif ($type == 'comment')
			{
				$which_count = 'comment_id';
			}
			else
			{
				return 0;
			}

			$total_rating = 0;

			// Get All Ratings and Sum them up
			foreach(ORM::factory('rating')
												->where($which_count,$id)
												->find_all() as $rating)
			{
				$total_rating += $rating->rating;
			}

			// Update Counts
			if ($type == 'original')
			{
				$incident = ORM::factory('incident', $id);
				if ($incident->loaded==true)
				{
					$incident->incident_rating = $total_rating;
					$incident->save();
				}
			}
			elseif ($type == 'comment')
			{
				$comment = ORM::factory('comment', $id);
				if ($comment->loaded==true)
				{
					$comment->comment_rating = $total_rating;
					$comment->save();
				}
			}

			return $total_rating;
		}
		else
		{
			return 0;
		}
	}

	/*
	* Retrieves Neighboring Incidents
	*/
	private function _get_neighbors($latitude = 0, $longitude = 0)
	{
		$proximity = new Proximity($latitude, $longitude, 100); // Within 100 Miles ( or Kms ;-) )

		// Generate query from proximity calculator
		$radius_query = "location.latitude >= '" . $proximity->minLat . "'
			AND ".$this->table_prefix."location.latitude <= '" . $proximity->maxLat . "'
			AND ".$this->table_prefix."location.longitude >= '" . $proximity->minLong . "'
			AND ".$this->table_prefix."location.longitude <= '" . $proximity->maxLong . "'
			AND incident_active = 1";

		$neighbors = ORM::factory('incident')
							 	 ->join('location', 'incident.location_id', 'location.id','INNER')
								 ->select('incident.*')
								 ->where($radius_query)
								 ->limit('5')
								 ->find_all();

		return $neighbors;
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
	/*public function switch_form()
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
	}*/

} // End Reports
