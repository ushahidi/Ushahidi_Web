<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles locations activities via the API.
 *
 * @version 25- Emmanuel Kala 2011-07-08
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
                    $this->response_data = $this->_get_locations(array('id' => $this->request['id']));
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
                    $this->response_data = $this->_get_locations(array('country_id' => $this->request['id']));
                }
            break;
            
            default:
                $this->response_data = $this->_get_locations();
        }        
    }

	/**
	 * Get a list of locations
	 * 
	 * @param array $where Key->value array of the set of filters to apply
	 * @return string JSON/XML string with the location data
	 */
	private function _get_locations($where = array())
	{
		// Fetch the location items
		$items = ORM::factory('Location')
				->select('location_name AS name', 'location.*') // Add extra name field for backwards compat
				->where($where)
				->where('location_visible', 1)
				->limit($this->list_limit)
				->find_all();
        
		//No record found.
		if ($items->count() == 0)
		{
			return $this->response(4);
		}
		
		// Counter
		$i = 0;
		
		// To hold the json data
		$json_locations = array();
		
		foreach ($items as $item)
		{
			$item = $item->as_array();
			// Hide variables we don't want publicly exposed
			unset($item['location_visible']);
			
			// Needs different treatment depending on the output
			if ($this->response_type == 'json' OR $this->response_type == 'jsonp')
			{
				$json_locations[] = array("location" => $item);
			} 
			else 
			{
				$json_locations['location'.$i] = array("location" => $item);

				$this->replar[] = 'location'.$i;
			}

			$i++;
		}
		
		// Array to be converted to either JSON or xml
		$data = array(
			"payload" => array(
			"domain" => $this->domain,
			"locations" => $json_locations),
			"error" => $this->api_service->get_error_msg(0)
		);

		return ($this->response_type == 'json' OR $this->response_type == 'jsonp') 
			? $this->array_as_json($data)
			: $this->array_as_xml($data, $this->replar);
	}
}
?>
