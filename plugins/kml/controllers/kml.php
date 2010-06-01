<?php defined('SYSPATH') or die('No direct script access.');
/**
 * KML Controller
 * Generates KML with PlaceMarkers and Category Styles
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Feed Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
* 
*/

class Kml_Controller extends Controller
{
	public function index()
	{
		// How Many Items Should We Retrieve?
		if (isset($_GET['l']) AND !empty($_GET['l']))
		{
			$limit = (int) $_GET['l'];
		}
		else
		{
			$limit = 100;
		}
		
		$incidents = ORM::factory('incident')
			->where('incident_active', '1')
			->orderby('incident_date', 'desc')
			->limit($limit)
			->find_all();
			
		$categories = ORM::factory('category')
			->where('category_visible', '1')
			->find_all();
		
		header("Content-Type: application/vnd.google-earth.kml+xml");
		header("Content-Disposition: attachment; filename=".time().".kml");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
		header("Cache-Control: cache, must-revalidate");
		header("Pragma: public");
		
		$view = new View("kml");
		$view->kml_name = htmlspecialchars(Kohana::config('settings.site_name'));
		$view->items = $incidents;
		$view->categories = $categories;
		$view->render(TRUE);
	}
}