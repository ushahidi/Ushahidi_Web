<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Sharing Hook - Load All Events
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

class sharing {
	
	/**
	 * Registers the main event add method
	 */

	protected $user;

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
		if (Router::$controller == 'manage' or Router::$controller == 'sharing')
		{
			Event::add('ushahidi_action.nav_admin_manage', array($this,'_sharing'));
		}
		elseif (strripos(Router::$current_uri, "main") !== false)
		{
			Event::add('ushahidi_action.header_scripts', array($this, 'sharing_js'));
			Event::add('ushahidi_action.main_sidebar', array($this, 'sharing_bar'));
		}
	}

	public function _sharing()
	{
		$this_sub_page = Event::$data;
		echo ($this_sub_page == "sharing") ? "Sharing" : "<a href=\"".url::site()."admin/manage/sharing\">Sharing</a>";
	}

	/**
	 * Loads the sharing bar on the side bar on the main page
	 */
	public function sharing_bar()
	{
		// Get all active Shares
		$shares = array();
		foreach (ORM::factory('sharing')
					->where('sharing_active', 1)
					->find_all() as $share)
		{
			$shares[$share->id] = array($share->sharing_name, $share->sharing_color);
		}

		$sharing_bar = View::factory('sharing/sharing_bar');

		$sharing_bar->shares = $shares;
		$sharing_bar->render(TRUE);
	}
	
	/**
	 * Loads the JavaScript for the sharing sidebar
	 */
	public function sharing_js()
	{
		$js = View::factory('js/sharing_bar_js');
		$js->render(TRUE);
	}
}
new sharing;
