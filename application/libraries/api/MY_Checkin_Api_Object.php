<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Checkin_Api_Object
 *
 * This class handles reports activities via the API.
 *
 * @version 1 - Brian Herbert (not sure what this version is for, though)
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Checkin_Api_Object extends Api_Object_Core {
	
	public function __construct($api_service)
	{
		parent::__construct($api_service);		
	}
	
	/**
	 * Implementation of abstract method in parent
	 *
	 * Handles the API task parameters
	 */
	public function perform_task()
	{	
		// Checkins have been removed from Ushahidi now
		$this->set_ci_error_message(array(
			"error" => $this->api_service->get_error_msg(010)
		));

		$this->show_response();
	}
	
	public function show_response()
	{
		if ($this->response_type == 'json')
		{
			echo json_encode($this->response);
		} 
		else 
		{
			echo $this->array_as_xml($this->response, array());
		}
	}

	public function set_ci_error_message($resp)
	{
		$this->response = $resp;
	}
}
