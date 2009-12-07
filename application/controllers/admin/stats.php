<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to manage users
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Users Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Stats_Controller extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'stats';
		
		// If this is not a super-user account, redirect to dashboard
		if (!$this->auth->logged_in('admin') && !$this->auth->logged_in('superadmin'))
		{
			 url::redirect('admin/dashboard');
		}
	}
	
	function index()
	{	
		$this->template->content = new View('admin/stats_hits');
		$this->template->content->title = 'Statistics';
		
		// Retrieve Current Settings
		$settings = ORM::factory('settings', 1);
		
		if($settings->stat_id === null || $settings->stat_id == 0) {
			$sitename = $settings->site_name;
			$url = url::base();
			$this->template->content->stat_id = $this->_create_site( $sitename, $url );
		}
		
		// Show the hits page since stats are already set up
		$this->hits();
		
	}
	
	function reports()
	{
		$this->template->content = new View('admin/stats_reports');
		$this->template->content->title = 'Statistics';
		
		// Javascript Header
		$this->template->protochart_enabled = TRUE;
		$this->template->js = new View('admin/stats_js');
		
		$this->template->content->failure = '';
		
		// Set the date range (how many days in the past from today?)
		$range = 10000; //get all reports so go back far into the past
		if(isset($_GET['range'])) $range = $_GET['range'];
		$this->template->content->range = $range;
		
		// Get an arbitrary date range
		$dp1 = null;
		if(isset($_GET['dp1'])) $dp1 = $_GET['dp1'];
		$dp2 = null;
		if(isset($_GET['dp2'])) $dp2 = $_GET['dp2'];
		
		// Report Data
		$data = Stats_Model::get_report_stats(false,false,$range,$dp1,$dp2);
		
		$reports_chart = new protochart;
		
		// This makes the chart a delicious pie chart
		$options = array(
			'pies'=>array('show'=>'true')
			);
		
		// Grab category data
		$cats = Category_Model::categories();
		
		$this->template->content->category_data = $cats;

		$report_data = array();
		$colors = array();
		$reports_per_cat = array();
		foreach($data['category_counts'] as $category_id => $count) {
			$category_name = $cats[$category_id]['category_title'];
			$report_data[$category_name] = $count;
			$colors[$category_name] = $cats[$category_id]['category_color'];
			
			foreach($count as $c) {				
				// Count up the total number of reports per category
				if(!isset($reports_per_cat[$category_id])) $reports_per_cat[$category_id] = 0;
				$reports_per_cat[$category_id] += $c;
			}
		}
		
		$this->template->content->num_categories = $data['total_categories'];
		$this->template->content->reports_per_cat = $reports_per_cat;
		
		$this->template->content->reports_chart = $reports_chart->chart('reports',$report_data,$options,$colors,350,350);
		
		$this->template->content->verified = 0;
		$this->template->content->unverified = 0;
		$this->template->content->approved = 0;
		$this->template->content->unapproved = 0;
		
		$report_status_chart = new protochart;
		$report_staus_data = array();
		
		foreach($data['verified_counts'] as $ver_or_un => $arr){
			if(!isset($report_staus_data[$ver_or_un][0])) $report_staus_data[$ver_or_un][0] = 0;
			if(!isset($this->template->content->$ver_or_un)) $this->template->content->$ver_or_un = 0;
			foreach($arr as $count) {
				$report_staus_data[$ver_or_un][0] += $count;
				$this->template->content->$ver_or_un += $count;
			}
		}
		
		$colors = array('verified'=>'0E7800','unverified'=>'FFCF00');
		$this->template->content->report_status_chart_ver = $report_status_chart->chart('report_status_ver',$report_staus_data,$options,$colors,150,150);
		
		$report_staus_data = array();
		
		foreach($data['approved_counts'] as $app_or_un => $arr){
			if(!isset($report_staus_data[$app_or_un][0])) $report_staus_data[$app_or_un][0] = 0;
			if(!isset($this->template->content->$app_or_un)) $this->template->content->$app_or_un = 0;
			foreach($arr as $count) {
				$report_staus_data[$app_or_un][0] += $count;
				$this->template->content->$app_or_un += $count;
			}
		}
		
		$this->template->content->num_reports = $data['total_reports'];
		
		$colors = array('approved'=>'0E7800','unapproved'=>'FFCF00');
		$this->template->content->report_status_chart_app = $report_status_chart->chart('report_status_app',$report_staus_data,$options,$colors,150,150);
		
		// Set the date
		$this->template->content->dp1 = date('Y-m-d',$data['earliest_report_time']);
		$this->template->content->dp2 = date('Y-m-d',$data['latest_report_time']);
	}
	
	function impact()
	{
		$this->template->content = new View('admin/stats_impact');
		$this->template->content->title = 'Statistics';
		
		// Javascript Header
		$this->template->raphael_enabled = TRUE;
		$this->template->js = new View('admin/stats_js');
		
		$this->template->content->failure = '';
		
		// Set the date range (how many days in the past from today?)
		$range = 10000; //get all reports so go back far into the past
		if(isset($_GET['range'])) $range = $_GET['range'];
		$this->template->content->range = $range;
		
		// Get an arbitrary date range
		$dp1 = null;
		if(isset($_GET['dp1'])) $dp1 = $_GET['dp1'];
		$dp2 = null;
		if(isset($_GET['dp2'])) $dp2 = $_GET['dp2'];
		
		// Report Data
		$data = Stats_Model::get_report_stats(false,true,$range,$dp1,$dp2);
		
		// If we failed to get hit data, fail.
		if(!isset($data['category_counts'])) {
			$this->template->content->num_reports = 0;
			$this->template->content->num_categories = 0;
			$this->template->content->dp1 = null;
			$this->template->content->dp2 = null;
			$this->template->impact_json = '';
			return false;
		}
		
		$json = '';
		$use_log = '';
		$json .= '"buckets":['."\n";
		$cat_report_count = array();
		$category_counter = array();
		foreach($data['category_counts'] as $timestamp => $count_array) {
			$comma_flag = false;
			$line = '';
			// If this number is greater than 0, we'll show the line
			$display_test = 0;
			foreach($count_array as $category_id => $count) {
				
				$category_counter[$category_id] = 1;
				
				// We aren't allowing 0s
				if($count > 0) {
					if($comma_flag) $line .= ',';
					$comma_flag = true;
					
					$line .= '['.$category_id.','.$count.']';
					
					$display_test += $count;
					
					// If we see a count over 50 (picked this arbitrarily), then switch to log format
					if($count > 50) $use_log = '"use_log":1,'."\n";
					
					// Count the number of reports so we have something useful to show in the legend
					if(!isset($cat_report_count[$category_id])) $cat_report_count[$category_id] = 0;
					$cat_report_count[$category_id] += $count;
				}
			}
			if($display_test > 0){
				$json .= '{"d":'.$timestamp.',"i":[';
				$json .= $line;
				$json .= ']},'."\n";
			}
		}
		
		$this->template->content->num_reports = $data['total_reports'];
		$this->template->content->num_categories = $data['total_categories'];
		
		$json .= '],'."\n";
		$json .= $use_log;
		$json .= '"categories":'."\n";
		$json .= '{'."\n";
		
		// Grab category data
		$cats = Category_Model::categories();
		
		foreach($cats as $category_id => $cat_array) {
			$report_count = 0;
			if(isset($cat_report_count[$category_id])) $report_count = $cat_report_count[$category_id];
			$json .= $category_id.':{"name":"'.$cat_array['category_title'].'","fill":"#'.$cat_array['category_color'].'","reports":'.$report_count.'},'."\n";
		}
		
		$json .= '}'."\n";
		
		$this->template->impact_json = $json;
		
		// Set the date
		$this->template->content->dp1 = date('Y-m-d',$data['earliest_report_time']);
		$this->template->content->dp2 = date('Y-m-d',$data['latest_report_time']);
		
	}
	
	function hits()
	{
		$this->template->content = new View('admin/stats_hits');
		$this->template->content->title = 'Statistics';
		
		// Javascript Header
		$this->template->protochart_enabled = TRUE;
		$this->template->js = new View('admin/stats_js');
		
		$this->template->content->failure = '';
		
		// Set the date range (how many days in the past from today?)
		$range = 30;
		if(isset($_GET['range'])) $range = $_GET['range'];
		$this->template->content->range = $range;
		
		// Get an arbitrary date range
		$dp1 = null;
		if(isset($_GET['dp1'])) $dp1 = $_GET['dp1'];
		$dp2 = null;
		if(isset($_GET['dp2'])) $dp2 = $_GET['dp2'];
		
		// Hit Data
		$data = Stats_Model::get_hit_stats($range,$dp1,$dp2);
		
		$this->template->content->uniques = 0;
		$this->template->content->visits = 0;
		$this->template->content->pageviews = 0;
		$this->template->content->active_tab = 'uniques';
		
		// Lazy tab switcher (not using javascript, just refreshing the page)
		if(isset($_GET['active_tab'])) $this->template->content->active_tab = $_GET['active_tab'];
		
		// If we failed to get hit data, fail.
		if(!$data) {
			$this->template->content->traffic_chart = 'Error displaying chart';
			$this->template->content->raw_data = array();
			$this->template->content->dp1 = null;
			$this->template->content->dp2 = null;
			$this->template->content->failure = 'Stat Collection Failed! Either your stat_id or stat_key in the settings table in the database are incorrect or our stat server is down. Try back in a bit to see if the server is up and running. If you are really in a pinch, you can always modify stat_id (set to null) and stat_key (set to 0) in the settings table of your database to get your stats back up and running. Keep in mind you will lose access to your stats currently on the stats server.';
			return false;
		}
		
		$counts = array();
		foreach($data as $label => $data_array) {
			if(!isset($this->template->content->$label)) $this->template->content->$label = 0;
			foreach($data_array as $timestamp => $count) $this->template->content->$label += $count;
		}
		
		$traffic_chart = new protochart;
		$options = array(
			'xaxis'=>array('mode'=>'"time"'),
			'legend'=>array('show'=>'true')
			);
		$this->template->content->traffic_chart = $traffic_chart->chart('traffic',$data,$options,null,884,300);
		$this->template->content->raw_data = $data;
		
		
		// Set the date
		reset($data['visits']);
		$this->template->content->dp1 = date('Y-m-d',(key($data['visits'])/1000));
		end($data['visits']);
		$this->template->content->dp2 = date('Y-m-d',(key($data['visits'])/1000));
	}
	
	function country()
	{
		$this->template->content = new View('admin/stats_country');
		$this->template->content->title = 'Statistics';
		
		// Javascript Header
		$this->template->js = new View('admin/stats_js');
		
		$this->template->content->failure = '';
		
		// Set the date range (how many days in the past from today?)
		$range = 30;
		if(isset($_GET['range'])) $range = $_GET['range'];
		$this->template->content->range = $range;
		
		// Get an arbitrary date range
		$dp1 = null;
		if(isset($_GET['dp1'])) $dp1 = $_GET['dp1'];
		$dp2 = null;
		if(isset($_GET['dp2'])) $dp2 = $_GET['dp2'];
		
		$countries = Stats_Model::get_hit_countries($range,$dp1,$dp2);
		
		// If we failed to get country data, fail.
		if(!$countries) {
			$this->template->content->countries = array();
			$this->template->content->num_countries = 0;
			$this->template->content->dp1 = null;
			$this->template->content->dp2 = null;
			$this->template->content->visitor_map = '';
			$this->template->content->uniques = 0;
			$this->template->content->visits = 0;
			$this->template->content->pageviews = 0;
			$this->template->content->active_tab = 'uniques';
			$this->template->content->failure = 'Stat Collection Failed!';
			return false;
		}
		
		//Set up country map and totals
		$country_total = array();
		$countries_reformatted = array();
		foreach($countries as $country){
			foreach($country as $code => $arr) {
				if(!isset($country_total[$code])) $country_total[$code] = 0;
				$country_total[$code] += $arr['uniques'];
				
				$name = $arr['label'];
				if(!isset($countries_reformatted[$name])) $countries_reformatted[$name] = array();
				if(!isset($countries_reformatted[$name]['count'])) $countries_reformatted[$name]['count'] = 0;
				$countries_reformatted[$name]['count'] += $arr['uniques'];
				$countries_reformatted[$name]['icon'] = $arr['logo'];
			}
		}
		
		arsort($countries_reformatted);
		
		$this->template->content->countries = $countries_reformatted;
		
		$this->template->content->num_countries = count($countries_reformatted);
		
		arsort($country_total);
		
		$codes = '';
		$values = '';
		$i = 0;
		foreach($country_total as $code => $uniques){
			if($i == 0) $highest = $uniques;
			if($i != 0) $values .= ',';
			$values .= ($uniques / $highest) * 100;
			$codes .= strtoupper($code);
			$i++;
		}
		$this->template->content->visitor_map = "http://chart.apis.google.com/chart?chs=440x220&chf=bg,s,ffffff&cht=t&chtm=world&chco=cccccc,A07B7B,a20000&chld=".$codes."&chd=t:".$values;
		
		// Hit Data
		$data = Stats_Model::get_hit_stats($range,$dp1,$dp2);
		
		$this->template->content->uniques = 0;
		$this->template->content->visits = 0;
		$this->template->content->pageviews = 0;
		$this->template->content->active_tab = 'uniques';
		
		// Lazy tab switcher (not using javascript)
		if(isset($_GET['active_tab'])) $this->template->content->active_tab = $_GET['active_tab'];
		
		// If we failed to get hit data, fail.
		if(!$data) {
			return false;
		}
		
		$counts = array();
		foreach($data as $label => $data_array) {
			if(!isset($this->template->content->$label)) $this->template->content->$label = 0;
			foreach($data_array as $timestamp => $count) $this->template->content->$label += $count;
		}
		
		// Set the date
		reset($data['visits']);
		$this->template->content->dp1 = date('Y-m-d',(key($data['visits'])/1000));
		end($data['visits']);
		$this->template->content->dp2 = date('Y-m-d',(key($data['visits'])/1000));
	}
	
	/**
	 * Creates a new site in centralized stat tracker
	 * @param sitename - name of the instance
	 * @param url - base url 
	 */
	public function _create_site( $sitename, $url ) 
	{
		$stat_url = 'http://tracker.ushahidi.com/px.php?task=cs&sitename='.urlencode($sitename).'&url='.urlencode($url);
		
		$xml = simplexml_load_string($this->_curl_req($stat_url));
		$stat_id = (string)$xml->id[0];
		$stat_key = (string)$xml->key[0];
		
		if($stat_id > 0){
			$settings = ORM::factory('settings',1);
			$settings->stat_id = $stat_id;
			$settings->stat_key = $stat_key;
			$settings->save();
			return $stat_id;
		}
		
		return false;
	}
	
	/**
	 * Helper function to send a cURL request
	 * @param url - URL for cURL to hit
	 */
	public function _curl_req( $url )
	{
		// Make sure cURL is installed
		if (!function_exists('curl_exec')) {
			throw new Kohana_Exception('stats.cURL_not_installed');
			return false;
		}
		
		$curl_handle = curl_init();
		curl_setopt($curl_handle,CURLOPT_URL,$url);
		curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,15); // Timeout set to 15 seconds. This is somewhat arbitrary and can be changed.
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1); //Set curl to store data in variable instead of print
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);
		
		return $buffer;
	}
}
