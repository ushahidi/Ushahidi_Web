<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This controller is used to add/ remove categories
 */
class Manage_Controller extends Admin_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'manage';		
	}
	
	
	function index()
	{	
		$this->template->content = new View('admin/categories');
		
		
		// setup and initialize form field names
		$form = array
	    (
			'action' => '',
	        'category_id'      => '',
			'category_title'      => '',
	        'category_description'    => '',
	        'category_color'  => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		
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
				$category_id = $post->category_id;
				$category = new Category_Model($category_id);
				//delete action
				if( $post->action == 'd' ){ 
	            	$category_id = $post->category_id;
					$category->delete( $category_id );
				
				} else {
					// Yes! everything is valid
					$category_id = $post->category_id;
					// SAVE Category
					
					$category->category_title = $post->category_title;
					$category->category_description =
					 $post->category_description;
					$category->category_color = $post->category_color;
					$category->save();
					$form_saved = TRUE;
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

        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->pagination = $pagination;
        $this->template->content->total_items = $pagination->total_items;
        $this->template->content->categories = $categories;

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
	        'organization_website'  => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
	        // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);
			
	         //  Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('organization_name','required', 'length[3,70]');
			$post->add_rules('organization_description','required');
			$post->add_rules('organization_website','required','url');
			
			// Test to see if things passed the rule checks
	        if ($post->validate())
	        {
				$organization_id = $post->organization_id;
				
				$organization = new Organization_Model($organization_id);
				
				//delete action
				if( $post->action == 'd' ) { 
	            	$organization_id = $post->organization_id;
					$organization->delete( $organization_id );
				
				} else {
					// Yes! everything is valid
					$organization_id = $post->organization_id;
					// SAVE Organization
					
					$organization->organization_name = $post->organization_name;
					$organization->organization_description =
					 $post->organization_description;
					
					$organization->organization_website = 
						$post->organization_website;
					$organization->save();
					$form_saved = TRUE;
				}       
	        }
            // No! We have validation errors, we need to show the form again, with the errors
	        else
			{
	            // repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

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

        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
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
		
		if( $_POST ) 
		{
			$post = Validation::factory( $_POST );
			
			 //  Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('feed_name','required', 'length[3,70]');
			$post->add_rules('feed_url','required','url');
			
			if( $post->validate() )
			{
				$feed_id = $post->feed_id;
				
				$feed = new Feed_Model($feed_id);
				//delete action
				if( $post->action == 'd' ) { 
					$feed->delete( $feed_id );
				
				} else if($post->action == 'v') {
					$feed_active = $post->feed_active == 1 ? 0 : 1;													  
					
					$feeds = ORM::factory('feed',$post->feed_id);
					$feeds->feed_active = $feed_active;
					$feeds->save();
				} else {
					// Yes! everything is valid
					// SAVE Organization
					
					$feed->feed_name = $post->feed_name;
					$feed->feed_url =
					 $post->feed_url;
					
					$feed->save();
					$form_saved = TRUE;
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
        $this->template->content->pagination = $pagination;
        $this->template->content->total_items = $pagination->total_items;
        $this->template->content->feeds = $feeds;
		$this->template->content->errors = $errors;

        // Javascript Header
        $this->template->colorpicker_enabled = TRUE;
        $this->template->js = new View('admin/feeds_js');
	}
	
	
}
