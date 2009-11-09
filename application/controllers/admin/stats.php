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
		$this->template->content = new View('admin/stats');
		$this->template->content->title = 'Statistics';
		
		$this->template->protochart_enabled = TRUE;
		
		// Retrieve Current Settings
		$settings = ORM::factory('settings', 1);
		
		if($settings->stat_id === null) {
			$sitename = $settings->site_name;
			$url = url::base();
			$this->template->content->stat_id = $this->_create_site( $sitename, $url );
		}else{
			$this->template->content->stat_id = $settings->stat_id;
		}
		
	}
	
	function reports()
	{
		$this->template->content = new View('admin/stats_reports');
		$this->template->content->title = 'Report Stats';
		
		// Retrieve Current Settings
		$settings = ORM::factory('settings', 1);
		$this->template->content->stat_id = $settings->stat_id;
		
		// Javascript Header
		$this->template->flot_enabled = TRUE;
		$this->template->js = new View('admin/stats_js');
		
		$report_stats = Stats_Model::get_report_stats();
		
		// START: Build the graph variable string for flot
		
		// Grab category names
		$cats = Category_Model::categories();
		
		// Individual Category chart data
		$categories_data = '[';
		$flag1 = 0; // flag for commas
		foreach($report_stats['category_counts'] as $category_id => $arr){
			
			if($flag1 != 0) $categories_data .= ',';
			$categories_data .= '{label:"'.$cats[$category_id]['category_title'].'",data:[';
			
			$flag2 = 0; // flag for commas
			foreach($arr as $timestamp => $count){
				if($flag2 != 0) $categories_data .= ',';
				$categories_data .= '['.$timestamp.'000, '.$count.']';
				$flag2 = 1;
			}
			
			$categories_data .= '],color: \'#'.$cats[$category_id]['category_color'].'\'}';
			$flag1 = 1;
		}
		$categories_data .= ']';
		
		// Generate Raw Data
		// Convert category ids to names
		$raw_category = array();
		foreach($report_stats['category_counts'] as $category_id => $arr) {
			$raw_category[$cats[$category_id]['category_title']] = $arr;
		}
		$this->template->content->raw_category_data = $raw_category;
		
		// Approved and Unapproved chart data
		$approved_verified_data = '[';
		$flag1 = 0; // flag for commas
		$to_graph = array('approved_counts','verified_counts'); // We are graphing these two arrays
		foreach($to_graph as $graph_key){
			foreach($report_stats[$graph_key] as $status => $arr){
				
				if($flag1 != 0) $approved_verified_data .= ',';
				$approved_verified_data .= '{label:"'.$status.'",data:[';
				
				$flag2 = 0; // flag for commas
				foreach($arr as $timestamp => $count){
					if($flag2 != 0) $approved_verified_data .= ',';
					$approved_verified_data .= '['.$timestamp.'000, '.$count.']';
					$flag2 = 1;
				}
				
				$approved_verified_data .= ']}';
				$flag1 = 1;
				
			}
		}
		$approved_verified_data .= ']';
		
		$this->template->content->raw_approved_verified_data = $report_stats['approved_counts'] + $report_stats['verified_counts'];
		
		// STOP: Building the graphs variable strings for flot
		
		$this->template->js->graph_data = array(0=>$categories_data,1=>$approved_verified_data);
		$this->template->js->custom_colors = array(0=>true,1=>false);
		
	}
	
	function hits()
	{
		$this->template->content = new View('admin/stats_hits');
		$this->template->content->title = 'Hit Summary';
		
		// Javascript Header
		$this->template->flot_enabled = TRUE;
		$this->template->js = new View('admin/stats_js');
		
		// Hit Data
		$data = Stats_Model::get_hit_stats();
		$this->template->js->graph_data = array(0=>$data['graph']);
		$this->template->content->raw_data = $data['raw'];
	}
	
	function country()
	{
		$this->template->content = new View('admin/stats_country');
		$this->template->content->title = 'Country Breakdown';
		
		$this->template->content->countries = Stats_Model::get_hit_countries();
		
		//Set up country map
		$country_total = array();
		foreach($this->template->content->countries as $country){
			foreach($country as $code => $arr) {
				if(!isset($country_total[$code])) $country_total[$code] = 0;
				$country_total[$code] += $arr['uniques'];
			}
		}
		
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
	}
	
	/**
	 * Creates a new site in centralized stat tracker
	 * @param sitename - name of the instance
	 * @param url - base url 
	 */
	public function _create_site( $sitename, $url ) 
	{
		$stat_url = 'http://tracker.ushahidi.com/px.php?task=cs&sitename='.urlencode($sitename).'&url='.urlencode($url);
		
		// FIXME: This method of extracting the stat_id will only work as 
		//        long as we are only returning the id and nothing else. It
		//        is just a quick and dirty implementation for now.
		$stat_id = trim(strip_tags($this->_curl_req($stat_url))); // Create site and get stat_id
		
		if($stat_id > 0){
			$settings = ORM::factory('settings',1);
			$settings->stat_id = $stat_id;
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
