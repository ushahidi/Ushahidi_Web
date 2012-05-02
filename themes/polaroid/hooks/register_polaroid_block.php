<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Polaroid Hook
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

 class polaroid_reports_block {

 	public function __construct()
 	{
 		$block = array(
 			"classname" => "polaroid_reports_block",
 			"name" => "Polaroid Reports",
 			"description" => "List the 35 latest reports in the system. Part of the Polaroid Theme."
 		);

 		blocks::register($block);
 		
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
 	}

 	public function block()
 	{
 		$content = new View('blocks/polaroid_reports');

 		// Get Reports
    // XXX: Might need to replace magic no. 8 with a constant
 		$content->total_items = ORM::factory('incident')
 			->where('incident_active', '1')
 			->limit('8')->count_all();
 		$content->incidents = ORM::factory('incident')
 			->where('incident_active', '1')
 			->limit('35')
 			->orderby('incident_date', 'desc')
 			->find_all();

 		echo $content;
 	}

 /**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		
	}
}

new polaroid_reports_block;
