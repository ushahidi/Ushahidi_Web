<?php defined('SYSPATH') or die('No direct script access.');
/**
* FrontlineSMS HTTP Post Controller
* Gets HTTP Post data from a FrontlineSMS Installation
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     FrontlineSMS Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class Frontlinesms_Controller extends Controller
{
	function index()
	{
		if (isset($_GET['key'])) {
			$frontlinesms_key = $_GET['key'];
		}
		
		if (isset($_GET['s'])) {
			$message_from = $_GET['s'];
			// Remove non-numeric characters from string
			$message_from = ereg_replace("[^0-9]", "", $message_from);
		}
		
		if (isset($_GET['m'])) {
			$message = $_GET['m'];
		}
		
		if (!empty($frontlinesms_key) && !empty($message_from) && !empty($message))
		{
			// Is this a valid FrontlineSMS Key?
			$keycheck = ORM::factory('settings', 1)->where('frontlinesms_key', $frontlinesms_key)->find();
			if ($keycheck->loaded==true)
			{
				// Is this cell phone number already in the DB?
				// If so, use as parent_id
				$parent = ORM::factory('message')->where('message_from', $message_from)->where('parent_id', 0)->find();
				if ($parent->loaded==true)
				{
					$newmessage = ORM::factory('message');
					$newmessage->parent_id = $parent->id;
					$newmessage->message_from = $message_from;
					$newmessage->message = $message;
					$newmessage->message_date = date("Y-m-d H:i:s",time());
					$newmessage->save();
				}
				// Cell phone number not in DB
				else
				{
					$newmessage = ORM::factory('message');
					$newmessage->message_from = $message_from;
					$newmessage->message = $message;
					$newmessage->message_date = date("Y-m-d H:i:s",time());
					$newmessage->save();
				}
			}
		}
	}
}
