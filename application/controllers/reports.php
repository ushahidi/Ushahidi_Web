<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This controller is used to list/ view and edit reports
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Reports_Controller extends Main_Controller {
	/**
	 * Whether an admin console user is logged in
	 * @var bool
	 */
	var $logged_in;

	public function __construct()
	{
		parent::__construct();

		$this->themes->validator_enabled = TRUE;

		// Is the Admin Logged In?
		$this->logged_in = Auth::instance()->logged_in();
	}

	/**
	 * Displays all reports.
	 */
	public function index()
	{
		// Cacheable Controller
		$this->is_cachable = TRUE;
		
		$this->template->header->this_page = 'reports';
		$this->template->content = new View('reports');
		$this->themes->js = new View('reports_js');
		
		// Store any exisitng URL parameters
		$this->themes->js->url_params = json_encode($_GET);
		
		// Enable the map
		$this->themes->map_enabled = TRUE;
		
		// Set the latitude and longitude
		$this->themes->js->latitude = Kohana::config('settings.default_lat');
		$this->themes->js->longitude = Kohana::config('settings.default_lon');
		$this->themes->js->default_map = Kohana::config('settings.default_map');
		$this->themes->js->default_zoom = Kohana::config('settings.default_zoom');
		
		// Load the alert radius view
		$alert_radius_view = new View('alert_radius_view');
		$alert_radius_view->show_usage_info = FALSE;
		$alert_radius_view->enable_find_location = FALSE;
		$alert_radius_view->css_class = "rb_location-radius";
		
		$this->template->content->alert_radius_view = $alert_radius_view;
		
		// Get locale
		$l = Kohana::config('locale.language.0');
		
		// Get the report listing view
		$report_listing_view = $this->_get_report_listing_view($l);
		
		// Set the view
		$this->template->content->report_listing_view = $report_listing_view;
		
		// Load the category
		$category_id = (isset($_GET['c']) AND intval($_GET['c']) > 0)? intval($_GET['c']) : 0;
		$category = ORM::factory('category', $category_id);

		if ($category->loaded)
		{
			$translated_title = Category_Lang_Model::category_title($category_id,$l);
			
			// Set the category title
			$this->template->content->category_title = ($translated_title)
				? $translated_title
				: $category->category_title;
		}
		else
		{
			$this->template->content->category_title = "";
		}

		// Collect report stats
		$this->template->content->report_stats = new View('reports_stats');
		// Total Reports

		$total_reports = Incident_Model::get_total_reports(TRUE);

		// Get the date of the oldest report
		$oldest_timestamp = Incident_Model::get_oldest_report_timestamp();
		
		// Get the date of the latest report
		$latest_timestamp = Incident_Model::get_latest_report_timestamp();

		// Round the number of days up to the nearest full day
		$days_since = ceil((time() - $oldest_timestamp) / 86400);
		$avg_reports_per_day = ($days_since < 1)? $total_reports : round(($total_reports / $days_since),2);
		
		// Percent Verified
		$total_verified = Incident_Model::get_total_reports_by_verified(true);
		$percent_verified = ($total_reports == 0) ? '-' : round((($total_verified / $total_reports) * 100),2).'%';
		
		$this->template->content->oldest_timestamp = $oldest_timestamp;
		$this->template->content->latest_timestamp = $latest_timestamp;
		$this->template->content->report_stats->total_reports = $total_reports;
		$this->template->content->report_stats->avg_reports_per_day = $avg_reports_per_day;
		$this->template->content->report_stats->percent_verified = $percent_verified;
		$this->template->content->services = Service_Model::get_array();

		$this->template->header->header_block = $this->themes->header_block();
	}
	
	/**
	 * Helper method to load the report listing view
	 */
	private function _get_report_listing_view($locale = '')
	{
		// Check if the local is empty
		if (empty($locale))
		{
			$locale = Kohana::config('locale.language.0');
		}
		
		// Load the report listing view
		$report_listing = new View('reports_listing');
		
		// Fetch all incidents
		$all_incidents = reports::fetch_incidents();

		// Pagination
		$pagination = new Pagination(array(
				'style' => 'front-end-reports',
				'query_string' => 'page',
				'items_per_page' => (int) Kohana::config('settings.items_per_page'),
				'total_items' => $all_incidents->count()
				));

		// Reports
		$incidents = Incident_Model::get_incidents(reports::$params, $pagination);
		
		// Swap out category titles with their proper localizations using an array (cleaner way to do this?)

		$localized_categories = array();
		foreach ($incidents as $incident)
		{
			$incident = ORM::factory('incident', $incident->incident_id);
			foreach ($incident->category AS $category)
			{
				$ct = (string)$category->category_title;
				if( ! isset($localized_categories[$ct]))
				{
					$translated_title = Category_Lang_Model::category_title($category->id, $locale);
					$localized_categories[$ct] = $category->category_title;
					if($translated_title)
					{
						$localized_categories[$ct] = $translated_title;
					}
				}
			}
		}
		// Set the view content
		$report_listing->incidents = $incidents;
		$report_listing->localized_categories = $localized_categories;
		
		//Set default as not showing pagination. Will change below if necessary.
		$report_listing->pagination = "";

		// Pagination and Total Num of Report Stats
		$plural = ($pagination->total_items == 1)? "" : "s";

		if ($pagination->total_items > 0)
		{
			$current_page = ($pagination->sql_offset/ $pagination->items_per_page) + 1;
			$total_pages = ceil($pagination->total_items/ $pagination->items_per_page);

			if ($total_pages >= 1)
			{
				$report_listing->pagination = $pagination;
				
				// Show the total of report
				// @todo This is only specific to the frontend reports theme
				$report_listing->stats_breadcrumb = $pagination->current_first_item.'-'
											. $pagination->current_last_item.' of '.$pagination->total_items.' '
											. Kohana::lang('ui_main.reports');
			}
			else
			{ // If we don't want to show pagination
				$report_listing->stats_breadcrumb = $pagination->total_items.' '.Kohana::lang('ui_admin.reports');
			}
		}
		else
		{
			$report_listing->stats_breadcrumb = '('.$pagination->total_items.' report'.$plural.')';
		}
		
		// Return
		return $report_listing;
	}
	
	public function fetch_reports()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		if ($_GET)
		{
			// Kohana::log('debug', Kohana::debug($_GET));
			
			$report_listing_view = $this->_get_report_listing_view();
			
			print $report_listing_view;
		}
		else
		{
			print "";
		}
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

		$form_saved = ($saved == 'saved');

		// Initialize Default Values
		$form['incident_date'] = date("m/d/Y",time());
		$form['incident_hour'] = date('g');
		$form['incident_minute'] = date('i');
		$form['incident_ampm'] = date('a');
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

			 //	 Add some filters
			$post->pre_filter('trim', TRUE);

			reports::validate($post);
			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				// STEP 1: SAVE LOCATION
				$location = new Location_Model();
				reports::save_location($location, $post);
				
				// STEP 2: SAVE INCIDENT
				$incident = new Incident_Model();
				reports::check_incident($incident, $location, $post);
				$incident->save();

				// STEP 3: SAVE CATEGORIES
				reports::save_category($post, $incident);

				// STEP 4: SAVE MEDIA
				reports::save_media($location, $incident, $post);

				// STEP 5: SAVE CUSTOM FORM FIELDS
				reports::save_custom_fields($incident, $post);

				// STEP 6: SAVE PERSONAL INFORMATION
				reports::save_personal_info($incident, $location, $post);

				// Action::report_add - Added a New Report
				Event::run('ushahidi_action.report_add', $incident);

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

		$categories = $this->get_categories($form['incident_category']);
		$this->template->content->categories = $categories;
		
		// Pass timezone
		$this->template->content->site_timezone = Kohana::config('settings.site_timezone');

		// Retrieve Custom Form Fields Structure
		$disp_custom_fields = $this->_get_custom_form_fields($id,$form['form_id'],false);
		$this->template->content->disp_custom_fields = $disp_custom_fields;

		// Javascript Header
		$this->themes->map_enabled = TRUE;
		$this->themes->datepicker_enabled = TRUE;
		$this->themes->treeview_enabled = TRUE;
		$this->themes->js = new View('reports_submit_js');
		$this->themes->js->default_map = Kohana::config('settings.default_map');
		$this->themes->js->default_zoom = Kohana::config('settings.default_zoom');
		if (!$form['latitude'] OR !$form['latitude'])
		{
			$this->themes->js->latitude = Kohana::config('settings.default_lat');
			$this->themes->js->longitude = Kohana::config('settings.default_lon');
		}
		else
		{
			$this->themes->js->latitude = $form['latitude'];
			$this->themes->js->longitude = $form['longitude'];
		}

		// Rebuild Header Block
		$this->template->header->header_block = $this->themes->header_block();
	}

	 /**
	 * Displays a report.
	 * @param boolean $id If id is supplied, a report with that id will be
	 * retrieved.
	 */
	public function view($id = FALSE)
	{
		$this->template->header->this_page = 'reports';
		$this->template->content = new View('reports_view');

		// Load Akismet API Key (Spam Blocker)

		$api_akismet = Kohana::config('settings.api_akismet');

		if ( ! Incident_Model::is_valid_incident($id, TRUE))
		{
			url::redirect('main');
		}
		else
		{
			$incident = ORM::factory('incident')
				->where('id',$id)
				->where('incident_active',1)
				->find();
			if ( $incident->id == 0 )	// Not Found
			{
				url::redirect('reports/view/');
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

			if ($_POST AND Kohana::config('settings.allow_comments') )
			{
				// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things

				$post = Validation::factory($_POST);

				// Add some filters

				$post->pre_filter('trim', TRUE);

				// Add some rules, the input field, followed by a list of checks, carried out in order
				
				if ( ! $this->user)
				{
					$post->add_rules('comment_author', 'required', 'length[3,100]');
					$post->add_rules('comment_email', 'required','email', 'length[4,100]');
				}
				$post->add_rules('comment_description', 'required');
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
							'website' => "",
							'body' => $post->comment_description,
							'user_ip' => $_SERVER['REMOTE_ADDR']
						);
						
						if ($this->user)
						{
							$comment['author'] = $this->user->name;
							$comment['email'] = $this->user->email;
						}
						else
						{
							$comment['author'] = $post->comment_author;
							$comment['email'] = $post->comment_email;
						}

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
							}
							elseif ($akismet->is_error('AKISMET_RESPONSE_FAILED'))
							{
								// throw new Kohana_Exception('akismet.server_failed');
							}
							elseif ($akismet->is_error('AKISMET_SERVER_NOT_FOUND'))
							{
								// throw new Kohana_Exception('akismet.server_not_found');
							}

							// If the server is down, we have to post
							// the comment :(
							// $this->_post_comment($comment);

							$comment_spam = 0;
						}
						else
						{
							$comment_spam = ($akismet->is_spam()) ? 1 : 0;
						}
					}
					else
					{
						// No API Key!!
						$comment_spam = 0;
					}

					$comment = new Comment_Model();
					$comment->incident_id = $id;
					if ($this->user)
					{
						$comment->user_id = $this->user->id;
						$comment->comment_author = $this->user->name;
						$comment->comment_email = $this->user->email;
					}
					else
					{
						$comment->comment_author = strip_tags($post->comment_author);
						$comment->comment_email = strip_tags($post->comment_email);
					}
					$comment->comment_description = strip_tags($post->comment_description);
					$comment->comment_ip = $_SERVER['REMOTE_ADDR'];
					$comment->comment_date = date("Y-m-d H:i:s",time());

					// Activate comment for now
					if ($comment_spam == 1)
					{
						$comment->comment_spam = 1;
						$comment->comment_active = 0;
					}
					else
					{
						$comment->comment_spam = 0;
						$comment->comment_active = (Kohana::config('settings.allow_comments') == 1)? 1 : 0;
					}
					$comment->save();

					// Event::comment_add - Added a New Comment
					Event::run('ushahidi_action.comment_add', $comment);

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

				}
				else
				{
					// No! We have validation errors, we need to show the form again, with the errors

					// Repopulate the form fields

					$form = arr::overwrite($form, $post->as_array());

					// Populate the error fields, if any

					$errors = arr::overwrite($errors, $post->errors('comments'));
					$form_error = TRUE;
				}
			}

			// Filters
			$incident_title = $incident->incident_title;
			$incident_description = nl2br($incident->incident_description);
			Event::run('ushahidi_filter.report_title', $incident_title);
			Event::run('ushahidi_filter.report_description', $incident_description);
			
			// Add Features
			$this->template->content->features_count = $incident->geometry->count();
			$this->template->content->features = $incident->geometry;
			
			$this->template->content->incident_id = $incident->id;
			$this->template->content->incident_title = $incident_title;
			$this->template->content->incident_description = $incident_description;
			$this->template->content->incident_location = $incident->location->location_name;
			$this->template->content->incident_latitude = $incident->location->latitude;
			$this->template->content->incident_longitude = $incident->location->longitude;
			$this->template->content->incident_date = date('M j Y', strtotime($incident->incident_date));
			$this->template->content->incident_time = date('H:i', strtotime($incident->incident_date));
			$this->template->content->incident_category = $incident->incident_category;

			// Incident rating
			$this->template->content->incident_rating = ($incident->incident_rating == '')
				? 0
				: $incident->incident_rating;

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
					$incident_comments = Incident_Model::get_comments($id);
				}
				$this->template->content->comments->incident_comments = $incident_comments;
			}
		}

		// Add Neighbors
		$this->template->content->incident_neighbors = Incident_Model::get_neighbouring_incidents($id, TRUE, 0, 5);
		
		// News Source links
		$this->template->content->incident_news = $incident_news;


		// Video links
		$this->template->content->incident_videos = $incident_video;

		// Images
		$this->template->content->incident_photos = $incident_photo;

		// Create object of the video embed class
		$video_embed = new VideoEmbed();
		$this->template->content->videos_embed = $video_embed;

		// Javascript Header

		$this->themes->map_enabled = TRUE;
		$this->themes->photoslider_enabled = TRUE;
		$this->themes->videoslider_enabled = TRUE;
		$this->themes->js = new View('reports_view_js');
		$this->themes->js->incident_id = $incident->id;
		$this->themes->js->default_map = Kohana::config('settings.default_map');
		$this->themes->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->themes->js->latitude = $incident->location->latitude;
		$this->themes->js->longitude = $incident->location->longitude;
		$this->themes->js->incident_zoom = $incident->incident_zoom;
		$this->themes->js->incident_photos = $incident_photo;

		// Initialize custom field array
		$form_field_names = $this->_get_custom_form_fields($id, $incident->form_id, FALSE);

		// Retrieve Custom Form Fields Structure
		$disp_custom_fields = $this->_get_custom_form_fields($id, $incident->form_id, TRUE);
		$this->template->content->disp_custom_fields = $disp_custom_fields;

		// Are we allowed to submit comments?
		$this->template->content->comments_form = "";
		if (Kohana::config('settings.allow_comments'))
		{
			$this->template->content->comments_form = new View('reports_comments_form');
			$this->template->content->comments_form->user = $this->user;
			$this->template->content->comments_form->form = $form;
			$this->template->content->comments_form->form_field_names = $form_field_names;
			$this->template->content->comments_form->captcha = $captcha;
			$this->template->content->comments_form->errors = $errors;
			$this->template->content->comments_form->form_error = $form_error;
		}

		// If the Admin is Logged in - Allow for an edit link
		$this->template->content->logged_in = $this->logged_in;

		// Rebuild Header Block
		$this->template->header->header_block = $this->themes->header_block();
	}

	/**
	 * Report Thanks Page
	 */
	public function thanks()
	{
		$this->template->header->this_page = 'reports_submit';
		$this->template->content = new View('reports_submit_thanks');

		// Rebuild Header Block
		$this->template->header->header_block = $this->themes->header_block();
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
					// Has this User or IP Address rated this post before?
					if ($this->user)
					{
						$filter = "user_id = ".$this->user->id;
					}
					else
					{
						$filter = "rating_ip = '".$_SERVER['REMOTE_ADDR']."' ";
					}
					
					if ($type == 'original')
					{
						$previous = ORM::factory('rating')
							->where('incident_id',$id)
							->where($filter)
							->find();
					}
					elseif ($type == 'comment')
					{
						$previous = ORM::factory('rating')
							->where('comment_id',$id)
							->where($filter)
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
					
					// Is there a user?
					if ($this->user)
					{
						$rating->user_id = $this->user->id;
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

	public function geocode()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		if (isset($_POST['address']) AND ! empty($_POST['address']))
		{
			$geocode = map::geocode($_POST['address']);
			if ($geocode)
			{
				echo json_encode(array("status"=>"success", "message"=>array($geocode['lat'], $geocode['lon'])));
			}
			else
			{
				echo json_encode(array("status"=>"error", "message"=>"ERROR!"));
			}
		}
		else
		{
			echo json_encode(array("status"=>"error", "message"=>"ERROR!"));
		}
	}

	/**
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

	/**
	 * Retrieves Total Rating For Specific Post
	 * Also Updates The Incident & Comment Tables (Ratings Column)
	 */
	private function _get_rating($id = FALSE, $type = NULL)
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
			foreach (ORM::factory('rating')
							->where($which_count,$id)
							->find_all() as $rating)
			{
				$total_rating += $rating->rating;
			}

			// Update Counts
			if ($type == 'original')
			{
				$incident = ORM::factory('incident', $id);
				if ($incident->loaded == TRUE)
				{
					$incident->incident_rating = $total_rating;
					$incident->save();
				}
			}
			elseif ($type == 'comment')
			{
				$comment = ORM::factory('comment', $id);
				if ($comment->loaded == TRUE)
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

	/**
	 * Retrieve Custom Form Fields
	 * @param bool|int $incident_id The unique incident_id of the original report
	 * @param int $form_id The unique form_id. Uses default form (1), if none selected
	 * @param bool $field_names_only Whether or not to include just fields names, or field names + data
	 * @param bool $data_only Whether or not to include just data
	 */
	private function _get_custom_form_fields($incident_id = FALSE, $form_id = 1, $data_only = FALSE)
	{
		$fields_array = array();

		if ( ! $form_id)
			$form_id = 1;

		$custom_form = ORM::factory('form', $form_id)->orderby('field_position','asc');

		foreach ($custom_form->form_field as $custom_formfield)
		{
			if ($data_only)
			{
				// Return Data Only
				$fields_array[$custom_formfield->id] = '';

				foreach ($custom_formfield->form_response as $form_response)
				{
					if ($form_response->incident_id == $incident_id)
						$fields_array[$custom_formfield->id] = $form_response->form_response;
				}
			}
			else
			{
				// Return Field Structure
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
				if ($field_param->field_required == 1 AND $field_response == "")
					return FALSE;

				// Validate for date
				if ($field_param->field_isdate == 1 AND $field_response != "")
				{
					$myvalid = new Valid();
					return $myvalid->date_mmddyyyy($field_response);
				}
			}
		}

		return true;
	}

	/**
	 * Validates a numeric array. All items contained in the array must be numbers or numeric strings
	 *
	 * @param array $nuemric_array Array to be verified
	 */
	private function _is_numeric_array($numeric_array=array())
	{
		if (count($numeric_array) == 0)
			return FALSE;
		else
		{
			foreach ($numeric_array as $item)
			{
				if (! is_numeric($item))
					return FALSE;
			}

			return TRUE;
		}
	}
	
} // End Reports
