<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Cluster Scheduler Controller
 * Retrieves Markers, filtering by viewport, start-date/end-date.
 * Also Clusters using K-Means algorithm. Returns clusters, levels and parent_ids.
 * Many thanks to Mike Purvis from University of Waterloo for K-Means strategies
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Cluster Controller
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */

class Cluster_Controller extends Controller
{	
	// How much to reduce the set at each level. It's important that this be low so that we get
	// lots of variation from zoom level to zoom level.
	private $mean_child_count = 4;

	// Minimimum size before we stop partitioning.
	private $min_divide_size = 10;

	// Unwind bottom-level clusters that fall below a certain size threshold. This is
	// implemented at the final stage, pruning nodes that fall below the threshold.
	private $min_cluster_size = 3;

	// Number of times to iterate k-means
	private $kmeans_iterations = 3;
	
	
	public function __construct()
    {
        parent::__construct();
		// $profiler = new Profiler;
	}
	
	
	public function index()
	{
		global $clusters_by_id;		
		
		$clusters_by_id = array();
		$root_id = $this->_nextClusterId();
		$root_children = array();		
		
		$items = ORM::factory('incident')
			->join('location', 'incident.location_id', 'location.id','INNER')
			->join('incident_category','incident.id','incident_category.incident_id','INNER')
			->join('category','incident_category.category_id','category.id','LEFT')
			->select('incident.id AS incident_id','incident.incident_title','incident.incident_date',
				'incident_category.category_id','category.category_color',
				'location.id AS location_id','latitude','longitude')
			->where('incident.incident_active = 1')
			->groupby('incident.id')
			->find_all();
			
		foreach($items as $item) 
		{
			if ($item->latitude == "0" && $item->longitude == "0") {
				// ignore incomplete rows.
				continue;
			}

			$next_id = $this->_nextClusterId();
			$clusters_by_id[$next_id] = array(
				'id' => $next_id,
				'location_id' => $item->location_id,
				'latitude' => (float)$item->latitude,
				'longitude' => (float)$item->longitude,
				'latitude_min' => (float)$item->latitude,
				'latitude_max' => (float)$item->latitude,
				'longitude_min' => (float)$item->longitude,
				'longitude_max' => (float)$item->longitude,
				'child_count' => 1,
				'incident_id' => $item->incident_id,
				'incident_title' => $item->incident_title,
				'incident_date' => $item->incident_date,
				'category_id' => $item->category_id,
				'category_color' => $item->category_color
				);
			$root_children[] = $next_id;
		}
		
		if(!empty($clusters_by_id))
		{
			$clusters_by_id[$root_id] = $this->_createCluster($root_id, $root_children);
			
			// Cluster Functions
			$this->_maybeSubdivideCluster($root_id);
			$this->_assign_left_right_recursive($root_id, 0, 0);
			
			// Empty Table
			ORM::factory('cluster')->delete_all();
			
			// Save Clusters
			foreach($clusters_by_id as $item)
			{				
				$cluster = new Cluster_Model();
				$cluster->id = $item['id'];
				$cluster->latitude = $item['latitude'];
				$cluster->longitude = $item['longitude'];
				$cluster->latitude_min = $item['latitude_min'];
				$cluster->latitude_max = $item['latitude_max'];
				$cluster->longitude_min = $item['longitude_min'];
				$cluster->longitude_max = $item['longitude_max'];
				$cluster->child_count = $item['child_count'];
				$cluster->parent_id = $item['parent_id'];
				$cluster->left_side = $item['left'];
				$cluster->right_side = $item['right'];
				$cluster->level = $item['level'];
				
				if (isset($item['location_id'])) {
					$cluster->location_id = $item['location_id'];
				}
				if (isset($item['incident_id'])) {
					$cluster->incident_id = $item['incident_id'];
					$cluster->incident_title = $item['incident_title'];
					$cluster->incident_date = strtotime($item['incident_date']);
				}
				if (isset($item['category_id'])) {
					$cluster->category_id = $item['category_id'];
					$cluster->category_color = $item['category_color'];
				}
				
				$cluster->save();			
			}		
			
		}
		else
		{ // No Results
			// Empty Table
			ORM::factory('cluster')->delete_all();
		}
	}
	
	
	/*
	Okay, now we have our all-encompassing root node, we run a recursive process that
	subdivides that root node, and for each of its children, further subdivides it if it's
	still larger than the threshold. 
	*/
	private function _maybeSubdivideCluster($cluster_id) {
	  global $clusters_by_id;

	  $this_cluster = &$clusters_by_id[$cluster_id];
	
	  if (count($this_cluster['children']) > $this->min_divide_size) {	    
		$child_clusters = $this->_doClustering($this_cluster['children'], $this->mean_child_count);

	    // Special case. If we only get a single cluster out of doClustering, then merge it with the parent
	    // cluster and proceed.
	    if (count($child_clusters) == 1) {
	      $one_cluster = reset($child_clusters);  // grab first and only item.
	      $this_cluster['children'] = array_merge($this_cluster['children'], $one_cluster['children']);
	    } else {
	      $this_cluster['children'] = array();

	      // Output of doClustering is an array of entities that each contain
	      // id, latitude, longitude, and an array of children ids.
	      foreach($child_clusters as $child_id => $child_cluster) {

	        // If one of the generated clusters only has a single child node, or its overall child_count
	        // is below the acceptable threshold, then we can discard it and hook the single child directly
	        // into the parent.	
	        $new_cluster = $this->_createCluster($child_id, $child_cluster['children']);

	        if (count($child_cluster['children']) > 1 && $new_cluster['child_count'] >= $this->min_cluster_size) {
	          $this_cluster['children'][] = $child_id;
	          $clusters_by_id[$child_id] = $new_cluster;
	          $this->_maybeSubdivideCluster($child_id);
	        } else {
	          $this_cluster['children'] = array_merge($this_cluster['children'], $new_cluster['children']);
	        }
	      }
	    }
	  }
	}
	
	
	/* 
	Final step is to provide the Left/Right values that will permit quick retrieval of this hierarchical
	structure once it's in SQL. This could just be baked into the recursive subdivision system, but it's
	cleaner to do it separately, since it really is a separate task. We also add in parent_id values, which
	simplify the recreation of the $cluster_by_id structure in the request-time code.
	*/
	function _assign_left_right_recursive($cluster_id, $parent_id, $level) {
	  static $numbering = 0;
	  global $clusters_by_id;

	  $this_cluster = &$clusters_by_id[$cluster_id];
	  $this_cluster['left'] = ++$numbering;
	  $this_cluster['level'] = $level;
	  $this_cluster['parent_id'] = $parent_id;

	  if (isset($this_cluster['children'])) {
	    foreach($this_cluster['children'] as $child_cluster_id) {
	      $this->_assign_left_right_recursive($child_cluster_id, $cluster_id, $level + 1);
	    }
	  }

	  $this_cluster['right'] = ++$numbering;

	}
	
	
	
