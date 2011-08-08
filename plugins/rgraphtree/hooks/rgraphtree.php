<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Hello Ushahidi Hook - Load All Events
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module     Hello Ushahidi Hook
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class rgraphtree {
	
	// Table Prefix
    protected $table_prefix;
	
	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{
		$this->table_prefix = Kohana::config('database.default.table_prefix');
		
		$this->cache = new Cache;
		
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
	}
	
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		Event::add('ushahidi_action.main_sidebar', array($this, '_display_rgraphtree'));
	}
	
	/**
	 * Render the Action Taken Information to the Report
	 * on the front end
	 */
	public function _display_rgraphtree()
	{
		// Load the View
		$view = View::factory('rgraphtree');
		$view->rgraphtree_js = new View('rgraphtree_js');
		
		$subdomain = Kohana::config('settings.subdomain');
		
		// Build JSON or grab it from cache
		$cache = Cache::instance();
		$json = $cache->get($subdomain.'_rgraphtree_json');
		if ( ! $json)
		{ // Cache is Empty so Re-Cache
		
			$array = array('id'=>'root_node','name'=>'<span style="color:#000000;font-weight:bold;font-style:italic;">'.Kohana::config('settings.site_name').'</span>','children'=>array());
			
			// Get parent categories
			$p_cats = ORM::factory('category')->where('parent_id',0)->where('category_visible',1)->find_all();
			$i = 0;
			foreach ($p_cats as $p_cat)
			{
				// Set parent category
				$array['children'][$i] = array('id'=>'c_'.$p_cat->id,'name'=>'<span style="color:#'.$p_cat->category_color.';font-weight:bold;font-style:italic;">'.$p_cat->category_title.'</span>','children'=>array());
				
				// Get child categories
				$c_cats = ORM::factory('category')->where('parent_id',$p_cat->id)->where('category_visible',1)->find_all();
				$n = 0;
				foreach ($c_cats as $c_cat)
				{
					// Set child in parent node
					$array['children'][$i]['children'][$n] = array('id'=>'c_'.$c_cat->id,'name'=>'<span style="color:#'.$c_cat->category_color.';font-weight:bold;">'.$c_cat->category_title.'</span>','children'=>array());
					
					// Get child categories reports
					$reports = $this->get_reports_in_category($c_cat->id);
	                $r = 0;
					foreach($reports as $report)
					{
						// Set report in the child category
						$array['children'][$i]['children'][$n]['children'][$r] = array('id'=>'r_'.$report->id,'name'=>substr($report->incident_title,0,5).'...');
						$r++;
					}
					
					$n++;
				}
				
				// Get parent categories reports
				
				$reports = $this->get_reports_in_category($p_cat->id);
				foreach($reports as $report)
				{
					// Set report in parent category
					//   note: Keep using 'n' as an index here
					$array['children'][$i]['children'][$n] = array('id'=>'r_'.$report->id,'name'=>substr($report->incident_title,0,5).'...');
					$n++;
				}
				
				$i++;
			}
			
			$json = json_encode($array);
			$cache->set($subdomain.'_rgraphtree_json', $json, array('rgraphtree_json'), 600);
		}
		
		$view->rgraphtree_js->json = $json;
		
		//echo '<br/><br/>';
		//echo $view->rgraphtree_js->json;
		//echo '<br/><br/>';
		
		$view->render(TRUE);
	}
	
	private function get_reports_in_category($catid)
	{
		return ORM::factory('incident')
			->select('DISTINCT incident.*')
			->join('incident_category', 'incident.id', 'incident_category.incident_id','LEFT')
			->where('incident.incident_active = 1 AND ('.$this->table_prefix.'incident_category.category_id = '.$catid.')')
			->find_all();
	}
}

new rgraphtree;