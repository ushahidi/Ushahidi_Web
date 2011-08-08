<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Facebook Social Hook - Load All Events
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

class facebooksocial {
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
		if (Router::$controller == 'reports' AND Router::$method == 'view')
		{
			Event::add('ushahidi_action.report_extra', array($this, 'embed_facebook'));
			// Event::add('ushahidi_action.main_sidebar', array($this, 'hello'));
			
			// Overwrite current comments block and comments form block
			//Event::add('ushahidi_filter.comment_block', array($this, '_overwrite_comments'));
			//Event::add('ushahidi_filter.comment_form_block', array($this, '_overwrite_comments_form'));
		}
	}
	
	public function embed_facebook()
	{
		$view = View::factory('facebook-social');
		$view->facebook_app_id = Kohana::config('facebook-social.facebook_api_key');
		$view->xd_receiver = url::site()."xd_receiver";
		$view->render(TRUE);
	}
}

new facebooksocial;