<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Dispatch Library
 * Run other controllers
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Maarten Van Vliet (dlib) http://code.google.com/p/kohana-mptt/
 * @package    Ushahidi - http://source.ushahididev.com
 * @port	   David Kobia <david@ushahidi.com> 
 * @module     Dispatch Library
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Dispatch_Core {

	/**
	 * Directory in which the controller file is located
	 * @var string
	 */
	protected $directory;

	/**
	 * Name of the controller
	 * @var string
	 */
	protected $controller;
	
	public static function controller($controller, $directory)
	{
		$controller_file=strtolower($controller);
		
		// Set controller class name
		$controller = ucfirst($controller).'_Controller';
		
		if(!class_exists($controller, FALSE))
		{					
			// If the file doesn't exist, just return
			if (($filepath = Kohana::find_file('controllers/'.$directory, $controller_file)) === FALSE)
					return FALSE;
					
			// Include the Controller file
			require_once $filepath;
		}

		// Run system.pre_controller
		Event::run('dispatch.pre_controller');
		
		// Initialize the controller
		$controller = new $controller;

		// Run system.post_controller_constructor
		Event::run('dispatch.post_controller_constructor');				
								
		return new Dispatch($controller);
	}

	public function __construct(Controller $controller, $directory = NULL)
	{
		$this->controller=$controller;
	}

	public function __get($key)
	{
		return ($key=='controller')
		    ? $this->$key
		    : $this->controller->$key;
	}

	public function __set($key,$value)
	{
		$this->controller->$key=$value;
	}

	public function __toString()
	{
		return $this->render();
	}

	public function render()
	{
		return (string) $this->controller;
	}

	public function __call($name,$arguments=null)
	{
		if (method_exists($this->controller,$name))
		{
			return $this->method($name,$arguments);
		}

		return FALSE;
	}

	public function method($method,$arguments=null)
	{
		if ( ! method_exists($this->controller,$method))
			return FALSE;
				
		if (method_exists($this->controller,'_remap'))
		{
			// Make the arguments routed
			$arguments = array($method, $arguments);

			// The method becomes part of the arguments
			array_unshift($arguments, $method);

			// Set the method to _remap
			$method = '_remap';
		}		
						
		ob_start();
		
		if (is_string($arguments))
		{
			$arguments=array($arguments);
		}
				
		switch (count($arguments))
		{
			case 1:
				$result=$this->controller->$method($arguments[0]);
			break;

			case 2:
				$result=$this->controller->$method($arguments[0], $arguments[1]);
			break;

			case 3:
				$result=$this->controller->$method($arguments[0],
					$arguments[1], $arguments[2]);
			break;

			case 4:
				$result=$this->controller->$method($arguments[0],
					$arguments[1], $arguments[2], $arguments[3]);
			break;

			default:
				// Resort to using call_user_func_array for many segments
				$result=call_user_func_array(
				    array($this->controller, $method),
				    $arguments
				);
			break;
		}				

		// Run system.post_controller
		Event::run('dispatch.post_controller');

		if ($result!=NULL)
		{
			$result=ob_get_contents();
			
			ob_end_clean();			
		}

		return $result;
	}
}
