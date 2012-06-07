<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller handles login requests.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Members
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Login_Controller extends Template_Controller {
	
	public $auto_render = TRUE;
	
	// Session Object
	protected $session;
	
	// Main template
	public $template = 'login';
	

	public function __construct()
	{
		parent::__construct();
		
		// REDIRECT TO NEW LOGIN CONTROLLER
		
		url::redirect("login");
	}
	
	public function index($user_id = 0)
	{
	
		// REDIRECT TO NEW LOGIN CONTROLLER
		
		url::redirect("login");
	}
	
	public function verify()
	{
		
		// REDIRECT TO NEW LOGIN CONTROLLER
		
		url::redirect("login");
	}
}
