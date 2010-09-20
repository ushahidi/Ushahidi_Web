<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles categories activities via the API.
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

class GetCategories
{
    private $json_categories; // Hold items from sql query.
    private $data; // items to parse to JSON.
    private $items; // categories to parse to JSON.
    private $query; // Holds the SQL query
    private $replar; // assists in proper XML generation.
    private $db;
    private $domain;
    private $messages;
    private $table_prefix;
    private $list_limit;
    private $error_messages;
    private $api_actions;
    public function __construct()
    {
        $this->api_actions = new ApiActions;
        $this->json_categories = array();
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
 	 * Get a single category
     * 
     * @param int id - The category id.
     * @param string response_type - XML or JSON
 	 */
	public function _category($id,$response_type)
    {
	
	    //find incidents
	    $this->query = "SELECT id, category_title, category_description,
            category_color FROM `".$this->table_prefix.
                    "category` WHERE category_visible = 1
				AND id=$id ORDER BY id DESC";

		$this->items = $this->db->query($this->query);
        
		$i = 0;

		foreach ($this->items as $item){

			//needs different treatment depending on the output
			if($response_type == 'json')
            {
			    $this->json_categories[] = array("category" => $item);
			} 
            else {
				$this->json_categories['category'.$i] = array(
                        "category" => $item) ;
				$this->replar[] = 'category'.$i;
			}

			$i++;
		}

		//create the json array
		$this->data = array(
                "payload" => array(
                    "domain" => $this->domain,
                    "categories" => $this->json_categories),
			    "error" => $this->api_actions->_get_error_msg(0)
		);

		if($response_type == 'json')
        {
			$this->ret_json_or_xml = $this->_array_as_JSON($this->data);
		} 
        else {
			$this->ret_json_or_xml = $this->_array_as_XML($this->data,
                    $this->replar);
		}

		return $this->ret_json_or_xml;
	}

    /**
     * Get all categories
     * 
     * @param string response_type - XML or JSON
     *
     * @return string
     */
	public function _categories($response_type)
    {

		$items = array(); //will hold the items from the query
		$data = array(); //items to parse to json
		$json_categories = array(); //incidents to parse to json

		$retJsonOrXml = ''; //will hold the json/xml string to return

		//find incidents
		$this->query = "SELECT id, category_title AS title, category_description AS
				description, category_color AS color FROM `".$this->table_prefix."category` WHERE
				category_visible = 1 ORDER BY id DESC";
        
		$this->items = $this->db->query($this->query);
		$i = 0;

		$this->replar = array(); //assists in proper xml generation

		foreach ($this->items as $item){

			//needs different treatment depending on the output
			if($response_type == 'json'){
				$this->json_categories[] = array("category" => $item);
			} else {
				$this->json_categories['category'.$i] = array(
                        "category" => $item) ;
				$this->replar[] = 'category'.$i;
			}

			$i++;
		}

		//create the json array
		$this->data = array(
                "payload" => array(
                    "domain" => $this->domain,
                    "categories" => $this->json_categories),
			    "error" => $this->api_actions->_get_error_msg(0)
		);

		if($response_type == 'json')
        {
			$this->ret_json_or_xml = $this->api_actions->_array_as_JSON(
                    $this->data);
		} 
        else {
            
			$this->ret_json_or_xml = $this->api_actions->_array_as_XML(
                    $this->data,$this->replar);
		}
        
		return $this->ret_json_or_xml;
	}

}
