<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Mobile Controller
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

	// Table Prefix
	protected $table_prefix;

    public function __construct()
    {
		parent::__construct();
		
		// Set Table Prefix
		$this->table_prefix = Kohana::config('database.default.table_prefix');
		
		// Load Header & Footer
        $this->template->header  = new View('mobile/header');
        $this->template->footer  = new View('mobile/footer');

		$this->template->header->site_name = Kohana::config('settings.site_name');
		$this->template->header->site_tagline = Kohana::config('settings.site_tagline');

		plugin::add_javascript('mobile/views/js/jquery');
		plugin::add_javascript('mobile/views/js/expand');
		plugin::add_stylesheet('mobile/views/css/styles');
		
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
				->orderby('category_title')
				->find_all() as $category)
        {
            // Get The Children
			$children = array();
			foreach ($category->children as $child)
			{
				$children[$child->id] = array(
					$child->category_title,
					$child->category_color,
					$child->category_image,
					$this->_category_count($child->id)
				);
			}

			// Put it all together
            $parent_categories[$category->id] = array(
				$category->category_title,
				$category->category_color,
				$category->category_image,
				$this->_category_count($category->id),
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
	
	private function _category_count($category_id = false)
	{
		if ($category_id)
		{
			return ORM::factory('incident_category')
				->join('incident', 'incident_category.incident_id', 'incident.id')
				->where('category_id', $category_id)
				->where('incident_active', '1')
				->count_all();
		}
		else
		{
			return 0;
		}
	}
}