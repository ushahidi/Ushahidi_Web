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