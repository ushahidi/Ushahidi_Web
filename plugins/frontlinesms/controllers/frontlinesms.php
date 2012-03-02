<?php defined('SYSPATH') or die('No direct script access.');
/**
* FrontlineSMS HTTP Post Controller
* Gets HTTP Post data from a FrontlineSMS Installation
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   FrontlineSMS Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class Frontlinesms_Controller extends Controller
{
	function index()
	{
		if (isset($_GET['key']))
		{
			$frontlinesms_key = $_GET['key'];
		}
		
		if (isset($_GET['s']))
		{
			$message_from = $_GET['s'];
			// Remove non-numeric characters from string
			$message_from = preg_replace("#[^0-9]#", "", $message_from);
		}
		
		if (isset($_GET['m']))
		{
			$message_description = $_GET['m'];
		}
		
		if ( ! empty($frontlinesms_key) AND ! empty($message_from) AND ! empty($message_description))
		{
			
			// Is this a valid FrontlineSMS Key?
			$keycheck = ORM::factory('frontlinesms')
				->where('frontlinesms_key', $frontlinesms_key)
				->find(1);

			if ($keycheck->loaded == TRUE)
			{
					sms::add($message_from, $message_description);
			}
		}
	}
}
