<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller handles RiverID
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Riverid_Controller extends Template_Controller {
	
	public $auto_render = TRUE;
	
	// Session Object
	protected $session;
	
	// Main template
	public $template = 'riverid';
	
	// RiverID Library Object
	public $riverid;
	

	public function __construct()
	{
		parent::__construct();
		
		$this->session = new Session();
		
		// Fire up the RiverID Object
		$this->riverid = new RiverID;
		
		// Set riverid vars
		$this->riverid->email = @$_GET['email'];
		
		header('Content-type: application/json');
		
	}
	
	public function index()
	{
		// We don't have anything here, maybe return some kind of status code
		//   that says we aren't passing variables
	}
	
	public function registered()
	{	
		$this->template->json = $this->riverid->registered();
	}
	
	
}
