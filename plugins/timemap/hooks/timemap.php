<?php defined('SYSPATH') or die('No direct script access.');
/**
 * TimeMap Hook
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

class timemap {
	
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
		// Hook only into the main router
		if (Router::$controller == 'main')
		{
			// Add Sidebar Content
			Event::add('ushahidi_action.main_sidebar', array($this, 'sidebar'));
		}
	}
	
	/**
	 * Add Sidebar Content
	 * 
	 * @return void
	 */
	public function sidebar()
	{
		$sidebar = View::factory('timemap/sidebar');
		$sidebar->render(TRUE);
	}
}

new timemap;