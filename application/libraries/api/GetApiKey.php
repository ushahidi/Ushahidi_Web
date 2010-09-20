<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles GET request for API Keys.
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

class GetApiKey 
{
    private $items; // hold sql query results
    private $data; // items to parse to json
    private $json_apikey = array(); // api string to parse to json
    private $ret_json_or_xml; // hold the json/xml strint to return
    private $query;
    private $response_type;
    private $domain;
    private $replar;
    private $db;
    private $table_prefix;
    private $api_actions;

    public function __construct()
    {
        $this->api_actions = new ApiActions;
        $this->query = "";
        $this->response_type = "json";
        $this->domain = $this->api_actions->_get_domain();
        $this->db = $this->api_actions->_get_db();
        $this->replar = array();
        $this->table_prefix = $this->api_actions->_get_table_prefix();
    }


    /**
 	 * Get api keys
     * @param string service - The map service providers, 
     * microsoft, google, osm.
     * @param string response_type - the response type; xml or json.
     * @return string 
 	 */
    public function _api_key($service,$response_type)
    {
        $this->response_type = $response_type;
        
		//find incidents
		$this->query = "SELECT id AS id, $service AS apikey FROM `".
            $this->table_prefix."settings` ORDER BY id DESC ;";

		$this->items = $this->db->query($this->query);
		$i = 0;

		foreach ($this->items as $item){
			//needs different treatment depending on the output
			if( $this->response_type == 'json'){
				$json_services[] = array("service" => $item);
			} else {
				$json_services['service'.$i] = array("service" => $item) ;
				$this->replar[] = 'service'.$i;
			}

			$i++;
		}

		//create the json array
		$this->data = array("payload" => array(
                "domain" => $this->domain,"services" => $json_services),
                "error" => $this->api_actions->_get_error_msg(0));
        
		if( $response_type == 'json')
        {
			$this->ret_json_or_xml = $this->api_actions->
                _array_as_JSON($this->data);
		} 
        else {
			$this->ret_json_or_xml = $this->api_actions->_array_as_XML(
                    $this->data, $this->replar);
		}

		return $this->ret_json_or_xml;
	}

}

?>
