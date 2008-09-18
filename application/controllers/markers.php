<?php defined('SYSPATH') or die('No direct script access.');

/**
* Markers Controller
* Generates KML File
*/
class Markers_Controller extends Template_Controller
{
	public $auto_render = TRUE;
	
	// Main template
	public $template = 'markers';
	
	function index()
	{
		$placemarks = "";
		// Retrieve individual markers
		foreach (ORM::factory('incident')->where('incident_active', '1')->orderby('incident_dateadd', 'desc')->find_all() as $marker)
		{
			$placemarks .= "<Placemark>";
			$placemarks .= "<name>" . htmlentities($marker->incident_title) . "</name>";
			$placemarks .= "<description>" . htmlentities($marker->incident_description) . "</description>";
			$placemarks .= "<Point>";
			$placemarks .= "<coordinates>" . htmlentities($marker->location->longitude . "," . $marker->location->latitude) . "</coordinates>";
			$placemarks .= "</Point>";
			foreach($marker->incident_category as $category) 
			{
				$placemarks .= "<IconStyle>";
				$placemarks .= "<Icon>";
				$placemarks .= "<href>" . htmlentities(url::base() . "swatch/?c=" . $category->category->category_color . "&w=16&h=16&.png") . "</href>";
				$placemarks .= "</Icon>";
				break 1;
			}
			$placemarks .= "</IconStyle>";
			$placemarks .= "</Placemark>";
		}
		
		$this->template->placemarks = $placemarks;
	}
}