<?php defined('SYSPATH') or die('No direct script access.');
/**
* REPORTS CONTROLLER
* LIST/VIEW/EDIT
*/

class Reports_Controller extends Main_Controller {

	function __construct()
	{
		parent::__construct();	
	}

	/*
	* View All Reports Method
	*/
	public function index()
	{		
		
	}
	
	/*
	* Create A New Report Method
	*/
	public function submit()
	{
		$this->template->header->this_page = 'reports';
		$this->template->content = new View('reports_submit');
		
		// setup and initialize form field names
		$form = array
	    (
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
		$form_saved = FALSE;
		
		// Retrieve Country Cities
		$default_country = Kohana::config('settings.default_country');
		$this->template->content->cities = $this->_get_cities($default_country);
		
		$this->template->content->form = $form;
		$this->template->content->categories = $this->_get_categories();
		
		// Javascript Header
		$this->template->header->map_enabled = TRUE;
        $this->template->header->datepicker_enabled = TRUE;
		$this->template->header->js = new View('reports_submit_js');
		$this->template->header->js->default_map = Kohana::config('settings.default_map');
		$this->template->header->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->template->header->js->latitude = Kohana::config('settings.default_lat');
		$this->template->header->js->longitude = Kohana::config('settings.default_lon');
	}
	
	/*
	* View Report Method
	*/
	public function view( $id = false )
	{
		$this->template->header->this_page = 'reports';
		$this->template->content = new View('reports_view');
		
		if ( !$id )
		{
			url::redirect('main');
		}
		else
		{
			$incident = ORM::factory('incident', $id);
			
			if ( $incident->id == 0 )	// Not Found
			{
				url::redirect('main');
			}
			
			$this->template->content->incident_title = $incident->incident_title;
			$this->template->content->incident_description = $incident->incident_description;
			$this->template->content->incident_location = $incident->location->location_name;
			$this->template->content->incident_latitude = $incident->location->latitude;
			$this->template->content->incident_longitude = $incident->location->longitude;
			
			$this->template->content->incident_date = date('M j Y', strtotime($incident->incident_date));
			$this->template->content->incident_time = date('H:i', strtotime($incident->incident_date));
			
			// Retrieve Categories
			$incident_category = array();
			foreach($incident->incident_category as $category) 
			{ 
				$incident_category[$category->category_id] = array($category->category->category_title, $category->category->category_color);
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
			
			if ( $incident->incident_verified == 1 )
			{
				$this->template->content->incident_verified = "<p><strong class=\"green\">YES</strong></p>";
			}
			else
			{
				$this->template->content->incident_verified = "<p><strong class=\"red\">NO</strong></p>";
			}
			
			
		}
		
		// Javascript Header
		$this->template->header->map_enabled = TRUE;
		$this->template->header->js = new View('reports_view_js');
		$this->template->header->js->incident_id = $incident->id;
		$this->template->header->js->default_map = Kohana::config('settings.default_map');
		$this->template->header->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->template->header->js->latitude = $incident->location->latitude;
		$this->template->header->js->longitude = $incident->location->longitude;
	}
	
	
	/*
	* Retrieves Previously Cached Geonames Cities
	*/
	private function _get_cities()
	{
		$cities = ORM::factory('city')->orderby('city', 'asc')->find_all();
		$city_select = array('' => 'Select A City');
		foreach ($cities as $city) {
			$city_select[$city->city_lon .  "," . $city->city_lat] = $city->city;
		}
		return $city_select;
	}
	
	private function _get_categories()
	{
		// Count categories to determine column length
		$categories_total = ORM::factory('category')->where('category_visible', '1')->count_all();
        $this->template->content->categories_total = $categories_total;

		$categories = array();
		foreach (ORM::factory('category')->where('category_visible', '1')->find_all() as $category)
		{
			// Create a list of all categories
			$categories[$category->id] = array($category->category_title, $category->category_color);
		}

        //format categories for 2 column display
        $this_col = 1; // First column
        $max_col = round($categories_total/2); // Maximum number of columns
        $html= "";
        foreach ($categories as $category => $category_extra)
        {
            $category_title = $category_extra[0];
            $category_color = $category_extra[1];
            if ($this_col == 1) 
                $html.="<ul>";
        
            if (!empty($form['incident_category']) 
                && in_array($category, $form['incident_category'])) {
                $category_checked = TRUE;
            }
            else
            {
                $category_checked = FALSE;
            }
                                                                            
            $html.="\n<li><label>";
            $html.=form::checkbox('incident_category[]', $category, $category_checked, ' class="check-box"');
            $html.="$category_title";
            $html.="</label></li>";
       
            if ($this_col == $max_col) 
                $html.="\n</ul>\n";
      
            if ($this_col < $max_col)
            {
                $this_col++;
            } 
            else 
            {
                $this_col = 1;
            }
        }
        return $html;
	}
	

} // End Main