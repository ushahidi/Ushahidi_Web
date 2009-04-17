<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to add/ remove categories
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Manage Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Manage_Controller extends Admin_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'manage';
		
		// If this is not a super-user account, redirect to dashboard
		if (!$this->auth->logged_in('admin'))
        {
             url::redirect('admin/dashboard');
		}
	}
	
	/*
	Add Edit Categories
	*/
	function index()
	{	
		$this->template->content = new View('admin/categories');
		$this->template->content->title = 'Categories';
		
		// setup and initialize form field names
		$form = array
	    (
			'action' => '',
	        'locale'      => '',
			'category_id'      => '',
			'category_title'      => '',
	        'category_description'    => '',
	        'category_color'  => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
	        // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);
			
	         //  Add some filters
	        $post->pre_filter('trim', TRUE);
	
			if ($post->action == 'a')		// Add Action
			{
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('locale','required','alpha_dash','length[5]');
				$post->add_rules('category_title','required', 'length[3,80]');
				$post->add_rules('category_description','required');
				$post->add_rules('category_color','required', 'length[6,6]');
			}
			
			// Test to see if things passed the rule checks
	        if ($post->validate())
	        {
				$category_id = $post->category_id;
				$category = new Category_Model($category_id);
				
				if( $post->action == 'd' )				// Delete Action
				{
					$category->delete( $category_id );
					$form_saved = TRUE;
					$form_action = "DELETED";
			
				}
				else if( $post->action == 'v' )			// Show/Hide Action
				{
	            	if ($category->loaded==true)
					{
						if ($category->category_visible == 1) {
							$category->category_visible = 0;
						}
						else {
							$category->category_visible = 1;
						}
						$category->save();
						$form_saved = TRUE;
						$form_action = "MODIFIED";
					}
				} 
				else if( $post->action == 'a' ) 		// Save Action
				{		
					// SAVE Category
					$category->locale = $post->locale;
					$category->category_title = $post->category_title;
					$category->category_description = $post->category_description;
					$category->category_color = $post->category_color;
					$category->save();
					$form_saved = TRUE;
					$form_action = "ADDED/EDITED";
				}
	        }
            // No! We have validation errors, we need to show the form again, with the errors
	        else
			{
	            // repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

               // populate the error fields, if any
                $errors = arr::overwrite($errors, $post->errors('category'));
                $form_error = TRUE;
            }
        }

        // Pagination
        $pagination = new Pagination(array(
                            'query_string' => 'page',
                            'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
                            'total_items'    => ORM::factory('category')->count_all()
                        ));

        $categories = ORM::factory('category')
                        ->orderby('category_title', 'asc')
                        ->find_all((int) Kohana::config('settings.items_per_page_admin'), 
                            $pagination->sql_offset);

		$this->template->content->errors = $errors;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
        $this->template->content->pagination = $pagination;
        $this->template->content->total_items = $pagination->total_items;
        $this->template->content->categories = $categories;

		// Locale (Language) Array
		$this->template->content->locale_array = Kohana::config('locale.all_languages');

        // Javascript Header
        $this->template->colorpicker_enabled = TRUE;
        $this->template->js = new View('admin/categories_js');
    }

	/*
	Add Edit Organizations
	*/
	function organizations()
	{
		$this->template->content = new View('admin/organizations');
		
		// setup and initialize form field names
		$form = array
	    (
			'action' => '',
	        'organization_id'      => '',
			'organization_name'      => '',
	        'organization_description'    => '',
	        'organization_website'  => '',
			'organization_email'  => '',
			'organization_phone1'  => '',
			'organization_phone2'  => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
	        // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);
			
	         //  Add some filters
	        $post->pre_filter('trim', TRUE);

			if ($post->action == 'a')		// Add Action
			{ // Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('organization_name','required', 'length[3,70]');
				$post->add_rules('organization_description','required');
				$post->add_rules('organization_website','required','url');
				$post->add_rules('organization_email', 'email', 'length[4,100]');
				$post->add_rules('organization_phone1', 'length[3,50]');
				$post->add_rules('organization_phone2', 'length[3,50]');
			}
			
			// Test to see if things passed the rule checks
	        if ($post->validate())
	        {
				$organization_id = $post->organization_id;
				
				$organization = new Organization_Model($organization_id);
				
				if( $post->action == 'd' )
				{ // Delete Action
					$organization->delete( $organization_id );
					$form_saved = TRUE;
					$form_action = "DELETED";
				
				}
				else if( $post->action == 'v' )			
				{ // Show/Hide Action
	            	if ($organization->loaded==true)
					{
						if ($organization->organization_active == 1) {
							$organization->organization_active = 0;
						}
						else {
							$organization->organization_active = 1;
						}
						$organization->save();
						$form_saved = TRUE;
						$form_action = "MODIFIED";
					}
				}
				else if( $post->action == 'a' ) 		
				{ // Save Action
					$organization->organization_name = $post->organization_name;
					$organization->organization_description = $post->organization_description;
					$organization->organization_website = $post->organization_website;
					$organization->organization_email = $post->organization_email;
					$organization->organization_phone1 = $post->organization_phone1;
					$organization->organization_phone2 = $post->organization_phone2;
					$organization->save();
					$form_saved = TRUE;
					$form_action = "ADDED/EDITED";
				}       
	        }
	        else
			{ // No! We have validation errors, we need to show the form again, with the errors
				
	             // repopulate the form fields
		         $form = arr::overwrite( $form, $post->as_array() ); 

               // populate the error fields, if any
                $errors = arr::overwrite($errors, 
					$post->errors('organization'));
                $form_error = TRUE;
            }
        }
		
        // Pagination
        $pagination = new Pagination(array(
                            'query_string' => 'page',
                            'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
                            'total_items'    =>
 							ORM::factory('organization')->count_all()
                        ));

        $organization = ORM::factory('organization')
                        ->orderby('organization_name', 'asc')
                        ->find_all((int) Kohana::config('settings.items_per_page_admin'), 
                            $pagination->sql_offset);

        $this->template->content->form = $form;
		$this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
        $this->template->content->pagination = $pagination;
        $this->template->content->total_items = $pagination->total_items;
        $this->template->content->organizations = $organization;
		$this->template->content->errors = $errors;

        // Javascript Header
        $this->template->colorpicker_enabled = TRUE;
        $this->template->js = new View('admin/organization_js');
	}
	
	/*
	Add Edit News Feeds
	*/
	function feeds()
	{
		$this->template->content = new View('admin/feeds');
		
		// setup and initialize form field names
		$form = array
	    (
			'action' => '',
	        'feed_id'      => '',
			'feed_name'      => '',
	        'feed_url'    => '',
	        'feed_active'  => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		if( $_POST ) 
		{
			$post = Validation::factory( $_POST );
			
			 //  Add some filters
	        $post->pre_filter('trim', TRUE);
	
			if ($post->action == 'a')		// Add Action
			{
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('feed_name','required', 'length[3,70]');
				$post->add_rules('feed_url','required','url');
			}
			
			if( $post->validate() )
			{
				$feed_id = $post->feed_id;
				
				$feed = new Feed_Model($feed_id);
				if ( $post->action == 'd' ) { 					// Delete Action
					if ($feed->loaded==true)
					{
						ORM::factory('feed_item')->where('feed_id',$feed_id)->delete_all();
					}
					$feed->delete( $feed_id );
					$form_saved = TRUE;
					$form_action = "DELETED";
				} else if($post->action == 'v') {				// Active/Inactive Action
					if ($feed->loaded==true)
					{
						if ($feed->feed_active == 1) {
							$feed->feed_active = 0;
						}
						else {
							$feed->feed_active = 1;
						}
						$feed->save();
						$form_saved = TRUE;
						$form_action = "MODIFIED";
					}
				}else if( $post->action == 'r' ) { 
					$this->_parse_feed();
				} else {										// Save Action
					// SAVE Feed
					$feed->feed_name = $post->feed_name;
					$feed->feed_url = $post->feed_url;
					$feed->save();
					$form_saved = TRUE;
					$form_action = "ADDED/EDITED";
				}
				
			} else {
				// repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

               // populate the error fields, if any
                $errors = arr::overwrite($errors, 
					$post->errors('feeds'));
                $form_error = TRUE;
			}
		}
		
        // Pagination
        $pagination = new Pagination(array(
                            'query_string' => 'page',
                            'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
                            'total_items'    => ORM::factory('feed')->count_all()
                        ));

        $feeds = ORM::factory('feed')
                        ->orderby('feed_name', 'asc')
                        ->find_all((int) Kohana::config('settings.items_per_page_admin'), 
                            $pagination->sql_offset);

        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
        $this->template->content->pagination = $pagination;
        $this->template->content->total_items = $pagination->total_items;
        $this->template->content->feeds = $feeds;
		$this->template->content->errors = $errors;

        // Javascript Header
        $this->template->colorpicker_enabled = TRUE;
        $this->template->js = new View('admin/feeds_js');
	}
	
	/*
	Add Edit Reporter Levels
	*/
	function levels()
	{	
		$this->template->content = new View('admin/levels');
		$this->template->content->title = 'Reporter Levels';
		
		// setup and initialize form field names
		$form = array
		(
			'level_id'      => '',
			'level_title'      => '',
			'level_description'    => '',
			'level_weight'  => ''
		);
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

			//  Add some filters
			$post->pre_filter('trim', TRUE);

			if ($post->action == 'a')		// Add Action
			{
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('level_title','required', 'length[3,80]');
				$post->add_rules('level_description','required');
				$post->add_rules('level_weight','required');
			}
			
			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				$level_id = $post->level_id;
				$level = new Level_Model($level_id);
				
				if( $post->action == 'd' )				// Delete Action
				{
					$level->delete( $level_id );
					$form_saved = TRUE;
					$form_action = "DELETED";
			
				}
				else if( $post->action == 'a' ) 		// Save Action
				{		
					// SAVE Category
					$level->level_title = $post->level_title;
					$level->level_description = $post->level_description;
					$level->level_weight = $post->level_weight;
					$level->save();
					$form_saved = TRUE;
					$form_action = "ADDED/EDITED";
				}
			}
			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('level'));
				$form_error = TRUE;
			}
		}

		// Pagination
		$pagination = new Pagination(array(
                            'query_string' => 'page',
                            'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
                            'total_items'    => ORM::factory('level')->count_all()
                        ));

		$levels = ORM::factory('level')
                        ->orderby('level_weight', 'asc')
                        ->find_all((int) Kohana::config('settings.items_per_page_admin'), 
                            $pagination->sql_offset);

		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->template->content->levels = $levels;
    }
	
	/*
	Add Edit Reporters
	*/
	function reporters()
	{	
		$this->template->content = new View('admin/reporters');
		$this->template->content->title = 'Reporters';

		// setup and initialize form field names
		$form = array
		(
			'reporter_id' => '',    
			'service_id' => '',
			'service_userid' => '',
			'service_account' => '',
			'reporter_level' => '',
			'reporter_first' => '',
			'reporter_last' => '',
			'reporter_email' => '',
			'reporter_phone' => '',
			'reporter_ip' => '',
			'reporter_date' => ''
		);
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";

			// check, has the form been submitted, if so, setup validation
			if ($_POST)
			{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

			    //  Add some filters
			$post->pre_filter('trim', TRUE);

			if ($post->action == 'a')		// Add Action
			{
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('service_id','required');				
				// we also require either service_userid or service_account, not necessarily both
			}

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				$reporter_id = $post->reporter_id;
				$reporter = new Reporter_Model($reporter_id);
				
				if( $post->action == 'd' )				// Delete Action
				{
					$level->delete( $level_id );
					$form_saved = TRUE;
					$form_action = "DELETED";

				}
				else if( $post->action == 'a' ) 		// Save Action
				{		
					// SAVE Reporter    
					$reporter->service_id = $post->service_id;
					/*$reporter->service_userid = $post->service_userid;
					$reporter->service_account = $post->service_account;*/
					$reporter->reporter_level = $post->reporter_level;
					/*$reporter->reporter_first = $post->reporter_first;
					$reporter->reporter_last = $post->reporter_last;
					$reporter->reporter_email = $post->reporter_email;
					$reporter->reporter_phone = $post->reporter_phone;
					$reporter->reporter_ip = $post->reporter_ip;
					$reporter->reporter_date = $post->reporter_date;*/
					
					$reporter->save();
					$form_saved = TRUE;
					$form_action = "ADDED/EDITED";
				}
			}
			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('level'));
				$form_error = TRUE;
			}
		}

		// Pagination
		$pagination = new Pagination(array(
		                    'query_string' => 'page',
		                    'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
		                    'total_items'    => ORM::factory('reporter')->count_all()
		                ));

		$reporters = ORM::factory('reporter')
		                ->orderby('reporter_first', 'asc')
		                ->find_all((int) Kohana::config('settings.items_per_page_admin'), 
		                    $pagination->sql_offset);

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->template->content->reporters = $reporters;

		// Level and Service Arrays
		$this->template->content->level_array = Level_Model::get_array();
		$this->template->content->service_array = Service_Model::get_array();

	}	
	/**
	 * setup simplepie
	 */
	private function _setup_simplepie( $feed_url ) {
			$data = new SimplePie();
			$data->set_feed_url( $feed_url );
			$data->enable_cache(false);
			$data->enable_order_by_date(true);
			$data->init();
			$data->handle_content_type();

			return $data;
	}
	
	/**
	 * parse feed and send feed items to database
	 */
	private function _parse_feed()
	{
		// Max number of feeds to keep
		$max_feeds = 100;
		
		// Today's Date
		$today = strtotime('now');
		
		// Get All Feeds From DB
		$feeds = ORM::factory('feed')->find_all();
		foreach ($feeds as $feed)
		{
			$last_update = $feed->feed_update;
			
			// Has it been more than 24 hours since the last update?
			if ( ((int)$today - (int)$last_update) > 86400	)	// 86400 = 24 hours
			{
				// Parse Feed URL using Feed Helper
				$feed_data = $this->_setup_simplepie( $feed->feed_url );

				foreach($feed_data->get_items(0,50) as $feed_data_item)
				{
					$title = $feed_data_item->get_title();
					$link = $feed_data_item->get_link();
					$description = $feed_data_item->get_description();
					$date = $feed_data_item->get_date();
					// Make Sure Title is Set (Atleast)
					if (isset($title) && !empty($title ))
					{
						// We need to check for duplicates!!!
						// Maybe combination of Title + Date? (Kinda Heavy on the Server :-( )
						$dupe_count = ORM::factory('feed_item')->where('item_title',$title)->where('item_date',date("Y-m-d H:i:s",strtotime($date)))->count_all();

						if ($dupe_count == 0) {
							$newitem = new Feed_Item_Model();
							$newitem->feed_id = $feed->id;
							$newitem->item_title = $title;
							if (isset($description) && !empty($description))
							{
								$newitem->item_description = $description;
							}
							if (isset($link) && !empty($link))
							{
								$newitem->item_link = $link;
							}
							if (isset($date) && !empty($date))
							{
								$newitem->item_date = date("Y-m-d H:i:s",strtotime($date));
							}
							// Set todays date
							else
							{
								$newitem->item_date = date("Y-m-d H:i:s",time());
							}
							$newitem->save();
						}
					}
				}
				
				// Get Feed Item Count
				$feed_count = ORM::factory('feed_item')->where('feed_id', $feed->id)->count_all();
				if ($feed_count > $max_feeds) {
					// Excess Feeds
					$feed_excess = $feed_count - $max_feeds;

					// Delete Excess Feeds
					foreach (ORM::factory('feed_item')
						->where('feed_id', $feed->id)
						->orderby('id', 'ASC')
						->limit($feed_excess)
						->find_all() as $del_feed)
					{
						$del_feed->delete($del_feed->id);
					}
				}

				// Set feed update date
				$feed->feed_update = strtotime('now');
				$feed->save();
			}
		}
	}
}
