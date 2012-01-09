<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Full Screen Map Hook - Load All Events
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

class fullscreenmap {
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
		if (Router::$controller == 'main')
		{
			plugin::add_javascript('fullscreenmap/views/js/jquery.colorbox');
			#plugin::add_javascript('fullscreenmap/views/js/ui.draggable');
			plugin::add_stylesheet('fullscreenmap/views/css/fullscreenmap');
			plugin::add_stylesheet('fullscreenmap/views/css/colorbox');
			
			Event::add('ushahidi_action.header_scripts', array($this, '_main_js'));
			Event::add('ushahidi_action.map_main_filters', array($this, '_button'));
		}
	}
	
	public function _main_js()
	{
		$js = View::factory('fullscreenmap/main_js');
		
		// Get all active top level categories
		$parent_categories = array();
		foreach (ORM::factory('category')
				->where('category_visible', '1')
				->where('parent_id', '0')
				->find_all() as $category)
		{
            // Get The Children
			$children = array();
			foreach ($category->children as $child)
			{
				$children[$child->id] = array(
					$child->category_title,
					$child->category_color,
					$child->category_image
				);
			}

			// Put it all together
			$parent_categories[$category->id] = array(
				$category->category_title,
				$category->category_color,
				$category->category_image,
				$children
			);
		}
		
		// ** Next Version Will Have Floating Windows on Map
		//$js->categories_view = View::factory('fullscreenmap/categories');
		//$js->categories_view->categories = $parent_categories;
		//$js->categories_view->default_map_all = Kohana::config('settings.default_map_all');
		$js->categories_view = "";
		$js->render(TRUE);
	}
	
	public function _button()
	{
		$button = View::factory('fullscreenmap/button');
		$button->render(TRUE);
	}
}
new fullscreenmap;