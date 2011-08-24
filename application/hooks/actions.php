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

	protected $qualifiers;

	protected $action_id;

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
			$this->action_id = $action->action_id;
			$trigger = $action->action;
			$this->qualifiers = unserialize($action->qualifiers);
			$response = $action->response;
			$response_vars = unserialize($action->response_vars);

			// If geometry isn't set because we didn't have any geometry to pass, set it as false
			//   to prevent errors when we need to call the variable later in the script.
			if( ! isset($this->qualifiers['geometry'])) $this->qualifiers['geometry'] = FALSE;

			// Check if we qualify for performing the response

			// --- Check User

			// If it's not for everyone and the user submitting isn't the user specified, then
			//   move on to the next action
			if( ! $this->__check_user($this->qualifiers['user'],$this->data->user_id)){
				// Not the correct user
				continue;
			}

			// --- Check Category
			if( isset($this->qualifiers['category'])
				AND ! $this->__check_category($this->qualifiers['category']) ){
				// Not in the correct category
				continue;
			}

			// --- Check Location
			if( ! $this->__check_location($this->qualifiers['location'],$this->qualifiers['geometry']))
			{
				// Not the right location
				continue;
			}

			// --- Check Keywords
			//     against subject and body. If both fail, then this action doesn't qualify

			if( ! $this->__check_keywords($this->qualifiers['keyword'],$this->data->incident_title)
				AND ! $this->__check_keywords($this->qualifiers['keyword'],$this->data->incident_description))
			{
				// Not the right keyword
				continue;
			}

			// --- Check Between Times
			if( ! $this->__check_between_times(strtotime($this->data->incident_date)))
			{
				// Not the right time
				continue;
			}

			// --- Check Specific Days
			if( ! $this->__check_specific_days(strtotime($this->data->incident_date)))
			{
				// Not the right day
				continue;
			}

			// --- Begin Response

			// Record that the magic happened
			$this->__record_log($this->action_id,$this->data->user_id);

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
			$this->action_id = $action->action_id;
			$trigger = $action->action;
			$this->qualifiers = unserialize($action->qualifiers);
			$response = $action->response;
			$response_vars = unserialize($action->response_vars);

			// If geometry isn't set because we didn't have any geometry to pass, set it as false
			//   to prevent errors when we need to call the variable later in the script.
			if( ! isset($this->qualifiers['geometry'])) $this->qualifiers['geometry'] = FALSE;

			// Check if we qualify for performing the response

			// If it's not for everyone and the user submitting isn't the user specified, then
			//   move on to the next action
			if( ! $this->__check_user($this->qualifiers['user'],$this->data->user_id)){
				// Not the correct user
				continue;
			}

			// Passed User Qualifier

			// Now check location
			if( ! $this->__check_location($this->qualifiers['location'],$this->qualifiers['geometry']))
			{
				// Not the right location
				continue;
			}

			// Passed Location Qualifier

			// Check keywords against subject and body. If both fail, then this action doesn't qualify
			if( ! $this->__check_keywords($this->qualifiers['keyword'],$this->data->checkin_description))
			{
				// Not the right keyword
				continue;
			}

			// Passed Keyword Qualifier

			// --- Check Between Times
			if( ! $this->__check_between_times(strtotime($this->data->checkin_date)))
			{
				// Not the right time
				continue;
			}

			// --- Check Specific Days
			if( ! $this->__check_specific_days(strtotime($this->data->checkin_date)))
			{
				// Not the right day
				continue;
			}

			// Record that the magic happened
			$this->__record_log($this->action_id,$this->data->user_id);

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

	// Checks if the data has any categories in the qualifier set of categories
	public function __check_category($categories)
	{
		$data_id = $this->data->id;

		$report_categories = $this->db->from('incident_category')->where(array('incident_id' => $data_id))->get();

		// This data doesn't seem to have any category
		if($report_categories->count() <= 0) return FALSE;

		foreach ($report_categories as $report_category)
		{
			if(in_array($report_category->category_id,$categories))
			{
				// Category Match!
				return TRUE;
			}
		}

		// Never matched a category
		return FALSE;
	}

	/**
	 * Checks if the item is on one of the qualified days
	 *   Accepts a timestamp for $time
	 */
	public function __check_specific_days($time)
	{

		if( ! isset($this->qualifiers['specific_days'])
			OR ! is_array($this->qualifiers['specific_days']))
		{
			// We aren't checking this so pass the test
			return TRUE;
		}

		$time = date('Y-m-d',$time);
		foreach($this->qualifiers['specific_days'] as $day){
			// Check if the dates match up
			$day = date('Y-m-d',$day);
			if($time == $day)
			{
				// Found it
				return TRUE;
			}
		}

		// Never matched
		return FALSE;
	}

	/**
	 * Checks if the item is on qualified days of the week
	 */
	public function __check_days_of_the_week($time)
	{

		if( ! isset($this->qualifiers['days_of_the_week'])
			OR ! is_array($this->qualifiers['days_of_the_week']))
		{
			// We aren't checking days_of_the_week so pass the test
			return TRUE;
		}

		$days_of_the_week = $this->qualifiers['days_of_the_week'];

		// Make sure everything is lowercase
		array_walk($days_of_the_week,'strtolower');

		$day = strtolower(date('D'),$time);

		if(in_array($day,$days_of_the_week))
		{
			// Found it
			return TRUE;
		}

		// Never matched
		return FALSE;
	}

	/**
	 * Checks if the item is between two times, set as the number of seconds from the start of the day
	 *   The variable being passed, time, is a full timestamp, not the number of seconds from the start
	 *   of the day.
	 */
	public function __check_between_times($time)
	{

		if( ! isset($this->qualifiers['between_times']) OR $this->qualifiers['between_times'] != 1)
		{
			// We aren't checking between_times so pass the test
			return TRUE;
		}

		// Convert time to seconds from the start of the day
		$time_at_beginning_of_today = mktime(0,0,0,date('n'),date('j'),date('Y'));

		$seconds_from_start_of_day = $time - $time_at_beginning_of_today;

		if($this->qualifiers['between_times_1'] <= $seconds_from_start_of_day
			AND $this->qualifiers['between_times_2'] >= $seconds_from_start_of_day)
		{
			return TRUE;
		}

		// Never matched
		return FALSE;
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
				// if we found it, get out of the function
				if(stripos($string,$kw) !== FALSE) {
					return TRUE;
				}
			}
		}else{
			// If no keywords were set, then you can simply pass this test
			return TRUE;
		}
	}

	// START COUNT / QUALIFIER / RESPONSE FUNCTIONS

	/**
	 * Returns TRUE if this is the specific count or if we aren't counting
	 */
	public function __pre_response_on_specific_count()
	{
		if( ! isset($this->qualifiers['on_specific_count'])
			OR $this->qualifiers['on_specific_count'] == ''
			OR $this->qualifiers['on_specific_count'] == 0)
		{
			// We arent checking
			return TRUE;
		}

		// Count is the specific count we need to hit
		$count = $this->qualifiers['on_specific_count'];

		// Collective determines if we look up count by user_id or by action_id
		$collective = FALSE;
		if(isset($this->qualifiers['on_specific_count_collective'])
			AND $this->qualifiers['on_specific_count_collective'] == 1)
		{
			$collective = TRUE;
		}

		// Look up count
		if($collective)
		{
			// Search by action_id
			$check_count = $this->db->where(array('action_id' => $this->action_id))->count_records('actions_log');
		}else{
			// Search by user_id
			$check_count = $this->db->where(array('action_id' => $this->action_id, 'user_id' => $this->data->user_id))->count_records('actions_log');
		}

		// Count matches
		if($check_count == $count) return TRUE;

		// We never matched the counts
		return FALSE;
	}

	// START RESPONSE FUNCTIONS

	/**
	 * Routes the response to the appropriate response function for processing
	 */
	public function __perform_response($response,$response_vars)
	{
		// Go through the list of count qualifiers to see if we should be performing a response or not

		// on_specific_count
		if( ! $this->__pre_response_on_specific_count()) return FALSE;
		//if( ! $this->__pre_response_on_specific_count()) return FALSE;
		// etc ...

		// Route and perform the response
		switch ($response) {
			case 'email':
				return $this->__response_email($response_vars);
			case 'approve_report':
				return $this->__response_approve_report($response_vars);
			case 'log_it':
				// This response is special in that it does nothing and allows
				//   a line to be written to the action log
				return TRUE;
			case 'assign_badge':
				return $this->__response_assign_badge($response_vars);
			default:
				return FALSE;
		}

		return FALSE;

	}

	/**
	 * Saves this action to the log, which is used as a qualifier in some cases
	 */
	public function __record_log($action_id,$user_id)
	{
		$actions_log = new Actions_Log_Model();
		$actions_log->action_id = $action_id;
		$actions_log->user_id = $user_id;
		$actions_log->time = time();
		$actions_log->save();

		return TRUE;
	}

	/**
	 * Approve a report and assign it to one or more categories
	 */
	public function __response_approve_report($vars)
	{
		$incident_id = $this->data->id;

		$categories = array();
		if( isset($vars['add_category']))
		{
			$categories = $vars['add_category'];
		}

		$verify = 0;
		if( isset($vars['verify']))
		{
			$verify = (int)$vars['verify'];
		}

		foreach($categories as $category_id)
		{
			// Assign Category
			Incident_Category_Model::assign_category_to_incident($incident_id,$category_id);
		}

		// Approve Report
		Incident_Model::set_approve($incident_id,1);

		// Set Verification
		Incident_Model::set_verification($incident_id,$verify);

		return TRUE;
	}

	/**
	 * Assigns a badge to the triggering user
	 */
	public function __response_assign_badge($vars)
	{
		$count = ORM::factory('badge_user')->where(array('badge_id' => (int)$vars['badge'], 'user_id' => (int)$this->data->user_id))->count_all();
		if($count == 0)
		{
			$badge_user = new Badge_User_Model();
			$badge_user->badge_id = $vars['badge']; // badge id
			$badge_user->user_id = $this->data->user_id;
			$badge_user->save();
		}
		return TRUE;
	}

	/**
	 * Shoots an email to the defined person
	 */
	public function __response_email($vars)
	{
		$settings = kohana::config('settings');

		if($vars['email_send_address'] == '0')
		{
			// If our send address is 0, then it means we need to send the email to
			//   the triggering user
			$to = User_Model::get_email($this->data->user_id);
		}else{
			$to = $vars['email_send_address'];
		}

		$from = array($settings['site_email'], $settings['site_name']);
		$subject = $vars['email_subject'];
		$message = $vars['email_body'];

		return email::send($to, $from, $subject, $message, FALSE);
	}
}

new actioner;
