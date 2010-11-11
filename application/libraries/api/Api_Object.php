<?php defined('SYSPATH') or die('No direct script access allowed');
/**
 * Api_Object
 *
 * Base abstract class for all API library implementations.
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
abstract class Api_Object_Core {

    protected $db; // Database instance for the queries    
    protected $table_preix; // Table prefix
    protected $messages = array(); // Form validation error messages
    protected $error_messages = ''; // API Validation error messages
    protected $domain; // Domain name of the calling site    
    protected $request = array(); // POST/GET data requested/submitted via the API service
    protected $response_type; // Format in which the data is to be returned to the client
    protected $response_data; // Response data to be returned to the client
    protected $list_limit; // Max number of records to be returned
    protected $api_service; // Instance of the API service
    protected $query; // SQL query to be used to fetch the requested data
    protected $replar; // Assists in proper XML generation
    protected $by; // Mode by which to fetch the requested information
    protected $id; // Id of an item, usually from the URL
    protected $record_count; // No. of records fetched by the API object
    private $api_settings;
    
    public function __construct($api_service)    
    {    
        $this->db = new Database();
        
        $this->api_settings = new Api_Settings_Model(1);

        // Set the list limit
        $this->list_limit = ((int) $this->api_settings->default_record_limit > 0)
            ? $this->api_settings->default_record_limit
            : (int) Kohana::config('settings.items_per_api_request');
        
        $this->domain = url::base();
        $this->record_count = 1;
        $this->table_prefix = Kohana::config('database.default.table_prefix');
        $this->api_service = $api_service;
        
        // Check if the response type for the API service has already been set
        if ( ! is_null($api_service->get_response_type()))
        {
            $this->request = $api_service->get_request();
            $this->response_type = $api_service->get_response_type();
        }
        else
        {
            $this->set_request($api_service->get_request());
        }
    }
    
    /**
     * Sets the request and determines the format in which the request data is
     * to be returned to the client
     */
    public function set_request($request)
    {        
        $this->request = $request;
        
        // Determine the response type
        if (! $this->api_service->verify_array_index($request, 'resp'))
        {
            $this->set_response_type('json');
        }
        else
        {
            $this->set_response_type($request['resp']);
        }
    }

    /**
     * Gets the response type
     *
     * @return string
     */    
    public function get_response_type()
    {
        return $this->response_type;
    }
    
    /**
     * Sets the response type
     *
     * @param $type Type of response for the output data
     */
    public function set_response_type($type)
    {
        // Set the response type for the API library object
        $this->response_type = $type;
        
        // Set the response type for the API service
        $this->api_service->set_response_type($type);
    }
    
    /**
     * Gets the response data
     *
     * If the error message has already been set, the error is returned instead
     */
    public function get_response_data()
    {
        return (isset($this->error_message))
            ? $this->error_message
            : $this->response_data;
            
    }
    
    /**
     * Gets the number of records fetched by the API library. If the error message
     * property has been set, a zero (0) will always be returned
     *
     * @return int
     */
    public function get_record_count()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') // For get methods, more than one record may be returned
        {
            return ((int) $this->record_count > 0 AND ! isset($this->error_message)) 
                ? $this->record_count 
                : 0;
        }
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST') // For post, only 1 record can be can worked on
        {
            return $this->record_count;
        }
    }
    
    /**
     * Sets the error message
     */    
    public function set_error_message($error_message)
    {
        if (is_array($error_message))
        {
            $this->error_message = ($this->response_type == 'json')
                ? $this->array_as_json($error_message)
                : $this->array_as_xml($error_message);
        }
        else
        {
            $this->error_message = $error_message;
        }
    }
        
    /**
     * Abstract method that must be implemented by all subclasses
     * It is this method that services the API request
     */    
    abstract public function perform_task();
    
    /**
     * Sets the list limit for the maximum no. of records to be fetched. The value
     * of @param $limit must be numeric else the list limit is set to a default
     * value of 20
     *
     * @param $limit Numerical value specifying the maximium no. of records to be fetched
     */
    protected function set_list_limit($limit)
    {
        // Check if the specified limit (@patam limit) is more than the specified system setting
        if ((isset($this->api_settings->max_record_limit)))
        {
            if ((int)$limit > $this->api_settings->max_record_limit)
            {
                // Limit exceeds maximum therefore scale it down to the allowed maximum
                $limit = $this->api_settings->max_record_limit;
            }
        }
        
        // Check if the specified limit is 
        // Set the list limit
        $this->list_limit = (is_numeric($limit) AND intval($limit) > 0) 
            ? $limit 
            : $this->list_limit;
    }
    
    /**
     * Response
     * 
     * @param int ret_value
     * @param string response_type = XML or JSON
     * @param string error_message - The error message to display
     * 
     * @return string
     */
    protected function response($ret_value, $error_messages='')
    {
        $ret_json_or_xml = '';
        $response = array();

        // Set the record count to zero where the value of @param ret_val <> 0
        $this->record_count = ($ret_value != 0) ? 0 : 1;
        
        if ($ret_value == 0)
        {
            $response = array(
                "payload" => array(
                    "domain" => $this->domain,
                    "success" => "true"
                ),
                "error" => $this->api_service->get_error_msg(0)
            );
            
        } 
        elseif ($ret_value == 1) 
        {
            $response = array(
                "payload" => array(
                    "domain" => $this->domain,
                    "success" => "false"
                ),
                "error" => $this->api_service->get_error_msg(003,'',$error_messages)
            );
        } 
        elseif ($ret_value == 2)
        {
            // Authentication Failed. Invalid User or App Key
            $response = array(
                "payload" => array("domain" => $this->domain,"success" =>
                    "false"),
                "error" => $this->api_service->get_error_msg(005)
            );
        }
        elseif ($ret_value == 4)
        {
            // No results got from the database query
            $response = array(
                "payload" => array(
                    "domain" => $this->domain,
                    "success" => "true"
                ),
                "error" => $this->api_service->get_error_msg(007)
            );
        }
        else 
        {
            $response = array(
                "payload" => array(
                    "domain" => $this->domain,
                    "success" => "false"
                ),
                "error" => $this->api_service->get_error_msg(004)
            );
        }

        if ($this->response_type == 'json')
        {
            $ret_json_or_xml = $this->array_as_json($response);
        } 
        else 
        {
            $ret_json_or_xml = $this->array_as_xml($response, array());
        }
        
        return $ret_json_or_xml;
    }
    
    /**
     * Creates a JSON response given an array.
     *
     * @param array data - The array.
     * 
     * @return array - The json code.
     */
    protected function array_as_json($data)
    {
        return json_encode($data);
    }

    /**
     * Converts an object to an array.
     * 
     * @param object object - The object to be converted into array.
     */
    protected function object_to_array($object) 
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
    protected function write(XMLWriter $xml, $data, $replar = ""){

        foreach ($data as $key => $value)
        {
            if (is_a($value, 'stdClass'))
            {
                $value = $this->object_to_array($value);
            }

            if (is_array($value))
            {

                $toprint = true;

                if (in_array($key, $replar))
                {
                    //move up one level
                    $keys = array_keys($value);
                    $key = $keys[0];
                    $value = $value[$key];
                }

                $xml->startElement($key);
                $this->write($xml, $value, $replar);
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
    protected function array_as_xml($data, $replar = array())
    {
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('response');

        $this->write($xml, $data, $replar);

        $xml->endElement();
        return $xml->outputMemory(true);
    }

    /**
     * Check the id value receieved from the URL is of the right datatype
     * int
     *
     * @param int id - The ID value
     *
     * @return int 
     */
    protected function check_id_value($id)
    {
        $this->id = (is_numeric($id) AND 
            intval($id)>0) ? $id : 0;

        return $this->id;

    }
}

?>
