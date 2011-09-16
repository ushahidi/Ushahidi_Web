<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Test Email Controller
 * Tests for IMAP Library
 * Tests pulling email via pop3 or imap
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Admin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Test_Email_Controller extends Admin_Controller {
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{		
		$this->template = "";
		$this->auto_render = FALSE;
		
		// First is IMAP PHP Library Installed?
		$modules = new Modulecheck;
		if ($modules->isLoaded('imap'))
		{
			// If SSL Enabled
			$ssl = Kohana::config('settings.email_ssl') == true ? "/ssl" : "";

			// Do not validate certificates (TLS/SSL server)
			//$novalidate = strtolower(Kohana::config('settings.email_servertype')) == "imap" ? "/novalidate-cert" : "";
			$novalidate = "/novalidate-cert";

			// If POP3 Disable TLS
			$notls = strtolower(Kohana::config('settings.email_servertype')) == "pop3" ? "/notls" : "";
			
			$service = "{".Kohana::config('settings.email_host').":"
				.Kohana::config('settings.email_port')."/"
				.Kohana::config('settings.email_servertype')
				.$notls.$ssl.$novalidate."}";
			
			// Connected!
			if (@imap_open($service, Kohana::config('settings.email_username')
				,Kohana::config('settings.email_password'), 0, 1))
			{
				echo json_encode(array("status"=>"success", "message"=>Kohana::lang('ui_main.success')));
			}
			// Connection Failed!
			else
			{
				echo json_encode(array("status"=>"error", "message"=>Kohana::lang('ui_main.error')." - ".imap_last_error()));
			}
		}
		// IMAP Not installed
		else
		{
			echo json_encode(array("status"=>"error", "message"=>Kohana::lang('ui_main.error')." - ".Kohana::lang('ui_admin.error_imap')));
		}
	}
}
