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

class Reports_Controller extends Mobile_Controller {

    public function __construct()
    {
		parent::__construct();
	}
	
	/**
	 * Displays a list of reports
	 * @param boolean $category_id If category_id is supplied filter by
	 * that category
	 */
	public function index($category_id = false)
	{
		$this->template->content = new View('mobile/reports');
		
		$db = new Database;
		
		$filter = ( $category_id )
			? " AND ( c.id='".$category_id."' OR 
				c.parent_id='".$category_id."' )  "
			: " AND 1 = 1";
			
		// Pagination
		$pagination = new Pagination(array(
				'query_string' => 'page',
				'items_per_page' => (int) Kohana::config('mobile.items_per_page'),
				'total_items' => $db->query("SELECT DISTINCT i.* FROM `".$this->table_prefix."incident` AS i JOIN `".$this->table_prefix."incident_category` AS ic ON (i.`id` = ic.`incident_id`) JOIN `".$this->table_prefix."category` AS c ON (c.`id` = ic.`category_id`) WHERE `incident_active` = '1' $filter")->count()
				));

		$incidents = $db->query("SELECT DISTINCT i.* FROM `".$this->table_prefix."incident` AS i JOIN `".$this->table_prefix."incident_category` AS ic ON (i.`id` = ic.`incident_id`) JOIN `".$this->table_prefix."category` AS c ON (c.`id` = ic.`category_id`) WHERE `incident_active` = '1' $filter ORDER BY incident_date DESC LIMIT ". (int) Kohana::config('mobile.items_per_page') . " OFFSET ".$pagination->sql_offset);
		
		// If Category Exists
		if ($category_id)
		{
			$category = ORM::factory("category", $category_id);
		}
		else
		{
			$category = FALSE;
		}
			
		$this->template->content->incidents = $incidents;
		$this->template->content->category = $category;
	}
	
	/**
	 * Displays a report.
	 * @param boolean $id If id is supplied, a report with that id will be
	 * retrieved.
	 */
	public function view($id = false)
	{
		$this->template->header->show_map = TRUE;
		$this->template->header->js = new View('mobile/reports_view_js');
		$this->template->content = new View('mobile/reports_view');
		
		if ( ! $id )
		{
			url::redirect('mobile');
		}
		else
		{
			$incident = ORM::factory('incident', $id);
			if ( ! $incident->loaded)
			{
				url::redirect('mobile');
			}
			
			$this->template->content->incident = $incident;
			
			$this->template->header->js->latitude = $incident->location->latitude;
			$this->template->header->js->longitude = $incident->location->longitude;
		}
	}
}