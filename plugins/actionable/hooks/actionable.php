<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Actionable Hook - Load All Events
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class actionable {

	private $media_filter;
	private $media_values;

	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{
		$this->actionable = "";
		$this->action_urgent = "";
		$this->action_taken = "";
		$this->action_summary = "";
		$this->action_closed = "";
		$this->media_values = array(
			101 => Kohana::lang('ui_main.all'),
			102 => Kohana::lang('actionable.actionable'),
			103 => Kohana::lang('actionable.urgent'),
			104 => Kohana::lang('actionable.action_taken'),
			105 => Kohana::lang('actionable.action_closed')
		);
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
	}

	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		// Add a Sub-Nav Link
		Event::add('ushahidi_action.nav_admin_reports', array($this, '_report_link'));
		// Only add the events if we are on that controller
		if (Router::$controller == 'reports')
		{
			switch (Router::$method)
			{
				// Hook into the Report Add/Edit Form in Admin
				case 'edit':
					// Hook into the form itself
					Event::add('ushahidi_action.report_form_admin', array($this, '_report_form'));
					// Hook into the report_submit_admin (post_POST) event right before saving
					// Event::add('ushahidi_action.report_submit_admin', array($this, '_report_validate'));
					// Hook into the report_edit (post_SAVE) event
					Event::add('ushahidi_action.report_edit', array($this, '_report_form_submit'));
					break;

				// Hook into the Report view (front end)
				case 'view':
					plugin::add_stylesheet('actionable/css/actionable');
					Event::add('ushahidi_action.report_meta', array($this, '_report_view'));
					break;

				case 'index':
					plugin::add_stylesheet('actionable/css/actionable');
					Event::add('ushahidi_action.report_filters_ui', array($this, '_report_filters_ui'));
					Event::add('ushahidi_action.report_js_filterReportsAction', array($this, '_report_js_filterReportsAction'));
					Event::add('ushahidi_action.report_js_filterReportsActionRemove', array($this, '_report_js_filterReportsActionRemove'));
					Event::add('ushahidi_action.report_js_keyToFilter', array($this, '_report_js_keyToFilter'));
					break;

				case 'fetch_reports':
					Event::add('ushahidi_filter.fetch_incidents_set_params', array($this, '_fetch_incidents_set_params'));
					break;
			}
		}
		elseif (Router::$controller == 'feed')
		{
			// Add Actionable Tag to RSS Feed
			Event::add('ushahidi_action.feed_rss_item', array($this, '_feed_rss'));
		}
		elseif (Router::$controller == 'reports')
		{
			Event::add('ushahidi_action.map_main_filters', array($this, '_map_main_filters'));
		}
		elseif (Router::$controller == 'main')
		{
			Event::add('ushahidi_action.map_main_filters', array($this, '_map_main_filters'));
		}
		elseif (Router::$controller == 'json' OR Router::$controller == 'bigmap_json')
		{
			Event::add('ushahidi_filter.fetch_incidents_set_params', array($this, '_fetch_incidents_set_params'));
			Event::add('ushahidi_filter.json_index_features', array($this, '_json_index_features'));

			// Never cluster actionable json
			if (Router::$method == 'cluster' AND $this->_check_media_type())
			{
				Router::$method = 'index';
			}
		} elseif(Router::$controller == 'export_reports' && Router::$method == 'index') {
            Event::add('ushahidi_filter.fetch_incidents_set_params', array($this, '_fetch_incidents_set_params_export'));
		}
	}

	/**
	 * Add Actionable Form input to the Report Submit Form
	 */
	public function _report_form()
	{
		// Load the View
		$form = View::factory('actionable_form');
		// Get the ID of the Incident (Report)
		$id = Event::$data;

		if ($id)
		{
			// Do We have an Existing Actionable Item for this Report?
			$action_item = ORM::factory('actionable')
				->where('incident_id', $id)
				->find();
			if ($action_item->loaded)
			{
				$this->actionable     = $action_item->actionable;
				$this->action_urgent  = $action_item->action_urgent;
				$this->action_taken   = $action_item->action_taken;
				$this->action_summary = $action_item->action_summary;
				$this->action_closed  = $action_item->action_closed;
			}
		}

		$form->actionable     = $this->actionable;
		$form->action_urgent  = $this->action_urgent;
		$form->action_taken   = $this->action_taken;
		$form->action_summary = $this->action_summary;
		$form->action_closed  = $this->action_closed;
		$form->render(TRUE);
	}

	/**
	 * Handle Form Submission and Save Data
	 */
	public function _report_form_submit()
	{
		$incident = Event::$data;

		if ($_POST)
		{
			$action_item = ORM::factory('actionable')
				->where('incident_id', $incident->id)
				->find();
			$action_item->incident_id = $incident->id;
			$action_item->actionable = isset($_POST['actionable']) ?
				$_POST['actionable'] : "";
		   $action_item->action_urgent = isset($_POST['action_urgent']) ?
				   $_POST['action_urgent'] : "";
			$action_item->action_taken = isset($_POST['action_taken']) ?
				$_POST['action_taken'] : "";
			$action_item->action_summary = $_POST['action_summary'];
			$action_item->action_closed = isset($_POST['action_closed']) ?
				$_POST['action_closed'] : "";
			$action_item->save();

		}
	}

	/**
	 * Render the Action Taken Information to the Report
	 * on the front end
	 */
	public function _report_view()
	{
		$incident_id = Event::$data;
		if ($incident_id)
		{
			$actionable = ORM::factory('actionable')
				->where('incident_id', $incident_id)
				->find();
			if ($actionable->loaded)
			{
				if ($actionable->actionable)
				{
					$report = View::factory('actionable_report');
					$report->actionable = $actionable->actionable;
					$report->action_urgent  = $actionable->action_urgent;
					$report->action_taken = $actionable->action_taken;
					$report->action_summary = $actionable->action_summary;
					$report->action_closed = $actionable->action_closed;
					$report->render(TRUE);
				}
			}
		}
	}

	/*
	 * Add actionable link to reports admin tabs
	 **/
	public function _report_link()
	{
		$this_sub_page = Event::$data;
		echo ($this_sub_page == "actionable") ? Kohana::lang('actionable.actionable') : "<a href=\"".url::site()."admin/actionable\">".Kohana::lang('actionable.actionable')."</a>";
	}

	/**
	 * Add the <actionable> tag to the RSS feed
	 */
	public function _feed_rss()
	{
		$incident_id = Event::$data;
		if ($incident_id)
		{
			$action_item = ORM::factory('actionable')
				->where('incident_id', $incident_id)
				->find();
			if ($action_item->loaded)
			{
				if ($action_item->actionable == 1) {
					echo "<actionable>YES</actionable>\n";
			    } else {
					   echo "<actionable>NO</actionable>\n";

				}
				if ($action_item->action_urgent == 1) {
					echo "<urgent>YES</urgent>\n";
				} else {
					echo "<urgent>NO</urgent>\n";
				}

				if ($action_item->action_taken) {
					echo "<actiontaken>YES</actiontaken>\n";
				} else {
					echo "<actiontaken>NO</actiontaken>\n";
				}

				if ($action_item->action_closed == 1) {
					echo "<actionclosed>YES</actionclosed>\n";
				} else {
					echo "<actionclosed>NO</actionclosed>\n";				
				}

			}
			else
			{
				echo "<actionable>NO</actionable>\n";
				echo "<urgent>NO</urgent>\n";
				echo "<actiontaken>NO</actiontaken>\n";
				echo "<actionclosed>NO</actionclosed>\n";				
			}
		}
	}

	/*
	 * Add actionable filters on main map
	 */
	public function _map_main_filters()
	{
		echo '</div><h3>'.Kohana::lang('actionable.actionable').'</h3><ul>';
		foreach ($this->media_values as $k => $val) {
			echo "<li><a id=\"media_$k\" href=\"#\"><span>$val</span></a></li>";
		}
		echo '</ul><div>';
	}

	/*
	* Add appropriate UI for reports page filter
	*/
	public function _report_filters_ui()
	{
		$filter = View::factory('actionable_filter');
		$filter->render(TRUE);
	}

	/*
	* Add appropriate filter logic for reports page
	*/
	public function _report_js_filterReportsAction()
	{
		$filter_js = View::factory('actionable_filter_js');
		$filter_js->render(TRUE);
	}

	/*
	* Remove appropriate filter logic for reports page
	*/
	public function _report_js_filterReportsActionRemove()
	{
		$filter_js = View::factory('actionable_filter_remove_js');
		$filter_js->render(TRUE);
	}

	/*
	* Remove appropriate filter logic for reports page
	*/
	public function _report_js_keyToFilter()
	{
		$filter_js = View::factory('actionable_filter_key_to_filter_js');
		$filter_js->render(TRUE);
	}

	/*
	* Perform the filtering of reports
	*//*
	public function _fetch_incidents_set_params($params)
	{
		echo '<pre>';
		var_dump($params);
		die();
	}*/

		/*
	 * Filter incidents for main map based on actionable status
	 */
	public function _fetch_incidents_set_params_export()
	{
		$params = Event::$data;

		// ---------- BEGIN HACKY IMPLEMENTATION (used on homepage map)
		if(!isset($_GET['plugin_actionable_filter']) OR !is_array($_GET['plugin_actionable_filter'])) {
			if(isset($_GET['plugin_actionable_filter']) && !is_array($_GET['plugin_actionable_filter'])) {
				// Fetch the URL data into a local variable
				$url_data = $_GET;
			
				// Split selected parameters on ","
				// For simplicity, always turn them into arrays even theres just one value
				$exclude_params = array('plugin_actionable_filter');
				foreach ($url_data as $key => $value)
				{
					if (in_array($key, $exclude_params) AND ! is_array($value))
					{
						$url_data[$key] = explode(",", $value);
					}
				}
				
			} else {
				// If we're doing the hacky fake media trick, run this.
				// Look for fake media type
				if ($filters = $this->_check_media_type())
				{
					// Remove media type filter based on fake actionable media type
					// @todo make this work with normal media filters too
					$sql = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'media WHERE media_type IN ('.implode(',',$this->_check_media_type()).'))';
					$key = array_search($sql, $params);
	
					if ($key !== FALSE)
					{
						unset($params[$key]);
					}
	
					$actionable_sql = array();
					foreach ($filters as $filter)
					{
						// Cast the $filter to int just in case
						$filter = intval($filter);
	
						// Add filter based on actionable status.
						switch ($filter)
						{
							case '102':
								$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
									WHERE actionable = 1 AND action_closed = 0)';
								break;
							case '103':
								$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
									WHERE action_urgent = 1 AND action_closed = 0)';
								break;
							case '104':
								$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
									WHERE action_taken = 1 AND action_closed = 0)';
								break;
							case '105':
								$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
									WHERE action_closed = 1)';
								break;
						}
					}
	
					if (count($actionable_sql) > 0)
					{
						$actionable_sql = '('.implode(' OR ',$actionable_sql).')';
						$params[] = $actionable_sql;
					}
				}
			Event::$data = $params;
			return;
			}
		
		}
		// ---------- END HACKY IMPLEMENTATION (used on homepage map)

		// This is the Non-hacky way of filtering reports

		$actionable_ids = isset($url_data['plugin_actionable_filter']) ? $url_data['plugin_actionable_filter'] : $_GET['plugin_actionable_filter']; 
		//$actionable_ids = $_GET['plugin_actionable_filter'];
		$actionable_sql = array();
		foreach($actionable_ids AS $id) {
			switch ($id) {
				case '5': //NotActionable
					$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
						WHERE actionable = 0 AND action_closed = 0)';
					break;
				case '1': //Actionable
					$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
						WHERE actionable = 1 AND action_closed = 0)';
					break;
				case '2': //Urgent
					$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
						WHERE action_urgent = 1 AND action_closed = 0)';
					break;
				case '3': //Closed
					$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
						WHERE action_closed = 1)';
					break;
				case '4': //ActionTaken
					$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
						WHERE action_taken = 1 AND action_closed = 0)';
					break;
			}
		}
		
		if (count($actionable_sql) > 0)
		{
			$actionable_sql = '('.implode(' OR ',$actionable_sql).')';	
			$params[] = $actionable_sql;			
		}

		Event::$data = $params;
	}

	/*
	 * Filter incidents for main map based on actionable status
	 */
	public function _fetch_incidents_set_params()
	{
		$params = Event::$data;
		// ---------- BEGIN HACKY IMPLEMENTATION (used on homepage map)
		if(!isset($_GET['plugin_actionable_filter']) OR !is_array($_GET['plugin_actionable_filter'])) {
			// If we're doing the hacky fake media trick, run this.
			// Look for fake media type
			if ($filters = $this->_check_media_type())
			{
				// Remove media type filter based on fake actionable media type
				// @todo make this work with normal media filters too
				$sql = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'media WHERE media_type IN ('.implode(',',$this->_check_media_type()).'))';
				$key = array_search($sql, $params);

				if ($key !== FALSE)
				{
					unset($params[$key]);
				}

				$actionable_sql = array();
				foreach ($filters as $filter)
				{
					// Cast the $filter to int just in case
					$filter = intval($filter);

					// Add filter based on actionable status.
					switch ($filter)
					{
						case '102':
							$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
								WHERE actionable = 1 AND action_closed = 0)';
							break;
						case '103':
							$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
								WHERE action_urgent = 1 AND action_closed = 0)';
							break;
						case '104':
							$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
								WHERE action_taken = 1 AND action_closed = 0)';
							break;
						case '105':
							$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
								WHERE action_closed = 1)';
							break;
					}
				}

				if (count($actionable_sql) > 0)
				{
					$actionable_sql = '('.implode(' OR ',$actionable_sql).')';
					$params[] = $actionable_sql;
				}
			}
			Event::$data = $params;
			return;
		}
		// ---------- END HACKY IMPLEMENTATION (used on homepage map)

		// This is the Non-hacky way of filtering reports

		$actionable_ids = $_GET['plugin_actionable_filter'];
		$actionable_sql = array();
		
		foreach($actionable_ids AS $id) {
			
			switch ($id) {
				case '5': //NotActionable
					$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
						WHERE actionable = 0 AND action_closed = 0)';
					break;
				case '1': //Actionable
					$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
						WHERE actionable = 1 AND action_closed = 0)';
					break;
				case '2': //Urgent
					$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
						WHERE action_urgent = 1 AND action_closed = 0)';
					break;
				case '3': //Closed
					$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
						WHERE action_closed = 1)';
					break;
				case '4': //ActionTaken
					$actionable_sql[] = 'i.id IN (SELECT DISTINCT incident_id FROM '.Kohana::config('database.default.table_prefix').'actionable
						WHERE action_taken = 1 AND action_closed = 0)';
					break;
			}
		}

		if (count($actionable_sql) > 0)
		{
			$actionable_sql = '('.implode(' OR ',$actionable_sql).')';
			$params[] = $actionable_sql;
		}

		Event::$data = $params;
	}

	/*
	 * Customise feature display based on actionable status
	 */
	public function _json_index_features()
	{
		if ($this->_check_media_type())
		{
			$features = Event::$data;
			$results = ORM::Factory('actionable')->find_all()->as_array();

			$actionables = array();
			foreach($results as $actionable)
			{
				$actionables[$actionable->incident_id] = $actionable;
			}

			foreach($features as $key => $feature)
			{
				$incident_id = $feature['properties']['id'];
				if ($actionables[$incident_id])
				{
					$feature['properties']['actionable'] = $actionables[$incident_id]->status();
					$feature['properties']['strokecolor'] = $actionables[$incident_id]->color();
					$feature['properties']['strokeopacity'] = 0.5;
					$feature['properties']['strokewidth'] = 5;
					$feature['properties']['radius'] = Kohana::config('map.marker_radius')*2.5;
					$feature['properties']['icon'] = '';
					$features[$key] = $feature;
				}
			}

			Event::$data = $features;
		}
	}

	/*
	 * Look for fake media types in GET param
	 */
	private function _check_media_type()
	{
		// Parse the GET param if we haven't yet.
		if (! isset($this->media_filter)) {
			$this->media_filter = array();
			if (isset($_GET['m']))
			{
				$this->media_filter = $_GET['m'];
				if (! is_array($this->media_filter))
				{
					$this->media_filter = explode(',',$this->media_filter);
				}
				// Keep only the
				$this->media_filter = array_intersect(array_keys($this->media_values), $this->media_filter);
			}
		}

		// Return filters, if any
		if (count($this->media_filter) > 0)
		{
			return $this->media_filter;
		}

		return FALSE;
	}

}

new actionable;
