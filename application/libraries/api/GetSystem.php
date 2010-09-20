<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles private functions that not accessbile by the public 
 * via the API.
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

class GetSystem
{
    private $data;
    private $items;
    private $query;
    private $replar;
    private $db;
    private $domain;
    private $ret_json_or_xml;
    private $api_actions;

    public function __construct()
    {
        $this->api_actions = new ApiActions;
        $this->data = array();
        $this->items = array();
        $this->ret_json_or_xml = '';
        $this->query = '';
        $this->replar = array();
        $this->domain = $this->api_actions->_get_domain();
        $this->db = $this->api_actions->_get_db();

    }

    /**
 	 * Get an ushahidi deployment version number.
     *
     * @param string response_type - JSON or XML
     *
     * @return string
 	 */
	public function _get_version_number($response_type)
    {
		$json_version = array();
		$version = Kohana::config('version.ushahidi_version');

		if($response_type == 'json')
        {
			$json_version[] = array("version" => $version);
		}
        else{
			$json_version['version'] = array("version" => $version);
			$this->replar[] = 'version';
		}

		//create the json array
		$this->data = array(
            "payload" => array(
                "domain" => $this->domain,
                "version" => $json_version
                ),
            "error" => $this->api_actions->_get_error_msg(0)
        );

		if($response_type == 'json') 
        {
			$this->ret_json_or_xml = $this->api_actions->
                _array_as_JSON($this->data);
		}
        else{
			$this->ret_json_or_xml = $this->api_actions->
                _array_as_XML($this->data,$this->replar);
		}

		return $this->ret_json_or_xml;
	}
}
