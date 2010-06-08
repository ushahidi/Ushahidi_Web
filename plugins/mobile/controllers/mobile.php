<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Mobile Controller
 * Generates KML with PlaceMarkers and Category Styles
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Mobile Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
* 
*/

class Mobile_Controller extends Template_Controller {
	
	public $auto_render = TRUE;
	public $mobile = TRUE;
	
	// Main template
    public $template = 'mobile/layout';

    public function __construct()
    {
		parent::__construct();
		
		// Load Header & Footer
        $this->template->header  = new View('mobile/header');
        $this->template->footer  = new View('mobile/footer');

		$this->template->header->site_name = Kohana::config('settings.site_name');
		$this->template->header->site_tagline = Kohana::config('settings.site_tagline');

		page::add_javascript('mobile/js/jquery');
		page::add_javascript('mobile/js/expand');
		page::add_stylesheet('mobile/css/styles');
		$this->template->header->show_map = FALSE;
		$this->template->header->js = "";
	}
	
	public function index()
	{
		$this->template->content  = new View('mobile/main');
		
		// Get 10 Most Recent Reports
		$this->template->content->incidents = ORM::factory('incident')
            ->where('incident_active', '1')
			->limit('10')
            ->orderby('incident_date', 'desc')
			->with('location')
            ->find_all();
		
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
        $this->template->content->categories = $parent_categories;

		// Get RSS News Feeds
		$this->template->content->feeds = ORM::factory('feed_item')
			->limit('10')
            ->orderby('item_date', 'desc')
            ->find_all();
	}
}