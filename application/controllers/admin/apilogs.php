<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Comments Controller.
 * This controller will take care of viewing and editing comments in the Admin section.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Reports Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Apilogs_Controller extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'apilogs';
	
	}
	
	
	/**
	* Lists the api logs.
    * @param int $page
    */
	function index()
	{
		$this->template->content = new View('admin/apilogs');
		$this->template->content->title = 'API Logs';
		$this->template->content->this_page = 'apilogs';
		// check, has the form been submitted?
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
	    if ($_POST)
	    {
			$post = Validation::factory($_POST);
			
	        //  Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('action','required', 'alpha', 'length[1,1]');
			$post->add_rules('api_log_id.*','required','numeric');
			
			if ($post->validate())
	        {
				
				if ($post->action == 'd')	// Delete Action
				{
					foreach($post->api_log_id as $item)
					{
						$update = new API_Log_Model($item);
						if ($update->loaded == true)
						{
							$update->delete();
						}					
					}
					$form_action = "DELETED";
				}
				elseif ($post->action == 'x')	// Delete All Logs Action
				{
					ORM::factory('api_log')->delete_all();
					$form_action = "DELETED";
				}
				$form_saved = TRUE;
			}
			else
			{
				$form_error = TRUE;
			}
			
		}
		
		
		// Pagination
		$pagination = new Pagination(array(
			'query_string'    => 'page',
			'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
			'total_items'    => ORM::factory('api_log')->count_all()
		));

		$api_logs = ORM::factory('api_log')->orderby('api_date', 'desc')->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);
		
		$this->template->content->api_logs = $api_logs;
		$this->template->content->pagination = $pagination;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		
		// Total Reports
		$this->template->content->total_items = $pagination->total_items;
		
		// Javascript Header
		$this->template->js = new View('admin/apilogs_js');		
	}
	
	/**
	* Lists the api logs.
    * @param int $page
    */
	function apibanned()
	{
		$this->template->content = new View('admin/api_banned');
		$this->template->content->title = 'API Banned';
		$this->template->content->this_page = 'apibanned';
		
		// check, has the form been submitted?
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
	    if ($_POST)
	    {
			$post = Validation::factory($_POST);
			
	        //  Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('action','required', 'alpha', 'length[1,1]');
			$post->add_rules('api_ban_id.*','required','numeric');
			
			if ($post->validate())
	        {
				
				if ($post->action == 'd')	// Unban Action
				{
					foreach($post->api_banned_id as $item)
					{
						$update = new API_Banned_Model($item);
						if ($update->loaded == true)
						{
							$update->delete();
						}					
					}
					$form_action = "UNBANNED";
				}
				elseif ($post->action == 'x')	// Unban All Logs Action
				{
					
					ORM::factory('api_banned')->delete_all();
					$form_action = "UNBANNED";
				}
				$form_saved = TRUE;
			}
			else
			{
				$form_error = TRUE;
			}
			
		}
		
		
		// Pagination
		$pagination = new Pagination(array(
			'query_string'    => 'page',
			'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
			'total_items'    => ORM::factory('api_banned')->count_all()
		));

		$api_bans = ORM::factory('api_banned')->orderby('banned_date', 'desc')->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);
		
		$this->template->content->api_bans = $api_bans;
		$this->template->content->pagination = $pagination;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		
		// Total Reports
		$this->template->content->total_items = $pagination->total_items;
		
		// Javascript Header
		$this->template->js = new View('admin/api_banned_js');		
	}
	
}
