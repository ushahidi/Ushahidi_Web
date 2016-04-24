<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Analysis Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author		 Ushahidi Team <team@ushahidi.com> 
 * @package		Ushahidi - http://source.ushahididev.com
 * @module		 Analysis Controller	
 * @copyright	Ushahidi - http://www.ushahidi.com
 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
* 
*/

class Analysis_Controller extends Admin_Controller {
	
	public function __construct()
		{
		parent::__construct();
		$this->template->this_page = 'reports';
	}
	
	public function index($id = NULL)
	{
		$this->template->content = new View('analysis/main');
		
		// Get all active top level categories
				$parent_categories = array();
				foreach (ORM::factory('category')
				->where('category_visible', '1')
				->where('parent_id', '0')
				->orderby('category_title', 'ASC')
				->find_all() as $category)
				{
						// Get The Children
			$children = array();
			foreach ($category->children as $child)
			{
				$children[$child->id] = array($child->category_title);
			}
			
			sort($children);
			
			// Put it all together
						$parent_categories[$category->id] = array(
				$category->category_title,
				$children
			);
				}
				$this->template->content->categories = $parent_categories;

		$this->template->content->date_picker_js = $this->_date_picker_js();
		$this->template->content->latitude = Kohana::config('settings.default_lat');
		$this->template->content->longitude = Kohana::config('settings.default_lon');
			
		// Javascript
		$this->template->map_enabled = TRUE;
		$this->template->js = new View('analysis/main_js');
		$this->template->js->default_map = Kohana::config('settings.default_map');
		$this->template->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->template->js->latitude = Kohana::config('settings.default_lat');
		$this->template->js->longitude = Kohana::config('settings.default_lon');
	}
	
