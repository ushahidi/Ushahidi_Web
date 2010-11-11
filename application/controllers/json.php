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

    // Table Prefix
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
    function index()
    {
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

        $category_id = ( isset($_GET['c']) AND ! empty($_GET['c']) ) ?
			(int) $_GET['c'] : 0;

        if (isset($_GET['i']) AND !empty($_GET['i']))
        {
            $incident_id = (int) $_GET['i'];
        }

        if (isset($_GET['n']) AND !empty($_GET['n']))
        {
            $neighboring = (int) $_GET['n'];
        }

        $where_text = '';
        // Do we have a media id to filter by?
        if (isset($_GET['m']) AND !empty($_GET['m']) AND $_GET['m'] != '0')
        {
            $media_type = (int) $_GET['m'];
            $where_text .= " AND ".$this->table_prefix."media.media_type = " . $media_type;
        }

        if (isset($_GET['s']) AND !empty($_GET['s']))
        {
            $start_date = (int) $_GET['s'];
            $where_text .= " AND UNIX_TIMESTAMP(".$this->table_prefix."incident.incident_date) >= '" . $start_date . "'";
        }

        if (isset($_GET['e']) AND !empty($_GET['e']))
        {
            $end_date = (int) $_GET['e'];
            $where_text .= " AND UNIX_TIMESTAMP(".$this->table_prefix."incident.incident_date) <= '" . $end_date . "'";
        }

        // Do we have a category id to filter by?
        if (is_numeric($category_id) AND $category_id != '0')
        {
            // Retrieve children categories and category color
            $category = ORM::factory('category', $category_id);
            $color = $category->category_color;
            $icon = $category->category_image;
            
            if ($icon)
            {
                $icon = Kohana::config('upload.relative_directory').'/'.$icon;
            }
            
            $where_child = "";
            $children = ORM::factory('category')
                ->where('parent_id', $category_id)
                ->find_all();
            foreach ($children as $child)
            {
                $where_child .= " OR incident_category.category_id = ".$child->id." ";
            }

            // Retrieve markers by category
            // XXX: Might need to replace magic numbers
            $markers = ORM::factory('incident')
                                    ->select('DISTINCT incident.*')
                                    ->with('location')
                                    ->join('incident_category', 'incident.id', 'incident_category.incident_id','LEFT')
                                    ->join('media', 'incident.id', 'media.incident_id','LEFT')
                                    ->where('incident.incident_active = 1 AND ('.$this->table_prefix.'incident_category.category_id = ' . $category_id . ' ' . $where_child . ')' . $where_text)
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

        $json_item_first = "";  // Variable to store individual item for report detail page
        foreach ($markers as $marker)
        {
            $json_item = "{";
            $json_item .= "\"type\":\"Feature\",";
            $json_item .= "\"properties\": {";
            $json_item .= "\"id\": \"".$marker->id."\", \n";
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

            if ($marker->id == $incident_id)
            {
                $json_item_first = $json_item;
            }
            else
            {
                array_push($json_array, $json_item);
            }
            $cat_array = array();
        }
        if ($json_item_first)
        { // Push individual marker in last so that it is layered on top when pulled into map
            array_push($json_array, $json_item_first);
        }
        $json = implode(",", $json_array);

        header('Content-type: application/json');
        $this->template->json = $json;
    }


    /**
     * Generate JSON in CLUSTER mode
     */
    public function cluster()
    {
        //$profiler = new Profiler;

        // Database
        $db = new Database();

        $json = "";
        $json_item = "";
        $json_array = array();

        $color = Kohana::config('settings.default_map_all');
        $icon = "";

        // Get Zoom Level
        $zoomLevel = (isset($_GET['z']) AND !empty($_GET['z'])) ?
            (int) $_GET['z'] : 8;

        //$distance = 60;
        $distance = (10000000 >> $zoomLevel) / 100000;

        // Category ID
        $category_id = (isset($_GET['c']) AND !empty($_GET['c']) &&
            is_numeric($_GET['c']) AND $_GET['c'] != 0) ?
            (int) $_GET['c'] : 0;

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

        $filter = "";
        $filter .= ($start_date) ?
            " AND i.incident_date >= '" . date("Y-m-d H:i:s", $start_date) . "'" : "";
        $filter .= ($end_date) ?
            " AND i.incident_date <= '" . date("Y-m-d H:i:s", $end_date) . "'" : "";

        if ($southwest AND $northeast)
        {
            list($latitude_min, $longitude_min) = explode(',', $southwest);
            list($latitude_max, $longitude_max) = explode(',', $northeast);

            $filter .= " AND l.latitude >=".(float) $latitude_min.
                " AND l.latitude <=".(float) $latitude_max;
            $filter .= " AND l.longitude >=".(float) $longitude_min.
                " AND l.longitude <=".(float) $longitude_max;
        }

        if ($category_id > 0)
        {
            $query_cat = $db->query("SELECT `category_color`, `category_image` FROM `".$this->table_prefix."category` WHERE id=$category_id");
            foreach ($query_cat as $cat)
            {
                $color = $cat->category_color;
                $icon = $cat->category_image;
                if ($icon)
                {
                    $icon = Kohana::config('upload.relative_directory').'/'.$icon;
                }
            }
        }

        // Get incidents

        $incidents_result = $db->query('SELECT i.id AS id, i.incident_title AS incident_title, i.location_id as location_id FROM '.$this->table_prefix.'incident AS i WHERE i.incident_active = 1 '.$filter.' ORDER BY i.id ASC');
        $incidents_result = $incidents_result->result_array(FALSE);

        $location_ids = array();
        $incidents = array();
        $allowed_ids = array();
        while (count($incidents_result))
        {
            foreach ($incidents_result as $key => $incident)
            {
                $location_ids[] = $incident['location_id'];
                $incidents[$incident['id']] = array('title'=>$incident['incident_title'],'location_id'=>$incident['location_id']);
                unset($incidents_result[$key]);
                $allowed_ids[$incident['id']] = 1;
            }
        }

        // Get categories if we need to
        if ($category_id != 0)
        {
            $query = 'SELECT ic.incident_id AS incident_id FROM '.$this->table_prefix.'incident_category AS ic INNER JOIN '.$this->table_prefix.'category AS c ON (ic.category_id = c.id)  WHERE c.id='.$category_id.' OR c.parent_id='.$category_id.';';
            $categories_result = $db->query($query);

            // Find the allowed incident ids
            $allowed_ids = array();
            foreach ($categories_result as $cat)
            {
                $allowed_ids[$cat->incident_id] = 1;
            }
        }

        // Get locations

        if (count($location_ids) > 0)
        {
            $locations_result = ORM::factory('location')->in('id',implode(',',$location_ids))->find_all();
        }else{
            $locations_result = ORM::factory('location')->find_all();
        }

        $locations = array();
        foreach ($locations_result as $loc)
        {
            $locations[$loc->id]['lat'] = $loc->latitude;
            $locations[$loc->id]['lon'] = $loc->longitude;
        }

        // Create markers by marrying the locations and incidents
        $markers = array();
        foreach ($incidents as $id => $incident)
        {
            if (isset($allowed_ids[$id]))
            {
                $markers[] = array(
                    'id' => $id,
                    'incident_title' => $incident['title'],
                    'latitude' => $locations[$incident['location_id']]['lat'],
                    'longitude' => $locations[$incident['location_id']]['lon']
                    );
            }
        }

        $clusters = array();    // Clustered
        $singles = array();     // Non Clustered

        // Loop until all markers have been compared
        while (count($markers))
        {
            $marker  = array_pop($markers);
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
            $json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href=" . url::base() . "reports/view/" . $single['id'] . "/>".str_replace('"','\"',$single['incident_title'])."</a>")) . "\",";   
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

        header('Content-type: application/json');
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


        $marker = ORM::factory('incident')
            ->where('id', $incident_id)
            ->find();

        if ($marker->loaded)
        {
            /* First We'll get all neighboring reports */
            $incident_date = date('Y-m', strtotime($marker->incident_date));
            $latitude = $marker->location->latitude;
            $longitude = $marker->location->longitude;

            $filter = "";
            // Uncomment this to display markers from this month alone
            // $filter .= " AND i.incident_date LIKE '$incident_date%' ";
            $filter .= " AND i.id <>".$marker->id;

            // Database
            $db = new Database();

            // Get Neighboring Markers Within 50 Kms (31 Miles)
            $query = $db->query("SELECT DISTINCT i.*, l.`latitude`, l.`longitude`,
            ((ACOS(SIN($latitude * PI() / 180) * SIN(l.`latitude` * PI() / 180) + COS($latitude * PI() / 180) * COS(l.`latitude` * PI() / 180) * COS(($longitude - l.`longitude`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance
             FROM `".$this->table_prefix."incident` AS i INNER JOIN `".$this->table_prefix."location` AS l ON (l.`id` = i.`location_id`) INNER JOIN `".$this->table_prefix."incident_category` AS ic ON (i.`id` = ic.`incident_id`) INNER JOIN `".$this->table_prefix."category` AS c ON (ic.`category_id` = c.`id`) WHERE i.incident_active=1 $filter
            HAVING distance<='20'
             ORDER BY i.`incident_date` DESC LIMIT 100 ");

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

        header('Content-type: application/json');
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
        if(count($allowed_ids))
        {
            $incident_id_in = ' AND id IN ('.implode(',',$allowed_ids).')';
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
                
                $query = $db->query("SELECT * FROM `".$this->table_prefix."sharing_incident` WHERE 1=1 $filter ORDER BY incident_id ASC "); 

                $markers = $query->result_array(FALSE);

                $clusters = array();    // Clustered
                $singles = array();     // Non Clustered

                // Loop until all markers have been compared
                while (count($markers))
                {
                    $marker  = array_pop($markers);
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
                    $json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='http://" . $sharing_url . "/reports/view/" . $marker->incident_id . "'>" . htmlentities($marker->incident_title) . "</a>")) . "\",";

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
        
        header('Content-type: application/json');
        $this->template->json = $json;
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