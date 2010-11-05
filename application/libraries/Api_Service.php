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
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

// Suffix for all API library names
define('API_LIBRARY_SUFFIX', '_Api_Object');

final class Api_Service {    
    private $request = array();
    private $response; // Response to be returned to the calling controller
    private $response_type;
    private $api_object; // Handle for the API library to loaded
    private $task_name; // Name of the task to be routed
    
    public function __construct()
    {
        // Set the request data
        $this->request = ($_SERVER['REQUEST_METHOD'] == 'POST')
            ? $_POST
            : $_GET;
        
        // Load the API configuration file
        Kohana::config_load('api');
    }

    /**
     * Runs the API service
     */
    public function run_service()
    {
        // Route the API task
        $this->_route_api_task();        
    }
    
    public function get_request()
    {
        return $this->request;        
    }
    
    /**
     * Sets the response type
     * @param $response_type New value for $this->response_type
     */
    public function set_response_type($response_type)
    {
        $this->response_type = $response_type;
    }
        
    /**
     * Returns the response type
     */
    public function get_response_type()
    {
        return $this->response_type;
    }
        
    /**
     * Sets the response data
     */
    public function set_response($response_data)
    {
        $this->response = (is_array($response_data))
            ? json_encode($response_data)
            : $response_data;
    }
    
    /**
     * Gets the response data
     */
    public function get_response()
    {
        return $this->response;
    }    
    
    /**
     * Gets the name of the task being handled by the API service
     */                 
    public function get_task_name()
    {
        return $this->task_name;
    }
    
    /**
     * Log user in.
     *
     * This method is mainly used for admin tasks performed via the API 
     *
     * @param string $username User's username.
     * @param string $password User's password.
     *
     * @return int user_id, false if authentication fails
     */
    public function _login($username, $password)
    {
        $auth = Auth::instance();

        // Is user previously authenticated?
        if ($auth->logged_in())
        {
            return $auth->get_user()->id;
        }
        else
        {
            // Attempt a login
            if ($auth->login($username, $password))
            {
                return $auth->get_user()->id;
            }
            else
            {
                return false;
            }
        }
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
            }
            else
            {   // Library not found
                $this->response = json_encode(array(
                    "error" => $this->get_error_msg(999)
                ));
                
                return;
            }
        }
        
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
     * @param $class_name Name of the implementing class
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
        
        // TODO: Log API requests
    
    }
    
    /**
     * Makes sure the appropriate key is there in a given 
     * array (POST or GET) and that it is set
     * @param ar Array - The given array.
     * @param index String - The index array
     * 
     * @return Boolean
     */
    public function verify_array_index(&$arr, $index)
    {
        
        if (isset($arr[$index]) AND array_key_exists($index, $arr))
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
    public function get_error_msg($errcode, $param = '', $message='')
    {
        switch ($errcode)
        {
            case 0:
                return array("code" => "0", 
                             "message" => Kohana::lang('ui_admin.no_error')
                         );
            case 001:
                return array("code" => "001",
                             "message" => Kohana::lang('ui_admin.missing_parameter')." - $param."
                         );
            case 002:
                return array("code" => "002",
                             "message" => Kohana::lang('ui_admin.invalid_parameter')
                         );
            case 003:
                return array("code" => "003", "message" => $message );
            case 004:
                return array("code" => "004", 
                             "message" => Kohana::lang('ui_admin.post_method_not_used')
                         );
            case 005:
                return array("code" => "005",
                             "message" => Kohana::lang('ui_admin.access_denied_credentials')
                         );
            case 006:
                return array("code" => "006",
                             "message" => Kohana::lang('ui_admin.access_denied_others')
                         );
            case 007:
                return array("code" => "007",
                        "message" => Kohana::lang('ui_admin.no_data'));
            default:
                return array("code" => "999",
                             "message" => Kohana::lang('ui_admin.not_found')
                         );
        }
    }
    
    /**
     * Looks up the api config file for the library that handles the task
     * specified in @param $task. The api config file is the API task routing
     * table
     *
     * @param $task - Task to be looked up in the routing table
     */
    private function _get_task_handler($task)
    {
        $task_handler = Kohana::config('api.'.$task);
        
        return (isset($task_handler))
            ? $task_handler
            : NULL;
    }
}
?>
