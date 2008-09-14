<?php defined('SYSPATH') or die('No direct script access.');

/**
* Dashboard Controller
*/
class Dashboard_Controller extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
	    require Kohana::find_file('vendor', 'pchart/pChart/pData', $required = TRUE, $ext = 'class');
	    require Kohana::find_file('vendor', 'pchart/pChart/pChart', $required = TRUE, $ext = 'class');		
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
		
		// Get Chart Settings
		if (isset($_GET['chart']) && strtolower($_GET['chart']) == 'day')
		{
			$this->template->content->timeline = '/day';
		}
		else
		{
			$this->template->content->timeline = '/month';
		}
		
		// Get reports for display
		$incidents = ORM::factory('incident')->limit(3)->orderby('incident_dateadd', 'desc')->find_all();
		$this->template->content->incidents = $incidents;
		
	}
	
	
	public function chart()
	{
		// Does this request have a URI?
		// The URI can either be chart/month or chart/day
		$timeline = $this->uri->segment('chart');
		
		// Number of days this month
		$month_days = date("t");
		
		// Number of hours in day
		$hours_day = 24;
		
		$day = date("d");
		$month = date("m");
		$year = date("Y");
		
		/**
		* Create the Date and Count Array
		* Used for creating the graph elements
		*/
		$date_array = array();
		$count_array = array();
		
		if (strtolower($timeline) == 'day')
		{
			for ($i=1; $i<=$hours_day; $i++)
			{
				if (strlen(Trim($i)) < 2 )
				{
					$i = "0" . $i;
				}
				$mysql_date = $year . "-" . $month . "-" . $day . " " . $i;
				$date_array[] = $i * 3600;	//Multiply by 3600. 3600 Seconds = 1 Hour
				$count_array[] = ORM::factory('incident')->where('incident_date LIKE \''.$mysql_date.'%\'')->count_all();
			}
		}
		else
		{
			for ($i=1; $i<=$month_days; $i++)
			{
				if (strlen(Trim($i)) < 2 )
				{
					$i = "0" . $i;
				}
				$mysql_date = $year . "-" . $month . "-" . $i;
				$date_array[] = mktime(0,0,0,$month,$i,$year);	//Timestamp format
				$count_array[] = ORM::factory('incident')->where('incident_date LIKE \''.$mysql_date.'%\'')->count_all();
			}
		}
		
		// If all the values in $count_array are Zero, set a fixed scale
		$setfixed = true;
		foreach ($count_array as $key => $value) {
			if ($value > 0) {
				$setfixed = false;
				break;
			}
		}
		
	    // Dataset definition
		$DataSet = new pData;    
		$DataSet->AddPoint($count_array,"Serie1");
		$DataSet->AddPoint($date_array,"Serie2");
		$DataSet->AddSerie("Serie1");  
		$DataSet->SetAbsciseLabelSerie("Serie2");  
		$DataSet->SetSerieName("Reports","Serie1"); 
		$DataSet->SetYAxisName("Reports Count");  
		$DataSet->SetYAxisFormat("number");
		if (strtolower($timeline) == 'day')
		{
			$DataSet->SetXAxisFormat("time");
		}
		else
		{
			$DataSet->SetXAxisFormat("date");
		}

		// Initialise the graph     
		$incidents_chart = new pChart(410,305);
		if ($setfixed)
		{
			$incidents_chart->setFixedScale(0,100);
		}
		$incidents_chart->setFontProperties(SYSPATH."fonts/tahoma.ttf",8);  
		$incidents_chart->setGraphArea(60,30,370,255);  
		$incidents_chart->drawFilledRoundedRectangle(7,7,403,298,5,240,240,240);  
		$incidents_chart->drawRoundedRectangle(5,5,405,300,5,230,230,230);  
		$incidents_chart->drawGraphArea(255,255,255,TRUE);  
		$incidents_chart->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2,0,6);

		// Draw the 0 line     
		$incidents_chart->setFontProperties(SYSPATH."fonts/tahoma.ttf",6);  
		$incidents_chart->drawTreshold(0,143,55,72,TRUE,TRUE);  

		// Draw the line graph  
		$incidents_chart->setColorPalette(0,255,0,0);	// Set the color of the first series to Red
		$incidents_chart->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());  
		$incidents_chart->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255); 
		
		// Draw A Cubic Curve graph instead
		// $incidents_chart->drawCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription()); 

		// Finish the graph  
		$incidents_chart->setFontProperties(SYSPATH."fonts/tahoma.ttf",8);  
		$incidents_chart->drawLegend(90,35,$DataSet->GetDataDescription(),255,255,255);  
		$incidents_chart->setFontProperties(SYSPATH."fonts/tahoma.ttf",10);  
		$incidents_chart->drawTitle(60,22,"",50,50,50,585);  
		$incidents_chart->stroke("chart.png");
	}
	
	
}


?>