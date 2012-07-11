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
 * @subpackage Scheduler
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
			$sharing_url = $share->sharing_url;

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

		$timeout = 5;
		$limit = 20;
		$since_id = 0;
		$modified_ids = array(); // this is an array of our primary keys
		$more_reports_to_pull = TRUE;

		while($more_reports_to_pull == TRUE)
		{
			$api_url = "/api?task=incidents&limit=".$limit."&resp=json&orderfield=incidentid&sort=0&by=sinceid&id=".$since_id;

			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, sharing_helper::clean_url($sharing_url).$api_url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
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

			$count = 0;
			foreach($all_data->payload->incidents as $incident)
			{
				// See if this incident already exists so we can edit it

				$item_check = ORM::factory('sharing_incident')
							->where('sharing_id', $sharing_id)
							->where('incident_id',$incident->incident->incidentid)
							->find();

				if ($item_check->loaded==TRUE)
				{
					$item = ORM::factory('sharing_incident',$item_check->id);
				}else{
					$item = ORM::factory('sharing_incident');
				}
				$item->sharing_id = $sharing_id;
				$item->incident_id = $incident->incident->incidentid;
				$item->incident_title = $incident->incident->incidenttitle;
				$item->latitude = $incident->incident->locationlatitude;
				$item->longitude = $incident->incident->locationlongitude;
				$item->incident_date = $incident->incident->incidentdate;
				$item->save();

				// Save the primary key of the row we touched. We will be deleting ones that weren't touched.

				$modified_ids[] = $item->id;

				// Save the highest pulled incident id so we can grab the next set from that id on

				$since_id = $incident->incident->incidentid;

				// Save count so we know if we need to pull any more reports or not

				$count++;
			}

			if($count < $limit)
			{
				$more_reports_to_pull = FALSE;
			}
		}

		// Delete the reports that are no longer being displayed on the shared site

		ORM::factory('sharing_incident')
			->notin('id',$modified_ids)
			->where('sharing_id', $sharing_id)
			->delete_all();
	}
}
