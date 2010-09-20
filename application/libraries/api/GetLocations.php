<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles locations activities via the API.
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

class GetLocations
{
    private $json_locations; // Hold items from sql query.
    private $data; // items to parse to JSON.
    private $items; // categories to parse to JSON.
    private $query; // Holds the SQL query
    private $replar; // assists in proper XML generation.
    private $db;
    private $domain;
    private $table_prefix;
    private $list_limit;
    private $api_actions;

    public function __construct()
    {
        $this->api_actions = new ApiActions;
        $this->json_locations = array();
        $this->data = array();
        $this->items = array();
        $this->ret_json_or_xml = '';
        $this->query = '';
        $this->replar = array();
        $this->db = $this->api_actions->_get_db();
        $this->domain = $this->api_actions->_get_domain();
        $this->list_limit = $this->api_actions->_get_list_limit();
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
	private function _get_locations($where = '', $limit = '',$response_type)
    {
	   
		//fetch locations
		$this->query = "SELECT id, location_name AS name, country_id ,
            latitude,longitude FROM `".
                $this->table_prefix."location` $where $limit ";

		$this->items = $this->db->query($this->query);
		$i = 0;

		foreach ($this->items as $item)
        {
			//needs different treatment depending on the output
			if($response_type == 'json')
            {
				$this->json_locations[] = array("location" => $item);
			} 
            else 
            {
				$this->json_locations['location'.$i] = array(
                        "location" => $item) ;
				$this->replar[] = 'location'.$i;
			}

			$i++;
		}

		//create the json array
		$this->data = array(
                "payload" => array(
                "domain" => $this->domain,
                "locations" => $this->json_locations),
			    "error" => $this->api_actions->_get_error_msg(0)
		);

		if($response_type == 'json')
        {
			$this->ret_json_or_xml = $this->api_actions->
                _array_as_JSON($this->data);
		} 
        else 
        {
            
			$this->ret_json_or_xml = $this->api_actions->
                _array_as_XML($this->data,
                    $this->replar);
		}

		return $this->ret_json_or_xml;
	}


    /**
 	 * Get a list of all location
     *
     * @param string response_type = XML or JSON
     *
     * @return string  
 	 */
	public function _locations($response_type)
    {
		$where = "\n WHERE location_visible = 1 ";
		$where .= "ORDER by id DESC";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_get_locations($where, $limit,$response_type);
	}

	/**
 	 * Get location by an id
     * 
     * @param int id - the location id
     * @param string response_type - XML or JSON
     *
     * @return string
 	 */
	public function _location_by_id($id,$response_type) 
    {
		$where = "\n WHERE location_visible = 1 AND id=$id ";
		$where .= "ORDER by id DESC";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_get_locations($where, $limit,$response_type);
	}

	/**
 	 * Get a location by country id
     *
     * @param int id - the id of the country
     * @param string response_type - XML or JSON
     *
     * @return string
 	 */
	public function _location_by_country_id($id,$response_type)
    {
		$where = "\n WHERE location_visible = 1 AND country_id=$id ";
		$where .= "ORDER by id DESC";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_get_locations($where, $limit,$response_type);
	}

}

?>
