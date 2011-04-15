<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles locations activities via the API.
 *
 * @version 24 - Emmanuel Kala 2010-10-222
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

class Locations_Api_Object extends Api_Object_Core {

    public function __construct($api_service)
    {
        parent::__construct($api_service);
    }
    
    public function perform_task()
    {
        // Check if the by parameter has been set    
        if ($this->api_service->verify_array_index($this->request, 'by'))
        {
            $this->by = $this->request['by'];
        }
                
        switch ($this->by)
        {
            case "latlon":
            break;
            
            // Get location by id
            case "locid":
                if ( ! $this->api_service->verify_array_index($this->request, 'id'))
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(001, 'id')
                    ));
                    
                    return;
                }
                else
                {
                    $this->response_data = $this->_get_location_by_id(
                        $this->check_id_value($this->request['id'])); 
                }
            break;
            
            // Get locations by country id
            case "country":
                if ( ! $this->api_service->verify_array_index($this->request, 'id'))
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(001, 'id')
                    ));
                    
                    return;
                }
                else
                {
                    $this->response_data = $this->_get_locations_by_country_id($this->check_id_value($this->request['id']));
                }
            break;
            
            default:
                $this->response_data = $this->_get_locations_by_all();
        }        
    }

    /**
     * Get a list of locations
     * 
     * @param string where - the where clause for sql
     * @param string limit - the limit number.
     * @param string response_type - XML or JSON
     *
     * @return string
     */
    private function _get_locations($where = '', $limit = '')
    {
        // Fetch locations
        $this->query = "SELECT id, location_name AS name, country_id ,
            latitude,longitude FROM `".
                $this->table_prefix."location` $where $limit ";

        $items = $this->db->query($this->query);
        
        // Set the no. of records fetched
        $this->record_count = $items->count();
        
        $i = 0;

        $json_locations = array();
        $ret_json_or_xml = ''; // Will hold the json/xml string to return
        
        //No record found.
        if ($items->count() == 0)
        {
            return $this->response(4);
        }

        foreach ($items as $item)
        {
            // Needs different treatment depending on the output
            if ($this->response_type == 'json')
            {
                $json_locations[] = array("location" => $item);
            } 
            else 
            {
                $json_locations['location'.$i] = array(
                        "location" => $item) ;
                        
                $this->replar[] = 'location'.$i;
            }

            $i++;
        }

        //create the json array
        $data = array(
                "payload" => array(
                "domain" => $this->domain,
                "locations" => $json_locations),
                "error" => $this->api_service->get_error_msg(0)
        );

        if ($this->response_type == 'json')
        {
            $ret_json_or_xml = $this->array_as_json($data);
        } 
        else 
        {
            
            $ret_json_or_xml = $this->array_as_xml($data, $this->replar);
        }

        return $ret_json_or_xml;
    }


    /**
     * Get a list of all location
     *
     * @return string  
     */
    public function _get_locations_by_all()
    {
        $where = "\n WHERE location_visible = 1 ";
        $where .= "ORDER by id DESC";
        $limit = "\nLIMIT 0, $this->list_limit";
        
        return $this->_get_locations($where, $limit);
    }

    /**
     * Get location by an id
     * 
     * @param int id - the location id
     * @param string response_type - XML or JSON
     *
     * @return string
     */
    private function _get_location_by_id($id) 
    {
        $where = "\n WHERE location_visible = 1 AND id=$id ";
        $where .= "ORDER by id DESC";
        $limit = "\nLIMIT 0, $this->list_limit";
        
        return $this->_get_locations($where, $limit);
    }

    /**
     * Get a location by country id
     *
     * @param int id - the id of the country
     * @param string response_type - XML or JSON
     *
     * @return string
     */
    private function _get_locations_by_country_id($id)
    {
        $where = "\n WHERE location_visible = 1 AND country_id=$id ";
        $where .= "ORDER by id DESC";
        $limit = "\nLIMIT 0, $this->list_limit";
        
        return $this->_get_locations($where, $limit);
    }

}
?>
