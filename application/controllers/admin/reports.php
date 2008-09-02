<?php

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
	
	function index($page = 1)
	{
		$this->template->content = new View('admin/reports');
		$this->template->content->title = 'Reports';
		
        $this->pagination = new Pagination(array(
                'uri_segment'    => 'page',
                'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
                'total_items'    => ORM::factory('incident')->count_all(),
                'style'          => 'digg'
        ));

        $this->template->content->posts = ORM::factory('incident')->orderby('incident_dateadd', 'desc')->find_all((int) Kohana::config('settings.items_per_page_admin'), $this->pagination->sql_offset);
        $this->template->content->pagination = $this->pagination;	
		
		// Total Reports
		$this->template->content->reports_total = $this->pagination->total_items;			
	}
	
	
	function create()
	{
		$this->template->content = new View('admin/reports_create');
		$this->template->content->title = 'Reports';
		
		$this->template->content->mapjs = new View('jquery_maps');
		
		// Total Reports
		$this->template->content->reports_total = ORM::factory('incident')->count_all();
		
		// setup and initialize form field names
		$form = array
	    (
	        'incident_title'      => '',
	        'incident_description'    => '',
	        'incident_date'  => '',
	        'incident_hour'      => '12',
			'incident_minute'      => '00',
			'incident_ampm' => 'pm'
	    );
		$this->template->content->form = $form;
			
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
		
	}
}