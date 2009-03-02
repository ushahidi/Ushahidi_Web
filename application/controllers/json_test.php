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

class Json_Test_Controller extends Template_Controller
{
    public $auto_render = TRUE;
	
    // Main template
    public $template = 'json';

	public function __construct()
	{
		parent::__construct();
	
		// $profiler = New Profiler;		
	}
	
    public function index()
    {
		global $clusters_by_id, $category_id, $start_date, $end_date;
		
		$southwest = "";
		$northeast = "";
		$start_date = "";
		$end_date = "";
		$category_id = "0";
		$incident_id = "0";
		$neighboring = "";
		
		$fetch_generations = 5;
		$viewport_points_upper_limit = 80;
		
        $json = "";
        $json_item = "";
        $json_array = array();
        $cat_array = array();
        $color = "000000";
		
		
		
		// Zoom Level
		// Adjust $viewport_points_upper_limit based on zoom
		if (isset($_GET['z']) && !empty($_GET['z']))
		{
			$zoom = $_GET['z'];
			if ($zoom <= 6)
			{
				$viewport_points_upper_limit = 30;
			}
			elseif ($zoom <= 4)
			{
				$viewport_points_upper_limit = 20;
			}
			elseif ($zoom <= 2)
			{
				$viewport_points_upper_limit = 15;
			}
		}
		
		// SouthWest Bound
		if (isset($_GET['sw']) && !empty($_GET['sw']))
		{
			$southwest = $_GET['sw'];
		}
		
		// NorthEast Bound
		if (isset($_GET['ne']) && !empty($_GET['ne']))
		{
			$northeast = $_GET['ne'];
		}
				
		// Category ID
		if (isset($_GET['c']) && !empty($_GET['c']) && 
			is_numeric($_GET['c']) && $_GET['c'] != 0)
		{
			$category_id = $_GET['c'];
		}
		
		// Incident ID
		if (isset($_GET['i']) && !empty($_GET['i']))
		{
			$incident_id = $_GET['i'];
		}
		
		// Retrieve Neighboring Markers?
		if (isset($_GET['n']) && !empty($_GET['n']))
		{
			$neighboring = $_GET['n'];
		}
		
		// Start Date
		if (isset($_GET['s']) && !empty($_GET['s'])) {
        	$start_date = $_GET['s']; 
        }
		
		// End Date
		if (isset($_GET['e']) && !empty($_GET['e'])) {
        	$end_date = $_GET['e'];
        }		
		
		
		// Firstly, we need to find the smallest cluster that fully encloses the given viewport. This
		// approach won't work across the dateline, which is why we need to first yank down the viewport
		// to be on one side or the other of it. A more intelligent approach might be to run two separate
		// queries, one for each side of the line. That approach still excludes the possibility of any
		// clusters straddling the dateline, but that's a whole different issue, since we know already
		// that the data doesn't contain anything like this.
		list($latitude_min, $longitude_min) = explode(',', $southwest);
		list($latitude_max, $longitude_max) = explode(',', $northeast);

		if ($longitude_min > $longitude_max) {
		  if (abs($longitude_min) > abs($longitude_max)) {
		    $longitude_min = -179.99;
		  } else {
		    $longitude_max = 179.99;
		  }
		}
		
		$viewport_query = ORM::factory('cluster')
			->where('latitude_min <= ' . $latitude_min)
			->where('latitude_max >= ' . $latitude_max)
			->where('longitude_min <= ' . $longitude_min)
			->where('longitude_max >= ' . $longitude_max)
			->orderby('level', 'DESC')
			->find();
		
		
		// If we can't find a singular cluster that encloses the whole viewport, we instead
		// grab the "max" cluster and work from there.
		if ($viewport_query->loaded == true)
		{
			$parent_cluster = $viewport_query;
		}
		else
		{
			$parent_cluster = ORM::factory('cluster')
				->where('level','0')
				->find();
		}
		
		
		// Now that we know which cluster encloses the viewport, we can fetch all children
		// of that view, up to a certain depth. The ordering is important here---we sort first
		// by level, and secondarily by the number of children in each node. This is providing
		// the order in which the clusters will be expanded: higher-level ones first, and
		// within each level, ones that are larger first.
		// The exlusion by lat/lng here is a little different than the other one. In this case,
		// we only want to eliminate sub-clusters that are *entirely* outside the viewport.
		$root_level = $parent_cluster->level;
		$min_level = $root_level + $fetch_generations;
		
		$children_query = ORM::factory('cluster')
			->where('left_side >= ' . $parent_cluster->left_side)
			->where('right_side <= ' . $parent_cluster->right_side)
			->where('level <= ' . $min_level)
			->where(' NOT ( '.
				' latitude_min > '. $latitude_max .
				' OR latitude_max < ' . $latitude_min .
				' OR longitude_min > ' . $longitude_max .
				' OR longitude_max < ' . $longitude_min . ' )')
			->orderby('level', 'ASC')
			->orderby('child_count', 'DESC')
			->find_all();
		
		$children_columns = ORM::factory('cluster')->table_columns;
		
		// Now we break up the flat list of fetched ids into an array structure keyed to 
		// the id, with each cluster containing an array of the ids of its children.
		$clusters_by_id = array();
		$root_cluster_id = 0;
		$all_clusters = array();
		foreach ($children_query as $child) 
		{
			$this_child = array();
			foreach ($children_columns as $key => $value) {
				$this_child[$key] = $child->$key;
			}
			$all_clusters[] = $this_child;
		}
		// print_r($all_clusters);
		
		foreach ($all_clusters as $this_cluster)
		{
			// print_r($this_cluster);
			$this_cluster_id = $this_cluster['id'];						
			$this_cluster['children'] = array();
			$this_cluster['included'] = false;
			$clusters_by_id[$this_cluster_id] = $this_cluster;
			if ($this_cluster['level'] == $root_level) {
				$root_cluster_id = $this_cluster_id;
			} else {
				$clusters_by_id[$this_cluster['parent_id']]['children'][] = $this_cluster_id;
			}
		}
		// print_r($clusters_by_id);
		
		
		// APPLY FILTERS
		$this->_filter_date();
		$this->_filter_category();
		
		// Finally we have to pick out the ones we want to actually show. The basic process here
		// that we start with the base node and then iterate, each time checking if we can expand the 
		// next cluster while still staying under the ceiling imposed by $viewport_points_upper_limit.

		$total = 1;
		$clusters_by_id[$root_cluster_id]['included'] = true;

		foreach($clusters_by_id as &$this_cluster) {
			if ($this_cluster['included']) {
				if ($total + count($this_cluster['children']) - 1 <= $viewport_points_upper_limit) {
					if (count($this_cluster['children']) > 0) {
						$total += count($this_cluster['children']) - 1;
						foreach($this_cluster['children'] as $included_id) {
							$clusters_by_id[$included_id]['included'] = true;
						}
						$this_cluster['included'] = false;
					}
				} else {
					break;  // If it doesn't fit, then we're done.
				}
			}
		}
		unset($this_cluster);  // Required for foreach with reference.		
		
		
		// print_r($clusters_by_id);
		
		//echo "/* ".count($clusters_by_id)." clusters from SQL, chose ".$total." for display. */\n";
		
		// Retrieve all markers
		foreach($clusters_by_id as $marker)
		{
			if ($marker['included'])
			{	
			    $json_item = "{";
			    $json_item .= "\"type\":\"Feature\",";
			    $json_item .= "\"properties\": {";
				if ($marker['child_count'] > 1)
				{ // Get Properties from Children
					$json_item .= "\"name\":\"" . htmlentities($marker['child_count']) . " Reports" . "\",";		
				    $json_item .= "\"category\":[0], ";
					$json_item .= "\"color\": \"990000\", ";
				    $json_item .= "\"timestamp\": \"0\", ";
				}
				else
				{ // Single point... not Cluster
					$json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='" . url::base() . "reports/view/" . $marker['incident_id'] . "'>" . htmlentities($marker['incident_title']) . "</a>")) . "\",";		
				    $json_item .= "\"category\":[" . $marker['category_id'] . "], ";
					$json_item .= "\"color\": \"" . $marker['category_color'] . "\", ";
				    $json_item .= "\"timestamp\": \"" . $marker['incident_date'] . "\", ";
				}
				$json_item .= "\"count\": \"" . htmlentities($marker['child_count']) . "\"";
			    $json_item .= "},";
			    $json_item .= "\"geometry\": {";
			    $json_item .= "\"type\":\"Point\", ";
			    $json_item .= "\"coordinates\":[" . $marker['longitude'] . ", " . $marker['latitude'] . "]";
			    $json_item .= "}";
			    $json_item .= "}";

			    array_push($json_array, $json_item);
			    $cat_array = array();
			}
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


	/**
	 * DATE FILTER
	 * Cycle Through Each Cluster Until All Items Are Filtered Out
	 * Start With the Root ID
	 */
	private function _filter_date()
	{
		global $clusters_by_id, $start_date, $end_date;
		
		if (!empty($start_date) && is_numeric($start_date) && 
			!empty($end_date) && is_numeric($end_date))
		{
			foreach ($clusters_by_id as $cluster)
			{
				if (count($cluster['children']) == 0 && !($cluster['incident_date'] >= $start_date
					&& $cluster['incident_date'] <= $end_date) )
				{
					unset($clusters_by_id[$cluster['id']]);
					$this->_remove_from_parent($cluster['id'], $cluster['parent_id']);
				}
			}
		}
	}
	
	
	/**
	 * CATEGORY FILTER
	 * Cycle Through Each Cluster Until All Items Are Filtered Out
	 * Start With the Root ID
	 */
	private function _filter_category()
	{
		global $clusters_by_id, $category_id;
		
		if (!empty($category_id) && $category_id !=0)
		{
			foreach ($clusters_by_id as $cluster)
			{
				if (count($cluster['children']) == 0 && $cluster['category_id'] != $category_id)
				{
					unset($clusters_by_id[$cluster['id']]);
					$this->_remove_from_parent($cluster['id'], $cluster['parent_id']);
				}
			}
		}
	}
	
	
	/**
	 * Cycle Through Each Parent Removing Filtered Children
	 */	
	private function _remove_from_parent($cluster_id, $parent_id)
	{
		global $clusters_by_id;
		
		if (isset($clusters_by_id[$parent_id]))
		{
			$parent_parent_id = $clusters_by_id[$parent_id]['parent_id'];
			foreach ($clusters_by_id[$parent_id]['children'] as $key => $child)
			{
				if ($child == $cluster_id)
				{
					if (!isset($clusters_by_id[$child]) || count($clusters_by_id[$child]['children']) == 0)
					{
						unset($clusters_by_id[$parent_id]['children'][$key]);
					}
				}
			}
			$clusters_by_id[$parent_id]['child_count']--;
			if (count($clusters_by_id[$parent_id]['children']) == 0)
			{
				unset($clusters_by_id[$parent_id]);
			}
			$this->_remove_from_parent($parent_id, $parent_parent_id);
		}
	}
	

}
