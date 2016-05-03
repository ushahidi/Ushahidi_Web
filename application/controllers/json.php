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

class Json_Controller extends Template_Controller {

	/**
	 * Disable automatic rendering
	 * @var bool
	 */
	public $auto_render = FALSE;

	/**
	 * Template for this controller
	 * @var string
	 */
	public $template = '';

	/**
	 * Database table prefix
	 * @var string
	 */
	protected $table_prefix;

	/**
	 * Geometry data
	 * @var array
	 */
	private static $geometry_data = array();


	public function __construct()
	{
		parent::__construct();
		
		// Disable profiler
		if (isset($this->profiler))
		{
			$this->profiler->disable();
		}

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
		$this->geojson('markers');
	}

	/**
	 * Generate JSON in CLUSTER mode
	 */
	public function cluster()
	{
		$this->geojson('clusters');
	}
	
	/**
	 * Generate geojson
	 * 
	 * @param string $type type of geojson to generate. Valid options are: 'clusters' and 'markers'
	 **/
	protected function geojson($type)
	{
		$color = Kohana::config('settings.default_map_all');
		$icon = "";
		$markers = FALSE;
		
		if (Kohana::config('settings.default_map_all_icon_id'))
		{
			$icon_object = ORM::factory('media')->find(Kohana::config('settings.default_map_all_icon_id'));
			$icon = url::convert_uploaded_to_abs($icon_object->media_medium);
		}
		
		// Category ID
		$category_id = (isset($_GET['c']) AND intval($_GET['c']) > 0) ? intval($_GET['c']) : 0;
		// Get the category colour
		if (Category_Model::is_valid_category($category_id))
		{
			// Get the color & icon
			$cat = ORM::factory('category', $category_id);
			$color = $cat->category_color;
			$icon = "";
			if ($cat->category_image)
			{
				$icon = url::convert_uploaded_to_abs($cat->category_image);
			}
		}
		
		$params = array('color' => $color, 'icon' => $icon);
		Event::run('ushahidi_filter.json_alter_params', $params);
		$color = $params['color'];
		$icon = $params['icon'];

		// Run event ushahidi_filter.json_replace_markers
		// This allows a plugin to completely replace $markers
		// If markers are added at this point we don't bother fetching incidents at all
		Event::run('ushahidi_filter.json_replace_markers', $markers);

		// Fetch the incidents
		if (! $markers)
		{
			$markers = (isset($_GET['page']) AND intval($_GET['page']) > 0)
			    ? reports::fetch_incidents(TRUE)
			    : reports::fetch_incidents();
		}
		
		// Run event ushahidi_filter.json_alter_markers
		// This allows a plugin to alter $markers
		// Plugins can add or remove markers as needed
		Event::run('ushahidi_filter.json_alter_markers', $markers);
		
		// Get geojson features array
		$function = "{$type}_geojson";
		$json_features = $this->$function($markers, $category_id, $color, $icon);
		
		$this->render_geojson($json_features);
	}

	/**
	 * Render geojson features array to geojson and output
	 * 
	 * @param array $json_features geojson features array
	 **/
	protected function render_geojson($json_features)
	{
		// Run event ushahidi_filter.json_features
		// Allow plugins to alter json array before its rendered
		Event::run('ushahidi_filter.json_features', $json_features);
		
		$json = json_encode(array(
			"type" => "FeatureCollection",
			"features" => $json_features
		));
		
		header('Content-type: application/json; charset=utf-8');
		echo $json;
	}

