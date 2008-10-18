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

	public function index()
	{		
		
	}
	
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
		$this->template->header->js->incident_photos = $incident_photo;
	}

} // End Main
