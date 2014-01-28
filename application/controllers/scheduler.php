<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Scheduler Controller (FAUX Cron)
 * Generates 1x1 pixel image while executing scheduled tasks
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Scheduler_Controller extends Controller {
	public function __construct()
	{
		parent::__construct();
		// Increase max execution time
		if (ini_get('max_execution_time') < 180)
		{
			ini_set('max_execution_time', 180);
		}

		// Set time limit only if we're not on safe_mode and set_time_limit is enabled
		$safe_mode_enabled = ini_get('safe_mode');
		$disabled_functions = ini_get('disable_functions');

		if (empty($safe_mode_enabled) && strstr($disabled_functions, "set_time_limit") === false)
		{
			set_time_limit(180);
		}
	}

	public function index()
	{
		// Debug
		$debug = "";
		
		
		// @todo abstract most of this into a library, especially locking
		
		// Ensure settings entry for `scheduler_lock` exists
		Database::instance()->query(
			"INSERT IGNORE INTO `".Kohana::config('database.default.table_prefix')."settings`
			(`key`, `value`) VALUES ('scheduler_lock', 0)");
		
		// Now try and update the scheduler_lock
		$result = Database::instance()->query(
			"UPDATE `".Kohana::config('database.default.table_prefix')."settings`
			SET `value` = UNIX_TIMESTAMP() + 180
			WHERE `value` < UNIX_TIMESTAMP() AND `key` = 'scheduler_lock';");
		
		// If no entries were changed, scheduler is already running
		if ($result->count() <= 0)
		{
			Kohana::log('info', 'Could not acquire scheduler lock');
			if (isset($_GET['debug']) AND $_GET['debug'] == 1)
			{
				echo 'Could not acquire scheduler lock';
			}
			return;
		}

		// Get all active scheduled items
		foreach (ORM::factory('scheduler')
		->where('scheduler_active','1')
		->find_all() as $scheduler)
		{
			$scheduler_id = $scheduler->id;
			$scheduler_last = $scheduler->scheduler_last;
			// Next run time
			$scheduler_weekday = $scheduler->scheduler_weekday;
			// Day of the week
			$scheduler_day = $scheduler->scheduler_day;
			// Day of the month
			$scheduler_hour = $scheduler->scheduler_hour;
			// Hour
			$scheduler_minute = $scheduler->scheduler_minute;
			// Minute

			// Controller that performs action
			$scheduler_controller = $scheduler->scheduler_controller;

			if ($scheduler_day <= -1)
			{
				// Ran every day?
				$scheduler_day = "*";
			}

			if ($scheduler_weekday <= -1)
			{
				// Ran every day?
				$scheduler_weekday = "*";
			}

			if ($scheduler_hour <= -1)
			{
				// Ran every hour?
				$scheduler_hour = "*";
			}

			if ($scheduler_minute <= -1)
			{
				// Ran every minute?
				$scheduler_minute = "*";
			}

			$scheduler_cron = $scheduler_minute . " " . $scheduler_hour . " " . $scheduler_day . " * " . $scheduler_weekday;

			//Start new cron parser instance
			$cron = new CronParser();

			if (!$cron->calcLastRan($scheduler_cron))
			{
				echo "Error parsing CRON";
			}

			$lastRan = $cron->getLastRan();
			//Array (0=minute, 1=hour, 2=dayOfMonth, 3=month, 4=week, 5=year)
			$cronRan = mktime($lastRan[1], $lastRan[0], 0, $lastRan[3], $lastRan[2], $lastRan[5]);

			if (isset($_GET['debug']) AND $_GET['debug'] == 1)
			{
				$debug .= "~~~~~~~~~~~~~~~~~~~~~~~~~~~" . "<BR />~~~~~~~~~~~~~~~~~~~~~~~~~~~" . "<BR />RUNNING: " . $scheduler->scheduler_name . "<BR />~~~~~~~~~~~~~~~~~~~~~~~~~~~" . "<BR /> LAST RUN: " . date("r", $scheduler_last) . "<BR /> LAST DUE AT: " . date('r', $cron->getLastRanUnix()) . "<BR /> SCHEDULE: <a href=\"http://en.wikipedia.org/wiki/Cron\" target=\"_blank\">" . $scheduler_cron . "</a>";
			}

			if ($scheduler_controller AND (!($scheduler_last > $cronRan) OR $scheduler_last == 0))
			{
				$run = FALSE;

				// Catch errors from missing scheduler or other bugs
				try {
					$dispatch = Dispatch::controller($scheduler_controller, "scheduler/");

					if ($dispatch instanceof Dispatch && method_exists($dispatch,'method'))
					{
						$run = $dispatch->method('index', '');
					}
				}
				catch (Exception $e)
				{
					// Nada.
				}

				// @todo allow tasks to save state between runs. 

				if ($run !== FALSE)
				{
					// Set last time of last execution
					$schedule_time = time();
					$scheduler->scheduler_last = $schedule_time;
					$scheduler->save();

					// Record Action to Log
					$scheduler_log = new Scheduler_Log_Model();
					$scheduler_log->scheduler_id = $scheduler_id;
					$scheduler_log->scheduler_status = "200";
					$scheduler_log->scheduler_date = $schedule_time;
					$scheduler_log->save();

					if (isset($_GET['debug']) AND $_GET['debug'] == 1)
					{
						$debug .= "<BR /> STATUS: {{ EXECUTED }}";
					}
				}
				else
				{
					if (isset($_GET['debug']) AND $_GET['debug'] == 1)
					{
						$debug .= "<BR /> STATUS: {{ SCHEDULER NOT FOUND! }}";
					}
				}

			}
			else
			{
				if (isset($_GET['debug']) AND $_GET['debug'] == 1)
				{
					$debug .= "<BR /> STATUS: {{ NOT RUN }}";
				}
			}
			if (isset($_GET['debug']) AND $_GET['debug'] == 1)
			{
				//$debug .= "<BR /><BR />CRON DEBUG:<BR />".nl2br($cron->getDebug());
				$debug .= "<BR />~~~~~~~~~~~~~~~~~~~~~~~~~~~<BR />~~~~~~~~~~~~~~~~~~~~~~~~~~~<BR /><BR /><BR />";
			}
		}

		if (Kohana::config('cdn.cdn_gradual_upgrade') != FALSE)
		{
			cdn::gradual_upgrade();
		}

		// If DEBUG is TRUE echo DEBUG info instead of transparent GIF
		if (isset($_GET['debug']) AND $_GET['debug'] == 1)
		{
			echo $debug;
			if (isset($this->profiler))
			{
				echo $this->profiler->render(TRUE);
		}
		}
		else
		{
			// Transparent GIF
			Header("Content-type: image/gif");
			Header("Expires: Wed, 11 Nov 1998 11:11:11 GMT");
			Header("Cache-Control: no-cache");
			Header("Cache-Control: must-revalidate");
			Header("Content-Length: 49");
			echo pack('H*', '47494638396101000100910000000000ffffffff' . 'ffff00000021f90405140002002c000000000100' . '01000002025401003b');
		}
		
		// Release lock
		$result = Database::instance()->query(
			"UPDATE `".Kohana::config('database.default.table_prefix')."settings`
			SET `value` = 0
			WHERE `key` = 'scheduler_lock';");
		
	}

}
