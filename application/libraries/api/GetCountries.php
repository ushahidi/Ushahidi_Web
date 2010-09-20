<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles countries activities via the API.
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

class GetCountries 
{
    private $json_countries; // Hold items from sql query.
    private $data; // items to parse to JSON.
    private $items; // categories to parse to JSON.
    private $query; // Holds the SQL query
    private $replar; // assists in proper XML generation.
    private $db;
    private $domain;
    private $table_prefix;
    private $list_limit;
    private $ret_json_or_xml;
    private $api_actions;

    public function __construct()
    {
        $this->api_actions = new ApiActions;
        $this->json_countries = array();
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
 	 * Fetch all countries
     *
     * @param string where - the where clause for sql
     * @param string limit - the limit number
     * @param string response_type - XML or JSON
     *
     * @return string 
 	 */
	public function _get_countries($where = '', $limit = '',$response_type)
    {

		//fetch countries
		$this->query = "SELECT id, iso, country as `name`, capital
			FROM `".$this->table_prefix."country` $where $limit";

		$this->items = $this->db->query($this->query);
		$i = 0;

		foreach ($this->items as $item)
        {

			//needs different treatment depending on the output
			if($response_type == 'json')
            {
				$this->json_countries[] = array("country" => $item);
			} 
            else 
            {
				$this->json_countries['country'.$i] = array(
                        "country" => $item);

				$this->replar[] = 'country'.$i;
			}

			$i++;
		}

		//create the json array
		$this->data = array(
                "payload" => array(
                "domain" => $this->domain,
                "countries" => $this->json_countries),
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
 	 * Get a list of all countries
     *
     * @param string response_type - XML or JSON
     *
     * @return string
 	 */
	public function _countries($response_type)
    {
		$where = "ORDER by id DESC ";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_get_countries($where, $limit,$response_type);
	}

	/**
 	 * Get a country by name
     *
     * @param string name - the name of country
     * @param string response_type - XML or JSON
 	 */
	public function _country_by_name($name,$response_type)
    {
		$where = "\n WHERE country = '$name' ";
		$where .= "ORDER by id DESC";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_get_countries($where, $limit,$response_type);
	}

	/**
 	 * Get a country by id
     * @param string id - the id of the country from an ushahidi deployment
 	 *
     * @param string response_type - XML or JSON
     */
	public function _country_by_id($id,$response_type)
    {
		$where = "\n WHERE id=$id ";
		$where .= "ORDER by id DESC";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_get_countries($where, $limit,$response_type);
	}

	/**
 	 * get a country by iso
     *
     * @param string response_type - XML or JSON
 	 */
	public function _country_by_iso($iso,$response_type)
    {
		$where = "\n WHERE iso='$iso' ";
		$where .= "ORDER by id DESC";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_get_countries($where, $limit,$response_type);
	}
}
