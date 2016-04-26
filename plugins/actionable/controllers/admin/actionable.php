<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Actionable Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author		 Ushahidi Team <team@ushahidi.com> 
 * @package		Ushahidi - http://source.ushahididev.com
 * @module		 Actionable Controller	
 * @copyright	Ushahidi - http://www.ushahidi.com
 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
* 
*/
// Manually require the admin reports controller - so we don't get the frontend controller instead
require_once(Kohana::find_file('controllers', 'admin/reports'));

class Actionable_Controller extends Reports_Controller {
	
	//public $auto_render = FALSE;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->template->this_page = 'actionable';
	}
	
	public function index($page = 1)
	{
		// If user doesn't have access, redirect to dashboard
		if ( ! admin::permissions($this->user, "reports_view"))
		{
			url::redirect(url::site().'admin/dashboard');
		}
		
		// Set status
		//$this->template->content->status = $status;
		$status = "0";
		if ( !empty($_GET['status']))
		{
			$status = $_GET['status'];

			if (strtolower($status) == 'action')
			{
				$actionable_filter = 'actionable = 1 AND action_taken = 0';
				$this->params['actionable'] = 'i.id IN (SELECT DISTINCT incident_id FROM `'.Kohana::config('database.default.table_prefix').'actionable` WHERE actionable = 1 AND action_taken = 0)';
			}
			elseif (strtolower($status) == 'urgent')
			{
				$actionable_filter = 'actionable = 2 AND action_taken = 0';
				$this->params['actionable'] = 'i.id IN (SELECT DISTINCT incident_id FROM `'.Kohana::config('database.default.table_prefix').'actionable` WHERE actionable = 2 AND action_taken = 0)';
			}
			elseif (strtolower($status) == 'taken')
			{
				$actionable_filter = 'action_taken = 1';
				$this->params['actionable'] = 'i.id IN (SELECT DISTINCT incident_id FROM `'.Kohana::config('database.default.table_prefix').'actionable` WHERE action_taken = 1)';
			}
			elseif (strtolower($status) == 'na')
			{
				$actionable_filter = 'actionable = 0 AND action_taken = 0';
				$this->params['actionable'] = '(
					i.id IN (SELECT DISTINCT incident_id FROM `'.Kohana::config('database.default.table_prefix').'actionable` WHERE actionable = 0 AND action_taken = 0) OR
					i.id NOT IN (SELECT DISTINCT incident_id FROM `'.Kohana::config('database.default.table_prefix').'actionable`)
				)';
			}
			else
			{
				$status = "0";
			}
		}
		
		parent::index($page);
		
		$this->template->content->status = $status;
		
		// Grab all actionable entries at once
		$factory = ORM::factory('actionable');
		if (isset($actionable_filter)) $factory->where($actionable_filter);
		
		$actionables = array();
		foreach ($factory->find_all() as $actionable) {
			$actionables[$actionable->incident_id] = $actionable;
		}
		
		$incidents = array();
		foreach ($this->template->content->incidents as $k => $incident)
		{
			if (isset($actionables[$incident->incident_id]))
			{
				$incident->actionable = $actionables[$incident->incident_id]->status();
			}
			$incidents[] = $incident;
		}
		$this->template->content->incidents = $incidents;
		
		// Set the filename
		$this->template->content->set_filename('admin/actionable');
		//$this->template->render();
	}
}
