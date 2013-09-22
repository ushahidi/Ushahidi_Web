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
 * @subpackage Admin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Stats_Controller extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'stats';

		// If user doesn't have access, redirect to dashboard
		if ( ! $this->auth->has_permission("stats"))
		{
			url::redirect(url::site().'admin/dashboard');
		}
    }

	public function index()
	{   
		$this->template->content = new View('admin/stats/hits');
		$this->template->content->title = Kohana::lang('ui_admin.statistics');

		// Retrieve Current Settings
		$stat_id = Settings_Model::get_setting('stat_id');

		if ($stat_id === NULL OR $stat_id == 0)
		{
			$sitename = Settings_Model::get_setting('site_name');
			$url = url::base();
			$this->template->content->stat_id = Stats_Model::create_site( $sitename, $url );
		}

		// Show the hits page since stats are already set up
		$this->hits();
	}
	
	/**
	 * Report statistics
	 */
	public function reports()
	{
		$this->template->content = new View('admin/stats/reports');
		$this->template->content->title = Kohana::lang('ui_admin.statistics');

		// Javascript Header
		$this->themes->protochart_enabled = TRUE;
		$this->themes->js = new View('admin/stats/stats_js');

		$this->template->content->failure = '';

		// Set the date range (how many days in the past from today?)
		$range = 10000;
		if (isset($_GET['range']))
		{
			$range = $this->input->xss_clean($_GET['range']);
			$range = (intval($range) > 0)? intval($range) : 10000;
		}

		$this->template->content->range = $range;

		// Get an arbitrary date range
		$dp1 = (isset($_GET['dp1'])) ? $_GET['dp1'] : null;
		$dp2 = (isset($_GET['dp2'])) ? $_GET['dp2'] : null;

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
		
		foreach ($data['category_counts'] as $category_id => $count)
		{
			// Verify if the array key $category_id exists before attempting to fetch
			if (array_key_exists($category_id, $cats))
			{
				$category_name = $cats[$category_id]['category_title'];
				$report_data[$category_name] = $count;
            
				$colors[$category_name] = (isset($cats[$category_id]['category_color']))
					? $cats[$category_id]['category_color']
					: 'FFFFFF';

				foreach ($count as $c)
				{             
					// Count up the total number of reports per category
					if ( ! isset($reports_per_cat[$category_id]))
					{
                        $reports_per_cat[$category_id] = 0;
                    }

					$reports_per_cat[$category_id] += $c;
				}
			}
		}
		asort($reports_per_cat, SORT_NUMERIC);
		$reports_per_cat = array_reverse($reports_per_cat, TRUE);
		
		$this->template->content->num_categories = $data['total_categories'];
		$this->template->content->reports_per_cat = $reports_per_cat;

		$this->template->content->reports_chart = $reports_chart->chart('reports',$report_data,$options,$colors,350,350);

		$this->template->content->verified = 0;
		$this->template->content->unverified = 0;
		$this->template->content->approved = 0;
		$this->template->content->unapproved = 0;

		$report_status_chart = new protochart;
		$report_staus_data = array();
        
        foreach ($data['verified_counts'] as $ver_or_un => $arr)
        {
            if ( ! isset($report_staus_data[$ver_or_un][0]))
            {
                $report_staus_data[$ver_or_un][0] = 0;
            }
                
            if ( ! isset($this->template->content->$ver_or_un))
            {
                $this->template->content->$ver_or_un = 0;
            }
            
            foreach($arr as $count)
            {
                $report_staus_data[$ver_or_un][0] += $count;
                $this->template->content->$ver_or_un += $count;
            }
        }
        
        $colors = array('verified'=>'0E7800','unverified'=>'FFCF00');
        $this->template->content->report_status_chart_ver = $report_status_chart->chart('report_status_ver',$report_staus_data,$options,$colors,150,150);
        
        $report_staus_data = array();
        
		foreach ($data['approved_counts'] as $app_or_un => $arr)
		{
            if ( ! isset($report_staus_data[$app_or_un][0]))
            {
                $report_staus_data[$app_or_un][0] = 0;
            }
            
            if ( ! isset($this->template->content->$app_or_un))
            {
                $this->template->content->$app_or_un = 0;
            }
            
            foreach($arr as $count)
            {
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
    
    public function impact()
    {
        $this->template->content = new View('admin/stats/impact');
        $this->template->content->title = Kohana::lang('ui_admin.statistics');
        
        // Javascript Header
        $this->themes->raphael_enabled = TRUE;
        $this->themes->js = new View('admin/stats/stats_js');
        
        $this->template->content->failure = '';
        
        // Set the date range (how many days in the past from today?)       
        $range = (isset($_GET['range']) AND is_int($_GET['range'])) 
            ? $_GET['range']
            : 10000; // Get all reports so go back far into the past
        
        $this->template->content->range = $range;
        
        // Get an arbitrary date range
        $dp1 = (isset($_GET['dp1'])) ? $_GET['dp1'] : null;
        
        $dp2 = (isset($_GET['dp2'])) ? $_GET['dp2'] : null;
        
        // Report Data
        $data = Stats_Model::get_report_stats(false,true,$range,$dp1,$dp2);
        
        // If we failed to get hit data, fail.
        if ( ! isset($data['category_counts']))
        {
            $this->template->content->num_reports = 0;
            $this->template->content->num_categories = 0;
            $this->template->impact_json = '';

            $this->template->content->dp1 = $dp1;
            $this->template->content->dp2 = $dp2;

            return false;
        }
        
        $json = array();
        $use_log = '';
        $json['buckets'] = array();
        $cat_report_count = array();
        $category_counter = array();
        
        foreach($data['category_counts'] as $timestamp => $count_array)
        {
            $line = array();
            // If this number is greater than 0, we'll show the line
            $display_test = 0;
            foreach($count_array as $category_id => $count)
            {
                $category_counter[$category_id] = 1;
                
                // We aren't allowing 0s
                if($count > 0)
                {
                    $line[] = array($category_id, $count);
                    
                    $display_test += $count;
                    
                    // If we see a count over 50 (picked this arbitrarily), then switch to log format
                    if($count > 50) $use_log = 1;
                    
                    // Count the number of reports so we have something useful to show in the legend
                    if ( ! isset($cat_report_count[$category_id])) $cat_report_count[$category_id] = 0;
                    $cat_report_count[$category_id] += $count;
                }
            }
            if ($display_test > 0)
            {
                $json['buckets'][] = array(
                  'd' => $timestamp,
                  'i' => $line
                );
            }
        }
        
        $this->template->content->num_reports = $data['total_reports'];
        $this->template->content->num_categories = $data['total_categories'];
        
        $json['use_log'] = $use_log;
        $json['categories'] = array();
        
        // Grab category data
        $cats = Category_Model::categories();
        
        foreach ($cats as $category_id => $cat_array)
        {
            $report_count = 0;
            if (isset($cat_report_count[$category_id]))
            { 
                $report_count = $cat_report_count[$category_id];
            }
            
            $json['categories'][$category_id] = array(
              "name" => $cat_array['category_title'],
              "fill" => '#'.$cat_array['category_color'],
              "reports" => $report_count
            );
        }

        $this->themes->impact_json = json_encode($json);

        // Set the date
        $this->template->content->dp1 = date('Y-m-d',$data['earliest_report_time']);
        $this->template->content->dp2 = date('Y-m-d',$data['latest_report_time']);
        
    }
    
	public function hits()
    {
        $this->template->content = new View('admin/stats/hits');
        $this->template->content->title = Kohana::lang('ui_admin.statistics');
        
        // Javascript Header
        $this->themes->protochart_enabled = TRUE;
        $this->themes->js = new View('admin/stats/stats_js');
        
        $this->template->content->failure = '';
        
        // Set the date range (how many days in the past from today?)
        $range = (isset($_GET['range'])) ? $_GET['range'] : 30;
        $this->template->content->range = $range;
        
        // Get an arbitrary date range
        $dp1 = (isset($_GET['dp1'])) ? $_GET['dp1']: null;
        $dp2 = (isset($_GET['dp2'])) ? $_GET['dp2']: null;
        
        // Hit Data
        $data = Stats_Model::get_hit_stats($range,$dp1,$dp2);
        
        $this->template->content->uniques = 0;
        $this->template->content->visits = 0;
        $this->template->content->pageviews = 0;
        $this->template->content->active_tab = 'uniques';
        
        // Lazy tab switcher (not using javascript, just refreshing the page)
        if (isset($_GET['active_tab']))
        {
            $this->template->content->active_tab = $_GET['active_tab'];
        }
        
        // If we failed to get hit data, fail.
        if ( ! $data)
        {
            $this->template->content->traffic_chart = Kohana::lang('ui_admin.chart_display_error');
            $this->template->content->raw_data = array();
            $this->template->content->dp1 = $dp1;
            $this->template->content->dp2 = $dp2;
            $this->template->content->failure = 'Stat Collection Failed! Either your stat_id or stat_key in the settings table in the database are incorrect or our stat server is down. Try back in a bit to see if the server is up and running. If you are really in a pinch, you can always modify stat_id (set to null) and stat_key (set to 0) in the settings table of your database to get your stats back up and running. Keep in mind you will lose access to your stats currently on the stats server.';
            return false;
        }
        
        $counts = array();
        foreach ($data as $label => $data_array)
        {
            if ( ! isset($this->template->content->$label))
            {
                $this->template->content->$label = 0;
            }
            
            foreach ($data_array as $timestamp => $count)
            {
                $this->template->content->$label += $count;
            }
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
        $this->template->content = new View('admin/stats/country');
        $this->template->content->title = Kohana::lang('ui_admin.statistics');
        
        // Javascript Header
        $this->themes->js = new View('admin/stats/stats_js');
        
        $this->template->content->failure = '';
        
        // Set the date range (how many days in the past from today?)
        $range = (isset($_GET['range'])) ? $_GET['range'] : 30;
        
        $this->template->content->range = $range;
        
        // Get an arbitrary date range
        $dp1 = (isset($_GET['dp1'])) ? $_GET['dp1'] : null;
        $dp2 = (isset($_GET['dp2'])) ? $_GET['dp2'] : null;
        
        $countries = Stats_Model::get_hit_countries($range,$dp1,$dp2);
        
        // If we failed to get country data, fail.
        if(!$countries) {
            $this->template->content->countries = array();
            $this->template->content->num_countries = 0;
            $this->template->content->dp1 = $dp1;
            $this->template->content->dp2 = $dp2;
            $this->template->content->visitor_map = '';
            $this->template->content->uniques = 0;
            $this->template->content->visits = 0;
            $this->template->content->pageviews = 0;
            $this->template->content->active_tab = 'uniques';
            $this->template->content->failure = Kohana::lang('ui_admin.stats_collection_error_short');
            return false;
        }
        
        //Set up country map and totals
        $country_total = array();
        $countries_reformatted = array();
        foreach($countries as $country)
        {
            foreach($country as $code => $arr)
            {
                if ( ! isset($country_total[$code])) $country_total[$code] = 0;
                $country_total[$code] += $arr['uniques'];
                
                $name = $arr['label'];
                if ( ! isset($countries_reformatted[$name]))
                {
                    $countries_reformatted[$name] = array();
                }
                
                if ( ! isset($countries_reformatted[$name]['count']))
                {
                    $countries_reformatted[$name]['count'] = 0;
                }
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
        foreach($country_total as $code => $uniques)
        {
            if($i == 0) $highest = $uniques;
            if($i != 0) $values .= ',';
            $values .= ($uniques / $highest) * 100;
            $codes .= strtoupper($code);
            $i++;
        }
        
        $this->template->content->visitor_map = Kohana::config('core.site_protocol')."://chart.googleapis.com/chart?chs=440x220&chf=bg,s,ffffff&cht=t&chtm=world&chco=cccccc,A07B7B,a20000&chld=".$codes."&chd=t:".$values;
        
        // Hit Data
        $data = Stats_Model::get_hit_stats($range,$dp1,$dp2);
        
        $this->template->content->uniques = 0;
        $this->template->content->visits = 0;
        $this->template->content->pageviews = 0;
        $this->template->content->active_tab = 'uniques';
        
        // Lazy tab switcher (not using javascript)
        if (isset($_GET['active_tab']))
        {
            $this->template->content->active_tab = $_GET['active_tab'];
        }
        
        // If we failed to get hit data, fail.
        if ( ! $data)
        {
            $this->template->content->dp1 = $dp1;
            $this->template->content->dp2 = $dp2;

            return false;
        }
        
        $counts = array();
        foreach ($data as $label => $data_array)
        {
            if ( ! isset($this->template->content->$label))
            {
                $this->template->content->$label = 0;
            }
            
            foreach ($data_array as $timestamp => $count)
            {
                $this->template->content->$label += $count;
            }
        }
        
        // Set the date
        reset($data['visits']);
        $this->template->content->dp1 = date('Y-m-d',(key($data['visits'])/1000));
        end($data['visits']);
        $this->template->content->dp2 = date('Y-m-d',(key($data['visits'])/1000));
    }
    
    function punchcard()
	{
		$this->template->content = new View('admin/stats/punchcard');
		$this->template->content->title = Kohana::lang('ui_admin.statistics');

		$incident_dates = Incident_Model::get_incident_dates();

		// Initialize the array. Zeroing everything out now to keep us from having to loop it

		$data = array('sun'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,
								11=>0,12=>0,13=>0,14=>0,15=>0,16=>0,17=>0,18=>0,19=>0,
								20=>0,21=>0,22=>0,23=>0),
					  'mon'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,
								11=>0,12=>0,13=>0,14=>0,15=>0,16=>0,17=>0,18=>0,19=>0,
								20=>0,21=>0,22=>0,23=>0),
					  'tue'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,
								11=>0,12=>0,13=>0,14=>0,15=>0,16=>0,17=>0,18=>0,19=>0,
								20=>0,21=>0,22=>0,23=>0),
					  'wed'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,
								11=>0,12=>0,13=>0,14=>0,15=>0,16=>0,17=>0,18=>0,19=>0,
								20=>0,21=>0,22=>0,23=>0),
					  'thu'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,
								11=>0,12=>0,13=>0,14=>0,15=>0,16=>0,17=>0,18=>0,19=>0,
								20=>0,21=>0,22=>0,23=>0),
					  'fri'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,
					  			11=>0,12=>0,13=>0,14=>0,15=>0,16=>0,17=>0,18=>0,19=>0,
					  			20=>0,21=>0,22=>0,23=>0),
					  'sat'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,
					  			11=>0,12=>0,13=>0,14=>0,15=>0,16=>0,17=>0,18=>0,19=>0,
					  			20=>0,21=>0,22=>0,23=>0));

		$highest_value = 0;
		foreach($incident_dates as $datetime)
		{
			$t = strtotime($datetime);
			$dow = strtolower(date('D',$t));
			$hour = date('G',$t);
			$data[$dow][$hour] += 1;
			if($data[$dow][$hour] > $highest_value)
			{
				$highest_value = $data[$dow][$hour];
			}
		}
		$this->template->content->chart_url = Kohana::config('core.site_protocol').'://chart.googleapis.com/chart?chs=905x300&chds=-1,24,-1,7,0,'.$highest_value.'&chf=bg,s,efefef&chd=t:0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23|0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7|'.implode(',',$data['sun']).','.implode(',',$data['mon']).','.implode(',',$data['tue']).','.implode(',',$data['wed']).','.implode(',',$data['thu']).','.implode(',',$data['fri']).','.implode(',',$data['sat']).',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0&chxt=x,y&chm=o,333333,1,1.0,30.0&chxl=0:||12'.Kohana::lang('datetime.am').'|1|2|3|4|5|6|7|8|9|10|11|12'.Kohana::lang('datetime.pm').'|1|2|3|4|5|6|7|8|9|10|11||1:||'.Kohana::lang('datetime.sunday.abbv').'|'.Kohana::lang('datetime.monday.abbv').'|'.Kohana::lang('datetime.tuesday.abbv').'|'.Kohana::lang('datetime.wednesday.abbv').'|'.Kohana::lang('datetime.thursday.abbv').'|'.Kohana::lang('datetime.friday.abbv').'|'.Kohana::lang('datetime.saturday.abbv').'|&cht=s';

	}
}
