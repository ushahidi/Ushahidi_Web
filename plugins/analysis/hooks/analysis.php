<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Analysis Hook - Load All Events
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author		 Ushahidi Team <team@ushahidi.com> 
 * @package		 Ushahidi - http://source.ushahididev.com
 * @copyright	Ushahidi - http://www.ushahidi.com
 * @license		 http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class analysis {
	
	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
		// Initialize this for later
		$this->post_data = null;
	}
	
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		// Add a Sub-Nav Link
		Event::add('ushahidi_action.nav_admin_reports', array($this, '_report_link'));
		
		// Only add the events if we are on that controller
		switch (Router::$current_uri) {
			case "admin/reports":
				plugin::add_stylesheet('analysis/views/css/buttons');
				
				// Add Buttons to the report List
				Event::add('ushahidi_action.report_extra_admin', array($this, '_reports_list_buttons_admin'));
			break;
			case "reports":
				plugin::add_stylesheet('analysis/views/css/buttons');
				
				// Add Buttons to the report List
				Event::add('ushahidi_action.report_extra_media', array($this, '_reports_list_buttons'));
			break;
		}
		
		if (Router::$controller == 'analysis')
		{
			plugin::add_javascript('analysis/views/js/ui.dialog');
			plugin::add_javascript('analysis/views/js/ui.draggable');
			plugin::add_javascript('analysis/views/js/ui.resizable');
			plugin::add_stylesheet('analysis/views/css/main');
		}
		// CLEAN UP THIS CODE
		elseif (strripos(Router::$current_uri, "admin/reports/edit") !== false)
		{
			plugin::add_stylesheet('analysis/views/css/report');
			Event::add('ushahidi_action.report_pre_form_admin', array($this, '_reports_list_analysis'));
			Event::add('ushahidi_action.header_scripts_admin', array($this, '_save_analysis_js'));
			Event::add('ushahidi_action.report_edit', array($this, '_save_analysis'));
		}
		elseif (strripos(Router::$current_uri, "reports/submit") !== false)
		{
			//Add dropdown fields to the submit form
			Event::add('ushahidi_action.report_form', array($this, '_submit_form'));

			//Save the contents of the dropdown
			Event::add('ushahidi_action.report_submit', array($this, '_handle_post_data'));
			Event::add('ushahidi_action.report_add', array($this, '_save_submit_form'));
		}
		elseif (strripos(Router::$current_uri, "reports/view") !== false)
		{
			plugin::add_stylesheet('analysis/views/css/buttons');
			
			Event::add('ushahidi_action.report_meta_after_time', array($this, '_reports_view_buttons'));
		}
	}

	public function _submit_form()
	{	
		//Load the view
		$form = View::factory('analysis/reports_submit_form');

		$form->render(TRUE);
	}
	
	public function _handle_post_data() {
		$this->post_data = Event::$data;
		// We should probably add validation here too.
	}

	public function _save_submit_form()
	{
		$incident = Event::$data;
		$post = $this->post_data;
		
		if ($post) {
			// Save Quality Information
			$quality = new Incident_Quality_Model();
			$quality->incident_id = $incident->id;
			$quality->incident_source = $post->incident_source;
			$quality->incident_information = $post->incident_information;
			$quality->save();
		}
		//print_r($post);exit;
	}

	
	public function _report_link()
	{
		$this_sub_page = Event::$data;
		echo ($this_sub_page == "analysis") ? Kohana::lang('analysis.analysis') : "<a href=\"".url::site()."admin/analysis\">".Kohana::lang('analysis.analysis')."</a>";
	}
	
	public function _reports_buttons()
	{
		$incident = Event::$data;
		if (is_object($incident))
		{
			$incident_id = $incident->incident_id;
		} else {
			$incident_id = (int)$incident;
		}
		
		// Is this report an Assessment?
		$analysis = ORM::factory('analysis')
			->where('incident_id', $incident_id)
			->find();
		if ($analysis->loaded)
		{
			$button = View::factory('analysis/buttons');
			$button->incident_id = $incident_id;
			$button->link = '';
			return $button;
		}
	}
	
	public function _reports_list_buttons_admin()
	{
		if ($button = $this->_reports_buttons() )
		{
			$button->link = url::base().'admin/reports/view/'.$button->incident_id;
			$button->render(TRUE);
		}
	}
	
	public function _reports_list_buttons()
	{
		if ($button = $this->_reports_buttons() )
		{
			$button->link = url::base().'reports/view/'.$button->incident_id;
			$button->render(TRUE);
		}
	}
	
	public function _reports_view_buttons()
	{
		if ($button = $this->_reports_buttons() )
		{
			$button->link = '#';
			$button->render(TRUE);
		}
	}
	
	public function _reports_list_analysis()
	{
		$incident_id = Event::$data;
		
		// Load Saved Analysis
		$analysis = ORM::factory('analysis')
			->where('incident_id', $incident_id)
			->find();
		if ($analysis->loaded)
		{
			// Find Attached Incidents
			$related = ORM::factory('analysis_incident')
				->where('analysis_id', $analysis->id)
				->find_all();
			
			$a_ids = array();
			foreach ($related as $a_id)
			{
				$a_ids[] = $a_id->incident_id;
			}
			$report_list = View::factory('analysis/report_list');
			$report_list->a_ids = $a_ids;
			$report_list->render(TRUE);
		}
		else
		{
			if (isset($_GET['a_id']))
			{
				$report_list = View::factory('analysis/report_list');
				$report_list->a_ids = $_GET['a_id'];
				$report_list->render(TRUE);
			}
		}
	}
	
	public function _save_analysis()
	{
		$incident = Event::$data;
		if (isset($_POST['a_id']) AND !empty($_POST['a_id']))
		{
			$a_ids = $_POST['a_id'];
			$analysis = ORM::factory('analysis')
				->where('incident_id', $incident->id)
				->find();

			// Save Analysis
			$analysis->incident_id = $incident->id;
			$analysis->user_id = $_SESSION['auth_user']->id;
			$analysis->analysis_date = date("Y-m-d H:i:s",time());
			$analysis->save();

			// Delete Reports associated with this Analysis (if any)
			ORM::factory('analysis_incident')
				->where('analysis_id', $analysis->id)
				->delete_all();

			// Save Associated Reports
			foreach ($a_ids as $a_id)
			{
				$analysis_incident = ORM::factory('analysis_incident');
				$analysis_incident->analysis_id = $analysis->id;
				$analysis_incident->incident_id = $a_id;
				$analysis_incident->save();

				// Deactivate reports associated with this analysis
				$deactivated = ORM::factory('incident')->find($a_id);
				if ($deactivated->loaded)
				{
					$deactivated->incident_active = 0;
					$deactivated->save();
				}
			}

			// Add to 'Analysis' category
			$incident_category = ORM::factory('incident_category')
				->where(array('incident_id' => $incident->id, 'category_id' => 1999))
				->find();
			if (!$incident_category->loaded)
			{
				$incident_category = new Incident_Category_Model();
				$incident_category->incident_id = $incident->id;
				$incident_category->category_id = 1999;
				$incident_category->save();
			}
		}
	}
	
	public function _save_analysis_js()
	{
		$this->template->js = View::factory('analysis/report_list_js');
		$this->template->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->template->js->render(TRUE);
	}
}

new analysis;
