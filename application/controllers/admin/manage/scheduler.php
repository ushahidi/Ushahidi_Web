<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Scheduler
 * This controller is used to manage the scheduled tasks
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

class Scheduler_Controller extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'manage';

		// If user doesn't have access, redirect to dashboard
		if ( ! $this->auth->has_permission("manage"))
		{
			url::redirect(url::site().'admin/dashboard');
		}
	}

	function index()
	{
		$this->template->content = new View('admin/manage/scheduler/main');

		// Check if we should be running the scheduler and then do it
		if (isset($_GET['run_scheduler'])){
			// Get all active scheduled items
			foreach (ORM::factory('scheduler')
				->where('scheduler_active','1')
				->find_all() as $scheduler)
			{
				$s_controller = $scheduler->scheduler_controller;
				try {
					$dispatch = Dispatch::controller($s_controller, "scheduler/");
					if ($dispatch instanceof Dispatch) $run = $dispatch->method('index', '');
				}
				catch (Exception $e)
				{
					$run = FALSE;
				}
				
				if ($run !== FALSE)
				{
					// Set last time of last execution
					$schedule_time = time();
					$scheduler->scheduler_last = $schedule_time;
					$scheduler->save();

					// Record Action to Log
					$scheduler_log = new Scheduler_Log_Model();
					$scheduler_log->scheduler_id = $scheduler->id;
					$scheduler_log->scheduler_status = "200";
					$scheduler_log->scheduler_date = $schedule_time;
					$scheduler_log->save();
				}
			}
		}

		// setup and initialize form field names
		$form = array(
			'action' => '',
			'schedule_id'	  => '',
			'scheduler_weekday'	  => '',
			'scheduler_day'	  => '',
			'scheduler_hour'	  => '',
			'scheduler_minute'	  => '',
			'scheduler_active'  => ''
		);
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";

		if ($_POST)
		{
			//print_r($_POST);
			$post = Validation::factory( $_POST );

			 //  Add some filters
			$post->pre_filter('trim', TRUE);

			if ($post->action == 'a')		// Add Action
			{
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('scheduler_weekday','required', 'between[1,7]');
				$post->add_rules('scheduler_day','required', 'between[-1,31]');
				$post->add_rules('scheduler_hour','required', 'between[-1,23]');
				$post->add_rules('scheduler_minute','required', 'between[-1,59]');
			}

			if ( $post->validate() )
			{
				$scheduler_id = $post->scheduler_id;

				$scheduler = new Scheduler_Model($scheduler_id);
				if ($post->action == 'v')
				{ // Active/Inactive Action
					if ($scheduler->loaded == TRUE)
					{
						$scheduler->scheduler_active = ($scheduler->scheduler_active == 1) ? 0 : 1;
						
						$scheduler->save();
						$form_saved = TRUE;
						$form_action = utf8::strtoupper(Kohana::lang('ui_admin.modified'));
					}
				}
				else
				{ // SAVE Schedule
					$scheduler->scheduler_weekday = $post->scheduler_weekday;
					$scheduler->scheduler_day = $post->scheduler_day;
					$scheduler->scheduler_hour = $post->scheduler_hour;
					$scheduler->scheduler_minute = $post->scheduler_minute;
					$scheduler->save();
					$form_saved = TRUE;
					$form_action = utf8::strtoupper(Kohana::lang('ui_admin.edited'));
				}

			} else {
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

               // populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('scheduler'));
				$form_error = TRUE;
			}
		}

        // Pagination
		$pagination = new Pagination(array(
			'query_string' => 'page',
			'items_per_page' => $this->items_per_page,
			'total_items'	=> ORM::factory('scheduler')->count_all()
			));

		$schedules = ORM::factory('scheduler')
			->orderby('scheduler_name', 'asc')
			->find_all($this->items_per_page, $pagination->sql_offset);

		$this->template->content->weekday_array = array(
			"-1"=>"ALL",
			"0"=>"Sunday",
			"1"=>"Monday",
			"2"=>"Tuesday",
			"3"=>"Wednesday",
			"4"=>"Thursday",
			"5"=>"Friday",
			"6"=>"Saturday",
			);

		for ($i=0; $i <= 31 ; $i++)
		{
			$day_array = $i;
		}
		$this->template->content->day_array = $day_array;

		$day_array = array();
		$day_array[-1] = "ALL";
		for ($i=1; $i <= 31 ; $i++)
		{
			$day_array[] = $i;
		}
		$this->template->content->day_array = $day_array;

		$hour_array = array();
		$hour_array[-1] = "ALL";
		for ($i=0; $i <= 23 ; $i++)
		{
			$hour_array[] = $i;
		}
		$this->template->content->hour_array = $hour_array;

		$minute_array = array();
		$minute_array[-1] = "ALL";
		for ($i=0; $i <= 59 ; $i++)
		{
			$minute_array[] = $i;
		}
		$this->template->content->minute_array = $minute_array;

		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->template->content->schedules = $schedules;
		$this->template->content->errors = $errors;

        // Javascript Header
		$this->template->js = new View('admin/manage/scheduler/scheduler_js');
	}


	public function log()
	{
		$this->template->content = new View('admin/manage/scheduler/log');

		// Pagination
		$pagination = new Pagination(array(
			'query_string'   => 'page',
			'items_per_page' => $this->items_per_page,
			'total_items'	=> ORM::factory('scheduler_log')
				->count_all()
		));


		$scheduler_logs = ORM::factory('scheduler_log')
			->orderby('scheduler_date','desc')
			->find_all($this->items_per_page, $pagination->sql_offset);

		$this->template->content->scheduler_logs = $scheduler_logs;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
	}
}
