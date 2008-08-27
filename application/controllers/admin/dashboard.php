<?php

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
	}
	
	
	public function chart()
	{
		// Does this request have a URI?
		// The URI can either be chart/month or chart/day
		$timeline = $this->uri->segment('chart');
		
		// New Database For Querybuilder
		$db = new Database();
		
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

				$db->select('count( * ) AS incident_count');
				$db->from('incident');
				$db->where('incident_date LIKE \''.$mysql_date.'%\'');
				$incident_count = $db->get()->current()->incident_count;
				$count_array[] = $incident_count;
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

				$db->select('count( * ) AS incident_count');
				$db->from('incident');
				$db->where('incident_date LIKE \''.$mysql_date.'%\'');
				$incident_count = $db->get()->current()->incident_count;
				$count_array[] = $incident_count;
			}
		}
		
	    // Dataset definition
		$DataSet = new pData;    
		$DataSet->AddPoint($count_array,"Serie1");
		$DataSet->AddPoint($date_array,"Serie2");
		$DataSet->AddSerie("Serie1");  
		$DataSet->SetAbsciseLabelSerie("Serie2");  
		$DataSet->SetSerieName("Incidents","Serie1"); 
		$DataSet->SetYAxisName("Incident Count");  
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
		$Test = new pChart(410,305);
		$Test->setFixedScale(0,100);
		$Test->setFontProperties(SYSPATH."fonts/tahoma.ttf",8);  
		$Test->setGraphArea(60,30,370,255);  
		$Test->drawFilledRoundedRectangle(7,7,403,298,5,240,240,240);  
		$Test->drawRoundedRectangle(5,5,405,300,5,230,230,230);  
		$Test->drawGraphArea(255,255,255,TRUE);  
		$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2,0,6);

		// Draw the 0 line     
		$Test->setFontProperties(SYSPATH."fonts/tahoma.ttf",6);  
		$Test->drawTreshold(0,143,55,72,TRUE,TRUE);  

		// Draw the line graph  
		$Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());  
		$Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255); 
		
		// Draw A Cubic Curve graph instead
		// $Test->drawCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription()); 

		// Finish the graph  
		$Test->setFontProperties(SYSPATH."fonts/tahoma.ttf",8);  
		$Test->drawLegend(90,35,$DataSet->GetDataDescription(),255,255,255);  
		$Test->setFontProperties(SYSPATH."fonts/tahoma.ttf",10);  
		$Test->drawTitle(60,22,"Incidents",50,50,50,585);  
		$Test->stroke("chart.png");
	}
	
	
}


?>