<?php defined('SYSPATH') or die('No direct script access.');

/**
* Manage Controller
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
		$this->template->content->title = 'Manage Categories';
		
		
		// setup and initialize form field names
		$form = array
	    (
	        'category_id'      => '',
			'category_title'      => '',
	        'category_description'    => '',
	        'category_color'  => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = TRUE;
		
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
	            // Yes! everything is valid
				$category_id = $post->category_id;
				// SAVE Category
				$category = new Category_Model($category_id);
				$category->category_title = $post->category_title;
				$category->category_description = $post->category_description;
				$category->category_color = $post->category_color;
				$category->save();
				$form_saved = TRUE;       
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
			'query_string'    => 'page',
			'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
			'total_items'    => ORM::factory('category')->count_all()
		));

		$categories = ORM::factory('category')->orderby('category_title', 'asc')->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);
		
		
		$this->template->content->form_error = FALSE;
		$this->template->content->form_saved = FALSE;
		$this->template->content->form_action = FALSE;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->template->content->categories = $categories;
		
		// Javascript Header
		$this->template->colorpicker_enabled = TRUE;
		$this->template->js = new View('admin/categories_js');
	}	
}