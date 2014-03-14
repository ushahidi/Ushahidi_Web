<?php defined('SYSPATH') or die('No direct script access.');
/**
 * SMSSync Hook
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class smssync {
	
	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{	
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
		Event::add('ushahidi_action.message_sms_add', array($this, 'after_add'));
	}
	
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		// SMS Provider
		plugin::add_sms_provider("smssync");
	}
	
	/**
	 * Adds incident_url to message for sms if incident is auto created for sms add 
	 */
	public function after_add() {
		$sms = Event::$data;
		if($sms->incident_id) {
			$incident_url = Incident_Model::get_url($sms->incident_id);
			$sms->message = $sms->message.'<a href="'.$incident_url.'">View incident</a>';
			$sms->save();
		}
	}

}

new smssync;