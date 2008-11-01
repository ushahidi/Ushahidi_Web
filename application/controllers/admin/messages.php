<?php defined('SYSPATH') or die('No direct script access.');

/**
* Messages Controller.
* View SMS Messages Received Via FrontlineSMS
*/
class Messages_Controller extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
	
		$this->template->this_page = 'messages';		
	}
	
	/**
	* Lists the messages.
    * @param int $page
    */
	function index($page = 1)
	{
		$this->template->content = new View('admin/messages');
		$this->template->content->title = 'Messages';
		
		// Is this an Inbox or Outbox Filter?
		if (!empty($_GET['type']))
		{
			$type = $_GET['type'];
			
			if ($type == '2')
			{
				$filter = 'message_type = 2';
			}
			else
			{
				$type = "1";
				$filter = 'message_type = 1';
			}
		}
		else
		{
			$type = "1";
			$filter = 'message_type = 1';
		}
		
		// check, has the form been submitted?
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		// Pagination
		$pagination = new Pagination(array(
			'query_string'    => 'page',
			'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
			'total_items'    => ORM::factory('message')->where($filter)->count_all()
		));

		$messages = ORM::factory('message')->where($filter)->orderby('message_date', 'desc')->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);
		
			$this->template->content->messages = $messages;
			$this->template->content->pagination = $pagination;
			$this->template->content->form_error = $form_error;
			$this->template->content->form_saved = $form_saved;
			$this->template->content->form_action = $form_action;

			// Total Reports
			$this->template->content->total_items = $pagination->total_items;

			// Message Type Tab - Inbox/Outbox
			$this->template->content->type = $type;	
		}
		
}