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
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
abstract class Api_Object_Core {
	
	/**
	 * Database object for processing queries
	 * @var Database
	 */
	protected $db;
	/**
	 * Prefix for the database tables
	 * @var string
	 */
	protected $table_preix;
	
	/**
	 * Form validation error messages
	 * @var array
	 */
	protected $messages = array();
	
	/**
	 * API validation error message
	 * @var mixed
	 */
	protected $error_messages = '';
	
	/**
	 * Domain name of the URL accessing the API
	 * @var string
	 */
	protected $domain;
	
	/**
	 * HTTP POST and/or GET data submitted via the API
	 * @var array
	 */
	protected $request = array();
	
	/**
	 * Format in which the data is to be returned to the client - defaults to JSON
	 * if none has been specified
	 * @var string
	 */
	protected $response_type;
	
	/**
	 * Response data to be returned to the client
	 * @var string
	 */
	protected $response_data;
	
	/**
	 * Maximum number of records that can be returned by a single API request
	 * @var int
	 */
	protected $list_limit;
	
	/**
	 * Api_Service object
	 * @var Api_Service
	 */
	protected $api_service;
	
	/**
	 * SQL query for fetching teh requested data
	 * @var string
	 */
	protected $query;
	
	/**
	 * Assists in proper XML generation
	 * @todo Review the value of having this has a class property
	 * @var mixed
	 */
	protected $replar;
	
	/**
	 * Attribute to be used for fetching the requested data
	 * @var string
	 */
	protected $by;
	
	/**
	 * Database ID of the requested item
	 * @var int
	 */
	protected $id;
	
	/**
	 * No. of records returned by the API request
	 * @var int
	 */
	protected $record_count;
	
	/**
	 * Api_Settings_Model object
	 * @var Api_Settings_Model
	 */
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
		if ( ! $this->api_service->verify_array_index($request, 'resp'))
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
	 * If the error message has already been set, the error is returned instead
	 *
	 * @return mixed The data fetched by the API request
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
	 * @return int The number of records returned by the API request
	 */
	public function get_record_count()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'GET') // For get methods, more than one record may be returned
		{
			return ((int) $this->record_count > 0 AND !isset($this->error_message)) 
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
	 *
	 * @param string $error_message Error message for the Api request
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
			if ((int) $limit > $this->api_settings->max_record_limit)
			{
				// Limit exceeds maximum therefore scale it down to the allowed maximum
				$limit = $this->api_settings->max_record_limit;
			}
		}

		// Check if the specified limit is
		// Set the list limit
		$this->list_limit = (intval($limit) > 0) ? intval($limit) : $this->list_limit;
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
				"error" => $this->api_service->get_error_msg(003, '', $error_messages)
			);
		}
		elseif ($ret_value == 2)
		{
			// Authentication Failed. Invalid User or App Key
			$response = array(
				"payload" => array("domain" => $this->domain, "success" =>
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

		return ($this->response_type == 'json')
			? $this->array_as_json($response)
			: $this->array_as_xml($response, array());
	}

	/**
	 * Creates a JSON response given an array.
	 *
	 * @param array $data Array to be converted to JSON
	 * @return string JSON representation of the data in @param $array
	 */
	protected function array_as_json($data)
	{
		return json_encode($data);
	}

	/**
	 * Converts an object to an array.
	 *
	 * @param object $object The object to be converted into array.
	 * @return array Array representation of the object
	 */
	protected function object_to_array($object)
	{
		$array = array();
		if (is_object($object))
		{
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
	protected function write(XMLWriter $xml, $data, $replar = "")
	{

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
		// The id value must be positive and non-zero
		$this->id = (preg_match('/^[1-9](\d*)$/', $id) > 0) ? (int) $id : 0;

		return $this->id;
    }
    
}

?>