	/**
	 * Generate GEOJSON from incidents
	 * 
	 * @param ORM_Iterator|Database_Result|array $incidents collection of incidents
	 * @param int $category_id
	 * @param string $color
	 * @param string $icon
	 * @return array $json_features geojson features array
	 **/
	protected function markers_geojson($incidents, $category_id, $color, $icon, $include_geometries = TRUE)
	{
		$json_features = array();
		
		// Extra params for markers only
		// Get the incidentid (to be added as first marker)
		$first_incident_id = (isset($_GET['i']) AND intval($_GET['i']) > 0)? intval($_GET['i']) : 0;
		
		$media_type = (isset($_GET['m']) AND intval($_GET['m']) > 0)? intval($_GET['m']) : 0;
		
		foreach ($incidents as $marker)
		{
			// Handle both reports::fetch_incidents() response and actual ORM objects
			$marker->id = isset($marker->incident_id) ? $marker->incident_id : $marker->id;
			if (isset($marker->latitude) AND isset($marker->longitude))
			{
				$latitude = $marker->latitude;
				$longitude = $marker->longitude;
			}
			elseif (isset($marker->location) AND isset($marker->location->latitude) AND isset($marker->location->longitude))
			{
				$latitude = $marker->location->latitude;
				$longitude = $marker->location->longitude;
			}
			else
			{
				// No location - skip this report
				continue;
			}
			
			// Get thumbnail
			$thumb = "";
			$media = ORM::factory('incident', $marker->id)->media;
			if ($media->count())
			{
				foreach ($media as $photo)
				{
					if ($photo->media_thumb)
					{
						// Get the first thumb
						$thumb = url::convert_uploaded_to_abs($photo->media_thumb);
						break;
					}
				}
			}

			// Get URL from object, fallback to Incident_Model::get() if object doesn't have url or url()
			if (method_exists($marker, 'url'))
			{
				$link = $marker->url();
			}
			elseif (isset($marker->url))
			{
				$link = $marker->url;
			}
			else
			{
				$link = Incident_Model::get_url($marker);
			}
			$item_name = $this->get_title($marker->incident_title, $link);

			$json_item = array();
			$json_item['type'] = 'Feature';
			$json_item['properties'] = array(
				'id' => $marker->id,
				'name' => $item_name,
				'link' => $link,
				'category' => array($category_id),
				'color' => $color,
				'icon' => $icon,
				'thumb' => $thumb,
				'timestamp' => strtotime($marker->incident_date),
				'count' => 1,
				'class' => get_class($marker),
				'title'  => $marker->incident_title
			);
			$json_item['geometry'] = array(
				'type' => 'Point',
				'coordinates' => array((float)$longitude, (float)$latitude)
			);

			if ($marker->id == $first_incident_id)
			{
				array_unshift($json_features, $json_item);
			}
			else
			{
				array_push($json_features, $json_item);
			}
			
			// Get Incident Geometries
			if ($include_geometries)
			{
				$geometry = $this->get_geometry($marker->id, $marker->incident_title, $marker->incident_date, $link);
				if (count($geometry))
				{
					foreach ($geometry as $g)
					{
						array_push($json_features, $g);
					}
				}
			}
		}
		
		Event::run('ushahidi_filter.json_index_features', $json_features);
		
		return $json_features;
	}

