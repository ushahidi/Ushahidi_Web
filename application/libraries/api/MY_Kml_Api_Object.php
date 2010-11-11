<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Kml_Api_Object
 *
 * This class handles GET request for KML via the API.
 *
 * @version 24 - Emmanuel Kala 2010-10-22
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
 
require_once Kohana::find_file('libraries/api', Kohana::config('config.extension_prefix').'Categories_Api_Object');
require_once Kohana::find_file('libraries/api', Kohana::config('config.extension_prefix').'Incidents_Api_Object');

class Kml_Api_Object extends Api_Object_Core {

    private $categories_api_object; // Categories API Object
    private $incidents_api_object; // Reports API Object
    
    public function __construct($api_service)
    {
        parent::__construct($api_service);
        
        // Set the response tpye for the API service to JSON
        $api_service->set_response_type('json');
        
        // Instantitate API objects to be used
        $this->categories_api_object = new Categories_Api_Object($api_service);
        $this->incidents_api_object = new Incidents_Api_Object($api_service);
        
        // Set the response type for this instance to json
        $this->set_response_type('json');
                
    }

    /**
     * Implementation of abstract method declared in superclass
     *
     * API task handler
     */
    public function perform_task()
    {
        $this->response_data  = $this->_3dkml();
    }
    
    /**
     * Get a list of incident categories
     * returns an array
     * FIXME: Might as well add functionality to return this in the API
     *
     * Return format: array[incident_id][] = category_id;
     *
     */
    private function _report_categories()
    {

        $this->query = "SELECT incident_id, category_id FROM `".
            $this->table_prefix."incident_category` ORDER BY id DESC";
            
        $items = $this->db->query($this->query);
        
        $data = array(); // Array to hold the return data
        
        foreach ($items as $item)
        {
            $data[$item->incident_id][] = $item->category_id;
        }
        return $data;
    }

    /**
     * return KML for 3d "geo spatial temporal" map
     * FIXME: This could probably be done in less than >5 foreach loops
     *
     * @param string response_type - XML or JSON
     */
    private function _3dkml()
    {
        $kml = '<?xml version="1.0" encoding="UTF-8"?>
        <kml xmlns="http://earth.google.com/kml/2.2">
        <Document>
        <name>Ushahidi</name>'."\n";

        // Get the categories that each incident belongs to
        $incident_categories = $this->_report_categories();

        // Get category colors in this format: $category_colors[id] = color
        $categories = json_decode($this->categories_api_object->get_categories_by_all());
        
        $categories = $categories->payload->categories;
        $category_colors = array();

        foreach ($categories as $category) 
        {
            $category_colors[$category->category->id] = $category->category->color;
        }
        
        // Finally, grab the incidents
        $incidents = json_decode($this->incidents_api_object->_get_incidents(
            'WHERE incident_active=1',''));

        $incidents = $incidents->payload->incidents;
        
        // Set the no. of incidents fetched
        $this->record_count = sizeof($incidents);
        
        // Calculate times for relative altitudes (
        // This is the whole idea behind 3D maps)

        $incident_times = array();

        foreach ($incidents as $inc_obj) 
        {
            $incident = $inc_obj->incident;
            $incident_times[$incident->incidentid] = strtotime(
                $incident->incidentdate);
        }

        // All times to be adjusted according to max altitude.

        $max_altitude = 10000;
        $newest = 0;

        foreach ($incident_times as $incident_id => $timestamp) 
        {
            if ( ! isset($oldest)) $oldest = $timestamp;

            $incident_times[$incident_id] -= $oldest;
            
            if ($newest < $incident_times[$incident_id]) $newest = 
                $incident_times[$incident_id];
        }

        foreach ($incident_times as $incident_id => $timestamp) 
        {
            $incident_altitude[$incident_id] = 0;
            
            if($newest != 0) $incident_altitude[$incident_id] =
                floor(($timestamp / $newest) * $max_altitude);
        }

        // Compile KML and output
        foreach ($incidents as $inc_obj) 
        {
            $incident = $inc_obj->incident;

            if (array_key_exists($incident->incidentid, $incident_categories))
            {
                $category_id = $incident_categories[
                    $incident->incidentid][0]; // Could be multiple categories. Pick the first one.
            
                if (array_key_exists($category_id, $category_colors))
                {
                    $hex_color =  $category_colors[$category_id];

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
            }
        }

        $kml .= '</Document>
        </kml>';

        return $kml;
    }

}