	public function find_reports()
	{
		//$profiler = new Profiler;
		
		$this->template = "";
		$this->auto_render = FALSE;	
		
		// check, has the form been submitted, if so, setup validation
			if ($_POST)
			{
			// Instantiate Validation, use $post, so we don't overwrite $_POST
			// fields with our own things
			$post = new Validation($_POST);

			// Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('latitude', 'required', 'between[-90,90]');
			$post->add_rules('longitude', 'required', 'between[-180,180]');
			$post->add_rules('analysis_radius', 'required', 'numeric');
			$post->add_rules('start_date','date_mmddyyyy');
			$post->add_rules('end_date','date_mmddyyyy');
			$post->add_rules('analysis_category', 'numeric');
			
			// Test to see if things passed the rule checks
					if ($post->validate())
					{
				// Database
				$db = new Database();
				
				$filter = "";
				
				// Radius Filter
				$radius = $post->analysis_radius / 1.609344; // Conversion KM -> Miles
				
				// Time Filter
				$start_date = strtotime($post->start_date);
				$end_date = strtotime($post->end_date);
				if ($start_date)
				{
					$filter .= " AND UNIX_TIMESTAMP(i.incident_date) >= $start_date ";
				}
				if ($end_date)
				{
					$filter .= " AND UNIX_TIMESTAMP(i.incident_date) <= $end_date ";
				}
				
				// Category Filter
				if ($post->analysis_category AND $post->analysis_category != 0)
				{
					$filter .= " AND ( c.id = '".$post->analysis_category."' OR c.parent_id = '".$post->analysis_category."' ) ";
				}
				
				// update here for seperate table
				// Note: * 1.1515 is for conversion from nautical miles to statute miles
				// (One nautical mile is the length of one minute of latitude at the equator)
				$query = $db->query("SELECT DISTINCT i.id, i.incident_title, i.incident_date, iq.incident_source, iq.incident_information, l.`latitude`, l.`longitude`, 
				((ACOS(SIN($post->latitude * PI() / 180) * SIN(l.`latitude` * PI() / 180) + COS($post->latitude * PI() / 180) * COS(l.`latitude` * PI() / 180) * COS(($post->longitude - l.`longitude`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance
				FROM `".$this->table_prefix."incident` AS i INNER JOIN `".$this->table_prefix."location` AS l ON (l.`id` = i.`location_id`) INNER JOIN `".$this->table_prefix."incident_category` AS ic ON (i.`id` = ic.`incident_id`) INNER JOIN `".$this->table_prefix."category` AS c ON (ic.`category_id` = c.`id`) LEFT JOIN `".$this->table_prefix."incident_quality` AS iq ON (i.`id` = iq.`incident_id`)
				WHERE 1=1 $filter HAVING distance<='$radius' ORDER BY i.`incident_date` ASC ");
				
				$markers = array();
				if ($query->count())
				{
					$html = "<h4>Found ".$query->count()." Results</h4>";
					$html .= "<ul>";
					foreach ($query as $row)
					{
						// Is this report an Assessment?
						$analysis = ORM::factory('analysis')
							->where('incident_id', $row->id)
							->find();
						
						if ( ! $analysis->loaded)
						{
							// Source/Information Qualification Information
							$source_qual = ($row->incident_source) ? $row->incident_source : "-";
							$info_qual = ($row->incident_information) ? $row->incident_information : "-";
							$qualification = $this->_qualification($source_qual, $info_qual);
							
							$html .= "<li><input type=\"checkbox\" name=\"a_id[]\" value=\"".$row->id."\">&nbsp;<a href=\"javascript:showReport(".$row->id.");\">".$row->incident_title."</a> <span class=\"qual\">".$qualification."</span><br /><span class=\"date\">".date('M j Y', strtotime($row->incident_date)).", ".date('H:i', strtotime($row->incident_date))."</span></li>";
							$markers[] = array($row->longitude,$row->latitude);
						}
					}
					$html .= "</ul>";
				}
				else
				{
					$html = "<h4>NO REPORTS FOUND</h4>";
					$markers = array();
				}
				
				echo json_encode(array("status"=>"success", "message"=>$html, "markers"=>$markers));
			}
			else
			{
				echo json_encode(array("status"=>"error", "message"=>"THERE IS AN ERROR WITH YOUR SUBMISSION"));
				//print_r($post->errors());
				//print_r($post);
			}
		}
		else
		{
			echo json_encode(array("status"=>"error", "message"=>"THERE IS AN ERROR WITH YOUR SUBMISSION"));
		}
	}
	
	public function get_report($id = NULL)
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		if ($id)
		{
			$incident = ORM::factory('incident', $id);
			if ($incident->loaded)
			{
				$incident_quality = ORM::factory('incident_quality')->where('incident_id', $id)->find();
				$source_qual = ($incident_quality->incident_source) ? $incident_quality->incident_source : "-";
				$info_qual = ($incident_quality->incident_information) ? $incident_quality->incident_information : "-";
				$qualification = $this->_qualification($source_qual, $info_qual);
				
				$html = "";
				$html .= "<h4 class=\"analysis-window-title\" >".$incident->incident_title."</h4>";
				$html .= "<div class=\"analysis-window-date\" >DATE: ".date('M j Y', strtotime($incident->incident_date))." TIME: ".date('H:i', strtotime($incident->incident_date))."</div>";
				$html .= "<div class=\"analysis-window-desc\" >".nl2br($incident->incident_description)."</div>";
				$html .= "<div class=\"analysis-window-cats\" >LISTED IN:<ul>";
				foreach($incident->incident_category as $category) 
				{
					$html .= "<li>".$category->category->category_title."</li>";
				}
				$html .= "</ul></div>";
				$html .= "<div class=\"analysis-window-cats\" >QUALIFICATION: <span class=\"qual\">".$qualification."</span></div>";
				$grid = new View('analysis/grid');
				$grid->source_qual = $incident_quality->incident_source;
				$grid->info_qual = $incident_quality->incident_information;
				$html .= $grid;
				echo $html;
			}
			else
			{
				echo "REPORT NOT FOUND";
			}
		}
		else
		{
			echo "REPORT NOT FOUND";
		}
	}
	
	private function _date_picker_js() 
		{
				return "<script type=\"text/javascript\">
				$(document).ready(function() {
					$(\"#start_date\").datepicker({ 
						showOn: \"both\", 
						buttonImage: \"" . url::base() . "media/img/icon-calendar.gif\", 
						buttonImageOnly: true,
						changeMonth: true,
						changeYear: true
					});
					
					$(\"#end_date\").datepicker({ 
						showOn: \"both\", 
						buttonImage: \"" . url::base() . "media/img/icon-calendar.gif\", 
						buttonImageOnly: true,
						changeMonth: true,
						changeYear: true
					});
				});
			</script>";	
		}

	private function _qualification($source_qual = 0, $info_qual = 0)
	{
		$sourcequal_array = array();
		$sourcequal_array[1] = "A";
		$sourcequal_array[2] = "B";
		$sourcequal_array[3] = "C";
		$sourcequal_array[4] = "D";
		$sourcequal_array[5] = "E";
		$sourcequal_array[6] = "F";
		
		if ($source_qual >= 1 AND $source_qual <= 6
			AND $info_qual >= 1 AND $info_qual <= 6) {
			return utf8::strtoupper($sourcequal_array[$source_qual].$info_qual);
		}
		else
		{
			return "--";
		}
	}
}