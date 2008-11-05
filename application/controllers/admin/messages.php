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
		
		// Javascript Header
		$this->template->js = new View('admin/messages_js');
	}
	
	/**
	* Send A New Message Using Clickatell Library
    */
	function send()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		// setup and initialize form field names
		$form = array
	    (
			'to_id' => '',
			'message' => ''
	    );
        //  Copy the form as errors, so the errors will be stored with keys
        //  corresponding to the form field names
        $errors = $form;
		$form_error = FALSE;
		
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
            // Instantiate Validation, use $post, so we don't overwrite $_POST
            // fields with our own things
            $post = new Validation($_POST);

	        // Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('to_id', 'required', 'numeric');
			$post->add_rules('message', 'required', 'length[1,160]');
			
			// Test to see if things passed the rule checks
	        if ($post->validate())
	        {
				// Yes! everything is valid				
				$reply_to = ORM::factory('message', $post->to_id);
				if ($reply_to->loaded == true) {
					// Yes! Replyto Exists
					// This is the message we're replying to
					$sms_to = $reply_to->message_from;
					
					// Load Users Settings
					$settings = new Settings_Model(1);
					if ($settings->loaded == true) {
						// Get SMS Numbers
						if (!empty($settings->sms_no3)) {
							$sms_from = $settings->sms_no3;
						}elseif (!empty($settings->sms_no2)) {
							$sms_from = $settings->sms_no2;
						}elseif (!empty($settings->sms_no1)) {
							$sms_from = $settings->sms_no1;
						}else{
							$sms_from = "000";		// User needs to set up an SMS number
						}
						
						// Create Clickatell Object
						$mysms = new Clickatell();
						$mysms->api_id = $settings->clickatell_api;
						$mysms->user = $settings->clickatell_username;
						$mysms->password = $settings->clickatell_password;
						$mysms->use_ssl = false;
						$mysms->sms();
						$send_me = $mysms->send ($sms_to, $sms_from, $post->message);
					
						// Message Went Through??
						if ($send_me == "OK") {
							$newmessage = ORM::factory('message');
							$newmessage->parent_id = $post->to_id;	// The parent message
							$newmessage->message_from = $sms_from;
							$newmessage->message_to = $sms_to;
							$newmessage->message = $post->message;
							$newmessage->message_type = 2;			// This is an outgoing message
							$newmessage->message_date = date("Y-m-d H:i:s",time());
							$newmessage->save();
							
							echo json_encode(array("status"=>"sent", "message"=>"Your message has been sent!"));
						}
						// Message Failed
						else {
							echo json_encode(array("status"=>"error", "message"=>"Error! - " . $send_me));
						}
					}
					else
					{
						echo json_encode(array("status"=>"error", "message"=>"Error! Please check your SMS settings!"));
					}
				}
				// Send_To Mobile Number Doesn't Exist
				else {
					echo json_encode(array("status"=>"error", "message"=>"Error! Please make sure your message is valid!"));
				}
	        }
	                    
            // No! We have validation errors, we need to show the form again,
            // with the errors
            else
	        {
	            // populate the error fields, if any
	            $errors = arr::overwrite($errors, $post->errors('messages'));
				echo json_encode(array("status"=>"error", "message"=>"Error! Please make sure your message is valid!"));
	        }
	    }
		
	}
		
}