<?php defined('SYSPATH') or die('No direct script access.');
/**
* Custom 404 Error Page Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Error Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class Error_Controller extends Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function error_404() {
		Header("HTTP/1.0 404 Not Found");
		
		$this->layout = new View('error');
		$this->layout->title = "Page Not Found!";
		$this->layout->content = "Sorry, the page you are trying to view is not here.
		<p><strong>Did you follow a link from somewhere else on our site?</strong><br />
		If you reached this page from another part of our site, please <a href=\"#\">contact us</a> so that we can correct our mistake.</p>
		<p><strong>Did you follow a link from another site? </strong><br />
		Links from other sites can sometimes be outdated or misspelled. <a href=\"#\">Tell us</a> where you came from and we can try to contact the other site in order to fix the problem.</p>
		<p><strong>Did you type the URL? </strong><br />
		You may have typed the address (URL) incorrectly. Check to make sure you've got the exact right spelling, capitalization, etc. </p>";

		$this->layout->render(true);
	}
	
}
