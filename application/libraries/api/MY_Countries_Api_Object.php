<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles countries activities via the API.
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

class Countries_Api_Object extends Api_Object_Core {

    public function __construct($api_service)
    {
        parent::__construct($api_service);
    }
    
    public function perform_task()
    {
        if ($this->api_service->verify_array_index($this->request, 'by'))
        {
            $this->by = $this->request['by'];
        }
        
        switch ($this->by)
        {
            // Get country by id (unique id in the database)
            case "countryid":
                if ( ! $this->api_service->verify_array_index($this->request, 'id'))
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(001, 'id')
                    ));
                    
                    return;
                }
                else
                {
                    $this->response_data = $this->_get_country_by_id(
                        $this->check_id_value($this->request['id']));
                }
            break;
            
            // Get country by name
            case "countryname":
                if ( ! $this->api_service->verify_array_index($this->request, 'name'))
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(001, 'name')
                    ));
                    
                    return;
                }
                else
                {
                    $this->response_data = $this->_get_country_by_name(
                            $this->request['name']);

                }
            break;
            
            // Get country by ISO
            case "countryiso":
                if ( ! $this->api_service->verify_array_index($this->request, 'iso'))
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(001, 'iso')
                    ));
                                        
                    return;
                }
                else
                {
                    $this->response_data = $this->_get_country_by_iso($this->request['iso']);
                }
            break;
            
            default:
                $this->response_data = $this->_get_countries_by_all();
        }
    }

    /**
     * Fetch all countries
     *
     * @param array where - array to pass to query builder
     * @param integer limit - number of results to return
     *
     * @return string 
     */
    private function _get_countries($where = array(), $limit = FALSE)
    {

        // Fetch countries
				$items = ORM::factory('Country')
						->select('country as name', 'country.*')
						->where($where)
						->orderby('id','DESC');
				
				if ($limit)
					$items->limit($limit);
				
				$items = $items->find_all();
				
        // Set the record count
        $this->record_count = count($items);
        
        $i = 0;

        $json_countries = array();
        $ret_json_or_xml = '';
        
				//No record found.
        if ($this->record_count == 0)
        {
            return $this->response(4);
        }

        foreach ($items as $item)
        {

            // Needs different treatment depending on the output
            if ($this->response_type == 'json' OR $this->response_type == 'jsonp')
            {
                $json_countries[] = array("country" => $item->as_array());
            } 
            else 
            {
                $json_countries['country'.$i] = array(
                        "country" => $item->as_array());

                $this->replar[] = 'country'.$i;
            }

            $i++;
        }

        // Create the json array
        $data = array(
                "payload" => array(
                    "domain" => $this->domain,
                    "countries" => $json_countries
                ),
                "error" => $this->api_service->get_error_msg(0)
        );
        
        if ($this->response_type == 'json' OR $this->response_type == 'jsonp')
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
     * Get a list of all countries
     *
     * @param string response_type - XML or JSON
     *
     * @return string
     */
    private function _get_countries_by_all()
    {
        return $this->_get_countries();
    }

    /**
     * Get a country by name
     *
     * @param string name - the name of country
     * @param string response_type - XML or JSON
     */
    private function _get_country_by_name($name)
    {
				$where = array('country' => $name);
        
        return $this->_get_countries($where, $this->list_limit);
    }

    /**
     * Get a country by id
     * @param string id - the id of the country from an ushahidi deployment
     *
     * @param string response_type - XML or JSON
     */
    private function _get_country_by_id($id)
    {
        $where = array('id' => $id);
        
        return $this->_get_countries($where, $this->list_limit);
    }

    /**
     * Get a country by iso
     *
     * @param string response_type - XML or JSON
     */
    private function _get_country_by_iso($iso)
    {
        $where = array('iso' => $iso);
        return $this->_get_countries($where, $this->list_limit);
    }
}

