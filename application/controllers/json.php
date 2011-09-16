<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Json Controller
 * Generates Map GeoJSON File
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Json_Controller extends Template_Controller
{
	/**
	 * Automatically render the views
	 * @var bool
	 */
	public $auto_render = TRUE;

	/**
	 * Name of the view template for this controller
	 * @var string
	 */
	public $template = 'json';

	/**
	 * Database table prefix
	 * @var string
	 */
	protected $table_prefix;

	public function __construct()
	{
		parent::__construct();

		// Set Table Prefix
		$this->table_prefix = Kohana::config('database.default.table_prefix');

		// Cacheable JSON Controller
		$this->is_cachable = TRUE;
	}


	/**
	 * Generate JSON in NON-CLUSTER mode
	 */
	public function index()
	{
		$json = "";
		$json_item = "";
		$json_array = array();
		$color = Kohana::config('settings.default_map_all');
		$icon = "";

		$media_type = (isset($_GET['m']) AND intval($_GET['m']) > 0)? intval($_GET['m']) : 0;
		
		// Get the incident and category id
		$category_id = (isset($_GET['c']) AND intval($_GET['c']) > 0)? intval($_GET['c']) : 0;
		$incident_id = (isset($_GET['i']) AND intval($_GET['i']) > 0)? intval($_GET['i']) : 0;
		
		// Get the category colour
		if (Category_Model::is_valid_category($category_id))
		{
			$color = ORM::factory('category', $category_id)->category_color;
		}
		
		// Fetch the incidents
		$markers = (isset($_GET['page']) AND intval($_GET['page']) > 0)? reports::fetch_incidents(TRUE) : reports::fetch_incidents();
		
		// Variable to store individual item for report detail page
		$json_item_first = "";	
		foreach ($markers as $marker)
		{
			$thumb = "";
			if ($media_type == 1)
			{
				$media = ORM::factory('incident', $marker->incident_id)->media;
				if ($media->count())
				{
					foreach ($media as $photo)
					{
						if ($photo->media_thumb)
						{ 
							// Get the first thumb
							$prefix = url::base().Kohana::config('upload.relative_directory');
							$thumb = $prefix."/".$photo->media_thumb;
							break;
						}
					}
				}
			}
			
			$json_item = "{";
			$json_item .= "\"type\":\"Feature\",";
			$json_item .= "\"properties\": {";
			$json_item .= "\"id\": \"".$marker->incident_id."\", \n";

			$encoded_title = utf8tohtml::convert($marker->incident_title, TRUE);
			$encoded_title = str_ireplace('"','&#34;',$encoded_title);
			$encoded_title = json_encode($encoded_title);
			$encoded_title = str_ireplace('"', '', $encoded_title);

			$json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a "
					. "href='".url::base()."reports/view/".$marker->incident_id."'>".$encoded_title)."</a>") . "\","
					. "\"link\": \"".url::base()."reports/view/".$marker->incident_id."\", ";

			$json_item .= (isset($category))
				? "\"category\":[" . $category_id . "], "
				: "\"category\":[0], ";

			$json_item .= "\"color\": \"".$color."\", \n";
			$json_item .= "\"icon\": \"".$icon."\", \n";
			$json_item .= "\"thumb\": \"".$thumb."\", \n";
			$json_item .= "\"timestamp\": \"" . strtotime($marker->incident_date) . "\"";
			$json_item .= "},";
			$json_item .= "\"geometry\": {";
			$json_item .= "\"type\":\"Point\", ";
			$json_item .= "\"coordinates\":[" . $marker->longitude . ", " . $marker->latitude . "]";
			$json_item .= "}";
			$json_item .= "}";

			if ($marker->incident_id == $incident_id)
			{
				$json_item_first = $json_item;
			}
			else
			{
				array_push($json_array, $json_item);
			}
			
			// Get Incident Geometries
			$geometry = $this->_get_geometry($marker->incident_id, $marker->incident_title, $marker->incident_date);
			if (count($geometry))
			{
				$json_item = implode(",", $geometry);
				array_push($json_array, $json_item);
			}
		}
		
		if ($json_item_first)
		{
			// Push individual marker in last so that it is layered on top when pulled into map
			array_push($json_array, $json_item_first);
		}
		
		$json = implode(",", $json_array);

		header('Content-type: application/json; charset=utf-8');
		$this->template->json = $json;
	}


	/**
	 * Generate JSON in CLUSTER mode
	 */
	public function cluster()
	{
		// Database
		$db = new Database();

		$json = "";
		$json_item = "";
		$json_array = array();
		$geometry_array = array();

		$color = Kohana::config('settings.default_map_all');
		$icon = "";

		// Get Zoom Level
		$zoomLevel = (isset($_GET['z']) AND !empty($_GET['z'])) ?
			(int) $_GET['z'] : 8;

		//$distance = 60;
		$distance = (10000000 >> $zoomLevel) / 100000;
		
		// Fetch the incidents using the specified parameters
		$incidents = reports::fetch_incidents();
		
		// Category ID
		$category_id = (isset($_GET['c']) AND intval($_GET['c']) > 0) ? intval($_GET['c']) : 0;
		
		// Start date
		$start_date = (isset($_GET['s']) AND intval($_GET['s']) > 0) ? intval($_GET['s']) : NULL;
		
		// End date
		$end_date = (isset($_GET['e']) AND intval($_GET['e']) > 0) ? intval($_GET['e']) : NULL;
		
		if (Category_Model::is_valid_category($category_id))
		{
			// Get the color
			$color = ORM::factory('category', $category_id)->category_color;
		}

		// Create markers by marrying the locations and incidents
		$markers = array();
		foreach ($incidents as $incident)
		{
			$markers[] = array(
				'id' => $incident->incident_id,
				'incident_title' => $incident->incident_title,
				'latitude' => $incident->latitude,
				'longitude' => $incident->longitude,
				'thumb' => ''
				);
		}

		$clusters = array();	// Clustered
		$singles = array();		// Non Clustered

		// Loop until all markers have been compared
		while (count($markers))
		{
			$marker	 = array_pop($markers);
			$cluster = array();

			// Compare marker against all remaining markers.
			foreach ($markers as $key => $target)
			{
				// This function returns the distance between two markers, at a defined zoom level.
				// $pixels = $this->_pixelDistance($marker['latitude'], $marker['longitude'],
				// $target['latitude'], $target['longitude'], $zoomLevel);

				$pixels = abs($marker['longitude']-$target['longitude']) +
					abs($marker['latitude']-$target['latitude']);
					
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
			
			// Get the time filter
			$time_filter = ( ! empty($start_date) AND ! empty($end_date))
				? "&s=".$start_date."&e=".$end_date
				: "";
			
			// Build out the JSON string
			$json_item = "{";
			$json_item .= "\"type\":\"Feature\",";
			$json_item .= "\"properties\": {";
			$json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href=" . url::base()
				 . "reports/index/?c=".$category_id."&sw=".$southwest."&ne=".$northeast.$time_filter.">" . $cluster_count . " Reports</a>")) . "\",";
			$json_item .= "\"link\": \"".url::base()."reports/index/?c=".$category_id."&sw=".$southwest."&ne=".$northeast.$time_filter."\", ";
			$json_item .= "\"category\":[0], ";
			$json_item .= "\"color\": \"".$color."\", ";
			$json_item .= "\"icon\": \"".$icon."\", ";
			$json_item .= "\"thumb\": \"\", ";
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
			$json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href=" . url::base()
					. "reports/view/" . $single['id'] . "/>".str_replace('"','\"',$single['incident_title'])."</a>")) . "\",";
			$json_item .= "\"link\": \"".url::base()."reports/view/".$single['id']."\", ";
			$json_item .= "\"category\":[0], ";
			$json_item .= "\"color\": \"".$color."\", ";
			$json_item .= "\"icon\": \"".$icon."\", ";
			// $json_item .= "\"thumb\": \"".$single['thumb']."\", ";
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
		
		// 
		// E.Kala July 27, 2011
		// @todo Parking this geometry business for review
		// 
		
		// if (count($geometry_array))
		// {
		// 	$json = implode(",", $geometry_array).",".$json;
		// }
		
		header('Content-type: application/json; charset=utf-8');
		$this->template->json = $json;
	}

	/**
	 * Retrieve Single Marker
	 */
	public function single($incident_id = 0)
	{
		$json = "";
		$json_item = "";
		$json_array = array();

		// Get the neigbouring incidents
		$neighbours = Incident_Model::get_neighbouring_incidents($incident_id, FALSE, 20, 100);

		if ($neighbours)
		{
			// Load the incident
			// @todo Get this fixed
			$marker = ORM::factory('incident', $incident_id);
			
			// Get the incident/report date
			$incident_date = date('Y-m', strtotime($marker->incident_date));

			foreach ($neighbours as $row)
			{
				$json_item = "{";
				$json_item .= "\"type\":\"Feature\",";
				$json_item .= "\"properties\": {";
				$json_item .= "\"id\": \"".$row->id."\", ";

				$encoded_title = utf8tohtml::convert($row->incident_title,TRUE);
				$encoded_title = str_ireplace('"','&#34;',$encoded_title);
				$encoded_title = json_encode($encoded_title);
				$encoded_title = str_ireplace('"','',$encoded_title);

				$json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='" . url::base()
						. "reports/view/" . $row->id . "'>".$encoded_title."</a>")) . "\",";
				$json_item .= "\"link\": \"".url::base()."reports/view/".$row->id."\", ";
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
			
			// Single Main Incident
			$json_single = "{";
			$json_single .= "\"type\":\"Feature\",";
			$json_single .= "\"properties\": {";
			$json_single .= "\"id\": \"".$marker->id."\", ";

			$encoded_title = utf8tohtml::convert($marker->incident_title,TRUE);

			$json_single .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='" . url::base()
					. "reports/view/" . $marker->id . "'>".$encoded_title."</a>")) . "\",";
			$json_single .= "\"link\": \"".url::base()."reports/view/".$marker->id."\", ";
			$json_single .= "\"category\":[0], ";
			$json_single .= "\"timestamp\": \"" . strtotime($marker->incident_date) . "\"";
			
			// Get Incident Geometries
			$geometry = $this->_get_geometry($marker->id, $marker->incident_title, $marker->incident_date);
			
			// If there are no geometries, use Single Incident Marker
			if ( ! count($geometry))
			{
				$json_item = "{";
				$json_item .= "\"type\":\"Feature\",";
				$json_item .= "\"properties\": {";
				$json_item .= "\"id\": \"".$marker->id."\", ";

				$encoded_title = utf8tohtml::convert($marker->incident_title,TRUE);

				$json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='" . url::base()
					. "reports/view/" . $marker->id . "'>".$encoded_title."</a>")) . "\",";
				$json_item .= "\"link\": \"".url::base()."reports/view/".$marker->id."\", ";
				$json_item .= "\"category\":[0], ";
				$json_item .= "\"timestamp\": \"" . strtotime($marker->incident_date) . "\"";
				$json_item .= "},\"geometry\":";
				$json_item .= "{\"type\":\"Point\", ";
				$json_item .= "\"coordinates\":[" . $marker->location->longitude . ", " . $marker->location->latitude . "]";
				$json_item .= "}";
				$json_item .= "}";
			}
			else
			{
				$json_item = implode(",", $geometry);
			}

			array_push($json_array, $json_item);
		}


		$json = implode(",", $json_array);
		
		header('Content-type: application/json; charset=utf-8');
		$this->template->json = $json;
	}

	/**
	 * Retrieve timeline JSON
	 */
	public function timeline( $category_id = 0 )
	{
		$category_id = (int) $category_id;

		$this->auto_render = FALSE;
		$db = new Database();

		$interval = (isset($_GET["i"]) AND !empty($_GET["i"])) ?
			$_GET["i"] : "month";

		// Get Category Info
		if ($category_id > 0)
		{
			$category = ORM::factory("category", $category_id);
			if ($category->loaded)
			{
				$category_title = $category->category_title;
				$category_color = "#".$category->category_color;
			}
			else
			{
				break;
			}
		}
		else
		{
			$category_title = "All Categories";
			$category_color = "#990000";
		}

		// Get the Counts
		$select_date_text = "DATE_FORMAT(incident_date, '%Y-%m-01')";
		$groupby_date_text = "DATE_FORMAT(incident_date, '%Y%m')";
		if ($interval == 'day')
		{
			$select_date_text = "DATE_FORMAT(incident_date, '%Y-%m-%d')";
			$groupby_date_text = "DATE_FORMAT(incident_date, '%Y%m%d')";
		}
		elseif ($interval == 'hour')
		{
			$select_date_text = "DATE_FORMAT(incident_date, '%Y-%m-%d %H:%M')";
			$groupby_date_text = "DATE_FORMAT(incident_date, '%Y%m%d%H')";
		}
		elseif ($interval == 'week')
		{
			$select_date_text = "STR_TO_DATE(CONCAT(CAST(YEARWEEK(incident_date) AS CHAR), ' Sunday'), '%X%V %W')";
			$groupby_date_text = "YEARWEEK(incident_date)";
		}

		$graph_data = array();
		$graph_data[0] = array();
		$graph_data[0]['label'] = $category_title;
		$graph_data[0]['color'] = $category_color;
		$graph_data[0]['data'] = array();

		// Gather allowed ids if we are looking at a specific category

		$allowed_ids = array();
		if($category_id != 0)
		{
			$query = 'SELECT ic.incident_id AS incident_id FROM '.$this->table_prefix.'incident_category AS ic INNER JOIN '.$this->table_prefix.'category AS c ON (ic.category_id = c.id)  WHERE c.id='.$category_id.' OR c.parent_id='.$category_id.';';
			$query = $db->query($query);

			foreach ( $query as $items )
			{
				$allowed_ids[] = $items->incident_id;
			}

		}

		// Add aditional filter here to only allow for incidents that are in the requested category
		$incident_id_in = '';
		if(count($allowed_ids) AND $category_id != 0)
		{
			$incident_id_in = ' AND id IN ('.implode(',',$allowed_ids).')';
		}
		elseif(count($allowed_ids) == 0 AND $category_id != 0)
		{
			$incident_id_in = ' AND 3 = 4';
		}

		$query = 'SELECT UNIX_TIMESTAMP('.$select_date_text.') AS time, COUNT(id) AS number FROM '.$this->table_prefix.'incident WHERE incident_active = 1 '.$incident_id_in.' GROUP BY '.$groupby_date_text;
		$query = $db->query($query);

		foreach ( $query as $items )
		{
			array_push($graph_data[0]['data'],
				array($items->time * 1000, $items->number));
		}

		echo json_encode($graph_data);
	}
	

	/**
	 * Read in new layer KML via file_get_contents
	 * @param int $layer_id - ID of the new KML Layer
	 */
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
				url::base().Kohana::config('upload.relative_directory').'/'.$layer_file :
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


	/**
	 * Read in new layer JSON from shared connection
	 * @param int $sharing_id - ID of the new Share Layer
	 */
	public function share( $sharing_id = false )
	{	
		$json = "";
		$json_item = "";
		$json_array = array();
		$sharing_data = "";
		$clustering = Kohana::config('settings.allow_clustering');
		
		if ($sharing_id)
		{
			// Get This Sharing ID Color
			$sharing = ORM::factory('sharing')
				->find($sharing_id);
			
			if( ! $sharing->loaded )
				return;
			
			$sharing_url = $sharing->sharing_url;
			$sharing_color = $sharing->sharing_color;
			
			if ($clustering)
			{
				// Database
				$db = new Database();
				
				// Start Date
				$start_date = (isset($_GET['s']) && !empty($_GET['s'])) ?
					(int) $_GET['s'] : "0";

				// End Date
				$end_date = (isset($_GET['e']) && !empty($_GET['e'])) ?
					(int) $_GET['e'] : "0";

				// SouthWest Bound
				$southwest = (isset($_GET['sw']) && !empty($_GET['sw'])) ?
					$_GET['sw'] : "0";

				$northeast = (isset($_GET['ne']) && !empty($_GET['ne'])) ?
					$_GET['ne'] : "0";
				
				// Get Zoom Level
				$zoomLevel = (isset($_GET['z']) && !empty($_GET['z'])) ?
					(int) $_GET['z'] : 8;

				//$distance = 60;
				$distance = (10000000 >> $zoomLevel) / 100000;
				
				$filter = "";
				$filter .= ($start_date) ? 
					" AND incident_date >= '" . date("Y-m-d H:i:s", $start_date) . "'" : "";
				$filter .= ($end_date) ? 
					" AND incident_date <= '" . date("Y-m-d H:i:s", $end_date) . "'" : "";

				if ($southwest && $northeast)
				{
					list($latitude_min, $longitude_min) = explode(',', $southwest);
					list($latitude_max, $longitude_max) = explode(',', $northeast);

					$filter .= " AND latitude >=".(float) $latitude_min.
						" AND latitude <=".(float) $latitude_max;
					$filter .= " AND longitude >=".(float) $longitude_min.
						" AND longitude <=".(float) $longitude_max;
				}
				
				$filter .= " AND sharing_id = ".(int)$sharing_id;

				$query = $db->query("SELECT * FROM `".$this->table_prefix."sharing_incident` WHERE 1=1 $filter ORDER BY incident_id ASC "); 

				$markers = $query->result_array(FALSE);

				$clusters = array();	// Clustered
				$singles = array();		// Non Clustered

				// Loop until all markers have been compared
				while (count($markers))
				{
					$marker	 = array_pop($markers);
					$cluster = array();

					// Compare marker against all remaining markers.
					foreach ($markers as $key => $target)
					{
						// This function returns the distance between two markers, at a defined zoom level.
						// $pixels = $this->_pixelDistance($marker['latitude'], $marker['longitude'], 
						// $target['latitude'], $target['longitude'], $zoomLevel);

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
					$json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='http://" . $sharing_url . "/reports/index/?c=0&sw=".$southwest."&ne=".$northeast."'>" . $cluster_count . " Reports</a>")) . "\",";
					$json_item .= "\"link\": \"http://".$sharing_url."reports/index/?c=0&sw=".$southwest."&ne=".$northeast."\", ";		  
					$json_item .= "\"category\":[0], ";
					$json_item .= "\"icon\": \"\", ";
					$json_item .= "\"color\": \"".$sharing_color."\", ";
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
					$json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='http://" . $sharing_url . "/reports/view/" . $single['id'] . "'>".$single['incident_title']."</a>")) . "\",";
					$json_item .= "\"link\": \"http://".$sharing_url."reports/view/".$single['id']."\", ";
					$json_item .= "\"category\":[0], ";
					$json_item .= "\"icon\": \"\", ";
					$json_item .= "\"color\": \"".$sharing_color."\", ";
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
				
			}
			else
			{
				// Retrieve all markers
				$markers = ORM::factory('sharing_incident')
										->where('sharing_id', $sharing_id)
										->find_all();

				foreach ($markers as $marker)
				{	
					$json_item = "{";
					$json_item .= "\"type\":\"Feature\",";
					$json_item .= "\"properties\": {";
					$json_item .= "\"id\": \"".$marker->incident_id."\", \n";

					$encoded_title = utf8tohtml::convert($marker->incident_title,TRUE);

					$json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='http://" . $sharing_url . "/reports/view/" . $marker->incident_id . "'>".$encoded_title."</a>")) . "\",";
					$json_item .= "\"link\": \"http://".$sharing_url."reports/view/".$marker->incident_id."\", ";
					$json_item .= "\"icon\": \"\", ";
					$json_item .= "\"color\": \"".$sharing_color ."\", \n";
					$json_item .= "\"timestamp\": \"" . strtotime($marker->incident_date) . "\"";
					$json_item .= "},";
					$json_item .= "\"geometry\": {";
					$json_item .= "\"type\":\"Point\", ";
					$json_item .= "\"coordinates\":[" . $marker->longitude . ", " . $marker->latitude . "]";
					$json_item .= "}";
					$json_item .= "}";

					array_push($json_array, $json_item);
				}

				$json = implode(",", $json_array);
			}
		}
		
		 header('Content-type: application/json; charset=utf-8');
		$this->template->json = $json;
	}


	/**
	 * Get Geometry JSON
	 * @param int $incident_id
	 * @param string $incident_title
	 * @param int $incident_date
	 * @return array $geometry
	 */
	private function _get_geometry($incident_id, $incident_title, $incident_date)
	{
		$geometry = array();
		if ($incident_id)
		{
			$db = new Database();
			// Get Incident Geometries via SQL query as ORM can't handle Spatial Data
			$sql = "SELECT id, AsText(geometry) as geometry, geometry_label, 
				geometry_comment, geometry_color, geometry_strokewidth FROM ".$this->table_prefix."geometry 
				WHERE incident_id=".$incident_id;
			$query = $db->query($sql);
			$wkt = new Wkt();

			foreach ( $query as $item )
			{
				$geom = $wkt->read($item->geometry);
				$geom_array = $geom->getGeoInterface();

				$json_item = "{";
				$json_item .= "\"type\":\"Feature\",";
				$json_item .= "\"properties\": {";
				$json_item .= "\"id\": \"".$incident_id."\", ";
				$json_item .= "\"feature_id\": \"".$item->id."\", ";

				$title = ($item->geometry_label) ? 
					utf8tohtml::convert($item->geometry_label,TRUE) : 
					utf8tohtml::convert($incident_title,TRUE);
					
				$fillcolor = ($item->geometry_color) ? 
					utf8tohtml::convert($item->geometry_color,TRUE) : "ffcc66";
					
				$strokecolor = ($item->geometry_color) ? 
					utf8tohtml::convert($item->geometry_color,TRUE) : "CC0000";
					
				$strokewidth = ($item->geometry_strokewidth) ? $item->geometry_strokewidth : "3";

				$json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='" . url::base() . "reports/view/" . $incident_id . "'>".$title."</a>")) . "\",";

				$json_item .= "\"description\": \"" . utf8tohtml::convert($item->geometry_comment,TRUE) . "\", ";
				$json_item .= "\"color\": \"" . $fillcolor . "\", ";
				$json_item .= "\"strokecolor\": \"" . $strokecolor . "\", ";
				$json_item .= "\"strokewidth\": \"" . $strokewidth . "\", ";
				$json_item .= "\"link\": \"".url::base()."reports/view/".$incident_id."\", ";
				$json_item .= "\"category\":[0], ";
				$json_item .= "\"timestamp\": \"" . strtotime($incident_date) . "\"";
				$json_item .= "},\"geometry\":".json_encode($geom_array)."}";
				$geometry[] = $json_item;
			}
		}
		
		return $geometry;
	}


	/**
	 * Convert Longitude to Cartesian (Pixels) value
	 * @param double $lon - Longitude
	 * @return int
	 */
	private function _lonToX($lon)
	{
		return round(OFFSET + RADIUS * $lon * pi() / 180);
	}

	/**
	 * Convert Latitude to Cartesian (Pixels) value
	 * @param double $lat - Latitude
	 * @return int
	 */
	private function _latToY($lat)
	{
		return round(OFFSET - RADIUS *
					log((1 + sin($lat * pi() / 180)) /
					(1 - sin($lat * pi() / 180))) / 2);
	}

	/**
	 * Calculate distance using Cartesian (pixel) coordinates
	 * @param int $lat1 - Latitude for point 1
	 * @param int $lon1 - Longitude for point 1
	 * @param int $lon2 - Latitude for point 2
	 * @param int $lon2 - Longitude for point 2
	 * @return int
	 */
	private function _pixelDistance($lat1, $lon1, $lat2, $lon2, $zoom)
	{
		$x1 = $this->_lonToX($lon1);
		$y1 = $this->_latToY($lat1);

		$x2 = $this->_lonToX($lon2);
		$y2 = $this->_latToY($lat2);

		return sqrt(pow(($x1-$x2),2) + pow(($y1-$y2),2)) >> (21 - $zoom);
	}

	/**
	 * Calculate the center of a cluster of markers
	 * @param array $cluster
	 * @return array - (center, southwest bound, northeast bound)
	 */
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
