<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Private Messages Controller.
 * This controller will take care of adding and editing reports in the Member section.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   Member Private Messages Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Private_Controller extends Members_Controller {
	function __construct()
	{
		parent::__construct();

		$this->template->this_page = 'private';
	}


	/**
	 * Lists the private messages.
	 * @param int $page
	 */
	public function index($page = 1)
	{
		$this->template->content = new View('members/private');
		$this->template->content->title = Kohana::lang('ui_admin.private_messages');
		
		// Is this an Inbox or Outbox Filter?
		if (!empty($_GET['type']))
		{
			$type = $_GET['type'];

			if ($type == '2')
			{ // OUTBOX
				$filter = 'from_user_id';
			}
			else
			{ // INBOX
				$type = "1";
				$filter = 'user_id';
			}
		}
		else
		{
			$type = "1";
			$filter = 'user_id';
		}

		// check, has the form been submitted?
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
	
		}
		
		// Pagination
		$pagination = new Pagination(array(
			'query_string'	 => 'page',
			'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
			'total_items'	 => ORM::factory('private_message')
				->where($filter, $this->user->id)
				->count_all()
			));

		$messages = ORM::factory('private_message')
			->where($filter, $this->user->id)
			->orderby('private_message_date', 'desc')
			->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);
			
		$this->template->content->messages = $messages;
		$this->template->content->pagination = $pagination;
		$this->template->content->type = $type;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;

		// Total Messages
		$this->template->content->total_items = $pagination->total_items;

	}
	
	/**
	 * Send a new private message
	 */
	public function send()
	{
		$this->template->content = new View('members/private_send');
		$this->template->content->title = Kohana::lang('ui_admin.private_messages');
		
		// setup and initialize form field names
		$form = array
		(
			'private_to'  => '',
			'private_subject'  => '',
			'private_message'  => ''
		);
		
		//	Copy the form as errors, so the errors will be stored with keys
		//	corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		
		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			$post = Validation::factory($_POST);
			
			 //	 Add some filters
			$post->pre_filter('trim', TRUE);
			$post->add_rules('private_to','required');
			if ( ! empty($_POST['private_to']))
			{
				$to_array = array_filter( explode(",", trim($_POST['private_to'])) );
				foreach ($to_array as $name)
				{
					$this->_user_name_chk($name, $post);
				}
			}
			$post->add_rules('private_subject','required','length[3,150]');
			$post->add_rules('private_message','required');
			
			if ($post->validate())
			{
				$to_array = array_filter( explode(",", $post->private_to) );
				foreach ($to_array as $name)
				{
					$account = ORM::factory('user')
						->where("name", $name)
						->where("id !=".$this->user->id)
						->find();

					if ($account->loaded)
					{
						$message = ORM::factory('private_message');
						$message->user_id = $account->id;
						$message->from_user_id = $this->user->id;
						$message->private_subject = $post->private_subject;
						$message->private_message = $post->private_message;
						$message->private_message_date = date("Y-m-d H:i:s",time());
						$message->save();
					}
				}
				
				$form_saved = TRUE;
				
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());
			}
			else 
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('private_message'));
				$form_error = TRUE;
			}
		}

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		
		// Javascript Header
		$this->template->autocomplete_enabled = TRUE;
		$this->template->js = new View('members/private_send_js');
	}
	
	/**
	 * Retrieve User by UserName or Real Name
	 */ 
	public function get_user()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		$name = (isset($_GET['q'])) ? strtolower($_GET['q']) : "";
		
		if ($name)
		{
			$users = ORM::factory("user")
				->where("id !=".$this->user->id) 
				->where("LOWER(name) LIKE '%".$name."%'")
				->find_all();
				
			foreach ($users as $user)
			{
				echo "$user->name\n";
			}
		}
		else
		{
			return;
		}
	}
	
	/**
	 * Checks if user_id is associated with an account.
	 * @param Validation $post $_POST variable with validation rules 
	 */
	private function _user_name_chk( $name, $post )
	{
		$account = ORM::factory('user')
			->where("name", $name)
			->where("id !=".$this->user->id)
			->find();
			
		if ( ! $account->loaded)
		{
			echo "{{{$name}}}";
			$post->add_error('private_to','exists');
		}
	}
}