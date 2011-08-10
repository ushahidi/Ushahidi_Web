<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Actions Hook
 * Determines the capabilities of the users browser
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Browser Hoook
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

// This file processes all of the administrator defined actions in the actions section of the
//   admin panel. The "actioner" class waits for events to be fired and then performs user
//   defined actions

class actioner {

	protected $db;
	
	protected $data;

	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{
		$this->db = new Database();

		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
	}

	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		Event::add('ushahidi_action.report_add', array($this, '_report_add'));
		Event::add('ushahidi_action.checkin_recorded', array($this, '_checkin_recorded'));
	}
	
	// START ACTION TRIGGER FUNCTIONS

	/**
	 * Perform actions for report_add
	 */
	public function _report_add()
	{
		$this->data = Event::$data;

		// Grab all action triggers that involve this fired action
		$actions = $this->db->from('actions')->where(array('action' => 'report_add', 'active' => 1))->get();

		// Get out of here as fast as possible if there are no actions.
		if($actions->count() <= 0) return false;

		foreach ($actions as $action)
		{
			// Collect variables for this action
			$action_id = $action->action_id;
			$trigger = $action->action;
			$qualifiers = unserialize($action->qualifiers);
			$response = $action->response;
			$response_vars = unserialize($action->response_vars);
			
			// If geometry isn't set because we didn't have any geometry to pass, set it as false
			//   to prevent errors when we need to call the variable later in the script.
			if( ! isset($qualifiers['geometry'])) $qualifiers['geometry'] = FALSE;

			// Check if we qualify for performing the response

			// If it's not for everyone and the user submitting isn't the user specified, then
			//   move on to the next action
			if( ! $this->__check_user($qualifiers['user'],$this->data->user_id)){
				// Not the correct user
				continue;
			}
			
			// Passed User Qualifier

			// Now check location
			if( ! $this->__check_location($qualifiers['location'],$qualifiers['geometry']))
			{
				// Not the right location
				continue;
			}
			
			// Passed Location Qualifier
			
			// Check keywords against subject and body. If both fail, then this action doesn't qualify
			if( ! $this->__check_keywords($qualifiers['keyword'],$this->data->incident_title)
				AND ! $this->__check_keywords($qualifiers['keyword'],$this->data->incident_description))
			{
				// Not the right keyword
				continue;
			}
			
			// Passed Keyword Qualifier
			
			// Qapla! Begin response phase since we passed all of the qualifier tests
			$this->__perform_response($response,$response_vars);
		}
	}
	
	/**
	 * Perform actions for checkin_recorded
	 */
	public function _checkin_recorded()
	{
		$this->data = Event::$data;
		
		// Grab all action triggers that involve this fired action
		$actions = $this->db->from('actions')->where(array('action' => 'checkin_recorded', 'active' => 1))->get();

		// Get out of here as fast as possible if there are no actions.
		if($actions->count() <= 0) return false;
		
		foreach ($actions as $action)
		{
			// Collect variables for this action
			$action_id = $action->action_id;
			$trigger = $action->action;
			$qualifiers = unserialize($action->qualifiers);
			$response = $action->response;
			$response_vars = unserialize($action->response_vars);
			
			// If geometry isn't set because we didn't have any geometry to pass, set it as false
			//   to prevent errors when we need to call the variable later in the script.
			if( ! isset($qualifiers['geometry'])) $qualifiers['geometry'] = FALSE;

			// Check if we qualify for performing the response

			// If it's not for everyone and the user submitting isn't the user specified, then
			//   move on to the next action
			if( ! $this->__check_user($qualifiers['user'],$this->data->user_id)){
				// Not the correct user
				continue;
			}
			
			// Passed User Qualifier

			// Now check location
			if( ! $this->__check_location($qualifiers['location'],$qualifiers['geometry']))
			{
				// Not the right location
				continue;
			}
			
			// Passed Location Qualifier
			
			// Check keywords against subject and body. If both fail, then this action doesn't qualify
			if( ! $this->__check_keywords($qualifiers['keyword'],$this->data->checkin_description))
			{
				// Not the right keyword
				continue;
			}
			
			// Passed Keyword Qualifier
			
			// Qapla! Begin response phase since we passed all of the qualifier tests
			$this->__perform_response($response,$response_vars);
		}
	}
	
	// START QUALIFIER CHECK FUNCTIONS
	
	// Checks if user is global and matches the data passed for userid
	public function __check_user($user,$user_check_against)
	{
		if(($user != 0) AND ($user != $user_check_against)){
			return false;
		}
		return true;
	}
	
	/**
	 * Takes a location as "lon lat" and checks if it is inside a polygon which
	 *   is passed as an array like array("lon lat","lon lat","lon lat","lon lat",. . .);
	 *   As far as I know, the polygon can be as complex as you like.
	 */
	public function __check_location($location,$m_geometry)
	{
		
		if($location == 'specific')
		{
			// So location now needs to be in a specific spot. Gotta crunch the numbers to see if the
			//   lat/lon of this report falls inside the user defined polygons

			$pointLocation = new pointinpoly();

			foreach($m_geometry as $geometry)
			{
				// Set the polygon of the fence

				$geometry = str_ireplace('{"geometry":"POLYGON((','',$geometry);
				$geometry = str_ireplace('))"}','',$geometry);
				$polygon = explode(',',(string)$geometry);

				// Find the lat,lon of the report

				$location = ORM::factory('location',$this->data->location_id);
				$point = $location->longitude.' '.$location->latitude;

				if($pointLocation->pointInPolygon($point, $polygon)){
					// It's inside the fence!
					return true;
				}
			}
			
			// It's not inside the fence. Sorry, bro.
			return false;
			
		}
		
		return true;
	}
	
	/**
	 * Takes a CSV list of keywords and checks each of them against a string
	 */
	public function __check_keywords($keywords,$string)
	{
		if($keywords != '')
		{
			// Okay, keywords were defined so lets check to see if the keywords match
			$exploded_kw = explode(',',$keywords);
			foreach($exploded_kw as $kw){
				echo 'Check against: '.$kw.'<br/>';
				// if we found it, get out of the function
				if(stripos($string,$kw) !== FALSE) {
					echo 'Found it, boss<br/>';
					return true;
				}
			}
		}else{
			// If no keywords were set, then you can simply pass this test
			echo 'No keywords set, passing test.<br/>';
			return true;
		}
	}
	
	// START RESPONSE FUNCTIONS
	
	/**
	 * Routes the response to the appropriate response function for processing
	 */
	public function __perform_response($response,$response_vars)
	{
		echo '<h3>Performing Response: '.$response.'</h3>';
		
		switch ($response) {
			case 'email':
				return $this->__response_email($response_vars);
			case 'approve_report':
				return $this->__response_approve_report($response_vars);
			default:
				return false;
		}
		
	}
	
	/**
	 * Approve a report and assign it to one or more categories
	 */
	public function __response_approve_report($vars)
	{	
		echo '<h1>Add category!111!!!!</h1>';
		$categories = $vars['add_category'];
		$incident_id = $this->data->id;
		
		foreach($categories as $category_id)
		{
			// Assign Category
			Incident_Category_Model::assign_category_to_incident($incident_id,$category_id);
		}
		
		// Approve Report
		Incident_Model::set_approve($incident_id,1);
		
		return true;
	}
	
	/**
	 * Shoots an email to the defined person
	 */
	public function __response_email($vars)
	{	
		$settings = kohana::config('settings');
		
		$to = $vars['email_send_address'];
		$from = array($settings['site_email'], $settings['site_name']);
		$subject = $vars['email_subject'];
		$message = $vars['email_body'];
		
		echo 'email sent';
		
		return email::send($to, $from, $subject, $message, FALSE);
	}
}

new actioner;
