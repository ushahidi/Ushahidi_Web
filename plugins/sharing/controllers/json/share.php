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

class Share_Controller extends Json_Controller {

	/**
	 * Read in new layer JSON from shared connection
	 * @param int $sharing_id - ID of the new Share Layer
	 */
	public function index( $sharing_id = FALSE )
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

			if( ! $sharing->loaded)
				throw new Kohana_404_Exception();

			$sharing_url = sharing_helper::clean_url($sharing->sharing_url);
			$sharing_color = $sharing->sharing_color;
			
			if ($clustering)
			{
				// Database
				$db = new Database();
				
				// Start Date
				$start_date = (isset($_GET['s']) AND !empty($_GET['s'])) ?
					(int) $_GET['s'] : "0";

				// End Date
				$end_date = (isset($_GET['e']) AND !empty($_GET['e'])) ?
					(int) $_GET['e'] : "0";

				// SouthWest Bound
				$southwest = (isset($_GET['sw']) AND !empty($_GET['sw'])) ?
					$_GET['sw'] : "0";

				$northeast = (isset($_GET['ne']) AND !empty($_GET['ne'])) ?
					$_GET['ne'] : "0";
				
				// Get Zoom Level
				$zoomLevel = (isset($_GET['z']) AND !empty($_GET['z'])) ?
					(int) $_GET['z'] : 8;

				//$distance = 60;
				$distance = (10000000 >> $zoomLevel) / 100000;
				
				$filter = "";
				$filter .= ($start_date) ? 
					" AND incident_date >= '" . date("Y-m-d H:i:s", $start_date) . "'" : "";
				$filter .= ($end_date) ? 
					" AND incident_date <= '" . date("Y-m-d H:i:s", $end_date) . "'" : "";

				if ($southwest AND $northeast)
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
					$bounds = $this->calculate_center($cluster);
					$cluster_center = array_values($bounds['center']);
					$southwest = $bounds['sw']['longitude'].','.$bounds['sw']['latitude'];
					$northeast = $bounds['ne']['longitude'].','.$bounds['ne']['latitude'];

					// Number of Items in Cluster
					$cluster_count = count($cluster);
					$link = $sharing_url."/reports/index/?c=0&sw=".$southwest."&ne=".$northeast;
					$item_name = $this->get_title(Kohana::lang('ui_main.reports_count', $cluster_count), $link);
					
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
					$link = $sharing_url."/reports/view/".$single['id'];
					$item_name = $this->get_title(html::specialchars(strip_tags($single['incident_title'])), $link);
		
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
					$link = $sharing_url."/reports/view/".$marker->incident_id;
					$item_name = $this->get_title($marker->incident_title, $link);

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
		echo $json;
	}

}