	/*
	This is a utility function that creates a cluster's data array based on the array of children_ids,
	including calculating its center point and extents.
	*/
	private function _createCluster($new_cluster_id, $children_ids) {
	  global $clusters_by_id;

	  $latitude_total = 0;
	  $longitude_total = 0;
	  $latitude_min = 90;
	  $latitude_max = -90;
	  $longitude_min = 180;
	  $longitude_max = -180;
	  $child_count = 0;

	  foreach($children_ids as $child_id) {
	    $child_cluster = $clusters_by_id[$child_id];
	    $latitude_total += $child_cluster['latitude'];
	    $longitude_total += $child_cluster['longitude'];
	    $latitude_min = min($latitude_min, $child_cluster['latitude_min']);
	    $latitude_max = max($latitude_max, $child_cluster['latitude_max']);
	    $longitude_min = min($longitude_min, $child_cluster['longitude_min']);
	    $longitude_max = max($longitude_max, $child_cluster['longitude_max']);
	    $child_count += $child_cluster['child_count'];
	  }

	  return array(
	    'id' => $new_cluster_id,
	    'latitude' => $latitude_total / count($children_ids),
	    'longitude' => $longitude_total / count($children_ids),
	    'latitude_min' => $latitude_min,
	    'latitude_max' => $latitude_max,
	    'longitude_min' => $longitude_min,
	    'longitude_max' => $longitude_max,
	    'child_count' => $child_count,
	    'children' => $children_ids
	  );
	}



	// Helper/clustering functions
	function _nextClusterId() {
	  static $next_id = 0;
	  return ++$next_id;
	}

	private function _randomScalar() {
	  return mt_rand(0, 2e9) / 2e9;
	}
	
	

	// Expects array entities both containing latitude and longitude members.
	private function _distanceSquared($a, $b) {
	  $latitude_delta = $a['latitude'] - $b['latitude'];
	  $longitude_delta = $a['longitude'] - $b['longitude'];
	  return ($latitude_delta * $latitude_delta + $longitude_delta * $longitude_delta);
	}


