<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used for the main Admin panel
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Members
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Dashboard_Controller extends Members_Controller {
	
	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$this->template->content = new View('members/dashboard');
		$this->template->content->title = Kohana::lang('ui_admin.dashboard');
		$this->template->this_page = 'dashboard';
		
		// User
		$this->template->content->user = $this->user;
		
		// User Reputation Score
		$this->template->content->reputation = reputation::calculate($this->user->id);
		
		// Get Badges
		$this->template->content->badges = Badge_Model::users_badges($this->user->id);

		// Retrieve Dashboard Counts...
		// Total Reports
		$this->template->content->reports_total = ORM::factory('incident')
			->where("user_id", $this->user->id)
			->count_all();

		// Total Unapproved Reports
		$this->template->content->reports_unapproved = ORM::factory('incident')
			->where('incident_active', '0')
			->where("user_id", $this->user->id)
			->count_all();
			
		// Total Checkins
		$this->template->content->checkins = ORM::factory('checkin')
			->where("user_id", $this->user->id)
			->count_all();
			
		// Total Alerts
		$this->template->content->alerts = ORM::factory('alert')
			->where("user_id", $this->user->id)
			->count_all();
			
		// Total Votes
		$this->template->content->votes = ORM::factory('rating')
			->where("user_id", $this->user->id)
			->count_all();
			
		// Total Votes Positive
		$this->template->content->votes_up = ORM::factory('rating')
			->where("user_id", $this->user->id)
			->where("rating", "1")
			->count_all();
			
		// Total Votes Negative
		$this->template->content->votes_down = ORM::factory('rating')
			->where("user_id", $this->user->id)
			->where("rating", "-1")
			->count_all();

		// Get reports for display
		$this->template->content->incidents = ORM::factory('incident')
				->where("user_id", $this->user->id)
				->limit(5)
				->orderby('incident_dateadd', 'desc')
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

		$this->template->content->failure = '';

		// Build dashboard chart

		// Set the date range (how many days in the past from today?)
		// Default to one year if invalid or not set
		$range = (isset($_GET['range']) AND preg_match('/^\d+$/', $_GET['range']) > 0)
			? (int) $_GET['range'] 
			: 365;
		
		// Phase 3 - Invoke Kohana's XSS cleaning mechanism just incase an outlier wasn't caught	
		$range = $this->input->xss_clean($range);
		$incident_data = Incident_Model::get_number_reports_by_date($range, $this->user->id);
		$data = array('Reports'=>$incident_data);
		$options = array('xaxis'=>array('mode'=>'"time"'));
		$this->template->content->report_chart = protochart::chart('report_chart',$data,$options,array('Reports'=>'CC0000'),410,310);
	}
}
?>
