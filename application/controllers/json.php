<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Json Controller
 * Generates Map GeoJSON File
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     JSON Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Json_Controller extends Template_Controller
{
    public $auto_render = TRUE;
	
    // Main template
    public $template = 'json';
	
    function index()
    {	
		// $profile = new Profiler;
		
        $json = "";
        $json_item = "";
        $json_array = array();
        $cat_array = array();
        $color = Kohana::config('settings.default_map_all');
		$icon = "";

		$category_id = "";
		$incident_id = "";
		$neighboring = "";
		$media_type = "";
		
		if (isset($_GET['c']) && !empty($_GET['c']))
		{
			$category_id = $_GET['c'];
		}
		
		if (isset($_GET['i']) && !empty($_GET['i']))
		{
			$incident_id = $_GET['i'];
		}
		
		if (isset($_GET['n']) && !empty($_GET['n']))
		{
			$neighboring = $_GET['n'];
		}
		
		$where_text = '';
		// Do we have a media id to filter by?
		if (isset($_GET['m']) && !empty($_GET['m']) && $_GET['m'] != '0')
		{
			$media_type = $_GET['m'];
			$where_text .= ' AND media.media_type = ' . $media_type;
		}
		
        if (isset($_GET['s']) && !empty($_GET['s']))
		{
        	$start_date = $_GET['s']; 
        	$where_text .= " AND UNIX_TIMESTAMP(incident.incident_date) >= '" . $start_date . "'";
        }
        
		if (isset($_GET['e']) && !empty($_GET['e']))
		{
        	$end_date = $_GET['e']; 
        	$where_text .= " AND UNIX_TIMESTAMP(incident.incident_date) <= '" . $end_date . "'";
        }
                
        // Do we have a category id to filter by?
        if (is_numeric($category_id) && $category_id != '0')
        {
            // Retrieve markers by category
            // XXX: Might need to replace magic numbers
			$markers = ORM::factory('incident')
				->select('DISTINCT incident.*')
				->with('location')
				->join('incident_category', 'incident.id', 'incident_category.incident_id','LEFT')
				->join('media', 'incident.id', 'media.incident_id','LEFT')
				->where('incident.incident_active = 1 AND 
					incident_category.category_id = ' . $category_id . $where_text)
				->find_all();

            // Retrieve category color
			$category = ORM::factory('category', $category_id);
            $color = $category->category_color;
			$icon = $category->category_image;
                     
        }// Do we have a single incident id to filter by?
        elseif (is_numeric($incident_id) && $incident_id != '0')
		{
		    // Retrieve individual marker
            $markers = ORM::factory('incident')
				->with('location')
				->where('incident.incident_active = 1 AND incident.id = ' . $incident_id)
				->find_all();
        }
        else
        {
			// Retrieve all markers
			$markers = ORM::factory('incident')
				->select('DISTINCT incident.*')
				->with('location')
				->join('media', 'incident.id', 'media.incident_id','LEFT')
				->where('incident.incident_active = 1 '.$where_text)
				->find_all();
        }
        
        foreach ($markers as $marker)
        {			
            $json_item = "{";
            $json_item .= "\"type\":\"Feature\",";
            $json_item .= "\"properties\": {";
            $json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='" . url::base() . "reports/view/" . $marker->id . "'>" . htmlentities($marker->incident_title) . "</a>")) . "\",";
			
			if (isset($category)) { 
				$json_item .= "\"category\":[" . $category_id . "], ";
			} else {
				$json_item .= "\"category\":[0], ";
			}
			
			$json_item .= "\"color\": \"".$color."\", \n";
			$json_item .= "\"icon\": \"".$icon."\", \n";
            $json_item .= "\"timestamp\": \"" . strtotime($marker->incident_date) . "\"";
            $json_item .= "},";
            $json_item .= "\"geometry\": {";
            $json_item .= "\"type\":\"Point\", ";
            $json_item .= "\"coordinates\":[" . $marker->location->longitude . ", " . $marker->location->latitude . "]";
            $json_item .= "}";
            $json_item .= "}";
		
            array_push($json_array, $json_item);
            $cat_array = array();
        }
        $json = implode(",", $json_array);
        $this->template->json = $json;

    }
    
    public function timeline() {
        $this->auto_render = FALSE;
        $this->template = new View('json/timeline');
        //$this->template->content = new View('json/timeline');
        
        $interval = 'day';
        $start_date = NULL;
        $end_date = NULL;
        $active = 'true';
        $media_type = NULL;
        if (isset($_GET['i'])) {
            $interval = $_GET['i'];
        }
        if (isset($_GET['s'])) {
            $start_date = $_GET['s'];
        }
        if (isset($_GET['e'])) {
            $end_date = $_GET['e'];
        }
        if (isset($_GET['active'])) {
            $active = $_GET['active'];
        }
        if (isset($_GET['m'])) {
            $media_type = $_GET['m'];
        }
        // get graph data
        $graph_data = array();
        $all_graphs = Incident_Model::get_incidents_by_interval($interval,$start_date,$end_date,$active,$media_type);
	    echo $all_graphs;
   	}

}
