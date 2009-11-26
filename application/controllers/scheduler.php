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
 * @module     Scheduler Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class Scheduler_Controller extends Controller
{
	public function __construct()
    {
        parent::__construct();
		// $profiler = new Profiler;
	}
	
	public function index()
	{
		// Get all active scheduled items
		foreach (ORM::factory('scheduler')
			->where('scheduler_active','1')
			->find_all() as $scheduler)
		{
			$scheduler_id = $scheduler->id;
			$scheduler_last = $scheduler->scheduler_last;			  // Next run time
			$scheduler_weekday = $scheduler->scheduler_weekday;		  // Day of the week
			$scheduler_day = $scheduler->scheduler_day;				  // Day of the month
			$scheduler_hour = $scheduler->scheduler_hour;			  // Hour
			$scheduler_minute = $scheduler->scheduler_minute;		  // Minute
			
			// Controller that performs action
			$scheduler_controller = $scheduler->scheduler_controller;
			
			if ($scheduler_day <= -1) 
			{ // Ran every day?
				$scheduler_day = "*";
			}
			
			
			if ($scheduler_weekday <= -1) 
			{ // Ran every day?
				$scheduler_weekday = "*";
			}
			
			
			if ($scheduler_hour <= -1) 
			{ // Ran every hour?
				$scheduler_hour = "*";
			}
			
			
			if ($scheduler_minute <= -1) 
			{ // Ran every minute?
				$scheduler_minute = "*";
			}
			
			
			$scheduler_cron = $scheduler_minute . " " . $scheduler_hour . " " . $scheduler_day . " * " . $scheduler_weekday;
			//Start new cron parser instance    	
			$cron = new CronParser($scheduler_cron);
			$lastRan = $cron->getLastRan(); //Array (0=minute, 1=hour, 2=dayOfMonth, 3=month, 4=week, 5=year)
			$cronRan = mktime ($lastRan[1] ,$lastRan[0],0 , $lastRan[3] ,$lastRan[2], $lastRan[5]);
			
			if (!($scheduler_last > ($cronRan-45)) || $scheduler_last == 0)
			{ // within 45 secs of cronRan time, so Execute control
				$site_url = url::base();
				$scheduler_status = remote::get( $site_url . "scheduler/" . $scheduler_controller );
				
				//XXX: ToDo Parse $scheduler_status
				
				// Set last time of last execution
				$schedule_time = time();
				$scheduler->scheduler_last = $schedule_time;
				$scheduler->save();

				// Record Action to Log				
				$scheduler_log = new Scheduler_Log_Model();
				$scheduler_log->scheduler_id = $scheduler_id;
				$scheduler_log->scheduler_name = $scheduler->scheduler_name;
				$scheduler_log->scheduler_status = "200";
				$scheduler_log->scheduler_date = $schedule_time;
				$scheduler_log->save();
			}
		}
		
	    //Header("Content-Type: image/gif");
		// Transparent GIF
		//echo base64_decode("R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==");
	}
}