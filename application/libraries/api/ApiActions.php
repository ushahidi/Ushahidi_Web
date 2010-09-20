<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles global functions of the API .
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

class ApiActions
{
    private $db; //Database instance for queries
	private $list_limit; //number of records to limit repose
	private $response_type; //type of response, either json or xml
	private $error_messages; // validation error messages
	private $messages = array(); // form validation error messages
	private $domain; // the domain name of the calling site
	private $table_prefix; // Table Prefix

    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->_set_db(new Database);
        $this->_set_list_limit(20);
        $this->_set_domain(url::base());
        $this->_set_table_prefix( 
            Kohana::config('database.default.table_prefix'));

    }
    
    /**
     * Initialize the db variable
     *
     * @param string db - the database object
     */
    public function _set_db($db)
    {
        $this->db = $db;
    }

    /**
     * Get the content of db variable
     *
     * @return object - the database object
     */
    public function _get_db()
    {
        return $this->db;
    }
    
    
    /**
     * Initialize the limit for report listing.
     *
     * @param int list_limit - the list limit
     */
    public function _set_list_limit($list_limit)
    {
        $this->list_limit = $list_limit;
    }

    /**
     * Get the list limit value
     *
     * @return int the list limit.
     */
    public function _get_list_limit()
    {
        return $this->list_limit;
    }
    
    /**
     * Initialize the domain.
     *
     * @param string domain - the domain 
     */
    public function _set_domain($domain)
    {
        return $this->domain = $domain;
    }

    /**
     * Get the domain value
     *
     * @return string
     */
    public function _get_domain()
    {
        return $this->domain;
    }

    /**
     * Initialize table prefix
     *
     * @param string table_prefix - the table prefix
     */
    public function _set_table_prefix($table_prefix)
    {
        $this->table_prefix = $table_prefix;
    }

    /**
     * Get table_prefix value
     *
     * @return string 
     */
    public function _get_table_prefix()
    {
        return $this->table_prefix;
    }

    /**
 	 * Makes sure the appropriate key is there in a given 
     * array (POST or GET) and that it is set
     * @param ar Array - The given array.
     * @param index String - The index array
     * 
     * @return Boolean
 	 */
	public function _verify_array_index(&$ar, $index)
    {
        
	    if(isset($ar[$index]) && array_key_exists($index, $ar))
        {
			return true;
		} 
        else 
        {
			return false;
		}
	}

	/**
     * Displays Error codes with their corresponding messages.
     * returns an array error - array("code" => "CODE", 
     * "message" => "MESSAGE") based on the given code
     * 
     * @param errcode String - The error code to be displayed.
     * @param param String - The missing parameter.
     * @param message String - The error message to be displayed.
     *
     * @return - Array  	
     */
	public function _get_error_msg($errcode, $param = '', $message='')
    {
	    switch($errcode)
        {
		    case 0:
			    return array("code" => "0", "message" => 
                        Kohana::lang('ui_admin.no_error'));
			case 001:
				return array("code" => "001", "message" => 
                        Kohana::lang('ui_admin.missing_parameter').
                        " - $param.");
			case 002:
				return array("code" => "002", "message" => 
                        Kohana::lang('ui_admin.invalid_parameter'));
			case 003:
				return array("code" => "003", "message" => $message );
			case 004:
				return array("code" => "004", "message" => 
                        Kohana::lang('ui_admin.post_method_not_used'));
			case 005:
				return array("code" => "005", "message" => 
                        Kohana::lang('ui_admin.access_denied_credentials'));
			case 006:
				return array("code" => "006", "message" => 
                        Kohana::lang('ui_admin.access_denied_others'));
			default:
				return array("code" => "999", "message" => 
                        Kohana::lang('ui_admin.not_found'));
		}
	}

    /**
 	 * Creates a JSON response given an array.
     *
     * @param array data - The array.
     * 
     * @return array - The json code.
 	 */
	public function _array_as_JSON($data)
    {
		return json_encode($data);
	}

	/**
 	 * Converts an object to an array.
     * 
     * @param object object - The object to be converted into array.
 	 */
	public function _object_to_array($object) 
    {
		if (is_object($object)) {
			
            foreach ($object as $key => $value) 
            {
				$array[$key] = $value;
			}

		}
        else 
        {
			$array = $object;
		}
		return $array;
	}

	/**
 	* Creates a XML response given an array
 	* CREDIT TO: http://snippets.dzone.com/posts/show/3391
    *
    * @param  XMLWriter xml - the XMLWriter object.
    * @param array data - the data to be formatted into XML.
    * @param string replar - the replar
 	*/
	public function _write(XMLWriter $xml, $data, $replar = ""){

		foreach($data as $key => $value)
        {
			if(is_a($value, 'stdClass'))
            {
				$value = $this->_object_to_array($value);
			}

			if(is_array($value))
            {

	 			$toprint = true;

				if(in_array($key, $replar))
                {
					//move up one level
					$keys = array_keys($value);
					$key = $keys[0];
					$value = $value[$key];
				}

				$xml->startElement($key);
				$this->_write($xml, $value, $replar);
				$xml->endElement();

				continue;
			}

			$xml->writeElement($key, $value);
 		}
	}

	/**
 	 * Creates a XML response given an array
 	 * CREDIT TO: http://snippets.dzone.com/posts/show/3391
     * 
     * @param string data - The data to be formatted as XML
     * @param array replar - The replar.
     *
     * @return object xml - The formatted XML.
 	 */
	public function _array_as_XML($data, $replar = array())
    {
		$xml = new XMLWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('response');

		$this->_write($xml, $data, $replar);

		$xml->endElement();
		return $xml->outputMemory(true);
	}

}
