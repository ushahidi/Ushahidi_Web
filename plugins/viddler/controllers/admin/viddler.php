<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Viddler Admin Controller 
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

class Viddler_Controller extends Controller {
	
	public function __construct()
    {
    	
    }
	
	function index()
	{
		
	}
	
	// Remove a video
	function delete($viddler_id){
		$viddler = new Viddler;
		$viddler->delete($viddler_id);
	}
}