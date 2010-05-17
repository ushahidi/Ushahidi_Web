<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Test Hook - Load All Events
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

class test {
	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{		
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
	}
	
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		// Only add the events if we are on that controller
		if (Router::$controller == "reports")
		{
			switch (Router::$method)
			{
				case "view":
					Event::add('ushahidi.report_title', array($this, 'foo'));
					break;
			}
		}
	}
	
	public function foo()
	{
		global $incident_title;
		$incident_title = Event::$data . " YESS!!!!!";
	}
}

new test;