<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used for the main Admin panel
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Dashboard Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Dashboard_Controller extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$this->template->content = new View('admin/dashboard');
		$this->template->content->title = 'Dashboard';
		$this->template->this_page = 'dashboard';
					
		// Retrieve Dashboard Count...
			
		// Total Reports
		$this->template->content->reports_total = ORM::factory('incident')->count_all();
			
		// Total Unapproved Reports
		$this->template->content->reports_unapproved = ORM::factory('incident')->where('incident_active', '0')->count_all();
			
		// Total Unverified Reports
		$this->template->content->reports_unverified = ORM::factory('incident')->where('incident_verified', '0')->count_all();
			
		// Total Categories
		$this->template->content->categories = ORM::factory('category')->count_all();
			
		// Total Locations
		$this->template->content->locations = ORM::factory('location')->count_all();
			
		// Total Incoming Media
		$this->template->content->incoming_media = ORM::factory('feed_item')->count_all();
			
		// Messages By Service
		$total_message_count = 0;
		$message_services = array();
		$services = ORM::factory('service')->find_all();
		foreach ($services as $service) {
			$message_count = ORM::factory('message')
				->join('reporter','message.reporter_id','reporter.id')
			->where('service_id', $service->id)
			->where('message_type', '1')
			->count_all();
			$message_services[] = array(
			'id' => $service->id,
			'name' => $service->service_name,
			'count' => $message_count
			);
			$total_message_count += $message_count;
		}	
		$this->template->content->message_services = $message_services;
			
		// Total Messages
		$this->template->content->message_count = $total_message_count;
			
			
		// Get reports for display
		$incidents = ORM::factory('incident')->limit(5)->orderby('incident_dateadd', 'desc')->find_all();
		$this->template->content->incidents = $incidents;
			
		// Get Incoming Media (We'll Use NewsFeeds for now)
		$this->template->content->feeds = ORM::factory('feed_item')->limit('3')
			->orderby('item_date', 'desc')
			->find_all();
			
		/*
		// Javascript Header
		$this->template->flot_enabled = TRUE;
		$this->template->js = new View('admin/dashboard_js');
		// Graph
		$this->template->js->all_graphs = Incident_Model::get_incidents_by_interval('ALL',NULL,NULL,'all');
		$this->template->js->current_date = date('Y') . '/' . date('m') . '/01';
		*/
		
		// Javascript Header
		$this->template->protochart_enabled = TRUE;
		$this->template->js = new View('admin/stats_js');
		
		// Set the date range (how many days in the past from today?)
		//    default to one year
		$range = 365;
		if(isset($_GET['range'])) $range = $_GET['range'];
		$this->template->content->range = $range;
		
		// Report Data
		$data = Stats_Model::get_report_stats(false,true,$range,null,null,true);
		
		$this->template->content->failure = '';
		// If we failed to get hit data, fail.
		if(!$data) {
			$now = time()*1000;
			$data['all'][$now] = 0;
			/*
			$this->template->content->report_chart = '';
			$this->template->content->failure = 'No report data for chart.';
			return false;
			*/
		}
		
		$report_chart = new protochart;
		$options = array(
			'xaxis'=>array('mode'=>'"time"'),
			'legend'=>array('show'=>'true')
			);
		
		$pass_data['Reports'] = $data['all'];
		$this->template->content->report_chart = $report_chart->chart('report_chart',$pass_data,$options,array('Reports'=>'CC0000'),410,310);
		
	}

}
?>
