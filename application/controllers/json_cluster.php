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

define('OFFSET', 268435456);
define('RADIUS', 85445659.4471); /* $offset / pi() */

class Json_Cluster_Controller extends Template_Controller
{
    public $auto_render = TRUE;

	// Cache this controller
	public $is_cachable = TRUE;

    // Main template
    public $template = 'json';

	public function __construct()
	{
		parent::__construct();
		
		set_time_limit(60);
	}
	
	public function index()
    {
		// Database
		$db = new Database();
		
		$json = "";
        $json_item = "";
        $json_array = array();

		$color = Kohana::config('settings.default_map_all');
		$icon = "";
		
		// Get Zoom Level
		$zoomLevel = (isset($_GET['z']) && !empty($_GET['z'])) ?
			$_GET['z'] : 8;
			
		//$distance = 60;
		$distance = (10000000 >> $zoomLevel) / 100000;
		
		// Category ID
		$category_id = (isset($_GET['c']) && !empty($_GET['c']) && 
			is_numeric($_GET['c']) && $_GET['c'] != 0) ?
			$_GET['c'] : 0;
		
		// Start Date
		$start_date = (isset($_GET['s']) && !empty($_GET['s'])) ?
			$_GET['s'] : "0";
		
		// End Date
		$end_date = (isset($_GET['e']) && !empty($_GET['e'])) ?
			$_GET['e'] : "0";
			
		// SouthWest Bound
		$southwest = (isset($_GET['sw']) && !empty($_GET['sw'])) ?
			$_GET['sw'] : "0";
		
		$northeast = (isset($_GET['ne']) && !empty($_GET['ne'])) ?
			$_GET['ne'] : "0";			
			
		$filter = "";
		$filter .= ($category_id !=0) ? " AND ( category.id=".$category_id
			." OR category.parent_id=".$category_id.") " : "";
		$filter .= ($start_date) ? 
			" AND incident.incident_date >= '" . date("Y-m-d H:i:s", $start_date) . "'" : "";
		$filter .= ($end_date) ? 
			" AND incident.incident_date <= '" . date("Y-m-d H:i:s", $end_date) . "'" : "";
			
		if ($southwest && $northeast)
		{
			list($latitude_min, $longitude_min) = explode(',', $southwest);
			list($latitude_max, $longitude_max) = explode(',', $northeast);
			
			$filter .= " AND location.latitude >=".$latitude_min.
				" AND location.latitude <=".$latitude_max;
			$filter .= " AND location.longitude >=".$longitude_min.
				" AND location.longitude <=".$longitude_max;
		}
		
		if ($category_id > 0)
		{
			$query_cat = $db->query("SELECT `category_color`, `category_image` FROM `category` WHERE id=$category_id");
			foreach ($query_cat as $cat)
			{
				$color = $cat->category_color;
				$icon = $cat->category_image;
			}
		}
		
			
//		$query = ORM::factory('incident')
//			->select('incident.*, location.latitude, location.longitude, incident_category.category_id')
//			->join('location', 'location.id', 'incident.location_id','INNER')
//			->join('incident_category', 'incident.id', 'incident_category.incident_id','INNER')
//			->join('category', 'category.id', 'incident_category.category_id','INNER')
//			->where('incident.incident_active=1'.$filter)
//			->find_all();

		$query = $db->query("SELECT DISTINCT `incident`.id, `location`.`latitude`, `location`.`longitude` FROM `incident` INNER JOIN `location` ON (`location`.`id` = `incident`.`location_id`) INNER JOIN `incident_category` ON (`incident`.`id` = `incident_category`.`incident_id`) INNER JOIN `category` ON (`incident_category`.`category_id` = `category`.`id`) WHERE incident.incident_active=1 $filter ORDER BY `incident`.`id` ASC ");	
		$query->result(FALSE, MYSQL_ASSOC);
//		echo count($query);

		//*** There has to be a more efficient way to do this than to
		// create a whole other array - to be examined later
		$markers = array();
		foreach ($query as $row)
		{
			$markers[] = $row;
		}

		$clusters = array();	// Clustered
		$singles = array();		// Non Clustered

		// Loop until all markers have been compared
		while (count($markers))
		{
			$marker  = array_pop($markers);
			$cluster = array();

			// Compare marker against all remaining markers.
			foreach ($markers as $key => $target)
			{
				// This function returns the distance between two markers, at a defined zoom level.
				//$pixels = $this->_pixelDistance($marker['latitude'], $marker['longitude'], 
				//	$target['latitude'], $target['longitude'], $zoomLevel);
				
				$pixels = abs($marker['longitude']-$target['longitude']) + 
					abs($marker['latitude']-$target['latitude']);
				// echo $pixels."<BR>";
				// If two markers are closer than defined distance, remove compareMarker from array and add to cluster.
				if ($pixels < $distance)
				{
					unset($markers[$key]);
					$target['distance'] = $pixels;
					$cluster[] = $target;
				}
			}

			// If a marker was added to cluster, also add the marker we were comparing to.
			if (count($cluster) > 0)
			{
				$cluster[] = $marker;
				$clusters[] = $cluster;
			}
			else
			{
				$singles[] = $marker;
			}
		}

		//print_r($clusters);
		//print_r($singles);

		// Create Json
		foreach ($clusters as $cluster)
		{
			// Calculate cluster center
			$bounds = $this->_calculateCenter($cluster);
			$cluster_center = $bounds['center'];
			$southwest = $bounds['sw'];
			$northeast = $bounds['ne'];
			
			// Number of Items in Cluster
			$cluster_count = count($cluster);
			
			$json_item = "{";
		    $json_item .= "\"type\":\"Feature\",";
		    $json_item .= "\"properties\": {";
			$json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href=" . url::base() . "reports/index/?c=".$category_id."&sw=".$southwest."&ne=".$northeast.">" . $cluster_count . " Reports</a>")) . "\",";			
		    $json_item .= "\"category\":[0], ";
			$json_item .= "\"color\": \"".$color."\", ";
			$json_item .= "\"icon\": \"".$icon."\", ";
		    $json_item .= "\"timestamp\": \"0\", ";
			$json_item .= "\"count\": \"" . $cluster_count . "\"";
		    $json_item .= "},";
		    $json_item .= "\"geometry\": {";
		    $json_item .= "\"type\":\"Point\", ";
		    $json_item .= "\"coordinates\":[" . $cluster_center . "]";
		    $json_item .= "}";
		    $json_item .= "}";

		    array_push($json_array, $json_item);
		}

		foreach ($singles as $single)
		{
			$json_item = "{";
		    $json_item .= "\"type\":\"Feature\",";
		    $json_item .= "\"properties\": {";
			$json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href=" . url::base() . "reports/view/" . $marker['id'] . "/>1 Report</a>")) . "\",";	
		    $json_item .= "\"category\":[0], ";
			$json_item .= "\"color\": \"".$color."\", ";
			$json_item .= "\"icon\": \"".$icon."\", ";
		    $json_item .= "\"timestamp\": \"0\", ";
			$json_item .= "\"count\": \"" . 1 . "\"";
		    $json_item .= "},";
		    $json_item .= "\"geometry\": {";
		    $json_item .= "\"type\":\"Point\", ";
		    $json_item .= "\"coordinates\":[" . $single['longitude'] . ", " . $single['latitude'] . "]";
		    $json_item .= "}";
		    $json_item .= "}";

		    array_push($json_array, $json_item);
		}

		$json = implode(",", $json_array);

		//header('Content-type: application/json');
		$this->template->json = $json;
		
	}
	
	
	/* Retrieve Single Marker */
	public function single($incident_id = 0)
	{
		$json = "";
		$json_item = "";
		$json_array = array();	
		
		
		$marker = ORM::factory('incident')
			->where('id', $incident_id)
			->find();
			
		if ($marker->loaded)
		{
			/* First We'll get all neighboring reports */
			$incident_date = date('Y-m', strtotime($marker->incident_date));
			$latitude = $marker->location->latitude;
			$longitude = $marker->location->longitude;
			
			$filter = " AND incident.incident_date LIKE '$incident_date%' ";
			$filter .= " AND incident.id <>".$marker->id;
			
			// Database
			$db = new Database();
			
			// Get Neighboring Markers From The Same Month Within A Mile
			$query = $db->query("SELECT DISTINCT `incident`.*, `location`.`latitude`, `location`.`longitude`, 
			((ACOS(SIN($latitude * PI() / 180) * SIN(`location`.`latitude` * PI() / 180) + COS($latitude * PI() / 180) * COS(`location`.`latitude` * PI() / 180) * COS(($longitude - `location`.`longitude`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance 
			 FROM `incident` INNER JOIN `location` ON (`location`.`id` = `incident`.`location_id`) INNER JOIN `incident_category` ON (`incident`.`id` = `incident_category`.`incident_id`) INNER JOIN `category` ON (`incident_category`.`category_id` = `category`.`id`) WHERE incident.incident_active=1 $filter 
			HAVING distance<='1'
			 ORDER BY `incident`.`id` ASC ");
			
			foreach ($query as $row)
			{
				$json_item = "{";
	            $json_item .= "\"type\":\"Feature\",";
	            $json_item .= "\"properties\": {";
				$json_item .= "\"id\": \"".$row->id."\", ";
	            $json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='" . url::base() . "reports/view/" . $row->id . "'>" . htmlentities($row->incident_title) . "</a>")) . "\",";
				$json_item .= "\"category\":[0], ";
	            $json_item .= "\"timestamp\": \"" . strtotime($row->incident_date) . "\"";
	            $json_item .= "},";
	            $json_item .= "\"geometry\": {";
	            $json_item .= "\"type\":\"Point\", ";
	            $json_item .= "\"coordinates\":[" . $row->longitude . ", " . $row->latitude . "]";
	            $json_item .= "}";
	            $json_item .= "}";
	
				array_push($json_array, $json_item);
			}
			
			$json_item = "{";
            $json_item .= "\"type\":\"Feature\",";
            $json_item .= "\"properties\": {";
			$json_item .= "\"id\": \"".$marker->id."\", ";
            $json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='" . url::base() . "reports/view/" . $marker->id . "'>" . htmlentities($marker->incident_title) . "</a>")) . "\",";
			$json_item .= "\"category\":[0], ";
            $json_item .= "\"timestamp\": \"" . strtotime($marker->incident_date) . "\"";
            $json_item .= "},";
            $json_item .= "\"geometry\": {";
            $json_item .= "\"type\":\"Point\", ";
            $json_item .= "\"coordinates\":[" . $marker->location->longitude . ", " . $marker->location->latitude . "]";
            $json_item .= "}";
            $json_item .= "}";

			array_push($json_array, $json_item);
		}
		
		
		$json = implode(",", $json_array);
		
		//header('Content-type: application/json');
		$this->template->json = $json;
	}
	
	
	public function timeline()
	{
        $this->auto_render = FALSE;
        $this->template = new View('json/timeline');
        //$this->template->content = new View('json/timeline');
        
        $interval = 'day';
        $start_date = NULL;
        $end_date = NULL;
        $active = 'true';
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
        
        // get graph data
        $graph_data = array();
        $all_graphs = Incident_Model::get_incidents_by_interval($interval,$start_date,$end_date,$active);
	    echo $all_graphs;
   	}

	/* Read the Layer IN via file_get_contents */
	public function layer($layer_id = 0)
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		$layer = ORM::factory('layer')
			->where('layer_visible', 1)
			->find($layer_id);
		
		if ($layer->loaded)
		{
			$layer_url = $layer->layer_url;
			$layer_file = $layer->layer_file;
			
			$layer_link = (!$layer_url) ?
				url::base().'media/uploads/'.$layer_file :
				$layer_url;
			
			$content = file_get_contents($layer_link);
			
			if ($content !== false)
			{
				echo $content;
			}
			else
			{
				echo "";
			}
		}
		else
		{
			echo "";
		}
	}
	
	
	private function _lonToX($lon)
	{
	    return round(OFFSET + RADIUS * $lon * pi() / 180);        
	}

	private function _latToY($lat)
	{
	    return round(OFFSET - RADIUS * 
	                log((1 + sin($lat * pi() / 180)) / 
	                (1 - sin($lat * pi() / 180))) / 2);
	}

	private function _pixelDistance($lat1, $lon1, $lat2, $lon2, $zoom)
	{
	    $x1 = $this->_lonToX($lon1);
	    $y1 = $this->_latToY($lat1);

	    $x2 = $this->_lonToX($lon2);
	    $y2 = $this->_latToY($lat2);

	    return sqrt(pow(($x1-$x2),2) + pow(($y1-$y2),2)) >> (21 - $zoom);
	}
	
	
    private function _calculateCenter($cluster)
	{
		// Calculate average lat and lon of clustered items
		$south = 0;
		$west = 0;
		$north = 0;
		$east = 0;
		
        $lat_sum = $lon_sum = 0;
        foreach ($cluster as $marker)
		{
			if (!$south)
			{
				$south = $marker['latitude'];
			}
			elseif ($marker['latitude'] < $south)
			{
				$south = $marker['latitude'];
			}
				
			if (!$west)
			{
				$west = $marker['longitude'];
			}
			elseif ($marker['longitude'] < $west)
			{
				$west = $marker['longitude'];
			}
			
			if (!$north)
			{
				$north = $marker['latitude'];
			}
			elseif ($marker['latitude'] > $north)
			{
				$north = $marker['latitude'];
			}
			
			if (!$east)
			{
				$east = $marker['longitude'];
			}
			elseif ($marker['longitude'] > $east)
			{
				$east = $marker['longitude'];
			}
			
			$lat_sum += $marker['latitude'];
			$lon_sum += $marker['longitude'];
        }
		$lat_avg = $lat_sum / count($cluster);
		$lon_avg = $lon_sum / count($cluster);
		
		$center = $lon_avg.",".$lat_avg;
		$sw = $west.",".$south;
		$ne = $east.",".$north;
		
		return array(
			"center"=>$center,
			"sw"=>$sw,
			"ne"=>$ne
		);
    }
	
	
}