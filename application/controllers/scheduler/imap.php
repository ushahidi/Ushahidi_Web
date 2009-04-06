<?php defined('SYSPATH') or die('No direct script access.');
/**
 * IMAP/ POP3 Scheduler Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Alerts Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class Imap_Controller extends Scheduler_Controller
{
	public function __construct()
    {
        parent::__construct();
	}	
	
	public function index() 
	{
		
		// 'pop3' or 'imap'
		$imap = Imap::factory('pop3');
		$messages = $imap->get_messages();

		var_dump($messages);

		foreach ($messages as $message)
		{
			
			$incident = ORM::factory('incident');
			$incident->incident_title = $message['subject'];
			$incident->incident_date = $message['date'];
			$incident->incident_description = $message['body'];
			$incident->save();
		}
    }
}
