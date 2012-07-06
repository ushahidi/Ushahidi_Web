<?php defined('SYSPATH') or die('No direct script access allowed');
/**
 * Api_Service
 *
 * This class runs the API service. It abstracts the details of handling of the API
 * requests from the API controller. All task switching and routing is handled by
 * this class.
 *
 * The API routing works through inversion of control (IoC). The name of the library
 * that services the api request is inferred from the name of the task. Not all API
 * requests have their own libraries. As such, this service makes use of a
 * "task routing table". This table is a key=>value array with the key being the
 * name of the api task and the value the name of the implementing library
 * or associative array of the class name and callback method to service the request
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

// Suffix for all API library names
define('API_LIBRARY_SUFFIX', '_Api_Object');

final class Api_Service {
	/**
	 * The API request parameters
	 * @var array
	 */
	private $request = array();

	/**
	 * Response to be returned to the calling controller
	 * @var string
	 */
	private $response;

	/**
	 * Format in which the response is returned to the client - defaults to JSON
	 * @var string
	 */
	private $response_type;

	/**
	 * API library object to handle the requested task
	 * @var Api_Object
	 */
	private $api_object;

	/**
	 * Name of the API task to be routed
	 * @var string
	 */
	private $task_name;

	/**
	 * IP Address of the client making the API request
	 * @var string
	 */
	private $request_ip_address;

	/**
	 * API request parameters
	 * @var array
	 */
	private $api_parameters;

	/**
	 * Api_Log_Model object
	 * @var Api_Log_Model
	 */
	private $api_logger;

	/**
	 * Database object
	 * @var Database
	 */
	private $db;

	public function __construct()
	{
		// Set the request data
		$this->request = ($_SERVER['REQUEST_METHOD'] == 'POST')
			? $_POST
			: $_GET;
			
		// Reset the session - API should be stateless
		$_SESSION = array();
		// Especially reset auth
		Session::instance()->set(Kohana::config('auth.session_key'), null);

		// Load the API configuration file
		Kohana::config_load('api');

		// Get the IP Address of the client submitting the API request

		// Check if the IP is from a shared internet connection
		if ( ! empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$this->request_ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		// Check if the IP address is passed from a proxy server such as Nginx
		elseif ( ! empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$this->request_ip_address  = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$this->request_ip_address = $_SERVER['REMOTE_ADDR'];
		}

		// Unpack the URL parameters
		$this->api_parameters = serialize(array_keys($this->request));

		// Instantiate the database
		$this->db = new Database();
	}

	/**
	 * Runs the API service
	 */
	public function run_service()
	{
		// Check the request is allowed
		if ($this->_is_api_request_allowed())
		{
			// Route the API task
			$this->_route_api_task();
		}
		else
		{
			// Set the response to "ACCESS DENIED"
			$this->set_response($this->get_error_msg(006));

			// Terminate execution
			return;
		}
	}

	/**
	 * Gets the API request parameters as an array
	 *
	 * @return array
	 */
	public function get_request()
	{
		return $this->request;
	}

	/**
	 * Sets the response type
	 *
	 * @param $response_type New value for $this->response_type
	 */
	public function set_response_type($response_type)
	{
		$this->response_type = $response_type;
	}

	/**
	 * Returns the response type
	 *
	 * @return string
	 */
	public function get_response_type()
	{
		return $this->response_type;
	}

	/**
	 * Sets the response data
	 *
	 * @param mixed $response_data
	 */
	public function set_response($response_data)
	{
		$this->response = (is_array($response_data))
			? json_encode($response_data)
			: $response_data;
	}

	/**
	 * Gets the response data
	 *
	 * @return string The response to the API request
	 */
	public function get_response()
	{
		return $this->response;
	}

	/**
	 * Gets the name of the task being handled by the API service
	 *
	 * @return string
	 */
	public function get_task_name()
	{
		return $this->task_name;
	}

	/**
	 * Log user in.
	 * This method is mainly used for admin tasks performed via the API
	 *
	 * @param bool $admin require admin access?
	 * @param bool $member require member access?
	 * @return mixed user_id, FALSE if authentication fails
	 */
	public function _login($admin = FALSE, $member = FALSE)
    {
		$auth = Auth::instance();

		// Is user previously authenticated?
		if ($auth->logged_in())
		{
			// Check if admin privileges are required
			if ($admin == FALSE OR $auth->has_permission('admin_ui'))
			{
				return $auth->get_user()->id;
			}
			// Check if member perms required, assume admins also have member perms
			else if ($member == FALSE OR $auth->has_permission('member_ui') OR $auth->has_permission('admin_ui'))
			{
				return $auth->get_user()->id;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			//Get username and password
			if (isset($_SERVER['PHP_AUTH_USER']) &&
				isset($_SERVER['PHP_AUTH_PW']))
			{
				$username = filter_var($_SERVER['PHP_AUTH_USER'],
				FILTER_SANITIZE_STRING,
				FILTER_FLAG_ENCODE_HIGH|FILTER_FLAG_ENCODE_LOW);
				
				$password = filter_var($_SERVER['PHP_AUTH_PW'],
				FILTER_SANITIZE_STRING,
				FILTER_FLAG_ENCODE_HIGH|FILTER_FLAG_ENCODE_LOW);

				try
				{
					if ($auth->login($username, $password))
					{
						// Check if admin privileges are required
						if ($admin == FALSE OR $auth->has_permission('admin_ui'))
						{
							return $auth->get_user()->id;
						}
						else
						{
							return FALSE;
						}
					}
					else
					{
						$this->_prompt_login();
						return FALSE;
					}
				}
				catch (Exception $e)
				{
					$this->_prompt_login();
					return FALSE;
				}

			}

			//prompt user to login
			$this->_prompt_login();
			return FALSE;
		}
	}

    /**
     * Prompts user to login.
     *
     * @param int user_id - The currently logged in user id to be passed as the
     *                      realm value.
     * @return void
     */
    private function _prompt_login($user_id = 0)
    {
        header('WWW-Authenticate: Basic realm="'.$user_id.'"');
        header('HTTP/1.0 401 Unauthorized');
    }

	/**
	 * Routes the API task requests to their respective API libraries
	 *
	 * The name of the library is inferred from the name of the task. If the
	 * library is not found, a lookup is done in the task routing table. If the
	 * lookup fails, the API task request returns a "not found"(404) error
	 */
	private function _route_api_task()
    {

		// Make sure we have a task to work with
		if ( ! $this->verify_array_index($this->request, 'task'))
		{
			$this->set_response($this->get_error_msg(001, 'task'));

			// Log the failed attempt
			$this->api_logger = new Api_Log_Model();

			// Set the log data
			$this->api_logger->api_task = 'None';
			$this->api_logger->api_parameters  = strlen($this->api_parameters > 0)
				? $this->api_parameters
				: serialize('None Specified');
			$this->api_logger->api_records = 0;
			$this->api_logger->api_ipaddress = $this->request_ip_address;
			$this->api_logger->api_date = date('Y-m-d H:i:s', time());

			// Save the log
			$this->api_logger->save();

			return;
		}
		else
		{
			$this->task_name = ucfirst($this->request['task']);
		}

		// Load the base API library
		require_once Kohana::find_file('libraries/api', 'Api_Object');

		// Get the task handler (from the api config file) for the requested task
		$task_handler = $this->_get_task_handler(strtolower($this->task_name));

		$task_library_found = FALSE;

		// Check if handler has been set
		if (isset($task_handler))
		{
			// Check if the handler is an array
			if (is_array($task_handler))
			{
				// Load the library for the specified class
				$this->_init_api_library($task_handler[0]);

				// Execute the callback function
				call_user_func(array($this->api_object, $task_handler[1]));
			}
			else
			{
				// Load the library specified in $task_handler
				$this->_init_api_library($task_handler);

				// Perform the requested task
				$this->api_object->perform_task();
			}

			// Set the response data
			$this->response = $this->api_object->get_response_data();

			$task_library_found = TRUE;
		}
		else // Task handler not found in routing table therefore look the implementing library
		{
			// All library file names *must* be suffixed with the value specified in API_LIBRARY_SUFFIX
			$library_file_name = $this->task_name.API_LIBRARY_SUFFIX;

			if (Kohana::find_file('libraries/api',
				Kohana::config('config.extension_prefix').$library_file_name)) // Library file exists
			{
				// Initialize the API library
				$this->_init_api_library($this->task_name);

				// Perform the requested task
				$this->api_object->perform_task();

				// Set the response data
				$this->response = $this->api_object->get_response_data();

				$task_library_found = TRUE;
			}
			else
			{   // Library not found
				$this->response = json_encode(array(
					"error" => $this->get_error_msg(999)
				));

				// Log the unsuccessful API request
				$this->_log_api_request($task_library_found);

				return;
			}
		}

		// Log successful API request
		$this->_log_api_request($task_library_found);

		// Discard the API object from memory
		if (isset($this->api_object))
		{
			unset($this->api_object);
		}
	}

	/**
	 * Initializes the API library to be used to service the API task
	 *
	 * The name of API library file containing the class implementation is
	 * constructed/inferred from the @param $class_name
	 *
	 * @param string $base_name Name of the implementing class
	 */
	private function _init_api_library($base_name)
	{
		// Generate the name of the class
		$class_name = $base_name.API_LIBRARY_SUFFIX;

		// Check if the implementing library exists
		if ( ! Kohana::find_file('libraries/api',
			Kohana::config('config.extension_prefix').$class_name))
		{
			throw new Kohana_Exception('libraries.api_library_not_found',
				Kohana::config('config.extension_prefix').$class_name.'.php', $class_name);
		}

		// Include the implementing API library file
		require_once Kohana::find_file('libraries/api', Kohana::config('config.extension_prefix').$class_name);

		// Temporary instance for type checking
		$temp_api_object = new $class_name($this);

		// Check if the implementing library is an instance of Api_Object_Core
		// NOTE: All API libraries *MUST* be subclasses of Api_Object_Core
		if ( ! $temp_api_object instanceof Api_Object_Core)
			throw new Kohana_Exception('libraries.invalid_api_library', $class_name, 'Api_Object_Core');

		// Discard the old copy
		unset($this->temp_api_object);

		// Instaniate a fresh copy of the API library
		$this->api_object = new $class_name($this);
	}

	/**
	 * Makes sure the appropriate key is there in a given
	 * array (POST or GET) and that it is set
	 *
	 * @param arrray $arr - The given array.
	 * @param string $index  The array key index to lookup
	 * @return bool
	 */
	public function verify_array_index(array & $arr, $index)
	{
		return (isset($arr[$index]) AND array_key_exists($index, $arr));
	}

	/**
	 * Displays Error codes with their corresponding messages.
	 * returns an array error - array("code" => "CODE",
	 * "message" => "MESSAGE") based on the given code
	 *
	 * @param string $errcode  - The error code to be displayed.
	 * @param string $param - The missing parameter.
	 * @param string $message - The error message to be displayed.
	 * @return array
	 */
	public function get_error_msg($errcode, $param = '', $message='')
	{
		switch ($errcode)
		{
			case 0:
				return array(
					"code" => "0",
					"message" => Kohana::lang('ui_admin.no_error')
				);

			case 001:
				return array(
					"code" => "001",
					"message" => Kohana::lang('ui_admin.missing_parameter')." - $param."
				);

			case 002:
				return array(
					"code" => "002",
					"message" => Kohana::lang('ui_admin.invalid_parameter')
				);

			case 003:
				return array("code" => "003", "message" => $message);

			case 004:
				return array(
					"code" => "004",
					"message" => Kohana::lang('ui_admin.post_method_not_used')
				);

			case 005:
				return array(
					"code" => "005",
					"message" => Kohana::lang('ui_admin.access_denied_credentials')
				);

			case 006:
				return array(
					"code" => "006",
					"message" => Kohana::lang('ui_admin.access_denied_others')
				);

			case 007:
				return array(
					"code" => "007",
					"message" => Kohana::lang('ui_admin.no_data')
				);

			case 010:
				return array(
					"code" => "010",
					"message" => Kohana::lang('ui_admin.disabled')
				);

			case 011:
				return array(
					"code" => "011",
					"message" => Kohana::lang('ui_admin.unknown_failure')
				);

			default:
				return array(
					"code" => "999",
					"message" => Kohana::lang('ui_admin.not_found')
				);
		}
	}

	/**
	 * Looks up the api config file for the library that handles the task
	 * specified in @param $task. The api config file is the API task routing
	 * table
	 *
	 * @param string $task - Task to be looked up in the routing table
	 * @return mixed
	 */
	private function _get_task_handler($task)
	{
		$task_handler = Kohana::config('api.'.$task);

		return (isset($task_handler))
			? $task_handler
			: NULL;
	}

	/**
	 * Logs API requests
	 * If @param task_library_found == FALSE the no. of records returned is 0
	 *
	 * @param bool $task_library_found
	 */
	private function _log_api_request($task_library_found)
	{
		// Log the API request
		$this->api_logger = new Api_Log_Model();

		$this->api_logger->api_task = strtolower($this->task_name);
		$this->api_logger->api_parameters = $this->api_parameters;
		$this->api_logger->api_ipaddress = $this->request_ip_address;

		$this->api_logger->api_records = ($task_library_found)? $this->api_object->get_record_count() : 0;

		$this->api_logger->api_date = date('Y-m-d H:i:s', time());
		$this->api_logger->save();
	}

	/**
	 * Checks if the API request is allowed. The function first checks if the request IP
	 * address has been banned then proceeds to check if the IP has exceeded the quota
	 * for the day/month
	 *
	 * @return boolean
	 */
	private function _is_api_request_allowed()
	{
		// STEP 1: Check to see if site is private
		if(Kohana::config('settings.private_deployment'))
		{
			if ( ! $this->_login())
			{
				// @todo better error message
				return FALSE;
			}
		}

		// STEP 2: Check if the IP has been banned
		$banned_count = ORM::factory('api_banned')
						->where('banned_ipaddress', $this->request_ip_address)
						->count_all();

		if ($banned_count > 0)
			return FALSE;

		// STEP 3: Check if the IP address has exceeded the request quota

		// Get the API settings
		$api_settings = new Api_Settings_Model(1);

		// Check if an API request quota has been set
		if ( ! isset ($api_settings->max_requests_per_ip_address))
			return TRUE;

		// Get the API request quota
		$request_quota = $api_settings->max_requests_per_ip_address;

		// Get the quota basis
		$quota_basis = (isset($api_settings->max_requests_quota_basis))
			? $api_settings->max_requests_quota_basis
			: NULL;

		$num_api_requests = -1; // Will hold the number of API requests for the specified IP

		// Database table prefix
		$table_prefix = Kohana::config('database.default.table_prefix');

		// Template query
		$template_query = "SELECT COUNT(*) AS record_count ";
		$template_query .= "FROM ".$table_prefix."api_log ";
		$template_query .= "WHERE DATE_FORMAT(api_date, '%s') = '%s' ";
		$template_query .= "AND api_ipaddress = '".$this->request_ip_address."'";

		// Get the number of api requests logged depending on the quota basis
		switch ($quota_basis)
		{
			// Per day quota
			case 0:
				$items = $this->db->query(sprintf($template_query, '%Y-%m-%d', date('Y-m-d', time())));
				$num_api_requests = (int)$items[0]->record_count;
			break;

			// Per month quota
			case 1:
				$items = $this->db->query(sprintf($template_query, '%Y-%m', date('Y-m', time())));
				$num_api_requests = (int)$items[0]->record_count;
			break;
		}

		// Return value
		return ($num_api_requests >= $request_quota)? FALSE : TRUE;
	}
}
?>
