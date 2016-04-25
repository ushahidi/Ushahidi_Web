<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Timemap Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   Timemap Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Timemap_Controller extends Template_Controller {

	/**
	 * Automatically render the views loaded in this controller
	 * @var bool
	 */
	public $auto_render = TRUE;

	/**
	 * Name of the template view
	 * @var string
	 */
	public $template = 'timemap/timemap';


	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Create The Timemap
	 */
	public function index()
	{
		$this->template->site_name = Kohana::config('settings.site_name');
		$this->template->site_tagline = Kohana::config('settings.site_tagline');
		$this->template->css_url = url::file_loc('css');
		$this->template->js_url = url::file_loc('js');

		$start_date = date('Y-m-d');
		$last_date = "";
		
		$incident = ORM::factory('incident')
			->where('incident.incident_active = 1 ')
			->orderby("incident_date", "asc")
			->find();

		if ($incident->loaded)
		{
			$start_date = date('Y-m-d',strtotime($incident->incident_date));
		}

		$this->template->start_date= $start_date;
	}
	
	/**
	 * Timemap JSON
	 */
	public function json()
	{
		$this->auto_render = FALSE;
		$this->template = '';

		// get timestamps
		$_GET['s'] = strtotime($_GET['start']);
		$_GET['e'] = strtotime($_GET['end']);
		$cb = $_GET['callback'];

		$json = array();
		$markers = reports::fetch_incidents();
		foreach ($markers as $marker)
		{
			$json[] = array(
				'title' => $marker->incident_title,
				'start' => date('Y-m-d\TH:i:sO', strtotime($marker->incident_date)),
				'point' => array(
					'lat' => $marker->latitude,
					'lon' => $marker->longitude
					),
				'options' => array(
					'description' => $marker->incident_description
					)
				);
		}

		echo $cb.'('.json_encode($json).')';
	}
}