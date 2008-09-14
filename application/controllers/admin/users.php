<?php defined('SYSPATH') or die('No direct script access.');

/**
* Users Controller
*/
class Users_Controller extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'users';		
	}
	
	
	function index()
	{	
		$this->template->content = new View('admin/users');
		$this->template->content->title = 'Manage Users';
		
		
		// setup and initialize form field names
		$form = array
	    (
	        'user_id'      => '',
			'password'      => '',
	        'password2'    => '',
	        'name'  => '',
			'role'  => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = TRUE;
		
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
	        
	    }
		
		
		
		// Pagination
		$pagination = new Pagination(array(
			'query_string'    => 'page',
			'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
			'total_items'    => ORM::factory('user')->count_all()
		));

		$users = ORM::factory('user')->orderby('name', 'asc')->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);
		
		// Get User Roles
		foreach (ORM::factory('role')->orderby('name', 'asc')->find_all() as $role)
		{
			$roles[$role->name] = $role->name;
		}
		
		$this->template->content->form_error = FALSE;
		$this->template->content->form_saved = FALSE;
		$this->template->content->form_action = FALSE;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->template->content->users = $users;
		$this->template->content->roles = $roles;
		
		// Javascript Header
		$this->template->colorpicker_enabled = TRUE;
		$this->template->js = new View('admin/users_js');
	}	
}