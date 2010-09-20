<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles GET request for KML via the API.
 *
 * @version 23 - Henry Addo 2010-09-20
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
require_once('ApiActions.php');
require_once('GetCategories.php');
require_once('GetReports.php');

class GetKml
{
    private $items; // hold sql query results
    private $data; // items to parse to json
    private $json_kml = array(); // api string to parse to json
    private $ret_json_or_xml; // hold the json/xml strint to return
    private $query;
    private $response_type;
    private $domain;
    private $replar;
    private $db;
    private $table_prefix;
    private $api_actions;
    private $get_categories;
    private $get_reports;

    public function __construct()
    {
        $this->api_actions = new ApiActions;
        $this->get_categories = new GetCategories;
        $this->get_reports = new GetReports;
        $this->query = "";
        $this->response_type = "json";
        $this->data = array();
        $this->domain = $this->api_actions->_get_domain();
        $this->db = $this->api_actions->_get_db();
        $this->replar = array();
        $this->table_prefix = $this->api_actions->_get_table_prefix();
    }

    /**
	 * get a list of incident categories
	 * returns an array
	 * FIXME: Might as well add functionality to return this in the API
	 *
	 * Return format: array[incident_id][] = category_id;
	 *
	 */
	function _report_categories()
    {

		$this->query = "SELECT incident_id, category_id FROM `".
            $this->table_prefix."incident_category` ORDER BY id DESC";
		$this->items = $this->db->query($this->query);
		
		foreach ($this->items as $item){
			$this->data[$item->incident_id][] = $item->category_id;
		}
		return $this->data;
	}

    /**
	 * return KML for 3d "geo spatial temporal" map
	 * FIXME: This could probably be done in less than >5 foreach loops
	 *
     * @param string response_type - XML or JSON
     */
	public function _3dkml($response_type)
    {
	    $kml = '<?xml version="1.0" encoding="UTF-8"?>
		<kml xmlns="http://earth.google.com/kml/2.2">
		<Document>
		<name>Ushahidi</name>'."\n";

		// Get the categories that each incident belongs to
		$incident_categories = $this->_report_categories();

		// Get category colors in this format: $category_colors[id] = color
		$categories = json_decode($this->get_categories->_categories(
                    $response_type));
		$categories = $categories->payload->categories;
		$category_colors = array();

		foreach($categories as $category) 
        {
			$category_colors[$category->category->id] = 
                $category->category->color;
		}

		// Finally, grab the incidents
		$incidents = json_decode($this->get_reports->_get_reports(
            'WHERE incident_active=1','',$response_type));

		$incidents = $incidents->payload->incidents;

		// Calculate times for relative altitudes (
        // This is the whole idea behind 3D maps)

		$incident_times = array();

		foreach($incidents as $inc_obj) 
        {
			$incident = $inc_obj->incident;
			$incident_times[$incident->incidentid] = strtotime(
                $incident->incidentdate);
		}

		// All times to be adjusted according to max altitude.

		$max_altitude = 10000;
		$newest = 0;

		foreach($incident_times as $incident_id => $timestamp) 
        {
			if(!isset($oldest)) $oldest = $timestamp;

			$incident_times[$incident_id] -= $oldest;
			
            if($newest < $incident_times[$incident_id]) $newest = 
                $incident_times[$incident_id];
		}

		foreach($incident_times as $incident_id => $timestamp) 
        {
			$incident_altitude[$incident_id] = 0;
			
            if($newest != 0) $incident_altitude[$incident_id] =
                floor(($timestamp / $newest) * $max_altitude);
		}

		// Compile KML and output
		foreach($incidents as $inc_obj) 
        {

			$incident = $inc_obj->incident;

			$category_id = $incident_categories[
                $incident->incidentid][0]; // Could be multiple categories. Pick the first one.
			$hex_color = $category_colors[$category_id];

			// Color for KML is not the traditional HTML Hex of (rrggbb). It's (aabbggrr). aa = alpha or transparency
			$color = 'FF'.$hex_color{4}.$hex_color{5}.
                $hex_color{2}.$hex_color{3}.$hex_color{0}.$hex_color{1};

			$kml .= '<Placemark>
				<name>'.$incident->incidenttitle.'</name>
				<description>'.$incident->incidentdescription.'</description>
				<Style>
					<IconStyle>
						<Icon>
							<href>'.url::base().'media/img/color_icon.php?c='.$hex_color.'</href>
						</Icon>
					</IconStyle>
					<LineStyle>
						<color>'.$color.'</color>
						<width>2</width>
					</LineStyle>
				</Style>
				<Point>
					<extrude>1</extrude>
					<altitudeMode>relativeToGround</altitudeMode>
					<coordinates>'.$incident->locationlongitude.','.$incident->locationlatitude.','.$incident_altitude[$incident->incidentid].'</coordinates>
				</Point>
			</Placemark>'."\n";

		}

		$kml .= '</Document>
		</kml>';

		return $kml;
	}

}
