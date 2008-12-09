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
	
    function index()
    {		
        $json = "";
        $json_item = "";
        $json_array = array();
        $cat_array = array();
        $color = "000000";

		$category_id = "";
		$incident_id = "";
		$neighboring = "";
		
		if (isset($_GET['c']))
		{
			if (!empty($_GET['c']))
			{
				$category_id = $_GET['c'];
			}
		}
		
		if (isset($_GET['i']))
		{
			if (!empty($_GET['i']))
			{
				$incident_id = $_GET['i'];
			}
		}
		
		if (isset($_GET['n']))
		{
			if (!empty($_GET['n']))
			{
				$neighboring = $_GET['n'];
			}
		}
		
		$where_text = '';
        if (isset($_GET['s']) && !empty($_GET['s'])) {
        	$start_date = $_GET['s']; 
        	$where_text .= " AND UNIX_TIMESTAMP(incident.incident_date) >= '" . $start_date . "'";
        }
        if (isset($_GET['e']) && !empty($_GET['e'])) {
        	$end_date = $_GET['e']; 
        	$where_text .= " AND UNIX_TIMESTAMP(incident.incident_date) <= '" . $end_date . "'";
        }
        // Do we have a category id to filter by?
        if (is_numeric($category_id) && $category_id != 0)
        {
            // Retrieve markers by category
            // XXX: Might need to replace magic numbers
  
            foreach (ORM::factory('incident')
                     ->join('incident_category', 'incident.id', 'incident_category.incident_id','INNER')
                     ->select('incident.*')
                     ->where('incident.incident_active = 1 AND incident_category.category_id = ' . $category_id . $where_text)->orderby('incident.incident_dateadd', 'desc')
                     ->find_all() as $marker)
            {			
                $json_item = "{";
                $json_item .= "\"type\":\"Feature\",";
                $json_item .= "\"properties\": {";
                $json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='" . url::base() . "reports/view/" . $marker->id . "'>" . htmlentities($marker->incident_title) . "</a>")) . "\",";
                // $json_item .= "\"description\":\"" . htmlentities(str_replace(chr(10), ' ', str_replace(chr(13), ' ', substr($marker->incident_description, 0, 150)))) . "...\", ";			
                $json_item .= "\"category\":[" . $category_id . "], ";
				
				// Retrieve category color
				$category = ORM::factory('category', $category_id);
				$color = $category->category_color;
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

        // Do we have a single incident id to filter by?
        elseif (is_numeric($incident_id) && $incident_id != 0)
		{
			$color = "CC0000";
		    // Retrieve individual marker
            $marker = ORM::factory('incident', $incident_id);

			if ( $marker->id != 0 )	// Not Found
			{
				if ($marker->incident_active == 1)
				{
	                $json_item = "{";
	                $json_item .= "\"type\":\"Feature\",";
	                $json_item .= "\"properties\": {";
	                $json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='" . url::base() . "reports/view/" . $marker->id . "'>" . htmlentities($marker->incident_title) . "</a>")) . "\",";
	                // $json_item .= "\"description\":\"" . htmlentities(str_replace(chr(10), ' ', str_replace(chr(13), ' ', substr($marker->incident_description, 0, 150)))) . "...\", ";			
	                $json_item .= "\"color\": \"" . $color . "\", \n";
	                $json_item .= "\"timestamp\": \"" . strtotime($marker->incident_date) . "\"";
	                $json_item .= "},";
	                $json_item .= "\"geometry\": {";
	                $json_item .= "\"type\":\"Point\", ";
	                $json_item .= "\"coordinates\":[" . $marker->location->longitude . ", " . $marker->location->latitude . "]";
	                $json_item .= "}";
	                $json_item .= "}";
				}
			}

               array_push($json_array, $json_item);
               $cat_array = array();

            $json = implode(",", $json_array);
            $this->template->json = $json;
		}
		
        else
        {
            // Retrieve all markers
            foreach (ORM::factory('incident')
                     ->where('incident_active = 1' . $where_text)
                     ->orderby('incident_dateadd', 'desc')
                     ->find_all() as $marker)
            {			
                $json_item = "{";
                $json_item .= "\"type\":\"Feature\",";
                $json_item .= "\"properties\": {";
                $json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='" . url::base() . "reports/view/" . $marker->id . "'>" . htmlentities($marker->incident_title) . "</a>")) . "\",";
                // $json_item .= "\"description\":\"" . htmlentities(str_replace(chr(10), ' ', str_replace(chr(13), ' ', substr($marker->incident_description, 0, 150)))) . "...\", ";			
                $json_item .= "\"category\":[";
                foreach($marker->incident_category as $category)
                {
                    array_push($cat_array, $category->category->id);
                    $color = $category->category->category_color;
                }
                $json_item .= implode(",", $cat_array);
                $json_item .= "], ";
				// Display as a neighboring marker on report/view page
				if ($neighboring == 'yes')
				{
					$json_item .= "\"color\": \"FF9933\", \n";
				} else {
					$json_item .= "\"color\": \"" . $color . "\", \n";
				}
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
    
    public function timeline() {
        $this->auto_render = FALSE;
        $this->template = new View('json/timeline');
        //$this->template->content = new View('json/timeline');
        
        
        $interval = 'day';
        $start_date = NULL;
        $end_date = NULL;
        if (isset($_GET['i'])) {
            $interval = $_GET['i'];
        }
        if (isset($_GET['s'])) {
            $start_date = $_GET['s'];
        }
        if (isset($_GET['e'])) {
            $end_date = $_GET['e'];
        }
        
        // get graph data
        $graph_data = array();
        $all_graphs = Incident_Model::get_incidents_by_interval('hour',$start_date,$end_date);
	    echo $all_graphs;
   	}

}