	// This is the heart of k-means++
	private function _chooseSmartCenters($item_ids, $cluster_count) {
	  global $clusters_by_id;

	  $centers = array();  // tuples of array('id' => id, 'latitude' => y, 'longitude' => x), keyed to id.

	  $num_local_tries = 2 + round(log($cluster_count));

	  // first center is created based on a position chosen randomly from the input data points.
	  $random_item_id = $item_ids[array_rand($item_ids)];

	  $next_id = $this->_nextClusterId();
	  $centers[$next_id] = array(
	    'id' => $next_id, 
	    'latitude' => $clusters_by_id[$random_item_id]['latitude'], 
	    'longitude' => $clusters_by_id[$random_item_id]['longitude'],
	  );

	  // Calculate distance to each of the items
	  $closest_distance_squared = array();
	  foreach($item_ids as $id) {
	    $closest_distance_squared[$id] = $this->_distanceSquared($clusters_by_id[$id], $centers[$next_id]);
	  }
	  $current_potential = array_sum($closest_distance_squared);

	  // Choose remaining centers  
	  for ($i = 1; $i < $cluster_count; $i++) {

	    // Repeat multiple trials
	    $best_new_potential = -1;
	    $best_new_index = 0;

	    // Take multiple stabs each of the successive centers
	    for ($j = 0; $j < $num_local_tries; $j++) {
	      $rand_val = $this->_randomScalar() * $current_potential;
	      foreach ($item_ids as $id_of_potential_center) {
	        if ($rand_val <= $closest_distance_squared[$id_of_potential_center]) {
	          break;
	        } else {
	          $rand_val -= $closest_distance_squared[$id_of_potential_center];
	        }
	      }

	      // Compute new potential based on index chosen for the new center.
	      $new_potential = 0;
	      foreach ($item_ids as $id) {
	        $new_potential += min($this->_distanceSquared($clusters_by_id[$id], $clusters_by_id[$id_of_potential_center]), $closest_distance_squared[$id]);
	      }

	      if ($best_new_potential < 0 || $new_potential < $best_new_potential) {
	        $best_new_potential = $new_potential;
	        $best_new_id = $id_of_potential_center;
	      }
	    }

	    $next_id = $this->_nextClusterId();
	    $centers[$next_id] = array(
	      'id' => $next_id,
	      'latitude' => $clusters_by_id[$best_new_id]['latitude'],
	      'longitude' => $clusters_by_id[$best_new_id]['longitude'],
	    );
	    $current_potential = $best_new_potential;

	    foreach ($item_ids as $id) {
	      $closest_distance_squared[$id] = min($this->_distanceSquared($clusters_by_id[$id], $clusters_by_id[$best_new_id]), $closest_distance_squared[$id]);
	    }
	  }

	  return $centers;
	}


	private function _assignItemsToNearestCluster($item_ids, &$clusters) {
	  global $clusters_by_id;

	  foreach($clusters as &$this_cluster) {
	    $this_cluster['children'] = array();
	  }
	  unset($this_cluster);

	  foreach ($item_ids as $item_id) {
	    $nearest_id = 0;
	    $nearest_distance = -1;
	    foreach($clusters as $cluster_id => $this_cluster) {
	      $distance = $this->_distanceSquared($clusters_by_id[$item_id], $this_cluster);
	      if ($nearest_distance < 0 || $distance < $nearest_distance) {
	        $nearest_distance = $distance;
	        $nearest_id = $cluster_id;
	      }
	    }
	    $clusters[$nearest_id]['children'][] = $item_id;
	  }
	  unset($this_item);

	  // Remove any clusters that don't have items. I believe this can only really happen if the dataset has multiple points at the exact same
	  // latlng. Under these conditions, it's impossible to further subdivide... all items get thrown into the first cluster created, and the
	  // others remain empty.
	  $cluster_ids = array_keys($clusters);
	  foreach($cluster_ids as $cluster_id) {
	    if (count($clusters[$cluster_id]['children']) < 1) {
	      unset($clusters[$cluster_id]);
	    }
	  }

	  //return $changed;
	}


	// If any of the clusters doesn't contain any member points, this function removes
	// it from the array.
	private function _computeClusterCenters(&$clusters) {
	  global $clusters_by_id;

	  foreach ($clusters as &$this_cluster) {
	    $count = count($this_cluster['children']);
	    if ($count < 1) {
	      unset($this_cluster);
	      continue;
	    }

	    $latitude_total = 0;
	    $longitude_total = 0;

	    foreach($this_cluster['children'] as $child_cluster_id) {
	      $latitude_total += $clusters_by_id[$child_cluster_id]['latitude'];
	      $longitude_total += $clusters_by_id[$child_cluster_id]['longitude'];      
	    }

	    $this_cluster['latitude'] = $latitude_total / $count;
	    $this_cluster['longitude'] = $longitude_total / $count;
	  }
	  unset($this_cluster);
	}


	// Use k-means++ smart selection to pick our starting centers, then ordinary
	// k-means to create clusters.
	private function _doClustering($item_ids, $clusters_to_find) {	
	  $centers = $this->_chooseSmartCenters($item_ids, $clusters_to_find);
	  $this->_assignItemsToNearestCluster($item_ids, $centers);  

	  // Now that we have the starting centers, we can use those to run the ordinary k-means.
	  for($i = 0; $i < $this->kmeans_iterations; $i++) {
	    $this->_computeClusterCenters($centers);
	    if (!$this->_assignItemsToNearestCluster($item_ids, $centers))  break;  // returns false if nothing changed.
	  }

	  return $centers;
	}
	
}