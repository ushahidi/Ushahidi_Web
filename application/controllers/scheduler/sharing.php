<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Sharing Scheduler Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Sharing Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
*/

class Sharing_Controller extends Controller
{
	public function __construct()
    {
        parent::__construct();
	}
	
	public function index()
	{
		// Get all currently active shares
		$shares = ORM::factory('sharing')
			->where('sharing_type', 1)
			->where('sharing_active', 1)
			->find_all();
		
		foreach ($shares as $share)
		{
			$sharing_key = $share->sharing_key;
			$sharing_url = "http://".$share->sharing_url;
			
			// Sharing Library
			$sharing_connect = new Sharing;
			if ( !($sharing_connect->share_notify($sharing_url, $sharing_key, 'request')) )
			{ // Successful Request!
				
			}
			
		}
	}
}