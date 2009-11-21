<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Categories of reported Incidents
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Category Model  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Category_Model extends ORM_Tree
{	
	protected $has_many = array('incident' => 'incident_category', 'category_lang');
	
	// Database table name
	protected $table_name = 'category';
	protected $children = "category";
	
	static function categories($id=NULL,$locale='en_US')
	{
		if($id == NULL){
			$categories = ORM::factory('category')->where('locale',$locale)->find_all();
		}else{
			$categories = ORM::factory('category')->where('id',$id)->find_all(); // Don't need locale if we specify an id
		}
		
		$cats = array();
		foreach($categories as $category) {
			$cats[$category->id]['category_id'] = $category->id;
			$cats[$category->id]['category_title'] = $category->category_title;
			$cats[$category->id]['category_color'] = $category->category_color;
		}
		
		return $cats;
	}
}
