<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Categories_Api_Object
 *
 * This class handles categories activities via the API.
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

class Categories_Api_Object extends Api_Object_Core {
    
    public function __construct($api_service)
    {
        parent::__construct($api_service);
    }

    /**
     * Implementation of abstract method in superclass
     *
     * Handles the API task parameters
     */
    public function perform_task()
    {
        if ($this->api_service->verify_array_index($this->request, 'by'))
        {
            $this->by = $this->request['by'];
        }
        
        switch($this->by)
        {            
            case "catid":
                if ( ! $this->api_service->verify_array_index($this->request, 'id'))
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(001, 'id')
                    ));
                    return;
                }
                else
                {
                    $this->response_data = $this->_get_categories_by_id($this->check_id_value($this->request['id']));
                }
                
            break;
            
            default:
                $this->response_data  = $this->get_categories_by_all();             
        }
    }
    
    /**
     * Get a single category
     * 
     * @param int id - The category id.
     * @param string response_type - XML or JSON
     */
    private function _get_categories_by_id($id)
    {
        // Find incidents
        $this->query = "SELECT id, parent_id, category_title, category_description, ";
        $this->query .= "category_color, category_position FROM `".$this->table_prefix."category` ";
        $this->query .= "WHERE category_visible = 1 AND id=$id ORDER BY category_position ASC";

        $items = $this->db->query($this->query);
        
        // Set the no. of records fetched
        $this->record_count = $items->count();
        
        $i = 0;
        
        $json_categories = array();
        $ret_json_or_xml = '';
        
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
                $json_categories[] = array("category" => $item);
            } 
            else
            {
                $json_categories['category'.$i] = array("category" => $item);
                $this->replar[] = 'category'.$i;
            }

            $i++;
        }

        // Create the json array
        $data = array(
                "payload" => array(
                    "domain" => $this->domain,
                    "categories" => $json_categories
                ),
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
     * Get all categories
     * 
     * @param string response_type - XML or JSON
     *
     * @return string
     */
    public function get_categories_by_all()
    {
        $items = array(); //will hold the items from the query
        $data = array(); //items to parse to json
        $json_categories = array(); //incidents to parse to json

        $ret_json_or_xml = ''; //will hold the json/xml string to return

        //find incidents
        $this->query = "SELECT id, parent_id, category_title as title, category_description as description, 
        	category_color as color, category_position as position FROM `".$this->table_prefix."category` WHERE
                category_visible = 1 ORDER BY category_position ASC";
        
        $items = $this->db->query($this->query);
        
        // Set the no. of records fetched
        $this->record_count = $items->count();
        
        $i = 0;
        
        $this->replar = array(); //assists in proper xml generation

        foreach ($items as $item)
        {

            //needs different treatment depending on the output
            if ($this->response_type == 'json')
            {
                $json_categories[] = array("category" => $item);
            }
            else
            {
                $json_categories['category'.$i] = array(
                        "category" => $item) ;
                $this->replar[] = 'category'.$i;
            }

            $i++;
        }
        
        //create the json array
        $data = array(
                "payload" => array(
                    "domain" => $this->domain,
                    "categories" => $json_categories),
                "error" => $this->api_service->get_error_msg(0)
        );

        if ($this->response_type == 'json')
        {
            $ret_json_or_xml = $this->array_as_json($data);
        }
        else
        {            
            $ret_json_or_xml = $this->array_as_xml(
                    $data, $this->replar);
        }
        
        return $ret_json_or_xml;
    }

}

?>
