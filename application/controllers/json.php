<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Json Controller
 * Generates Map GeoJSON File
 */
class Json_Controller extends Template_Controller
{
    public $auto_render = TRUE;
	
    // Main template
    public $template = 'json';
	
    function index( $category_id = 0 )
    {		
        $json = "";
        $json_item = "";
        $json_array = array();
        $cat_array = array();
        $color = "000000";
		
        // Do we have a category id to filter by?
        if (is_numeric($category_id) && $category_id != 0)
        {
            // Retrieve individual markers
            // XXX: Might need to replace magic numbers
            foreach (ORM::factory('incident')
                     ->join('incident_category', 'incident.id', 'incident_category.incident_id','INNER')
                     ->select('incident.*')
                     ->where('incident.incident_active = 1 AND incident_category.category_id = ' . $category_id)->orderby('incident.incident_dateadd', 'desc')
                     ->find_all() as $marker)
            {			
                $json_item = "{";
                $json_item .= "\"type\":\"Feature\",";
                $json_item .= "\"properties\": {";
                $json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='" . url::base() . "reports/view/" . $marker->id . "'>" . $marker->incident_title . "</a>")) . "\",";
                $json_item .= "\"description\":\"" . htmlentities(str_replace(chr(10), ' ', str_replace(chr(13), ' ', substr($marker->incident_description, 0, 150)))) . "...\", ";			
                $json_item .= "\"category\":[" . $category_id . "], ";
                $json_item .= "\"color\": \"" . $color . "\", \n";
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
        else
        {
            // Retrieve individual markers
            foreach (ORM::factory('incident')
                     ->where('incident_active = 1')
                     ->orderby('incident_dateadd', 'desc')
                     ->find_all() as $marker)
            {			
                $json_item = "{";
                $json_item .= "\"type\":\"Feature\",";
                $json_item .= "\"properties\": {";
                $json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='" . url::base() . "reports/view/" . $marker->id . "'>" . $marker->incident_title . "</a>")) . "\",";
                $json_item .= "\"description\":\"" . htmlentities(str_replace(chr(10), ' ', str_replace(chr(13), ' ', substr($marker->incident_description, 0, 150)))) . "...\", ";			
                $json_item .= "\"category\":[";
                foreach($marker->incident_category as $category)
                {
                    array_push($cat_array, $category->category->id);
                    $color = $category->category->category_color;
                }
                $json_item .= implode(",", $cat_array);
                $json_item .= "], ";
                $json_item .= "\"color\": \"" . $color . "\", \n";
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
    }
}