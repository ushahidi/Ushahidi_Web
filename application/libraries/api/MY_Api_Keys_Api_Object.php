<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Api_Key_Object
 *
 * This class handles GET request for API Keys.
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

class Api_Keys_Api_Object extends Api_Object_Core {

    public function __construct($api_service)
    {
        parent::__construct($api_service);
    }


    public function perform_task()
    {
        if ( ! $this->api_service->verify_array_index($this->request, 'by'))
        {
            $this->set_error_message(array(
                "error" => $this->api_service->get_error_msg(001, 'by')
            ));
            
            return;
        }
        else
        {
            $this->by = $this->request['by'];
        }
        
        switch ($this->by)
        {
            case "google":
                $this->response_data = $this->_get_api_key('api_google');
            break;
            
            case "yahoo":
                $this->response_data = $this->_get_api_key('api_yahoo');
            break;
            
            case "microsoft":
                $this->response_data = $this->_get_api_key('api_live');
            break;
            
            default:
                $this->set_error_message(array(
                    "error" => $this->api_service->get_error_msg(002)
                ));
        }
        
    }
    
    /**
     * Get api keys
     * @param string service - The map service providers, 
     * microsoft, google, osm.
     * @param service Name of the service whose API key is to be retrieved
     * @return string 
     */
    private function _get_api_key($service)
    {
    
        $ret_json_or_xml = ''; // Will hold the JSON/XML string
        
        // Find settings item
        $this->query = "SELECT id AS id, $service AS apikey FROM `".
            $this->table_prefix."settings` ORDER BY id DESC ;";

        $items = $this->db->query($this->query);
        $i = 0;
        
        //No record found.
        if ($items->count() == 0)
        {
            return $this->response(4);
        }

        foreach ($items as $item)
        {
            //needs different treatment depending on the output
            if ($this->response_type == 'json')
            {
                $json_services[] = array("service" => $item);
            }
            else
            {
                $json_services['service'.$i] = array("service" => $item) ;
                $this->replar[] = 'service'.$i;
            }

            $i++;
        }

        //create the json array
        $data = array(
                "payload" => array(
                    "domain" => $this->domain,
                    "services" => $json_services
                ),
                "error" => $this->api_service->get_error_msg(0));
        
        if( $this->response_type == 'json')
        {
            $ret_json_or_xml = $this->array_as_json($data);
        } 
        else
        {
            $ret_json_or_xml = $this->array_as_xml($data, $this->replar);
        }

        return $ret_json_or_xml;
    }
}

?>
