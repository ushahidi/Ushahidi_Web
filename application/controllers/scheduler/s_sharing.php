<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Sharing Scheduler Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Sharing Scheduler Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
*/

class S_Sharing_Controller extends Controller {
	
	public function __construct()
    {
        parent::__construct();
	}
	
	public function index()
	{
		// Get all currently active shares
		$shares = ORM::factory('sharing')
			->where('sharing_active', 1)
			->find_all();
		
		foreach ($shares as $share)
		{
			$sharing_url = "http://".$share->sharing_url;
			
			$this->_parse_json($share->id, $sharing_url);
		}
	}
	
	/**
	 * Use remote Ushahidi deployments API to get Incident Data
	 * Limit to 20 not to kill remote server
	 */
	private function _parse_json($sharing_id = NULL, $sharing_url = NULL)
	{
		if ( ! $sharing_id OR ! $sharing_url)
		{
			return false;
		}
		
		$since_id = 0;
		// Use any existing incidents from remote url as a starter
		$existing = ORM::factory('sharing_incident')
			->where('sharing_id', $sharing_id)
			->orderby('incident_id', 'DESC')
			->find_all(1);
		
		foreach ($existing as $item)
		{
			$since_id = $item->incident_id;
		}
		
		$ch = curl_init();
		$timeout = 5;
		$api_url = "/api?task=incidents&limit=20&resp=json&orderfield=incidentid&sort=0&by=sinceid&id=".$since_id;
		
		curl_setopt($ch,CURLOPT_URL,$sharing_url.$api_url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$json = curl_exec($ch);
		curl_close($ch);
		
		$all_data = json_decode($json, false);
		if ( ! $all_data)
		{
			return false;
		}
		
		if ( ! isset($all_data->payload->incidents))
		{
			return false;
		}
		
		// Parse Incidents Into Database
		foreach($all_data->payload->incidents as $incident)
		{
			$item = ORM::factory('sharing_incident');
			$item->sharing_id = $sharing_id;
			$item->incident_id = $incident->incident->incidentid;
			$item->incident_title = $incident->incident->incidenttitle;
			$item->latitude = $incident->incident->locationlatitude;
			$item->longitude = $incident->incident->locationlongitude;
			$item->incident_date = $incident->incident->incidentdate;
			$item->save();
		}
	}
}