	/**
	 * Generate clustered GEOJSON from incidents
	 * 
	 * @param ORM_Iterator|Database_Result|array $incidents collection of incidents
	 * @param int $category_id
	 * @param string $color
	 * @param string $icon
	 * @return array $json_features geojson features array
	 **/
	protected function clusters_geojson($incidents, $category_id, $color, $icon)
	{
		$json_features = array();
		
		// Extra params for clustering
		// Start date
		$start_date = (isset($_GET['s']) AND intval($_GET['s']) > 0) ? intval($_GET['s']) : NULL;
		
		// End date
		$end_date = (isset($_GET['e']) AND intval($_GET['e']) > 0) ? intval($_GET['e']) : NULL;
		
		// Get Zoom Level
		$zoomLevel = (isset($_GET['z']) AND !empty($_GET['z'])) ? (int) $_GET['z'] : 8;
		$distance = (10000000 >> $zoomLevel) / 100000;
		
		// Get markers array
		if ($incidents instanceof ORM_Iterator)
		{
			$markers = $incidents->as_array();
		}
		elseif ($incidents instanceof Database_Result)
		{
			$markers = $incidents->result_array();
		}
		else
		{
			$markers = $incidents;
		}

		$clusters = array();	// Clustered
		$singles = array();	// Non Clustered

		// Loop until all markers have been compared
		while (count($markers))
		{
			$marker	 = array_pop($markers);
			$cluster = array();
			
			// Handle both reports::fetch_incidents() response and actual ORM objects
			$marker->id = isset($marker->incident_id) ? $marker->incident_id : $marker->id;
			if (isset($marker->latitude) AND isset($marker->longitude))
			{
				$marker_latitude = $marker->latitude;
				$marker_longitude = $marker->longitude;
			}
			elseif (isset($marker->location) AND isset($marker->location->latitude) AND isset($marker->location->longitude))
			{
				$marker_latitude = $marker->location->latitude;
				$marker_longitude = $marker->location->longitude;
			}
			else
			{
				// No location - skip this report
				continue;
			}

			// Compare marker against all remaining markers.
			foreach ($markers as $key => $target)
			{
				// Handle both reports::fetch_incidents() response and actual ORM objects
				if (isset($target->latitude) AND isset($target->longitude))
				{
					$target_latitude = $target->latitude;
					$target_longitude = $target->longitude;
				}
				elseif (isset($target->location) AND isset($target->location->latitude) AND isset($target->location->longitude))
				{
					$target_latitude = $target->location->latitude;
					$target_longitude = $target->location->longitude;
				}
				else
				{
					// No location - skip this report
					continue;
				}
				
				// This function returns the distance between two markers, at a defined zoom level.
				// $pixels = $this->_pixelDistance($marker['latitude'], $marker['longitude'],
				// $target['latitude'], $target['longitude'], $zoomLevel);

				$pixels = abs($marker_longitude - $target_longitude) +
					abs($marker_latitude - $target_latitude);
					
				// If two markers are closer than defined distance, remove compareMarker from array and add to cluster.
				if ($pixels < $distance)
				{
					unset($markers[$key]);
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
			$bounds = $this->calculate_center($cluster);
			$cluster_center = array_values($bounds['center']);
			$southwest = $bounds['sw']['longitude'].','.$bounds['sw']['latitude'];
			$northeast = $bounds['ne']['longitude'].','.$bounds['ne']['latitude'];

			// Number of Items in Cluster
			$cluster_count = count($cluster);
			
			// Get the time filter
			$time_filter = ( ! empty($start_date) AND ! empty($end_date))
				? "&s=".$start_date."&e=".$end_date
				: "";
			
			// Build query string for title link, passing through any GET params
			// This allows plugins to extend more easily
			$query = http_build_query(array_merge(
				array(
					'sw' => $southwest,
					'ne' => $northeast
				),
				$_GET
			));
			
			// Build out the JSON string
			$link = url::site("reports/index/?$query");
			$item_name = $this->get_title(Kohana::lang('ui_main.reports_count', $cluster_count), $link);
			
			$json_item = array();
			$json_item['type'] = 'Feature';
			$json_item['properties'] = array(
				'name' => $item_name,
				'link' => $link,
				'category' => array($category_id),
				'color' => $color,
				'icon' => $icon,
				'thumb' => '',
				'timestamp' => 0,
				'count' => $cluster_count,
			);
			$json_item['geometry'] = array(
				'type' => 'Point',
				'coordinates' => $cluster_center
			);

			array_push($json_features, $json_item);
		}

		// Pass single points to standard markers json
		$json_features = array_merge($json_features, $this->markers_geojson($singles, $category_id, $color, $icon, FALSE));
		
		// 
		// E.Kala July 27, 2011
		// @todo Parking this geometry business for review
		// 
		/*
		//Get Incident Geometries
		$geometry = $this->_get_geometry($marker->incident_id, $marker->incident_title, $marker->incident_date);
		if (count($geometry))
		{
			foreach ($geometry as $g)
			{
				array_push($json_features, $g);
			}
		}
		*/
		
		Event::run('ushahidi_filter.json_cluster_features', $json_features);
		
		return $json_features;
	}

	/**
	 * Retrieve Single Marker (and its neighbours)
	 * 
	 * @param int $incident_id
	 */
	public function single($incident_id = 0)
	{
		$json_features = array();

		$incident_id = intval($incident_id);

		// Check if incident valid/approved
		if ( ! Incident_Model::is_valid_incident($incident_id, TRUE) )
		{
			throw new Kohana_404_Exception();
		}

		// Load the incident
		// @todo avoid the double load here
		$marker = ORM::factory('incident')->where('incident.incident_active', 1)->with('location')->find($incident_id);
		if ( ! $marker->loaded )
		{
			throw new Kohana_404_Exception();
		}
		
		// Get geojson features for main incident (including geometry) 
		$json_features = $this->markers_geojson(array($marker), 0, null, null, TRUE);

		// Get the neigbouring incidents & their json (without geometries)
		$neighbours = Incident_Model::get_neighbouring_incidents($incident_id, FALSE, 20, 100);
		if ($neighbours)
		{
			$json_features = array_merge($json_features, $this->markers_geojson($neighbours, 0, null, null, FALSE));
		}

		Event::run('ushahidi_filter.json_single_features', $json_features);

		$this->render_geojson($json_features);
	}

	/**
	 * Retrieve timeline JSON
	 */
	public function timeline($category_id = 0)
	{
		$category_id = (isset($_GET["c"]) AND ! empty($_GET["c"])) ? (int) $_GET["c"] : (int) $category_id; // HT: set category from url param is 'c' set

		$this->auto_render = FALSE;
		$db = new Database();

		$interval = (isset($_GET["i"]) AND ! empty($_GET["i"]))
		    ? $_GET["i"]
		    : "month";

		// Get Category Info
		$category_title = "All Categories";
		$category_color = "#990000";
		if ($category_id > 0)
		{
			$category = ORM::factory("category", $category_id);
			if ($category->loaded)
			{
				$category_title = $category->category_title;
				$category_color = "#".$category->category_color;
			}
		}

		// Change select / group by expression based on interval
		// Not a great way to do this but can't think of a better option
		// Default values: month
		$select_date_text = "DATE_FORMAT(i.incident_date, '%Y-%m-01')";
		$groupby_date_text = "DATE_FORMAT(i.incident_date, '%Y%m')";
		if ($interval == 'day')
		{
			$select_date_text = "DATE_FORMAT(i.incident_date, '%Y-%m-%d')";
			$groupby_date_text = "DATE_FORMAT(i.incident_date, '%Y%m%d')";
		}
		elseif ($interval == 'hour')
		{
			$select_date_text = "DATE_FORMAT(i.incident_date, '%Y-%m-%d %H:%M')";
			$groupby_date_text = "DATE_FORMAT(i.incident_date, '%Y%m%d%H')";
		}
		elseif ($interval == 'week')
		{
			$select_date_text = "STR_TO_DATE(CONCAT(CAST(YEARWEEK(i.incident_date) AS CHAR), ' Sunday'), '%X%V %W')";
			$groupby_date_text = "YEARWEEK(i.incident_date)";
		}

		$graph_data = array();
		$graph_data[0] = array();
		$graph_data[0]['label'] = $category_title;
		$graph_data[0]['color'] = $category_color;
		$graph_data[0]['data'] = array();

		// Gather allowed ids if we are looking at a specific category
		$incident_id_in = '';
		$params = array();
		if ($category_id != 0)
		{
			$query = 'SELECT ic.incident_id AS id '
			    . 'FROM '.$this->table_prefix.'incident_category ic '
			    . 'INNER JOIN '.$this->table_prefix.'category c ON (ic.category_id = c.id) '
			    . 'WHERE (c.id = :cid OR c.parent_id = :cid)';
			
			$params[':cid'] = $category_id;
			$incident_id_in .= " AND i.id IN ( $query ) ";
		}
		
		// Apply start and end date filters
		if (isset($_GET['s']) AND isset($_GET['e']))
		{
			$query = 'SELECT filtered_incidents.id FROM '.$this->table_prefix.'incident AS filtered_incidents '
			    . 'WHERE filtered_incidents.incident_date >= :datestart '
			    . 'AND filtered_incidents.incident_date <= :dateend ';

			// Cast timestamps to int to avoid php error - they'll be sanitized again by db_query
			$params[':datestart'] = date("Y-m-d H:i:s", (int)$_GET['s']);
			$params[':dateend'] = date('Y-m-d H:i:s', (int)$_GET['e']);
			$incident_id_in .= " AND i.id IN ( $query ) ";
		}

		// Apply media type filters
		if (isset($_GET['m']) AND intval($_GET['m']) > 0)
		{
			$query = "SELECT incident_id AS id FROM ".$this->table_prefix."media "
			    . "WHERE media_type = :mtype ";

			$params[':mtype'] = $_GET['m'];
			$incident_id_in .= " AND i.id IN ( $query ) ";
		}

		// Fetch the timeline data
		$query = 'SELECT UNIX_TIMESTAMP('.$select_date_text.') AS time, COUNT(i.id) AS number '
		    . 'FROM '.$this->table_prefix.'incident AS i '
		    . 'WHERE i.incident_active = 1 '.$incident_id_in.' '
		    . 'GROUP BY '.$groupby_date_text;

		foreach ($db->query($query, $params) as $items)
		{
			array_push($graph_data[0]['data'], array($items->time * 1000, $items->number));
		}
		
		// If no data fake a flat line graph
		// This is so jqplot still plots something
		if (count($graph_data[0]['data']) == 0)
		{
			array_push($graph_data[0]['data'], array((int)$_GET['s'] * 1000, 0));
			array_push($graph_data[0]['data'], array((int)$_GET['e'] * 1000, 0));
		}
		// HT: If only one point append start and end with 0 unless start or end has value
		elseif (count($graph_data[0]['data']) == 1) { 
			$start = $end = false;
			if($graph_data[0]['data'][0][0] == (int)$_GET['s']) $start = true;
			if($graph_data[0]['data'][0][0] == (int)$_GET['e']) $end = true;
			if(!$start) array_unshift($graph_data[0]['data'], array((int)$_GET['s'] * 1000, 0));
			if(!$end) array_push($graph_data[0]['data'], array((int)$_GET['e'] * 1000, 0));
		}

		// Debug: push the query back in json
		//$graph_data['query'] = $db->last_query();

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($graph_data);
	}
	

	/**
	 * Read in new layer KML via HttpClient
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

			$content === FALSE;
			
			if ($layer_url != '')
			{
				// Pull from a URL
				$layer_link = $layer_url;
        			$layer_request = new HttpClient($layer_link);
        			$content = $layer_request->execute();
        			if ($content === FALSE) 
				{
					throw new Kohana_Exception($layer_request->get_error_msg());
				}
			}
			else
			{
				// Pull from an uploaded file
				$layer_link = Kohana::config('upload.directory').'/'.$layer_file;
				$content = readfile($layer_link);
				if ($content === FALSE) 
				{
					throw new Kohana_Exception("Couldn't read KML file: " . $layer_link);
				}
			}

			echo $content;
		}
		else
		{
			throw new Kohana_404_Exception();
		}
	}

	/**
	 * Get Geometry JSON
	 * @param int $incident_id
	 * @param string $incident_title
	 * @param int $incident_date
	 * @param string $incident_link
	 * @return array $geometry
	 */
	protected function get_geometry($incident_id, $incident_title, $incident_date, $incident_link)
	{
		$geometry = array();
		if ($incident_id)
		{
			$geom_data = $this->_get_geometry_data_for_incident($incident_id);
			$wkt = new Wkt();

			foreach ( $geom_data as $item )
			{
				$geom = $wkt->read($item->geometry);
				$geom_array = $geom->getGeoInterface();

				$title = ($item->geometry_label) ? $item->geometry_label : $incident_title;
				$item_name = $this->get_title($title, $incident_link);
					
				$fillcolor = ($item->geometry_color) ? $item->geometry_color : "ffcc66";
					
				$strokecolor = ($item->geometry_color) ? $item->geometry_color : "CC0000";
					
				$strokewidth = ($item->geometry_strokewidth) ? $item->geometry_strokewidth : "3";

				$json_item = array();
				$json_item['type'] = 'Feature';
				$json_item['properties'] = array(
					'id' => $incident_id,
					'feature_id' => $item->id,
					'name' => $item_name,
					'description' => $item->geometry_comment,
					'color' => $fillcolor,
					'icon' => '',
					'strokecolor' => $strokecolor,
					'strokewidth' => $strokewidth,
					'link' => $incident_link,
					'category' => array(0),
					'timestamp' => strtotime($incident_date),
				);
				$json_item['geometry'] = $geom_array;

				$geometry[] = $json_item;
			}
		}

		return $geometry;
	}


	/**
	 * Get geometry records from the database and cache 'em.
	 *
	 * They're heavily read from, no point going back to the db constantly to
	 * get them.
	 * @param int $incident_id - Incident to get geometry for
	 * @return array
	 */
	public function _get_geometry_data_for_incident($incident_id) {
		if (self::$geometry_data) {
			return isset(self::$geometry_data[$incident_id]) ? self::$geometry_data[$incident_id] : array();
		}

		$db = new Database();
		// Get Incident Geometries via SQL query as ORM can't handle Spatial Data
		$sql = "SELECT id, incident_id, AsText(geometry) as geometry, geometry_label, 
			geometry_comment, geometry_color, geometry_strokewidth FROM ".$this->table_prefix."geometry";
		$query = $db->query($sql);

		foreach ( $query as $item )
		{
			self::$geometry_data[$item->incident_id][] = $item;
		}

		return isset(self::$geometry_data[$incident_id]) ? self::$geometry_data[$incident_id] : array();
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
	protected function calculate_center($cluster)
	{
		// Calculate average lat and lon of clustered items
		$south = 90;
		$west = 180;
		$north = -90;
		$east = -180;

		$lat_sum = $lon_sum = 0;
		foreach ($cluster as $marker)
		{
			// Normalising data
			if (is_array($marker))
			{
				$marker = (object) $marker;
			}

			// Handle both reports::fetch_incidents() response and actual ORM objects
			$latitude = isset($marker->latitude) ? $marker->latitude : $marker->location->latitude;
			$longitude = isset($marker->longitude) ? $marker->longitude : $marker->location->longitude;
			
			if ($latitude < $south)
			{
				$south = $latitude;
			}

			if ($longitude < $west)
			{
				$west = $longitude;
			}

			if ($latitude > $north)
			{
				$north = $latitude;
			}

			if ($longitude > $east)
			{
				$east = $longitude;
			}

			$lat_sum += $latitude;
			$lon_sum += $longitude;
		}
		$lat_avg = $lat_sum / count($cluster);
		$lon_avg = $lon_sum / count($cluster);

		$center = array('longitude' => $lon_avg, 'latitude' => $lat_avg);
		$sw = array('longitude' => $west,'latitude' => $south);
		$ne = array('longitude' => $east,'latitude' => $north);

		return array(
			"center"=>$center,
			"sw"=>$sw,
			"ne"=>$ne
		);
	}
	
	/**
	 * Get encoded title linked to url
	 * @param string $title - Item title
	 * @param string $url - URL to link to
	 * @return string
	 */
	protected function get_title($title, $url)
	{
		$item_name = "<a href='$url'>".$title."</a>";
		$item_name = str_replace(array(chr(10),chr(13)), ' ', $item_name);
		return $item_name;
	}
}
