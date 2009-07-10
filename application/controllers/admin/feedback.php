<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to manage users
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Users Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Feedback_Controller extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'users';
		
		// If this is not a super-user account, redirect to dashboard
		if (!$this->auth->logged_in('admin'))
        {
             url::redirect('admin/dashboard');
		}
	}
	
	function index()
	{	
		$this->template->content = new View('admin/feedback');
		$this->template->content->title = 'Feedback';
		
	}
	
}
