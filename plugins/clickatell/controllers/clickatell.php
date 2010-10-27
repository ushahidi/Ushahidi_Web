<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Clickatell HTTP Post Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   Clickatell Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class Clickatell_Controller extends Controller
{
	function index()
	{
		if (isset($_GET['key']))
		{
			$clickatell_key = $_GET['key'];
		}
		
		if (isset($_POST['s']))
		{
			$message_from = $_POST['s'];
			// Remove non-numeric characters from string
			$message_from = preg_replace("#[^0-9]#", "", $message_from);
		}
		
		if (isset($_POST['m']))
		{
			$message_description = $_POST['m'];
		}
		
		if ( ! empty($clickatell_key) AND ! empty($message_from) AND ! empty($message_description))
		{
			// Is this a valid FrontlineSMS Key?
			$keycheck = ORM::factory('clickatell')
				->where('clickatell_key', $clickatell_key)
				->find(1);

			if ($keycheck->loaded == TRUE)
			{
				sms::add($message_from, $message_description);
			}
		}
	}
}
