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

	// Geometry data
	private static $geometry_data = array();

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
		$json = '';
		$json_item = array();
		$json_item_first = array();
		$json_features = array();
		$color = Kohana::config('settings.default_map_all');
		$icon = "";
		
		if (Kohana::config('settings.default_map_all_icon_id'))
		{
			$icon_object = ORM::factory('media')->find(Kohana::config('settings.default_map_all_icon_id'));
			$icon = url::convert_uploaded_to_abs($icon_object->media_medium);
		}

		$media_type = (isset($_GET['m']) AND intval($_GET['m']) > 0)? intval($_GET['m']) : 0;
		
		// Get the incident and category id
		$category_id = (isset($_GET['c']) AND intval($_GET['c']) > 0)? intval($_GET['c']) : 0;
		$incident_id = (isset($_GET['i']) AND intval($_GET['i']) > 0)? intval($_GET['i']) : 0;
		
		// Get the category colour
		if (Category_Model::is_valid_category($category_id))
		{
			// Get the color & icon
			$cat = ORM::factory('category', $category_id);
			$color = $cat->category_color;
			if ($cat->category_image)
			{
				$icon = url::convert_uploaded_to_abs($cat->category_image);
			}
		}
		
		// Fetch the incidents
		$markers = (isset($_GET['page']) AND intval($_GET['page']) > 0)? reports::fetch_incidents(TRUE) : reports::fetch_incidents();
		
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

			$link = url::base()."reports/view/".$marker->incident_id;
			$item_name = $this->_get_title($marker->incident_title, $link);

			$json_item = array();
			$json_item['type'] = 'Feature';
			$json_item['properties'] = array(
				'id' => $marker->incident_id,
				'name' => $item_name,
				'link' => $link,
				'category' => array($category_id),
				'color' => $color,
				'icon' => $icon,
				'thumb' => $thumb,
				'timestamp' => strtotime($marker->incident_date)
			);
			$json_item['geometry'] = array(
				'type' => 'Point',
				'coordinates' => array($marker->longitude, $marker->latitude)
			);

			if ($marker->incident_id == $incident_id)
			{
				$json_item_first = $json_item;
			}
			else
			{
				array_push($json_features, $json_item);
			}
			
			// Get Incident Geometries
			$geometry = $this->_get_geometry($marker->incident_id, $marker->incident_title, $marker->incident_date);
			if (count($geometry))
			{
				foreach($geometry as $g)
				{
					array_push($json_features, $g);
				}
			}
		}
		
		if ($json_item_first)
		{
			// Push individual marker in last so that it is layered on top when pulled into map
			array_push($json_features, $json_item_first);
		}
		
		Event::run('ushahidi_filter.json_index_features', $json_features);
		
		$json = json_encode(array(
			"type" => "FeatureCollection",
			"features" => $json_features
		));

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

		$json = '';
		$json_item = array();
		$json_features = array();
		$geometry_array = array();

		$color = Kohana::config('settings.default_map_all');
		$icon = "";
		
		if (Kohana::config('settings.default_map_all_icon_id'))
		{
			$icon_object = ORM::factory('media')->find(Kohana::config('settings.default_map_all_icon_id'));
			$icon = url::convert_uploaded_to_abs($icon_object->media_medium);
		}

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
			// Get the color & icon
			$cat = ORM::factory('category', $category_id);
			$color = $cat->category_color;
			if ($cat->category_image)
			{
				$icon = url::convert_uploaded_to_abs($cat->category_image);
			}
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
			$cluster_center = array_values($bounds['center']);
			$southwest = $bounds['sw']['longitude'].','.$bounds['sw']['latitude'];
			$northeast = $bounds['ne']['longitude'].','.$bounds['ne']['latitude'];

			// Number of Items in Cluster
			$cluster_count = count($cluster);
			
			// Get the time filter
			$time_filter = ( ! empty($start_date) AND ! empty($end_date))
				? "&s=".$start_date."&e=".$end_date
				: "";
			
			// Build out the JSON string
			$link = url::base()."reports/index/?c=".$category_id."&sw=".$southwest."&ne=".$northeast.$time_filter;
			$item_name = $this->_get_title($cluster_count . Kohana::lang('json.cluster_name_reports'), $link);
			
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

		foreach ($singles as $single)
		{
			$link = url::base()."reports/view/".$single['id'];
			$item_name = $this->_get_title($single['incident_title'], $link);
			
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
				'count' => 1,
			);
			$json_item['geometry'] = array(
				'type' => 'Point',
				'coordinates' => array($single['longitude'], $single['latitude']),
			);

			array_push($json_features, $json_item);
		}
		
		// 
		// E.Kala July 27, 2011
		// @todo Parking this geometry business for review
		// 
		
		// if (count($geometry_array))
		// {
		// 	$json = implode(",", $geometry_array).",".$json;
		// }
		
		Event::run('ushahidi_filter.json_cluster_features', $json_features);
		
		$json = json_encode(array(
			"type" => "FeatureCollection",
			"features" => $json_features
		));
		
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
		$json_features = array();

		$incident_id = intval($incident_id);

		// Check if incident valid/approved
		if ( ! Incident_Model::is_valid_incident($incident_id, TRUE) )
		{
			throw new Kohana_404_Exception();
		}

		// Get the neigbouring incidents
		$neighbours = Incident_Model::get_neighbouring_incidents($incident_id, FALSE, 20, 100);

		if ($neighbours)
		{
			// Load the incident
			// @todo Get this fixed
			$marker = ORM::factory('incident')->where('incident.incident_active',1)->find($incident_id);
			if ( ! $marker->loaded )
			{
				throw new Kohana_404_Exception();
			}
			
			// Get the incident/report date
			$incident_date = date('Y-m', strtotime($marker->incident_date));

			foreach ($neighbours as $row)
			{
				$link = url::base()."reports/view/".$row->id;
				$item_name = $this->_get_title($row->incident_title, $link);
				
				$json_item = array();
				$json_item['type'] = 'Feature';
				$json_item['properties'] = array(
					'id' => $row->id,
					'name' => $item_name,
					'link' => $link,
					'category' => array(0),
					'timestamp' => strtotime($row->incident_date)
				);
				$json_item['geometry'] = array(
					'type' => 'Point',
					'coordinates' => array($row->longitude, $row->latitude)
				);

				array_push($json_features, $json_item);
			}
			
			// Get Incident Geometries
			$geometry = $this->_get_geometry($marker->id, $marker->incident_title, $marker->incident_date);
			
			// If there are no geometries, use Single Incident Marker
			if ( ! count($geometry))
			{
				// Single Main Incident
				$link = url::base()."reports/view/".$marker->id;
				$item_name = $this->_get_title($marker->incident_title, $link);
	
				$json_item = array();
				$json_item['type'] = 'Feature';
				$json_item['properties'] = array(
					'id' => $marker->id,
					'name' => $item_name,
					'link' => $link,
					'category' => array(0),
					'timestamp' => strtotime($marker->incident_date)
				);
				$json_item['geometry'] = array(
					'type' => 'Point',
					'coordinates' => array($marker->location->longitude, $marker->location->latitude)
				);
				
				array_push($json_features, $json_item);
			}
			else
			{
				foreach($geometry as $g)
				{
					array_push($json_features, $g);
				}
			}
		}

		Event::run('ushahidi_filter.json_single_features', $json_features);

		$json = json_encode(array(
			"type" => "FeatureCollection",
			"features" => $json_features
		));
		
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

			if ($layer_url != '')
			{
				// Pull from a URL
				$layer_link = $layer_url;
			}else{
				// Pull from an uploaded file
				$layer_link = Kohana::config('upload.directory').'/'.$layer_file;
			}

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
			throw new Kohana_404_Exception();
		}
	}


	/**
	 * Read in new layer JSON from shared connection
	 * @param int $sharing_id - ID of the new Share Layer
	 */
	public function share( $sharing_id = false )
	{
		$json = '';
		$json_item = array();
		$json_features = array();
		$sharing_data = "";
		$clustering = Kohana::config('settings.allow_clustering');
		
		if ($sharing_id)
		{
			// Get This Sharing ID Color
			$sharing = ORM::factory('sharing')
				->find($sharing_id);
			
			if( ! $sharing->loaded )
				throw new Kohana_404_Exception();
			
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
					$cluster_center = array_values($bounds['center']);
					$southwest = $bounds['sw']['longitude'].','.$bounds['sw']['latitude'];
					$northeast = $bounds['ne']['longitude'].','.$bounds['ne']['latitude'];

					// Number of Items in Cluster
					$cluster_count = count($cluster);
					
					$link = "http://".$sharing_url."reports/index/?c=0&sw=".$southwest."&ne=".$northeast;
					$item_name = $this->_get_title($cluster_count . Kohana::lang('json.cluster_name_reports'), $link);
					
					$json_item = array();
					$json_item['type'] = 'Feature';
					$json_item['properties'] = array(
						'name' => $item_name,
						'link' => $link,
						'category' => array(0),
						'color' => $sharing_color,
						'icon' => '',
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

				foreach ($singles as $single)
				{
					$link = "http://".$sharing_url."reports/view/".$single['id'];
					$item_name = $this->_get_title($single['incident_title'], $link);
		
					$json_item = array();
					$json_item['type'] = 'Feature';
					$json_item['properties'] = array(
						'name' => $item_name,
						'link' => $link,
						'category' => array(0),
						'color' => $sharing_color,
						'icon' => '',
						'timestamp' => 0,
						'count' => 1
					);
					$json_item['geometry'] = array(
						'type' => 'Point',
						'coordinates' => array($single['longitude'],$single['latitude'])
					);

					array_push($json_features, $json_item);
				}
			}
			else
			{
				// Retrieve all markers
				$markers = ORM::factory('sharing_incident')
										->where('sharing_id', $sharing_id)
										->find_all();

				foreach ($markers as $marker)
				{
					$link = "http://".$sharing_url."reports/view/".$marker->incident_id;
					$item_name = $this->_get_title($marker->incident_title, $link);

					$json_item = array();
					$json_item['type'] = 'Feature';
					$json_item['properties'] = array(
						'id' => $marker->incident_id,
						'name' => $item_name,
						'link' => $link,
						'color' => $sharing_color,
						'icon' => '',
						'timestamp' => strtotime($marker->incident_date)
					);
					$json_item['geometry'] = array(
						'type' => 'Point',
						'coordinates' => array($marker->longitude, $marker->latitude)
					);

					array_push($json_features, $json_item);
				}
			}

			Event::run('ushahidi_filter.json_share_features', $json_features);

			$json = json_encode(array(
				"type" => "FeatureCollection",
				"features" => $json_features
			));
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
			$geom_data = $this->_get_geometry_data_for_incident($incident_id);
			$wkt = new Wkt();

			foreach ( $geom_data as $item )
			{
				$geom = $wkt->read($item->geometry);
				$geom_array = $geom->getGeoInterface();

				$title = ($item->geometry_label) ? $item->geometry_label : $incident_title;
				$link =  url::base()."reports/view/".$incident_id;
				$item_name = $this->_get_title($title, $link);
					
				$fillcolor = ($item->geometry_color) ? 
					utf8tohtml::convert($item->geometry_color,TRUE) : "ffcc66";
					
				$strokecolor = ($item->geometry_color) ? 
					utf8tohtml::convert($item->geometry_color,TRUE) : "CC0000";
					
				$strokewidth = ($item->geometry_strokewidth) ? $item->geometry_strokewidth : "3";

				$json_item = array();
				$json_item['type'] = 'Feature';
				$json_item['properties'] = array(
					'id' => $incident_id,
					'feature_id' => $item->id,
					'name' => $item_name,
					'description' => utf8tohtml::convert($item->geometry_comment,TRUE),
					'color' => $fillcolor,
					'icon' => '',
					'strokecolor' => $strokecolor,
					'strokewidth' => $strokewidth,
					'link' => $link,
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
	private function _calculateCenter($cluster)
	{
		// Calculate average lat and lon of clustered items
		$south = 90;
		$west = 180;
		$north = -90;
		$east = -180;

		$lat_sum = $lon_sum = 0;
		foreach ($cluster as $marker)
		{
			if ($marker['latitude'] < $south)
			{
				$south = $marker['latitude'];
			}

			if ($marker['longitude'] < $west)
			{
				$west = $marker['longitude'];
			}

			if ($marker['latitude'] > $north)
			{
				$north = $marker['latitude'];
			}

			if ($marker['longitude'] > $east)
			{
				$east = $marker['longitude'];
			}

			$lat_sum += $marker['latitude'];
			$lon_sum += $marker['longitude'];
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
	private function _get_title($title, $url)
	{
		$encoded_title = utf8tohtml::convert($title, TRUE);
		$encoded_title = str_ireplace('"','&#34;',$encoded_title);
		$item_name = "<a href='$url'>".$encoded_title."</a>";
		$item_name = str_replace(array(chr(10),chr(13)), ' ', $item_name);
		return $item_name;
	}
}
